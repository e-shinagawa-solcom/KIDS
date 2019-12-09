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
    
    // Ǽ��������    
    var now = new Date();
    now.setDate(1);("00" + (now.getMonth() + 1)).slice(-2)
    var start = now.getFullYear() + '/' + ("00" + (now.getMonth() + 1)).slice(-2) + '/' + ("00" + now.getDate()).slice(-2);
    var date2 = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    var end = date2.getFullYear() + '/' + ("00" + (date2.getMonth() + 1)).slice(-2) + '/' + date2.getDate();
    $('input[name="From_dtmDeliveryDate"]').val(start);
    $('input[name="To_dtmDeliveryDate"]').val(end);
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
            IsIncludingResale: $('input[name="IsIncludingResale"]').prop("checked") ? 'On' : 'Off',
        };

        // --------------------------------------------------------------
        //   �����ͤΥХ�ǡ������
        // --------------------------------------------------------------
        if (!validateCondition(search_condition)){
            return false;
        }

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

    // ------------------------------------
    //  functions
    // ------------------------------------
    // ���Ϥ��줿�������ΥХ�ǡ������
    function validateCondition(cnd){

        // �ܵҥ�����ɬ�ܥ����å�
        if(!cnd.strCompanyDisplayCode){
            alert("�ܵҥ����ɤ�̤���ϤǤ�");
            return false;
        }

        // FROMǼ��������
        if (cnd.From_dtmDeliveryDate){
            if (!isValidDate(cnd.From_dtmDeliveryDate)){
                alert("Ǽ����FROM�ˤ����Ϸ����������Ǥ�");
                return false;
            }
        }

        // TOǼ��������
        if (cnd.To_dtmDeliveryDate){
            if (!isValidDate(cnd.To_dtmDeliveryDate)){
                alert("Ǽ����TO�ˤ����Ϸ����������Ǥ�");
                return false;
            }
        }

        // FROMǼ����TOǼ��
        if (cnd.From_dtmDeliveryDate && cnd.To_dtmDeliveryDate){
            var from = new Date(cnd.From_dtmDeliveryDate);
            var to = new Date(cnd.To_dtmDeliveryDate);

            if (from.getTime() > to.getTime()){
                alert("Ǽ����TO�ˤ�Ǽ����FROM�ˤ��������Ǥ�");
                return false;
            }
        }

        return true;
                
    };

    // ���ս񼰥����å�
    function isValidDate(text) {
        if (!/^\d{1,4}(\/|-)\d{1,2}\1\d{1,2}$/.test(text)) {
          return false;
        }
      
        const [year, month, day] = text.split(/\/|-/).map(v => parseInt(v, 10));
      
        return year >= 1
          && (1 <= month && month <= 12)
          && (1 <= day && day <= daysInMonth(year, month));
      
        function daysInMonth(year, month) {
          if (month === 2 && isLeapYear(year)) {
            return 29;
          }
      
          return {
            1: 31, 2: 28, 3: 31, 4: 30,
            5: 31, 6: 30, 7: 31, 8: 31,
            9: 30, 10: 31, 11: 30, 12: 31
          }[month];
        }
      
        function isLeapYear(year) {
          return ((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0);
        }
      };

});