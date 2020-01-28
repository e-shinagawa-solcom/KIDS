
(function () {

    // マスタ検索共通
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
        dataType: 'json'
    };

    // 顧客-表示会社コード イベント登録
    $('input[name="lngCustomerCode"]').on({
        'change': function () {
            var msg = '納品対象の明細をすべてクリアします。\nよろしいですか？';
            console.log(msg);

            var warn = ($('#tbl_detail tbody tr').length > 0 || $('#tbl_edit_detail_body tbody tr').length > 0) ? true : false;

            if (warn && window.confirm(msg) === false) {
                return;
            }
            $('#tbl_detail tbody tr').remove();
            $('#tbl_detail_chkbox tbody tr').remove();
            $('#tbl_edit_detail_body tbody tr').remove();
            $('#tbl_edit_no_body tbody tr').remove();

            $('#tbl_detail tbody tr td').width('');
            $('#tbl_detail_head thead tr th').width('');
            $('#tbl_edit_detail_body tbody tr td').width('');
            $('#tbl_edit_detail_head thead tr th').width('');


            $("#tbl_detail_head").trigger("update");
            $("#tbl_detail").trigger("update");

            var iscustomer = $(this).attr('iscustomer');
            if (iscustomer == "yes") {
                selectCustomerName($(this));
            } else {
                // 表示名を索引
                selectSupplierName($(this));
            }
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strCustomerName"]').focus();

            SetSearchConditionWindowValue($(this).val(), $('input[name="strCustomerName"]').val());

            // ------------------
            // フォームに値を設定
            // ------------------
            $('input[name="strTotalAmount"]').val("0.0000");
            $('input[name="strTaxAmount"]').val("0.0000");
        }
    });

    // 起票者-表示ユーザーコード イベント登録
    $('input[name="lngInsertUserCode"]').on({
        'change': function () {
            // 表示名を索引
            selectInputUserName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strInsertUserName"]').focus();
        }
    });

    // 納品場所-表示会社コード イベント登録
    $('input[name="lngDeliveryPlaceCode"]').on({
        'change': function () {
            // 表示名を索引
            selectLocationName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strDeliveryPlaceName"]').focus();
        }
    });


    // --------------------------------------------------------------------------
    // 納品場所-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 納品場所-表示会社コードから表示名を索引
    var selectLocationName = function (invoker) {
        console.log("工場-表示会社コード->表示名 change value=" + $(invoker).val());
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectLocationName',
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
                $(targetCssSelector).val(response[0].locationdisplayname);
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // --------------------------------------------------------------------------
    // 仕入先-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 仕入先-表示会社コードから表示名を索引
    var selectSupplierName = function (invoker) {
        console.log("仕入先-表示会社コード->表示名 change");
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectSupplierName',
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
                $(targetCssSelector).val(response[0].supplierdisplayname);
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // --------------------------------------------------------------------------
    // 顧客-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 顧客-表示会社コードから表示名を索引
    var selectCustomerName = function (invoker) {
        console.log("顧客-表示会社コード->表示名 change");
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectCustomerName',
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
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    // --------------------------------------------------------------------------
    // 入力者-表示ユーザコードによるデータ索引
    // --------------------------------------------------------------------------
    // 入力者-表示ユーザコードから表示名を索引
    var selectInputUserName = function (invoker) {
        console.log("担当者-表示ユーザコード->表示名 change");// 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // 表示フラグ制限の取得
        var displayFlagLimit = $(invoker).attr('displayFlagLimit');
        if (displayFlagLimit == '0') {
            displayFlagLimit0 = true;
            displayFlagLimit1 = false;
        } else {
            displayFlagLimit0 = true;
            displayFlagLimit1 = true;
        }

        console.log(displayFlagLimit1);
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectInputUserName',
                Conditions: {
                    UserDisplayName: $(invoker).val(),
                    displayFlagLimit0: displayFlagLimit0,
                    displayFlagLimit1: displayFlagLimit1
                }
            }
        };
        console.log(condition);
        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("担当者-表示ユーザコード->表示名 done");
                console.log(response);
                // 担当者-表示名に値をセット
                $(targetCssSelector).val(response[0].userdisplayname);
            })
            .fail(function (response) {
                console.log("担当者-表示ユーザコード->表示名 fail");
                console.log(response);
                console.log(response.responseText);
                // 担当者-表示名の値をリセット
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
})();
