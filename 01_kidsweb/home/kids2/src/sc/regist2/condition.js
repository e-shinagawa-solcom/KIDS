//
// condition.js
//
jQuery(function($){
    // 別ウィンドウを開いてPOSTする
    function post_open(url, data, target, features) {

        window.open('', target, features);
       
        // フォームを動的に生成
        var html = '<form id="temp_form" style="display:none;">';
        for(var x in data) {
          if(data[x] == undefined || data[x] == null) {
            continue;
          }
          var _val = data[x].replace(/'/g, '\'');
          html += "<input type='hidden' name='" + x + "' value='" + _val + "' >";
        }
        html += '</form>';
        $("body").append(html);
       
        $('#temp_form').attr("action",url);
        $('#temp_form').attr("target",target);
        $('#temp_form').attr("method","POST");
        $('#temp_form').submit();
       
        // フォームを削除
        $('#temp_form').remove();
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