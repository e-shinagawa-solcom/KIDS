
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
    var msgLessThanToday = "̤������դ����ꤵ��ޤ�����";
    var msgLessThantToDate = "FROM��TO���̤������դ����ꤵ��ޤ�����";

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
            return value != "0";
        },
        msgRequired
    );

    // �ܵҼ����ֹ��Ⱦ�ѱѿ�����[-],[,],[ ]�ʳ���ʸ�������ϤǤ��ʤ�
    $.validator.addMethod(
        "checkStrCustomerReceiveCode",
        function (value, element, params) {
            if (params && value!="") {
                return this.optional(element) || /^[a-zA-Z0-9-, ]+$/.test(value);
            } 
            return true;
        },
        msgSpecialFormat
    );

    // �������ɤν񼰥����å�
    $.validator.addMethod(
        "checkStrReceiveCode",
        function (value, element, params) {
            if (params && value!="") {
                return this.optional(element) || /^d\d{9}(_\d{2})?$/.test(value);
            }
            return true;
        },
        msgSpecialFormat
    );
����// ���ʥ����ɤν񼰥����å�
    $.validator.addMethod(
        "checkStrProductCode",
        function (value, element, params) {
            if (params && value!="") {
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
            if (params && value!="") {
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

    // ���դ�̤�����Ǥʤ��� ActionDate
    $.validator.addMethod(
        "isLessThanToday",
        function (value, element, params) {
            if (params && value!="") {
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // ���ߤ����������
                var nowDi = new Date();
                // ���Ϥ���ǯ�����ߤ�꾮���������
                if (nowDi.getFullYear() > di.getFullYear()) {
                    return true;
                    // ���Ϥ���ǯ�����ߤ���礭����Х��顼
                } else if (nowDi.getFullYear() < di.getFullYear()) {
                    return false;
                    // ���Ϥ���ǯ�����ߤ�Ʊ�����
                } else if (nowDi.getFullYear() == di.getFullYear()) {
                    // ���Ϥ�������ߤ�꾮���������
                    if (nowDi.getMonth() > di.getMonth()) {
                        return true;
                        // ���Ϥ�������ߤ���礭����Х��顼
                    } else if (nowDi.getMonth() < di.getMonth()) {
                        return false;
                    } else if (nowDi.getMonth() == di.getMonth()) {
                        // ���Ϥ����������ߤ�Ʊ���������꾮���������
                        if (nowDi.getDate() >= di.getDate()) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } return true;
        },
        msgLessThanToday
    );

    // FROM_XXXX��TO_XXXX��꾮������(Ʊ���Բ�)
    $.validator.addMethod(
        "isGreaterThanFromDate",
        function (value, element, params) {
            if (params[0] && value!="") {
                // FROM_XXXX�����Ϥ��줿��硢
                if ($(params[1]).val() != "") {
                    var regResult = regDate.exec($(params[1]).val());
                    var yyyy = regResult[1];
                    var mm = regResult[2];
                    var dd = regResult[3];
                    var fromDate = new Date(yyyy, mm, dd);
                    regResult = regDate.exec(value);
                    yyyy = regResult[1];
                    mm = regResult[2];
                    dd = regResult[3];
                    var di = new Date(yyyy, mm, dd);
                    // ���Ϥ���ǯ��FROM_XXXX��꾮������Х��顼
                    if (fromDate.getFullYear() > di.getFullYear()) {
                        return false;
                        // ���Ϥ���ǯ��FROM_XXXX����礭�������
                    } else if (fromDate.getFullYear() < di.getFullYear()) {
                        return true;
                        // ���Ϥ���ǯ��FROM_XXXX��Ʊ�����
                    } else if (fromDate.getFullYear() == di.getFullYear()) {
                        // ���Ϥ����FROM_XXXX��꾮������Х��顼
                        if (fromDate.getMonth() > di.getMonth()) {
                            return false;
                            // ���Ϥ����FROM_XXXX����礭�������
                        } else if (fromDate.getMonth() < di.getMonth()) {
                            return true;
                            // ���Ϥ����FROM_XXXX��Ʊ�����
                        } else if (fromDate.getMonth() == di.getMonth()) {
                            // ���Ϥ�������FROM_XXXX��꾮������Х��顼
                            if (fromDate.getDate() > di.getDate()) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
            return true;
        },
        msgLessThantToDate
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
            // ��Ͽ��
            From_dtmInsertDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked && $('input[name="To_dtmInsertDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                },
                isLessThanToday: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                }
            },
            To_dtmInsertDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked && $('input[name="From_dtmInsertDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmInsertDate"]').get(0).checked, 'input[name="From_dtmInsertDate"]'];
                }
                
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
                }
            },
            // �ܵҼ����ֹ�                        
            From_strCustomerReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked && $('input[name="To_strCustomerReceiveCode"]').val() == "";
                },
                checkStrCustomerReceiveCode: function() {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked;
                }
            },
            To_strCustomerReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked && $('input[name="From_strCustomerReceiveCode"]').val() == "";
                },
                checkStrCustomerReceiveCode: function() {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked;
                }
            },
            // ����No.            
            From_strReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked && $('input[name="To_strReceiveCode"]').val() == "";
                },
                checkStrReceiveCode: function() {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked;
                }
            },
            To_strReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked && $('input[name="From_strReceiveCode"]').val() == "";
                },
                checkStrReceiveCode: function() {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked;
                }
            },
            // ���ʥ�����            
            From_strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked && $('input[name="To_strProductCode"]').val() == "";
                },
                checkStrProductCode: function() {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                }
            },
            To_strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked && $('input[name="From_strProductCode"]').val() == "";
                },
                checkStrProductCode: function() {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                }
            },
            // ����̾            
            strProductName: {
                required: function () {
                    return $('input[name="IsSearch_strProductName"]').get(0).checked;
                }
            },
            // ����̾�ʱѸ��            
            strProductEnglishName: {
                required: function () {
                    return $('input[name="IsSearch_strProductEnglishName"]').get(0).checked;
                }
            },
            // �Ķ�����            
            lngInChargeGroupCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeGroupCode"]').get(0).checked;
                }
            },
            // ��ȯô����            
            lngInChargeUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeUserCode"]').get(0).checked;
                }
            },
            // ����ʬ            
            lngSalesClassCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngSalesClassCode"]').get(0).checked;
                }
            },
            // �ܵ�����            
            strGoodsCode: {
                required: function () {
                    return $('input[name="IsSearch_strGoodsCode"]').get(0).checked;
                }
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCompanyCode"]').get(0).checked;
                }
            },
            // Ǽ��            
            From_dtmDeliveryDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked && $('input[name="To_dtmDeliveryDate"]').val() == "";
                }
            },
            To_dtmDeliveryDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked && $('input[name="From_dtmDeliveryDate"]').val() == "";
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked, 'input[name="From_dtmDeliveryDate"]'];
                }
            },
            // ����            
            'lngReceiveStatusCode[]': {
                required: function () {
                    return $('input[name="IsSearch_lngReceiveStatusCode"]').get(0).checked;
                }
            }
        },
        // -----------------------------------------------
        // ���顼��å�����
        // -----------------------------------------------
        messages: {
            // ��Ͽ��
            From_dtmInsertDate: {
                required: msgRequired
            },
            To_dtmInsertDate: {
                required: msgRequired
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: msgRequired
            },
            // �ܵҼ����ֹ�                        
            From_strCustomerReceiveCode: {
                required: msgRequired
            },
            To_strCustomerReceiveCode: {
                required: msgRequired
            },
            // ����No.            
            From_strReceiveCode: {
                required: msgRequired
            },
            To_strReceiveCode: {
                required: msgRequired
            },
            // ���ʥ�����            
            From_strProductCode: {
                required: msgRequired
            },
            To_strProductCode: {
                required: msgRequired
            },
            // ����̾            
            strProductName: {
                required: msgRequired
            },
            // ����̾�ʱѸ��            
            strProductEnglishName: {
                required: msgRequired
            },
            // �Ķ�����            
            lngInChargeGroupCode: {
                required: msgRequired
            },
            // ��ȯô����            
            lngInChargeUserCode: {
                required: msgRequired
            },
            // ����ʬ            
            lngSalesClassCode: {
                required: msgRequired
            },
            // �ܵ�����            
            strGoodsCode: {
                required: msgRequired
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: msgRequired
            },
            // Ǽ��            
            From_dtmDeliveryDate: {
                required: msgRequired
            },
            To_dtmDeliveryDate: {
                required: msgRequired
            },
            // ����            
            'lngReceiveStatusCode[]': {
                required: msgRequired
            }
        }
    });
})();
