
(function(){
    // �ե�����
    var form = $('form[name="Invoice"]');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';

    // ���顼��å�����(ɬ�ܹ���)
    var msgRequired = "����ɬ�ܹ��ܤǤ���";
    // ���顼��å�����(ɬ�ܹ���)
    var msgEmpty    = "��̤���ϤǤ���";

    // ���顼��å�����(���ٽ���̵��)
    var msgSlipEmpty    = "�������٤����򤵤�Ƥ��ޤ���";

    // ���顼��å�����(����)
    var msgDateFormat = "yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������";
    var msgGreaterThanToday = "���ߤ��������դ������ϤǤ��ޤ���";
    // ���顼��å�����(��ư�褬�ݴɸ���Ʊ�칩��)
    var msgSameFactory = "��ư�蹩����ݴɸ������Ʊ���������ꤹ�뤳�ȤϤǤ��ޤ���";
    // yyyy/mm/dd �ե����ޥå�
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;
    // ���եե����ޥå� yyyy/mm(m)/dd(d)����
    var regDate2 = /([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})/;

    // validation���å�
    $('.hasDatepicker').on({
        'change': function(){
            $(this).blur();
        }
    })

    // �ݴɹ���Ȱ�ư�蹩�줬�԰��פ��ɤ���
    $.validator.addMethod(
        "difFactory",
        function(value, element, params) {
            return value != params.val();
        },
        msgSameFactory
    );

    // ���դ�yyyy/mm/dd�����˥ޥå����Ƥ��뤫,ͭ�������դ�
    $.validator.addMethod(
        "checkDateFormat",
        function(value, params) {
        	if(!value){return true;}
            if(params){
                // yyyy/mm(m)/dd(d)������
                if (!(regDate2.test(value))) {
                    return false;
                }

                var regResult = regDate2.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // ���դ�ͭ���������å�
                if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
                    return true;
                }
            }return true;
        },
        msgDateFormat
    );


    // ���դ����Ǥʤ��� ActionDate
    $.validator.addMethod(
        "equalsOrGreaterThanToday",
        function(value, element, params) {
            if(params){
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // ���ߤ����������
                var nowDi = new Date();
                // ���Ϥ���ǯ�����ߤ�꾮������Х��顼
                if (nowDi.getFullYear() > di.getFullYear()){
                    return false;
                // ���Ϥ���ǯ�����ߤ���礭�������
                } else if (nowDi.getFullYear() < di.getFullYear()) {
                    return true;
                // ���Ϥ���ǯ�����ߤ�Ʊ�����
                } else if (nowDi.getFullYear() == di.getFullYear()) {
                    // ���Ϥ�������ߤ�꾮������Х��顼
                    if (nowDi.getMonth() > di.getMonth()){
                        return false;
                    // ���Ϥ�������ߤ���礭�������
                    } else if (nowDi.getMonth() < di.getMonth()){
                        return true;
                    } else if (nowDi.getMonth() == di.getMonth()){
                        // ���Ϥ����������ߤ�Ʊ���������꾮������Х��顼
                        if (nowDi.getDate() > di.getDate()) {
                            return false;
                        }
                    }
                    return true;
                }
            }return true;
        },
        msgGreaterThanToday
    );

    // ��������
    form.validate({
        // -----------------------------------------------
        // ���顼ɽ������
        // -----------------------------------------------
        errorPlacement: function (error, element){
            invalidImg = $('<img>')
                            .attr('class', classNameErrorIcon)
                            .attr('src', urlErrorIcon)
                            // CSS����(ɽ������)
                            .css({
                                position: 'absolute',
                                top: $(element).position().top,
                                left: $(element).position().left - 20,
                                opacity: 'inherit'
                            })
                            // �ġ�����å�ɽ��
                            .tooltipster({
                                trigger: 'hover',
                                onlyone: false,
                                position: 'top',
                                content: error.text()
                            });

            // ���顼��������¸�ߤ��ʤ����
            if ($(element).prev('img.' + classNameErrorIcon).length <= 0){
                // ���顼���������ɽ��
                $(element).before(invalidImg);
            }
            // ���顼��������¸�ߤ�����
            else {
                // ��¸�Υ��顼��������Υġ�����åץƥ����Ȥ򹹿�
                $(element).prev('img.' + classNameErrorIcon)
                            .tooltipster('content', error.text());
            }
        },
        // -----------------------------------------------
        // ����OK���ν���
        // -----------------------------------------------
        unhighlight: function(element){
                // ���顼����������
                $(element).prev('img.' + classNameErrorIcon).remove();
        },
        // -----------------------------------------------
        // ���ڥ롼��
        // -----------------------------------------------
        rules:{
            // �ܵҥ�����
            lngCustomerCode: {
                required: true
            },
//            // ���Ƕ�ʬ
//            lngTaxClassCode: {
//                required: true
//            },
            // Ǽ����From
            dtmDeliveryDate: {
            	checkDateFormat: true
            },
            // Ǽ����To
            dtmDeliveryDate: {
            	checkDateFormat: true
            },
            // ���������
            curthismonthamount: {
//                required: true
            },
            // ������
            ActionDate: {
                checkDateFormat: true,
                required: true
            },


        },
        // -----------------------------------------------
        // ���顼��å�����
        // -----------------------------------------------
        messages: {
            ActionDate: {
                required: msgRequired
            },
            // �ܵҥ�����
            lngCustomerCode: {
                required: '�ܵҥ�����' + msgEmpty
            },
//            // ���Ƕ�ʬ
//            lngTaxClassCode: {
//                required: '���Ƕ�ʬ' + msgEmpty
//            },
            // Ǽ����From
            dtmDeliveryDate: {
                required: + msgDateFormat
            },
            // Ǽ����To
            dtmDeliveryDate: {
                required: + msgDateFormat
            },
            // ���������
            curthismonthamount: {
                required:  msgSlipEmpty
            }

        }
    });
})();
