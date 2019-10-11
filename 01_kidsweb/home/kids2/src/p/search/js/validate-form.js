
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

    ����// ���ʥ����ɤν񼰥����å�
    $.validator.addMethod(
        "checkStrProductCode",
        function (value, element, params) {
            if (params) {
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

    // ����̾�ʱѸ�ˤ�Ⱦ�ѱѿ����ʳ���ʸ�������ϤǤ��ʤ�
    $.validator.addMethod(
        "checkStrProductEnglishName",
        function (value, element, params) {
            if (params) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
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

    // ���դ�̤�����Ǥʤ��� ActionDate
    $.validator.addMethod(
        "isLessThanToday",
        function (value, element, params) {
            if (params) {
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
            if (params[0]) {
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
            // ��������
            From_dtmUpdateDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmUpdateDate"]').get(0).checked && $('input[name="To_dtmUpdateDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmUpdateDate"]').get(0).checked;
                },
                isLessThanToday: function () {
                    return $('input[name="IsSearch_dtmUpdateDate"]').get(0).checked;
                }
            },
            To_dtmUpdateDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmUpdateDate"]').get(0).checked && $('input[name="From_dtmUpdateDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmUpdateDate"]').get(0).checked;
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmUpdateDate"]').get(0).checked, 'input[name="From_dtmUpdateDate"]'];
                }
            },
            // �ܵ�����            
            strGoodsCode: {
                required: function () {
                    return $('input[name="IsSearch_strGoodsCode"]').get(0).checked;
                }
            },
            // ����̾��            
            strGoodsName: {
                required: function () {
                    return $('input[name="IsSearch_strGoodsName"]').get(0).checked;
                }
            },
            // �ܵ�            
            lngCustomerCompanyCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCompanyCode"]').get(0).checked;
                }
            },
            // �ܵ�ô����            
            lngCustomerUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerUserCode"]').get(0).checked;
                }
            },
            // ��������            
            lngFactoryCode: {
                required: function () {
                    return $('input[name="IsSearch_lngFactoryCode"]').get(0).checked;
                }
            },
            // ���å���֥깩��            
            lngAssemblyFactoryCode: {
                required: function () {
                    return $('input[name="IsSearch_lngAssemblyFactoryCode"]').get(0).checked;
                }
            },
            // Ǽ�ʾ��            
            lngDeliveryPlaceCode: {
                required: function () {
                    return $('input[name="IsSearch_lngDeliveryPlaceCode"]').get(0).checked;
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
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
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
                },
                checkStrProductEnglishName: function () {
                    return $('input[name="IsSearch_strProductEnglishName"]').get(0).checked;
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
            // ��ȯô����            
            lngDevelopUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngDevelopUserCode"]').get(0).checked;
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
            },// ��������
            From_dtmUpdateDate: {
                required: msgRequired
            },
            To_dtmUpdateDate: {
                required: msgRequired
            },
            // �ܵ�����            
            strGoodsCode: {
                required: msgRequired
            },
            // ����̾��            
            strGoodsName: {
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
            // ��������            
            lngFactoryCode: {
                required: msgRequired
            },
            // ���å���֥깩��            
            lngAssemblyFactoryCode: {
                required: msgRequired
            },
            // Ǽ�ʾ��            
            lngDeliveryPlaceCode: {
                required: msgRequired
            },
            // Ǽ��
            From_dtmDeliveryLimitDate: {
                required: msgRequired
            },
            To_dtmDeliveryLimitDate: {
                required: msgRequired
            },
            // ���ϼ�            
            lngInputUserCode: {
                required: msgRequired
            },
            // ���ʥ�����            
            strProductCode: {
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
            // ô����            
            lngInChargeUserCode: {
                required: msgRequired
            },
            // ��ȯô����            
            lngDevelopUserCode: {
                required: msgRequired
            }
        }
    });
})();
