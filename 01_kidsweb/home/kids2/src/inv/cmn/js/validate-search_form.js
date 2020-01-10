
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

    // ��Ͽ�ܥ��󥤥٥�Ȳ����
    var events = $._data($('img.search').get(0), 'events');
    var originalHandler = [];
    for(var i = 0; i < events.click.length; i++){
        originalHandler[i] = events.click[i].handler;
    }
    // ���ߤΥ��٥�Ȥ��Ǥ��ä�
    $('img.search').off('click');
    $('img.search').on('click', {next:originalHandler}, function(event){
        var result = checkedCheckbox($('input.is-search'), "�����������å��ܥå��������򤵤�Ƥ��ޤ���");
        if(!result){
            return false;
        }


        // ��α���Ƥ������٥�Ȥ�¹�
        for(var i = 0; i < event.data.next.length; i++){
            event.data.next[i]();
        }
    });
    function checkedCheckbox(e, msg){
        var result = isChecked(e);
        if(!result){
            alert(msg);
        }
        return result;
    }
    function isChecked(e){
        var result = false;
        $(e).each(function(){
            if($(this).prop('checked')){
                result = true;
                return false;
            }
        });
        return result;
    }

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
                } else {
                    return false;
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
            // �����׾���
            From_DtmAppropriationDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_DtmAppropriationDate"]').get(0).checked;
                }
            },
            To_DtmAppropriationDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_DtmAppropriationDate"]').get(0).checked;
                }
            },
            // �»���
            From_ActionDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionDate"]').get(0).checked;
                }
            },
            To_ActionDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionDate"]').get(0).checked;
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
