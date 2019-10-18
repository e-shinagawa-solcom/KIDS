
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

    // ���ʥ����ɤν񼰥����å�
    $.validator.addMethod(
        "checkStrProductCode",
        function (value, element, params) {
            if (params && value != '') {
                var codes = value.split(',');
                var result = true;
                $.each(codes, function (ind, val) {
                    if (val.indexOf('-') == -1) {
                        result = /\d{5}(_\d{2})?$/.test(val);
                        if (!result) {
                            return result;
                        }
                    } else {
                        result = /\d{5}(-\d{5})/.test(val);
                        if (!result) {
                            return result;
                        }
                    }
                });
                return result;
            }
            return true;
        },
        msgSpecialFormat
    );


    // ���դ�yyyy/mm/dd�����˥ޥå����Ƥ��뤫,ͭ�������դ�
    $.validator.addMethod(
        "checkDateFormat",
        function (value, element, params) {
            if (params && value != '') {
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
            if (params && value != '') {
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
            if (params[0] && value != '') {
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
            // ��������
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
            // ������
            From_dtmAppropriationDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked && $('input[name="To_dtmAppropriationDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked;
                }
            },
            To_dtmAppropriationDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked && $('input[name="From_dtmAppropriationDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked;
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked, 'input[name="From_dtmAppropriationDate"]'];
                }
            },
            // ���NO.
            strSalesCode: {
                required: function () {
                    return $('input[name="IsSearch_strSalesCode"]').get(0).checked;
                }
            },
            // �ܵҼ����ֹ�
            strCustomerReceiveCode: {
                required: function () {
                    return $('input[name="strCustomerReceiveCode"]').get(0).checked;
                }
            },
            // Ǽ�ʽ�NO.
            strSlipCode: {
                required: function () {
                    return $('input[name="IsSearch_strSlipCode"]').get(0).checked;
                }
            },
            // ���ʥ�����            
            strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                },
                checkStrProductCode: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                }
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
                }
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCompanyCode"]').get(0).checked;
                }
            },
            // ����            
            'lngSalesStatusCode[]': {
                required: function () {
                    return $('input[name="IsSearch_lngSalesStatusCode"]').get(0).checked;
                }
            },
            // �Ķ�����            
            lngInChargeGroupCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeGroupCode"]').get(0).checked;
                }
            },
            // ô����            
            lngInChargeUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeUserCode"]').get(0).checked;
                }
            },
            // ����̾            
            strProductName: {
                required: function () {
                    return $('input[name="IsSearch_strProductName"]').get(0).checked;
                }
            },
            // �ܵ�����            
            strGoodsCode: {
                required: function () {
                    return $('input[name="IsSearch_strGoodsCode"]').get(0).checked;
                }
            },
            // Ǽ��
            From_dtmDeliveryLimitDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryLimitDate"]').get(0).checked && $('input[name="To_dtmDeliveryLimitDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmDeliveryLimitDate"]').get(0).checked;
                }
            },
            To_dtmDeliveryLimitDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryLimitDate"]').get(0).checked && $('input[name="From_dtmDeliveryLimitDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmDeliveryLimitDate"]').get(0).checked;
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmDeliveryLimitDate"]').get(0).checked, 'input[name="From_dtmDeliveryLimitDate"]'];
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
            // ������
            From_dtmAppropriationDate: {
                required: msgRequired
            },
            To_dtmAppropriationDate: {
                required: msgRequired
            },
            // ���NO.
            strSalesCode: {
                required: msgRequired
            },
            // �ܵҼ����ֹ�
            strCustomerReceiveCode: {
                required: msgRequired
            },
            // Ǽ�ʽ�NO.
            strSlipCode: {
                required: msgRequired
            },
            // ���ʥ�����            
            strProductCode: {
                required: msgRequired
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: msgRequired
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: msgRequired
            },
            // ����            
            'lngSalesStatusCode[]': {
                required: msgRequired
            },
            // �Ķ�����            
            lngInChargeGroupCode: {
                required: msgRequired
            },
            // ô����            
            lngInChargeUserCode: {
                required: msgRequired
            },
            // ����̾            
            strProductName: {
                required: msgRequired
            },
            // �ܵ�����            
            strGoodsCode: {
                required: msgRequired
            },
            // Ǽ��
            From_dtmDeliveryLimitDate: {
                required: msgRequired
            },
            To_dtmDeliveryLimitDate: {
                required: msgRequired
            }
        }
    });
})();
