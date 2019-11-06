
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

    $.validator.addMethod(
        "checkStrProductCode",
        function (value, element, params) {
            if (params && value!='') {
                return this.optional(element) || /\d{5}(_\d{2})?$/.test(value);
            }
            return true;
        },
        msgSpecialFormat
    );
    // �����������ο����ʳ���ʸ�������ϤǤ��ʤ�
    $.validator.addMethod(
        "checkAscii",
        function (value, element, params) {
            if (params && value!='') {
                return this.optional(element) || /\d{0,10}/.test(value);
            }
            return true;
        },
        msgSpecialFormat
    );
    // ���դ�yyyy/mm/dd�����˥ޥå����Ƥ��뤫,ͭ�������դ�
    $.validator.addMethod(
        "checkDateFormat",
        function (value, element, params) {
            if (params && value!='') {
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

    // ���դ�̤�����Ǥʤ��� ActionDate
    $.validator.addMethod(
        "isLessThanToday",
        function (value, element, params) {
            if (params && value!='') {
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
            if (params[0] && value!='') {
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

            // ������
            From_dtmAppropriationDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked && $('input[name="To_dtmAppropriationDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmAppropriationDate"]').get(0).checked;
                },
                isLessThanToday: function () {
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
            // ����NO.
            From_strStockCode: {
                required: function () {
                    return $('input[name="IsSearch_strStockCode"]').get(0).checked && $('input[name="To_strStockCode"]').val() == "";
                },
                checkAscii: function () {
                    return $('input[name="IsSearch_strStockCode"]').get(0).checked;
                }
            },
            To_strStockCode: {
                required: function () {
                    return $('input[name="IsSearch_strStockCode"]').get(0).checked && $('input[name="From_strStockCode"]').val() == "";
                },
                checkAscii: function () {
                    return $('input[name="IsSearch_strStockCode"]').get(0).checked;
                }
            },
            // ȯ���NO.
            From_strOrderCode: {
                required: function () {
                    return $('input[name="IsSearch_strOrderCode"]').get(0).checked && $('input[name="To_strOrderCode"]').val() == "";
                },
                checkAscii: function () {
                    return $('input[name="IsSearch_strOrderCode"]').get(0).checked;
                }
            },
            To_strOrderCode: {
                required: function () {
                    return $('input[name="IsSearch_strOrderCode"]').get(0).checked && $('input[name="From_strOrderCode"]').val() == "";
                },
                checkAscii: function () {
                    return $('input[name="IsSearch_strOrderCode"]').get(0).checked;
                }
            },

            // Ǽ�ʽ�NO.
            strSlipCode: {
                required: function () {
                    return $('input[name="IsSearch_strSlipCode"]').get(0).checked;
                },
                checkAscii: function () {
                    return $('input[name="IsSearch_strSlipCode"]').get(0).checked;
                }
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
                }
            },
            // ������            
            lngCustomerCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCode"]').get(0).checked;
                }
            },
            // ��ʧ���            
            lngPayConditionCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngPayConditionCode"]').get(0).checked;
                }
            },
            // ����������           
            From_dtmExpirationDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmExpirationDate"]').get(0).checked && $('input[name="To_dtmExpirationDate"]').val() == "";
                }
            },
            To_dtmExpirationDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmExpirationDate"]').get(0).checked && $('input[name="From_dtmExpirationDate"]').val() == "";
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmExpirationDate"]').get(0).checked, 'input[name="From_dtmExpirationDate"]'];
                }
            },
            // ��������            
            lngStockSubjectCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngStockSubjectCode"]').get(0).checked;
                }
            },
            // ��������            
            lngStockItemCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngStockItemCode"]').get(0).checked;
                }
            },
            // ���ʥ�����            
            strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked
                }
            },
            // ����̾��            
            strProductName: {
                required: function () {
                    return $('input[name="IsSearch_strProductName"]').get(0).checked;
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
            // ����            
            'lngStockStatusCode[]': {
                required: function () {
                    return $('input[name="IsSearch_lngStockStatusCode"]').get(0).checked;
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
            },// ����NO.
            From_strStockCode: {
                required: msgRequired
            },
            To_strStockCode: {
                required: msgRequired
            },
            // ȯ���NO.
            From_strOrderCode: {
                required: msgRequired
            },
            To_strOrderCode: {
                required: msgRequired
            },
            // Ǽ�ʽ�NO.
            strSlipCode: {
                required: msgRequired
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: msgRequired
            },
            // ������            
            lngCustomerCode: {
                required: msgRequired
            },
            // ��ʧ���            
            lngPayConditionCode: {
                required: msgRequired
            },
            // ����������           
            From_dtmExpirationDate: {
                required: msgRequired
            },
            To_dtmExpirationDate: {
                required: msgRequired
            },
            // ��������            
            lngStockSubjectCode: {
                required: msgRequired
            },
            // ��������            
            lngStockItemCode: {
                required: msgRequired
            },
            // ���ʥ�����            
            From_strProductCode: {
                required: msgRequired
            },
            To_strProductCode: {
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
            // ����            
            'lngStockStatusCode[]': {
                required: msgRequired
            }
        }
    });
})();
