//
// condition.js
//
jQuery(function($){
    
    // �Ʋ��̤�������Ѥ����ܵҥ����ɤ򥻥å�
    var strCustomerCompanyDisplayCode = $('#strCustomerCompanyDisplayCode').val();
    if ( 0 < strCustomerCompanyDisplayCode.length){
        // �ܵҥ����ɤ򥻥å�
        $('input[name="lngCustomerCode"]').val(strCustomerCompanyDisplayCode);
        // �ܵ�̾��ɽ���Τ���change���٥�Ȥ��ưȯ��
        $('input[name="lngCustomerCode"]').trigger('change');
    }

    // ------------------------------------
    //  events
    // ------------------------------------
    // OK�ܥ���
    $('#OkBt').on('click', function(){
        
        // ���������ѿ��˥��å�
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
        // �Ʋ��̤Υե��󥯥�����ƤӽФ�
        // ------------------------------------------
        // �������������
        window.opener.SetSearchConditionWindowValue(search_condition);
        // ���ٸ����¹�
        window.opener.SearchReceiveDetail(search_condition);

        // �Ҳ��̤��Ĥ���
        window.close();
    });

    // �Ĥ���ܥ���
    $('#CancelBt').on('click', function(){
        window.close();
    });

});