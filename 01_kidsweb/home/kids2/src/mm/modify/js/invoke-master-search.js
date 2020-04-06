
(function () {

    // マスタ検索共通
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $('input[name="strSessionID"]').val(),
        type: 'post',
        dataType: 'json'
    };

    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            console.log($(this).find('img.msw-button'));
            $(this).find('img').click();
        }
    });
    // --------------------------------------------------------------------------
    // イベント登録
    // --------------------------------------------------------------------------
    // 保管元工場/移動先工場-表示会社コード イベント登録
    $('input[name="SourceFactory"], input[name="DestinationFactory"]').on({
        'change': function () {
            // 表示名を索引
            selectFactoryName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを表示会社名に合わせる
            $(this).next('input').focus();
        }
    });

    // --------------------------------------------------------------------------
    // 工場-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 工場-表示会社コードから表示名を索引
    var selectFactoryName = function (invoker) {
        console.log("工場-表示会社コード->表示名 change");
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="' + $(invoker).attr('name') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="' + $(invoker).attr('name') + '"]';

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectFactoryName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("工場-表示会社コード->表示名 done");
                // 工場-表示名に値をセット
                $(targetCssSelector).val(response[0].companydisplayname);
                if ($(invoker).attr('name') == "SourceFactory") {
                    $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
                }
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                var listlength = $('.mold-selection__choosen-list').find('option').length;
                if ($(invoker).attr('name') == "SourceFactory") {
                    if (listlength > 0) {
                        $('input[name="SourceFactoryName"] + img').css('visibility', 'visible');
                    } else {
                        $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
                    }
                }
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

})();
