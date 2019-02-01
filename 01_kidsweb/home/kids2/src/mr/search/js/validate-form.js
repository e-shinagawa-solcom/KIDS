
(function(){
    // �ե�����
    var form = $('form');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // ���顼��å�����(����)
    var msgDateFormat = "yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������";
    // ���եե����ޥå� yyyy/mm/dd����
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;

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
            }return true;
        },
        msgDateFormat
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
            // ������
            From_RequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_RequestDate"]').get(0).checked;
                }
            },
            To_RequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_RequestDate"]').get(0).checked;
                }
            },
            // ��˾��
            From_ActionRequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionRequestDate"]').get(0).checked;
                }
            },
            To_ActionRequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionRequestDate"]').get(0).checked;
                }
            },
            // �ֵ�ͽ����
            From_ReturnSchedule: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ReturnSchedule"]').get(0).checked;
                }
            },
            To_ReturnSchedule: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ReturnSchedule"]').get(0).checked;
                }
            },
            // ��Ͽ��
            From_Created: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Created"]').get(0).checked;
                }
            },
            To_Created: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Created"]').get(0).checked;
                }
            },
            // ������
            From_Updated: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Updated"]').get(0).checked;
                }
            },
            To_Updated: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Updated"]').get(0).checked;
                }
            }
        }
    });
})();
