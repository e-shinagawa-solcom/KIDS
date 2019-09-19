//
// condition.js
//
jQuery(function($){
    // �̥�����ɥ��򳫤���POST����
    function post_open(url, data, target, features) {

        window.open('', target, features);
       
        // �ե������ưŪ������
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
       
        // �ե��������
        $('#temp_form').remove();
    }

    // ------------------------------------
    //  events
    // ------------------------------------
    // OK�ܥ���
    $('#OkBt').on('click', function(){
        
        // ���������ѿ��˥��å�
        var search_condition = {
            lngCustomerCode: $('input[name="lngCustomerCode"]').val(),
            strCustomerName: $('input[name="strCustomerName"]').val(),
            strCustomerReceiveCode: $('input[name="strCustomerReceiveCode"]').val(),
            lngReceiveNo: $('input[name="lngReceiveNo"]').val(),
            strReceiveDetailProductCode: $('input[name="strReceiveDetailProductCode"]').val(),
            strGoodsCode: $('input[name="strGoodsCode"]').val(),
            lngInChargeGroupCode: $('input[name="lngInChargeGroupCode"]').val(),
            strInChargeGroupName: $('input[name="strInChargeGroupName"]').val(),
            lngSalesClassCode: $('select[name="lngSalesClassCode"]').children('option:selected').val(),
            strProductCode: $('input[name="strProductCode"]').val(),
            From_dtmDeliveryDate: $('input[name="From_dtmDeliveryDate"]').val(),
            To_dtmDeliveryDate: $('input[name="To_dtmDeliveryDate"]').val(),
            lngMonetaryUnitCode: $('select[name="lngMonetaryUnitCode"]').children('option:selected').val(),
            strNote: $('input[name="strNote"]').val(),
            IsIncludingResale: $('input[name="IsIncludingResale"]').prop("checked"),
        };

        // �Ʋ��̤ξܺٸ����ե��󥯥�����ƤӽФ�
        window.opener.SearchReceiveDetail(search_condition);

        // �Ҳ��̤��Ĥ���
        window.close();
    });

    // �Ĥ���ܥ���
    $('#CancelBt').on('click', function(){
        window.close();
    });

});