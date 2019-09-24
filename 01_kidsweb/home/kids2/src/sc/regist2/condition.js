//
// condition.js
//
jQuery(function($){
    
    // �Ʋ��̤�������Ѥ����ܵҥ����ɤ򥻥å�
    var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
    if ( 0 < strDefaultCompanyDisplayCode.length){
        // �ܵҥ����ɤ򥻥å�
        $('input[name="lngCustomerCode"]').val(strDefaultCompanyDisplayCode);
        // �ܵ�̾��ɽ���Τ���change���٥�Ȥ��ưȯ��
        $('input[name="lngCustomerCode"]').trigger('change');
    }

    // ------------------------------------
    //  events
    // ------------------------------------
    // OK�ܥ���
    $('#OkBt').on('click', function(){
        
        // ---------------------------
        //  �����͡ʸ������ˤμ���
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
        //   �ܵҥ����ɤޤ�������ʬ������ͤȰۤʤ���Υ����å�
        // --------------------------------------------------------------
        // ����ͤμ���
        var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
        var lngDefaultSalesClassCode = $('#lngDefaultSalesClassCode').val();
        
        // �����å���ɬ�פȤ�������������Ƥ��뤫�ɤ���
        var needConfirm = ((0 < strDefaultCompanyDisplayCode.length) 
                           && (strDefaultCompanyDisplayCode != search_condition.strCompanyDisplayCode))
                          ||
                          ((0 < lngDefaultSalesClassCode.length) 
                           && (lngDefaultSalesClassCode != search_condition.lngSalesClassCode));

        // �桼�����˳�ǧ
        if (needConfirm){
            if (confirm("���򤵤줿���٤����ƥ��ꥢ���ޤ�����������Ǥ�����")){
                // �Ʋ��̤��������٤����ƥ��ꥢ
                window.opener.ClearAllEditDetail();
            }else{
                //�֥���󥻥�פ��������줿�����ܲ��̤��Ĥ���
                window.close();
            }
        }

        // --------------------------------------------------------------
        //   �Ʋ��̤˻Ҳ��̤��ͤ�����Ѥ������ٸ�����¹�
        // --------------------------------------------------------------
        // �������������
        window.opener.SetSearchConditionWindowValue(search_condition);
        // ���ٸ����¹�
        window.opener.SearchReceiveDetail(search_condition);
        // �ܲ��̤��Ĥ���
        window.close();

    });

    // �Ĥ���ܥ���
    $('#CancelBt').on('click', function(){
        window.close();
    });

});