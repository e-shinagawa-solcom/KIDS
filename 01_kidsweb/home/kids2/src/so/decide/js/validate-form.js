
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

    // �������ɤν񼰥����å�
    $.validator.addMethod(
        "checkStrReceiveCode",
        function (value, element, params) {
            if (params) {
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
            if (value != "") {
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
            // ����No.            
            strReceiveCode: {
                checkStrReceiveCode: true
            },
            // ���ʥ�����            
            strProductCode: {
                checkStrProductCode: true
            },
            // Ǽ��
            From_dtmDeliveryDate: {
                checkDateFormat: true
            },
            // Ǽ��
            To_dtmDeliveryDate: {
                checkDateFormat: true,
                isGreaterThanFromDate: function () {
                    return [true, 'input[name="From_dtmDeliveryDate"]']
                }
            }
        }
    });
})();
