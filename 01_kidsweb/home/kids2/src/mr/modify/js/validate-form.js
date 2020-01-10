
(function(){
    // �ե�����
    var form = $('form[name="RegistMoldReport"]');
    // �إå�����
    var header = $('div.regist-tab-header');
    // �ܺ٥���
    var detail = $('div.regist-tab-detail');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // ������ζⷿ�ꥹ�ȤΥ�٥�
    var labelChoosenMoldList = $('table.mold-selection')
                                    .find('tr:nth-of-type(1)')
                                    .find('th:nth-of-type(3)');

    // ���顼��å�����(ɬ�ܹ���)
    var msgRequired = "����ɬ�ܹ��ܤǤ���";
    // ���顼��å�����(����)
    var msgDateFormat = "yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������";
    var msgGreaterThanToday = "���ߤ��������դ������ϤǤ��ޤ���";
    var msgGreaterThanRequestDate = "��˾�����������դ������ϤǤ��ޤ���";
    // ���顼��å�����(��ư�褬�ݴɸ���Ʊ�칩��)
    var msgSameFactory = "��ư�蹩����ݴɸ������Ʊ���������ꤹ�뤳�ȤϤǤ��ޤ���";
    // Ģɼ����¾��κ������ϲ�ǽʸ����
    var noteMaxLen = 38;
    // ���顼��å�����(����¾�κ���ʸ�����ޤ�)
    var msgNote = noteMaxLen + "ʸ���ޤǤ������ϤǤ��ޤ���"

    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;

    // validation���å�
    $('.hasDatepicker').on({
        'change': function(){
            $(this).blur();
        }
    })

    // ��˾���ѹ������ֵ�ͽ������validation���å�
    $('input[name="ActionRequestDate"]').on({
        'blur': function(){
            $('input[name="ReturnSchedule"]').blur();
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
        function(value, element, params) {
            if(params){
                // yyyy/mm/dd������
                if (!(regDate.test(value))) {
                    return false;
                }

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
            }
            return true;
        },
        msgDateFormat
    );

    // ���դ����Ǥʤ��� �����Բ�
    $.validator.addMethod(
        "isGreaterThanToday",
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
                    // ���Ϥ�������ߤ�Ʊ�����
                    } else if (nowDi.getMonth() == di.getMonth()){
                        // ���Ϥ����������ߤ�Ʊ���������꾮������Х��顼
                        if (nowDi.getDate() >= di.getDate()) {
                            return false;
                        }
                    }
                    return true;
                }
            }
            return true;
        },
        msgGreaterThanToday
    );

    // �ֵ�ͽ��������˾������礭����(Ʊ���Բ�)
    $.validator.addMethod(
        "isGreaterThanRequestDate",
        function (value, element, params) {
            // ������ν�����20�ξ������å�
            if(params){
                // ��˾�������Ϥ���Ƥ���������å�
                if ($('input[name="ActionRequestDate"]').val() != "") {
                    var actionRequestDate = $('input[name="ActionRequestDate"]').val();
                    var regResult = regDate.exec(actionRequestDate);
                    var yyyy = regResult[1];
                    var mm = regResult[2];
                    var dd = regResult[3];
                    var RequestDate = new Date(yyyy, mm - 1, dd);

                    regResult = regDate.exec(value);
                    yyyy = regResult[1];
                    mm = regResult[2];
                    dd = regResult[3];
                    var di = new Date(yyyy, mm - 1, dd);
                    // ��˾�������������
                    // ���Ϥ���ǯ����˾����꾮������Х��顼
                    if (RequestDate.getFullYear() > di.getFullYear()) {
                        return false;
                    // ���Ϥ���ǯ����˾������礭�������
                    } else if (RequestDate.getFullYear() < di.getFullYear()) {
                        return true;
                    // ���Ϥ���ǯ�����ߤ�Ʊ�����
                    } else if (RequestDate.getFullYear() == di.getFullYear()) {
                        // ���Ϥ�������ߤ�꾮������Х��顼
                        if (RequestDate.getMonth() > di.getMonth()){
                            return false;
                        // ���Ϥ�������ߤ���礭�������
                        } else if (RequestDate.getMonth() < di.getMonth()){
                            return true;
                        // ���Ϥ�������ߤ�Ʊ�����
                        } else if (RequestDate.getMonth() == di.getMonth()){
                            // ���Ϥ����������ߤ�Ʊ���������꾮������Х��顼
                            if (RequestDate.getDate() >= di.getDate()) {
                                return false;
                            }
                        }
                        return true;
                    }
                }
            }
            return true;
        },
        msgGreaterThanRequestDate
    );

    $.validator.addMethod(
        "maxlength",
        function (value, element, params) {
            // ̤���Ϥξ������å����ʤ�
            return !value ? true : (value.length <= params) ? true : false;
        },
        msgNote
    );

    // �ֵ�ͽ����������å����뤫�ξ��
    $.validator.addMethod(
        "requiredWhenFinalKeepIsReturn",
        function (value, element, params) {
            // ������ν�����[20:�ֵ�]�ξ��ɬ��
            return (($('select[name="FinalKeep"]')).val() != 20) ? true : value ? true : false;
        },
        msgRequired
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
            // ���ʥ�����
            ProductCode: {
                required: true
            },
            // �ܵ�����
            GoodsCode: {
                required: true
            },
            // Ģɼ��ʬ
            ReportCategory: {
                required: true
            },
            // ������
            RequestDate: {
                checkDateFormat: true,
                required: true
            },
            // �����ʬ
            RequestCategory: {
                required: true
            },
            // ��˾��
            ActionRequestDate: {
                checkDateFormat: true,
                isGreaterThanToday: true,
                required: true
            },
            // ��ư��ˡ
            TransferMethod: {
                required: true
            },
            // �ؼ���ʬ
            InstructionCategory: {
                required: true
            },
            // ������(�ܵ�)
            CustomerCode: {
                required: true
            },
            // KWGô������
            KuwagataGroupCode: {
                required: true
            },
            // KWGô����
            KuwagataUserCode: {
                required: true
            },
            // ������ν���
            FinalKeep: {
                required: true
            },
            // �ֵ�ͽ���� ������ν������ֵѤξ��
            ReturnSchedule: {
                requiredWhenFinalKeepIsReturn: true,
                checkDateFormat: true,
                isGreaterThanToday: true,
                isGreaterThanRequestDate: true
            },
            // ����¾
            Note: {
                maxlength: noteMaxLen
            },
            // ����Ѥߤζⷿ�ꥹ��
            // ChoosenMoldList: {
            //    required: true
            //},
            // �ݴɹ���
            SourceFactory: {
                required: true
            },
            // ��ư�蹩��
            DestinationFactory: {
                required: true,
                difFactory: $('input[name="SourceFactory"]')
            }
        },
        // -----------------------------------------------
        // ���顼��å�����
        // -----------------------------------------------
        messages: {
            // ���ʥ�����
            ProductCode: {
                required: msgRequired
            },
            // �ܵ�����
            GoodsCode: {
                required: msgRequired
            },
            // Ģɼ��ʬ
            ReportCategory: {
                required: msgRequired
            },
            // ������
            RequestDate: {
                required: msgRequired
            },
            // �����ʬ
            RequestCategory: {
                required: msgRequired
            },
            // ��˾��
            ActionRequestDate: {
                required: msgRequired
            },
            // ��ư��ˡ
            TransferMethod: {
                required: msgRequired
            },
            // �ؼ���ʬ
            InstructionCategory: {
                required: msgRequired
            },
            // ������(�ܵ�)
            CustomerCode: {
                required: msgRequired
            },
            // KWGô������
            KuwagataGroupCode: {
                required: msgRequired
            },
            // KWGô����
            KuwagataUserCode: {
                required: msgRequired
            },
            // ������ν���
            FinalKeep: {
                required: msgRequired
            },
            // �ֵ�ͽ���� ������ν������ֵѤξ��
            ReturnSchedule: {
                required: msgRequired
            },
            // ����Ѥߤζⷿ�ꥹ��
            // ChoosenMoldList: {
            //    required: true
            //},
            // �ݴɹ���
            SourceFactory: {
                required: msgRequired
            },
            // ��ư�蹩��
            DestinationFactory: {
                required: msgRequired
            }
        }
    });
})();
