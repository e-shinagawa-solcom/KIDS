
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
    // ヘッダタブ 製品コード イベント登録
    $('input[name="ProductCode"]').on({
        'change': function () {
            var revisecode = $('input[name="strReviseCode"]').val();
            // 金型リスト索引
            selectMoldSelectionListForListSearch($(this), revisecode);
        }
    });
    // ヘッダタブ 再販コード イベント登録
    $('input[name="strReviseCode"]').on({
        'change': function () {
            var revisecode = $(this).val();
            var productcode = $('input[name="ProductCode"]');
            if (productcode.val() != "") {
                // 金型リスト索引
                selectMoldSelectionListForListSearch(productcode, revisecode);
            }
        }
    });
    // 製品コードから金型リストを索引
    var selectMoldSelectionListForListSearch = function (invoker, revisecode) {
        console.log("製品コード->金型リスト change");
        var queryname = 'selectMoldByProductcodeForListSearch';
        var conditions = {
            ProductCode: $(invoker).val()
        };
        if (revisecode != "") {
            queryname = 'selectMoldByCodeForListSearch';
            conditions = {
                ProductCode: $(invoker).val(),
                ReviseCode: revisecode
            };
        }
        // 検索条件
        var condition = {
            data: {
                QueryName: queryname,
                Conditions: conditions
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("製品コード->金型リスト done");

                // 金型セレクトボックスの取得
                var moldList = $('.mold-selection__list');
                var moldChoosenList = $('.mold-selection__choosen-list');

                // 既存OPTION要素の削除
                moldList.find('option').remove();

                // 索引件数分走査
                $.each(response, function (index, row) {
                    // OPTION要素作成
                    moldList.append(
                        $('<option>')
                            .val(row.strmoldno)
                            .html(row.strmoldno)
                    );
                });
            })
            .fail(function (response) {
                console.log("製品コード->金型リスト fail");
                console.log(response.responseText);

                // 金型セレクトボックスの取得
                var moldList = $('.mold-selection__list');
                var moldChoosenList = $('.mold-selection__choosen-list');

                // 既存OPTION要素の削除
                moldList.find('option').remove();
            });
    };
})();
