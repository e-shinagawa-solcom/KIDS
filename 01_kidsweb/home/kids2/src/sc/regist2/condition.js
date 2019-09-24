//
// condition.js
//
jQuery(function($){
    
    // 親画面から引き継いだ顧客コードをセット
    var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
    if ( 0 < strDefaultCompanyDisplayCode.length){
        // 顧客コードをセット
        $('input[name="lngCustomerCode"]').val(strDefaultCompanyDisplayCode);
        // 顧客名の表示のためchangeイベントを手動発生
        $('input[name="lngCustomerCode"]').trigger('change');
    }

    // ------------------------------------
    //  events
    // ------------------------------------
    // OKボタン
    $('#OkBt').on('click', function(){
        
        // ---------------------------
        //  入力値（検索条件）の収集
        // ---------------------------
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

        // --------------------------------------------------------------
        //   顧客コードまたは売上区分が初期値と異なる場合のチェック
        // --------------------------------------------------------------
        // 初期値の取得
        var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
        var lngDefaultSalesClassCode = $('#lngDefaultSalesClassCode').val();
        
        // チェックを必要とする条件を満たしているかどうか
        var needConfirm = ((0 < strDefaultCompanyDisplayCode.length) 
                           && (strDefaultCompanyDisplayCode != search_condition.strCompanyDisplayCode))
                          ||
                          ((0 < lngDefaultSalesClassCode.length) 
                           && (lngDefaultSalesClassCode != search_condition.lngSalesClassCode));

        // ユーザーに確認
        if (needConfirm){
            if (confirm("選択された明細を全てクリアしますが、よろしいですか？")){
                // 親画面の選択明細を全てクリア
                window.opener.ClearAllEditDetail();
            }else{
                //「キャンセル」が押下された場合は本画面を閉じる
                window.close();
            }
        }

        // --------------------------------------------------------------
        //   親画面に子画面の値を引き継いで明細検索を実行
        // --------------------------------------------------------------
        // 検索条件値設定
        window.opener.SetSearchConditionWindowValue(search_condition);
        // 明細検索実行
        window.opener.SearchReceiveDetail(search_condition);
        // 本画面を閉じる
        window.close();

    });

    // 閉じるボタン
    $('#CancelBt').on('click', function(){
        window.close();
    });

});