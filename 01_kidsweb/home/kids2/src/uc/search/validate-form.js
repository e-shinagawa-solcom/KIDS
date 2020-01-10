
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
            if (params) {
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
            if (params) {
                return this.optional(element) || /^d\d{8}(_\d{2})?$/.test(value);
            }
            return true;
        },
        msgSpecialFormat
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
            if (params && value != "") {
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                } else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)) {
                    if (value.length == 7) {
                        var str = value.trim();
                        var y = str.substr(0, 4);
                        var m = str.substr(5, 2);
                        var d = '01';
                        value = y + "/" + m + "/" + d;
                    }
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
                } else {
                    return false;
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
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
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
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
                var params1 = $(params[1]).val();
                // FROM_XXXX�����Ϥ��줿��硢                
                if (params1 != "") {
                    if (/^[0-9]{8}$/.test(params1)) {
                        var str = params1.trim();
                        var y = str.substr(0, 4);
                        var m = str.substr(4, 2);
                        var d = str.substr(6, 2);
                        params1 = y + "/" + m + "/" + d;
                    }
                    var regResult = regDate.exec(params1);
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
            // ���������
            bytInvalidFlag: {
                required: function () {
                    return $('input[name="IsSearch_bytInvalidFlagConditions"]').get(0).checked;
                }
            },
            // �桼����������
            lngUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngUserCodeConditions"]').get(0).checked;
                }
            },
            // �桼����ID
            strUserID: {
                required: function () {
                    return $('input[name="IsSearch_strUserIDConditions"]').get(0).checked;
                }
            },
            // �᡼���ۿ�����
            bytMailTransmitFlag: {
                required: function () {
                    return $('input[name="IsSearch_bytMailTransmitFlagConditions"]').get(0).checked;
                }
            },
            // �᡼�륢�ɥ쥹
            strMailAddress: {
                required: function () {
                    return $('input[name="IsSearch_strMailAddressConditions"]').get(0).checked;
                }
            },
            // �桼����ɽ��
            bytUserDisplayFlag: {
                required: function () {
                    return $('input[name="IsSearch_bytUserDisplayFlagConditions"]').get(0).checked;
                }
            },
            // ɽ���桼����������
            strUserDisplayCode: {
                required: function () {
                    return $('input[name="IsSearch_strUserDisplayCodeConditions"]').get(0).checked;
                }
            },
            // ɽ���桼����̾
            strUserDisplayName: {
                required: function () {
                    return $('input[name="IsSearch_strUserDisplayNameConditions"]').get(0).checked;
                }
            },
            // �ե�͡���
            strUserFullName: {
                required: function () {
                    return $('input[name="IsSearch_strUserFullNameConditions"]').get(0).checked;
                }
            },
            // ���
            lngCompanyCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngCompanyCodeConditions"]').get(0).checked;
                }
            },
            // ���롼��
            lngGroupCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngGroupCodeConditions"]').get(0).checked;
                }
            },
            // ���¥��롼��
            lngAuthorityGroupCode: {
                required: function () {
                    return $('input[name="IsSearch_lngAuthorityGroupCodeConditions"]').get(0).checked;
                }
            },
            // ��������IP���ɥ쥹
            lngAccessIPAddressCode: {
                required: function () {
                    return $('input[name="IsSearch_lngAccessIPAddressCodeConditions"]').get(0).checked;
                }
            }
        },
        // -----------------------------------------------
        // ���顼��å�����
        // -----------------------------------------------
        messages: {
            // ���������
            bytInvalidFlag: {
                required: msgRequired
            },
            // �桼����������
            lngUserCode: {
                required: msgRequired
            },
            // �桼����ID
            strUserID: {
                required: msgRequired
            },
            // �᡼���ۿ�����
            bytMailTransmitFlag: {
                required: msgRequired
            },
            // �᡼�륢�ɥ쥹
            strMailAddress: {
                required: msgRequired
            },
            // �桼����ɽ��
            bytUserDisplayFlag: {
                required: msgRequired
            },
            // ɽ���桼����������
            strUserDisplayCode: {
                required: msgRequired
            },
            // ɽ���桼����̾
            strUserDisplayName: {
                required: msgRequired
            },
            // �ե�͡���
            strUserFullName: {
                required: msgRequired
            },
            // ���
            lngCompanyCode: {
                required: msgRequired
            },
            // ���롼��
            lngGroupCode: {
                required: msgRequired
            },
            // ���¥��롼��
            lngAuthorityGroupCode: {
                required: msgRequired
            },
            // ��������IP���ɥ쥹
            lngAccessIPAddressCode: {
                required: msgRequired
            }
        }
    });
})();
