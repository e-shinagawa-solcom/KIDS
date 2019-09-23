//
// condition.js
//
jQuery(function($){
    
    // 親画面から引き継いだ顧客コードをセット
    var strCustomerCompanyDisplayCode = $('#strCustomerCompanyDisplayCode').val();
    if ( 0 < strCustomerCompanyDisplayCode.length){
        // 顧客コードをセット
        $('input[name="lngCustomerCode"]').val(strCustomerCompanyDisplayCode);
        // 顧客名の表示のためchangeイベントを手動発生
        $('input[name="lngCustomerCode"]').trigger('change');
    }

    // ------------------------------------
    //  events
    // ------------------------------------
    // OKボタン
    $('#OkBt').on('click', function(){
        
        // 検索条件を変数にセット
        var search_condition = {
            strCompanyDisplayCode: $('input[name="lngCustomerCode"]').val(),
            strCompanyDisplayName: $('input[name="strCustomerName"]').val(),
            strCustomerReceiveCode: $('input[name="strCustomerReceiveCode"]').val(),
            lngReceiveNo: $('input[name="lngReceiveNo"]').val(),
            strReceiveDetailProductCode: $('input[name="strReceiveDetailProductCode"]').val(),
            strGoodsCode: $('input[name="strGoodsCode"]').val(),
            lngInChargeGroupCode: $('input[name="lngInChargeGroupCode"]').val(),
            strInChargeGroupName: $('input[name="strInChargeGroupName"]').val(),
            lngSalesClassCode: $('select[name="lngSalesClassCode"]').children('option:selected').val(),
            From_dtmDeliveryDate: $('input[name="From_dtmDeliveryDate"]').val(),
            To_dtmDeliveryDate: $('input[name="To_dtmDeliveryDate"]').val(),
            strNote: $('input[name="strNote"]').val(),
            IsIncludingResale: $('input[name="IsIncludingResale"]').prop("checked"),
        };

        // ------------------------------------------
        // 親画面のファンクションを呼び出す
        // ------------------------------------------
        // 検索条件値設定
        window.opener.SetSearchConditionWindowValue(search_condition);
        // 明細検索実行
        window.opener.SearchReceiveDetail(search_condition);

        // 子画面を閉じる
        window.close();
    });

    // 閉じるボタン
    $('#CancelBt').on('click', function(){
        window.close();
    });

});