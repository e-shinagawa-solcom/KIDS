
(function(){
    // �ե�����
    var workForm = $('form[name="RegistMoldHistory"]');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // ���ꥢ�ܥ���
    var btnClear = $('.form-buttons__clear');
    // ��Ͽ�ܥ���
    var btnRegist = $('.form-buttons__regist');
    // ������ζⷿ�ꥹ��
    var choosenMoldList = $('select[name="ChoosenMoldList"]');
    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // ���ꥢ�ܥ���
    btnClear.on('click', function(){
        // �ⷿ����ơ��֥�ʲ���SELECT���Ǥλ����Ǥ���
        workForm.find('table.mold-selection select').children().remove();
        // �ⷿ�����ơ��֥��۲���TBODY���Ǥλ����Ǥ���
        workForm.find('table.table-description').find('tbody').children().remove();
        // �ƥ��������ϲս��ꥻ�å�
        workForm.find('input, textarea').val('');
        // �إå����֤�SELECT���Ǥ���ƬOPTION���Ǥ˥ꥻ�å�
        workForm.find('select').each(function(index){
            $(this).val($(this).find('option').first().val());
        });
    });

    // ��Ͽ�ܥ��󲡲����ν���
    btnRegist.on('click', function(){
            clickRegist(this);
    });

    // ��Ͽ�ܥ��󥯥�å����˸ƤӽФ�function
    var clickRegist = function(invoker) {
        // �ե����ม��
        if(validate(invoker)){
            // ���֥ߥåȽ���
            submitMoldReport(invoker);
        }
    }

    // ���ڽ����Υ��å�
    var validate = function(invoker) {
        // ���ڷ��
        var result = false;

        // �ڷ�̤�OK�ξ��
        if(workForm.valid()){
                result = true;
        }
        // �ܺ٥��ָ��ڷ�̤�NG�ξ��
        else {
            // ���֥ߥå�(���ڷ�̤�ɽ��)
            workForm.find(':submit').click();
        }

        return result;
    };

    // �ⷿ���򥵥֥ߥå�
    var submitMoldReport = function(invoker) {
        // ����å����٥��̵����
        $(invoker).off('click');

        var formData = workForm.serializeArray();
        // ChoosenMoldList����ⷿNO����Ф��ƥե�����ǡ������ɲ�
        choosenMoldList.find('option').each(function(index, option){
            formData.push({
                name: 'MoldNo'+ (index + 1),
                value: option.value
            });
        });

        // �ǥХå�����
        $.each(formData, function(index, data){
            console.log(data.name + ' : ' + data.value);
        });

        // �ꥯ����������
        $.ajax({
            url: '/mold/validation/MoldHistory/regist.php?strSessionID=' + $.cookie('strSessionID'),
            type: 'post',
            dataType: 'json',
            data: formData
        })
        .done(function(response){
            console.log('�ⷿ������Ͽ-���� done');

            // ����OK�ξ��
            if (response.resultHash)
            {
                console.log('�ⷿ������Ͽ-���ڷ�� OK');

                // ��ǧ����URL
                var confirmURL = '/mm/confirm/mm_confirm.php?strSessionID=' + $.cookie('strSessionID') + "&resultHash=" + response.resultHash

                // ��ǧ������iframe����
                $dialogContent = $('<iframe>')
                                    .attr("class", "regist-confirm")
                                    .attr("src", confirmURL)
                                    .attr("frameborder", 0)
                                    .attr("style", "width: 300px; height: 600px;");

                // ������������(jQuery UI)
                $dialogContent.dialog({
                    autoOpen: true,
                    closeOnEscape: true,
                    modal: true,
                    resizable: true,
                    draggable: true,
                    position: {
                        at: "left top"
                    },
                    hight: 500,
                    width: "auto",
                    // �Ĥ���ݤ�iframe���˴�����
                    close: function(event, ui){
                        try {
                            // ��ǧ���̰ʳ����Ĥ������ϥ���ɤ�����
                            if (location.origin != this.contentWindow.location.origin ||
                                !/\/mm\/confirm\/mm_confirm.php/.test(this.contentWindow.location.href)){
                                location.reload();
                            }
                            // ��������/iframe�˴�
                            $(this).dialog('destroy');
                            $(event.target).remove();
                        }
                        // ���顼�ξ��ϥ����
                        catch (e){
                            location.reload();
                        }

                        // focus�Ǥ��ʤ��ʤ�Х��б�
                        $($('input[name="ProductCode"]')[0]).focus();

                        // ��Ͽ�ܥ��󲡲���������
                        btnRegist.on('click', function(){
                                clickRegist(this);
                        });
                    }
                });

                // jQuery UI�Ǽ�ưŪ�����ꤵ��륹���������
                $dialogContent.removeAttr("style");
                // ���������򥻥󥿥�󥰤���
                var divDialog = $('body > .ui-dialog');
                divDialog.css("top", ( $(window).height() - divDialog.height() ) / 2 + $(window).scrollTop() + "px")
                         .css("left", ( $(window).width() - divDialog.width() ) / 2 + $(window).scrollLeft() + "px");
            }
            // ����NG�ξ��
            else {
                console.log('�ⷿĢɼ��Ͽ-���ڷ�� NG');
                console.log(response);

                // alert��ɽ���������å�������
                var alertMessages = '';

                // ���顼��å������Υե����ɥХå�
                $.each(response, function(name, msgError){
                    var element = $('[name="' + name + '"]');

                    // name°�������פ�����
                    if (1 <= element.length){
                        invalidImg = $('<img>')
                                        .attr('class', classNameErrorIcon)
                                        .attr('src', urlErrorIcon)
                                        // CSS����(ɽ������)
                                        .css({
                                            position: 'absolute',
                                            top: $(element).position().top,
                                            left: $(element).position().left - 20,
                                        })
                                        // �ġ�����å�ɽ��
                                        .tooltipster({
                                            trigger: 'hover',
                                            onlyone: false,
                                            position: 'top',
                                            content: msgError
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
                                        .tooltipster('content', msgError);
                        }
                    }
                    // ����ʳ��Υ��顼��å�������alert��ɽ�����롣
                    else {
                        alertMessages += msgError + "\r\n";
                    }
                });

                // alert��å����������ꤵ��Ƥ�����
                if (alertMessages){
                    alert(alertMessages);
                }

                // ��Ͽ�ܥ��󲡲���������
                btnRegist.on('click', function(){
                        clickRegist(this);
                });
            }
        })
        .fail(function(response){
            console.log('�ⷿ������Ͽ-���� fail');
            console.log(response.responseText);
            alert(
                "�ꥯ�����Ȥν�����˥��顼��ȯ�����ޤ�����" + "\r\n" +
                "�ƥ������ԤäƤ⤳�Υ��顼����ä���ʤ����ϥ����ƥ�ô���Ԥˤ�Ϣ��������"
            );

            // ��Ͽ�ܥ��󲡲���������
            btnRegist.on('click', function(){
                    clickRegist(this);
            });
        });
    };
})();
