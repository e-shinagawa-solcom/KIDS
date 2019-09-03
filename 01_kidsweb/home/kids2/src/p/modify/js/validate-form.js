
(function(){
    // �ե�����
    var form = $('form');
    // �إå�����
    var header = $('div.regist-tab-header');
    // �ܺ٥���
    var detail = $('div.regist-tab-detail');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';

    // ���顼��å�����(ɬ�ܹ���)
    var msgRequired = "����ɬ�ܹ��ܤǤ���";
    // ���顼��å�����(����)
    var msgDateFormat = "yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������";
    // Ģɼ����¾��κ������ϲ�ǽʸ����
    var noteMaxLen = 38;
    // ���顼��å�����(����¾�κ���ʸ�����ޤ�)
    var msgNote = noteMaxLen + "ʸ���ޤǤ������ϤǤ��ޤ���"

    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/;

    // validation���å�
    $('.hasDatepicker').on({
        'change': function(){
            $(this).blur();
        }
    });

    // ���դ�yyyy/mm/dd�����˥ޥå����Ƥ��뤫,ͭ�������դ�
    $.validator.addMethod(
        "checkDateFormat",
        function(value, element, params) {
            if(params){
                // yyyy/mm/dd������
                if (!(regDate.test(value))) {
                    return false;
                }

                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = "01";
                var di = new Date(yyyy, mm - 1, dd);
                // ���դ�ͭ���������å�
                if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
                    return true;
                }
            }
            return true;
        },
        msgDateFormat
    );

    $.validator.addMethod(
        "maxlength",
        function (value, element, params) {
            // ̤���Ϥξ������å����ʤ�
            return !value ? true : (value.length <= params) ? true : false;
        },
        msgNote
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
            // ����̾�Ρ����ܸ��
            strProductName: {
                required: true
            },
            // ����̾�ΡʱѸ��
            strProductEnglishName: {
                required: true
            },
            // �Ķ�����
            lngInchargeGroupCode: {
                required: true
            },
            // ô����
            lngInchargeUserCode: {
                required: true
            },
            // ��ȯô����
            lngDevelopUserCode: {
                required: true
            },
            // �ܵ�
            lngCustomerCompanyCode: {
                required: true
            },
            // �ܵ�ô����
            lngCustomerUserCode: {
                required: true
            },
            // ���ʷ���
            lngProductFormCode: {
                required: true
            },
            // �����ȥ�����
            lngCartonQuantity: {
                required: true
            },
            // ����ͽ���
            lngProductionQuantity: {
                required: true
            },
            // ���Ǽ�ʿ�
            lngFirstDeliveryQuantity: {
                required: true
            },
            // Ǽ��
            dtmDeliveryLimitDate: {
                required: true,
                checkDateFormat: true
            },
            // Ǽ��(pcsñ��)
            curProductPrice: {
                required: true
            },
            // ����(pcsñ��)
            curretailPrice: {
                required: true
            },
            // ���ʹ���
            strProductComposition: {
                required: true
            }
        },
        // -----------------------------------------------
        // ���顼��å�����
        // -----------------------------------------------
        messages: {
            // ����̾�Ρ����ܸ��
            strProductName: {
                required: msgRequired
            },
            // ����̾�ΡʱѸ��
            strProductEnglishName: {
                required: msgRequired
            },
            // �Ķ�����
            lngInchargeGroupCode: {
                required: msgRequired
            },
            // ô����
            lngInchargeUserCode: {
                required: msgRequired
            },
            // ��ȯô����
            lngDevelopUserCode: {
                required: msgRequired
            },
            // �ܵ�
            lngCustomerCompanyCode: {
                required: msgRequired
            },
            // �ܵ�ô����
            lngCustomerUserCode: {
                required: msgRequired
            },
            // ���ʷ���
            lngProductFormCode: {
                required: msgRequired
            },
            // �����ȥ�����
            lngCartonQuantity: {
                required: msgRequired
            },
            // ����ͽ���
            lngProductionQuantity: {
                required: msgRequired
            },
            // ���Ǽ�ʿ�
            lngFirstDeliveryQuantity: {
                required: msgRequired
            },
            // Ǽ��
            dtmDeliveryLimitDate: {
                required: msgRequired
            },
            // Ǽ��(pcsñ��)
            curProductPrice: {
                required: msgRequired
            },
            // ����(pcsñ��)
            curretailPrice: {
                required: msgRequired
            },
            // ���ʹ���
            strProductComposition: {
                required: msgRequired
            }
        }
    });
})();
