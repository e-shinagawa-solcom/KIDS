
(function () {
    // �ե�����
    var form = $('form');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // ���顼��å�����(ɬ�ܹ���)
    var msgRequired = "����ɬ�ܹ��ܤǤ���";
    // ���顼��å�����(����)
    var msgDateFormat = "yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������";
    // ���եե����ޥå� yyyy/mm/dd����
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;
    // ���顼��å������ʽ񼰸���
    var msgSpecialFormat = "�񼰤˸�꤬����ޤ���"

    // validation���å�
    $('.hasDatepicker').on({
        'change': function () {
            $(this).blur();
        }
    });

    // ��ʬ��ɬ�ܥ����å�
    $.validator.addMethod(
        "checkSelect",
        function (value, element, params) {
            return value != "";
        },
        msgRequired
    );
����// ���ʥ����ɤν񼰥����å�
    $.validator.addMethod(
        "checkStrProductCode",
        function (value, element, params) {
            if (params) {
                return this.optional(element) || /\d{5}(_\d{2})?$/.test(value);
            }
            return true;
        },
        msgSpecialFormat
    );

    // ���դ�yyyy/mm/dd�����˥ޥå����Ƥ��뤫,ͭ�������դ�
    $.validator.addMethod(
        "checkDateFormat",
        function (value, element, params) {
            if (params) {
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                } else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(5, 2);
                    var d = '01';
                    value = y + "/" + m + "/" + d;
                } else if (/(19[0-9]{2}|2[0-9]{3})(0[1-9]|1[0-2])/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = '01';
                    value = y + "/" + m + "/" + d;
                }

                // yyyy/mm/dd������
                if (!(regDate.test(value))) {
                    return false;
                }
                // ����ʸ����λ���ʬ��
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // ���դ�ͭ���������å�
                if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
                    return true;
                }
            } return true;
        },
        msgDateFormat
    );

    // ��������
    form.validate({
        // -----------------------------------------------
        // ���顼ɽ������
        // -----------------------------------------------
        errorPlacement: function (error, element) {
            invalidImg = $('<img>')
                .attr('class', classNameErrorIcon)
                .attr('src', urlErrorIcon)
                // CSS����(ɽ������)
                .css({
                    position: 'relative',
                    top: -1,
                    left: -2,
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
            if ($(element).prev('img.' + classNameErrorIcon).length <= 0) {
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
        unhighlight: function (element) {
            // ���顼����������
            $(element).prev('img.' + classNameErrorIcon).remove();
        },
        // -----------------------------------------------
        // ���ڥ롼��
        // -----------------------------------------------
        rules: {
            // Ǽ����
            From_dtmDeliveryDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked && $('input[name="To_dtmDeliveryDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked;
                }
            },
            To_dtmDeliveryDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked && $('input[name="From_dtmDeliveryDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked;
                }
                
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCompanyCode"]').get(0).checked;
                }
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
                }
            },
            // ����ʬ            
            lngSalesClassCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngSalesClassCode"]').get(0).checked;
                }
            },
            // �����Ƕ�ʬ            
            lngTaxClassCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngTaxClassCode"]').get(0).checked;
                }
            },
            // Ǽ�ʽ�NO.            
            strSlipCode: {
                required: function () {
                    return $('input[name="IsSearch_strSlipCode"]').get(0).checked;
                }
            },
            // Ǽ����
            lngDeliveryPlaceCode: {
                required: function () {
                    return $('input[name="IsSearch_lngDeliveryPlaceCode"]').get(0).checked;
                }
            },
            // ��ɼ��            
            lngInsertUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInsertUserCode"]').get(0).checked;
                }
            },
            // ��ʸ��NO.            
            strCustomerSalesCode: {
                required: function () {
                    return $('input[name="IsSearch_strCustomerSalesCode"]').get(0).checked;
                }
            },
            // �ܵ�����            
            strGoodsCode: {
                required: function () {
                    return $('input[name="IsSearch_strGoodsCode"]').get(0).checked;
                }
            },
        },
        // -----------------------------------------------
        // ���顼��å�����
        // -----------------------------------------------
        messages: {
            // Ǽ����
            From_dtmDeliveryDate: {
                required: msgRequired
            },
            To_dtmDeliveryDate: {
                required: msgRequired               
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: msgRequired
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: msgRequired
            },
            // Ǽ�ʽ�NO.            
            strSlipCode: {
                required: msgRequired
            },
            // Ǽ����
            lngDeliveryPlaceCode: {
                required: msgRequired
            },
            // ��ɼ��            
            lngInsertUserCode: {
                required: msgRequired
            },
            // ��ʸ��NO.            
            strCustomerSalesCode: {
                required: msgRequired
            },
            // �ܵ�����            
            strGoodsCode: {
                required: msgRequired
            }
        }
    });
})();
