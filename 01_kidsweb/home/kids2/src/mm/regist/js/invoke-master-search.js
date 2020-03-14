
(function () {

    // マスタ検索共通
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
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

            revisecode = $('input[name="ReviseCode"]').val();
            if (revisecode != "") {
                // 製品名称索引
                selectProductByCode($(this), revisecode);
                // 金型リスト索引
                selectMoldSelectionListByReviseCode($(this), revisecode);
            } else {
                // 製品名称索引
                selectProductName($(this));
            }
        }
    });


    // ヘッダタブ 製品コード イベント登録
    $('input[name="ReviseCode"]').on({
        'change': function () {
            var revisecode = $(this).val();
            var productcode = $('input[name="ProductCode"]');
            if (productcode.val() != "") {
                // 製品名称索引
                selectProductByCode(productcode, revisecode);
                // 金型リスト索引
                selectMoldSelectionListByReviseCode(productcode, revisecode);
            }
        }
    });
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
    // 製品コード、再販コードによるデータ索引
    // --------------------------------------------------------------------------
    // 製品コード、再販コードから製品名称を検索
    var selectProductByCode = function (invoker, revisecode) {
        console.log("製品コード->製品名称 change");
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectProductByCode',
                Conditions: {
                    ProductCode: invoker.val(),
                    ReviseCode: revisecode
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log(response);
                console.log(response.length);
                console.log("製品コード->製品名称 done");
                // ヘッダタブ/詳細タブの製品コード及び製品名称に値をセット
                $('input[name="ProductCode"]').val(invoker.val());
                $('input[name="ReviseCode"]').val(response[0].revisecode);
                $('input[name="ProductName"]').val(response[0].productname);

                // JQuery Validation Pluginで検知させる為イベントキック
                $('input[name="ProductCode"]').trigger('blur');
                $('input[name="ReviseCode"]').trigger('blur');
                $('input[name="ProductName"]').trigger('blur');
            })
            .fail(function (response) {
                console.log("製品コード->製品名称 fail");
                console.log(response.responseText);
                // ヘッダタブ/詳細タブの製品コード及び製品名称の値をリセット
                $('input[name="ProductCode"]').val('');
                $('input[name="ReviseCode"]').val('');
                $('input[name="ProductName"]').val('');

                // JQuery Validation Pluginで検知させる為イベントキック
                $('input[name="ProductCode"]').trigger('blur');
                $('input[name="ReviseCode"]').trigger('blur');
                $('input[name="ProductName"]').trigger('blur');
            });
    };
    // --------------------------------------------------------------------------
    // 製品コードによるデータ索引
    // --------------------------------------------------------------------------
    // 製品コードから製品名称を検索
    var selectProductName = function (invoker) {
        console.log("製品コード->製品名称 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectProductName',
                Conditions: {
                    ProductCode: invoker.val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("製品コード->製品名称 done");
                // ヘッダタブ/詳細タブの製品コード及び製品名称に値をセット
                $('input[name="ProductCode"]').val(invoker.val());
                if (response.length == 1) {
                    $('input[name="ReviseCode"]').val(response[0].revisecode);
                    $('input[name="ProductName"]').val(response[0].productname);
                    revisecode = response[0].revisecode;
                    // 金型リスト索引
                    selectMoldSelectionListByReviseCode(invoker, revisecode);
                }

                // JQuery Validation Pluginで検知させる為イベントキック
                $('input[name="ProductCode"]').trigger('blur');
                $('input[name="ProductName"]').trigger('blur');
            })
            .fail(function (response) {
                console.log("製品コード->製品名称 fail");
                console.log(response.responseText);
                // ヘッダタブ/詳細タブの製品コード及び製品名称の値をリセット
                $('input[name="ProductCode"]').val('');
                $('input[name="ProductName"]').val('');

                // JQuery Validation Pluginで検知させる為イベントキック
                $('input[name="ProductCode"]').trigger('blur');
                $('input[name="ProductName"]').trigger('blur');
            });
    };

    // 製品コードから金型リストを索引
    var selectMoldSelectionListByReviseCode = function (invoker, revisecode) {
        console.log("製品コード->金型リスト change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectMoldSelectionListByReviseCode',
                Conditions: {
                    ProductCode: $(invoker).val(),
                    ReviseCode: revisecode
                }
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
                moldChoosenList.find('option').remove();

                // 索引件数分走査
                $.each(response, function (index, row) {
                    // OPTION要素作成
                    moldList.append(
                        $('<option>')
                            .val(row.moldno)
                            .attr('displaycode', row.companydisplaycode)
                            .html(row.moldno + ' : ' + '[' + row.companydisplaycode + ']' + ' ' + row.companydisplayname)
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
                moldChoosenList.find('option').remove();
            });
    };

    // --------------------------------------------------------------------------
    // 担当グループ-表示グループコードによるデータ索引
    // --------------------------------------------------------------------------
    // 担当グループ-表示グループコードから表示名を索引
    var selectGroupName = function (invoker) {
        console.log("担当グループ-表示グループコード->表示名 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectGroupName',
                Conditions: {
                    GroupDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("担当グループ-表示グループコード->表示名 done");
                // 事業部(顧客)-表示名に値をセット
                $('input[name="KuwagataGroupName"]').val(response[0].groupdisplayname);
            })
            .fail(function (response) {
                console.log("担当グループ-表示グループコード->表示名 fail");
                console.log(response.responseText);
                // 事業部(顧客)-表示名の値をリセット
                $('input[name="KuwagataGroupName"]').val('');
            });
    };
    // --------------------------------------------------------------------------
    // 担当者-表示ユーザコードによるデータ索引
    // --------------------------------------------------------------------------
    // 担当者-表示ユーザコードから表示名を索引
    var selectUserName = function (invoker) {
        console.log("担当者-表示ユーザコード->表示名 change");

        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("担当者-表示ユーザコード->表示名 done");
                // 担当者-表示名に値をセット
                $('input[name="KuwagataUserName"]').val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("担当者-表示ユーザコード->表示名 fail");
                console.log(response.responseText);
                // 担当者-表示名の値をリセット
                $('input[name="KuwagataUserName"]').val('');
            });
    };

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
