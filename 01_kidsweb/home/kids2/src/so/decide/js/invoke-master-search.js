
(function () {
    $('select[name="lngSalesDivisionCode"]').val(0);
    // マスタ検索共通
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
        dataType: 'json'
    };

    // 顧客-表示会社コード イベント登録
    $('input[name="lngCustomerCode"]').on({
        'change': function () {        
            // 表示名を索引
            selectCustomerName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strCustomerName"]').focus();
        }
    });

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
                QueryName: 'selectCustomerNameForSo',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log(response);
                console.log("工場-表示会社コード->表示名 done");
                // 工場-表示名に値をセット
                $(targetCssSelector).val(response[0].customerdisplayname);
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };

    // 顧客-表示会社コード イベント登録
    $('select[name="lngSalesDivisionCode"]').on({
        'change': function () {
            var val = $('select[name="lngSalesDivisionCode"] option:selected').val();
            $.ajax({
                url: "/cmn/getdropdowndata.php",
                type: 'post',
                data: {
                    'lngProcessID': "cnSalesClassCode",
                    'strFormValue': val
                }
            })
                .done(function (response) {
                    var data = JSON.parse(response);
                    $('select[name="lngSalesClassCode"] option').remove();
                    $('select[name="lngSalesClassCode"]').append("<option value=''></option>");
                    $('select[name="lngSalesClassCode"]').append(data.pulldown);
                })
                .fail(function (response) {
                    alert("fail");
                })
        }
    });    
})();