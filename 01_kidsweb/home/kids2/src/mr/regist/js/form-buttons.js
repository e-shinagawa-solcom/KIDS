
(function(){
    // �ե�����
    var workForm = $('form[name="RegistMoldReport"]');
    // ����DIV
    var tabs = $('.tabs');
    // �إå�����
    var tabHeader = $('.tabs__header');
    // �ܺ٥���
    var tabDetail = $('.tabs__detail');
    // �إå�DIV
    var divHeader = $('div.regist-tab-header');
    // �ܺ�DIV
    var divDetail = $('div.regist-tab-detail');
    // ���顼�������󥯥饹̾
    var classNameErrorIcon = 'error-icon';
    // ���顼��������꥽����URL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // ���ꥢ�ܥ���
    var btnClear = $('.form-buttons__clear');
    // ��Ͽ�ܥ���
    var btnRegist = $('.form-buttons__regist');

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
        workForm.find('.regist-tab-header select').each(function(index){
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

        // ξ�������Ƥ�Ʃ���ˤ��ư�ö����
        divHeader.css('opacity', 0.0);
        divDetail.css('opacity', 0.0);

        // �إå����ָ���
        tabHeader.click();
        // �إå����֤θ��ڷ�̤�OK�ξ��
        if(workForm.valid()){
            // �ܺ٥��ָ���
            tabDetail.click();
            // �ܺ٥��ָ��ڷ�̤�OK�ξ��
            if(workForm.valid()){
                // ���ڷ��OK
                result = true;
            }
            // �ܺ٥��ָ��ڷ�̤�NG�ξ��
            else {
                // ���֥ߥå�(���ڷ�̤�ɽ��)
                workForm.find(':submit').click();
            }
        }
        // �إå����֤θ��ڷ�̤�NG�ξ��
        else {
            // �ܺ٥��ָ���
            tabDetail.click();
            // ���֥ߥå�(���ڷ�̤�ɽ��)
            workForm.find(':submit').click();
            // �إå����֤��ڤ��ؤ�
            tabHeader.click();
        }

        // ξ���֤�Ʃ��������
        divHeader.css('opacity', '');
        divDetail.css('opacity', '');

        return result;
    };

    // �ⷿĢɼ���֥ߥå�
    var submitMoldReport = function(invoker) {
        // ����å����٥��̵����
        $(invoker).off('click');

        var formData = workForm.serializeArray();

        // �ǥХå�����
        $.each(formData, function(index, data){
            console.log(data.name + ' : ' + data.value);
        });

        // �ꥯ����������
        $.ajax({
            url: '/mold/validation/MoldReport/regist.php?strSessionID=' + $.cookie('strSessionID'),
            type: 'post',
            dataType: 'json',
            data: formData
        })
        .done(function(response){
            console.log('�ⷿĢɼ��Ͽ-���� done');

            // ����OK�ξ��
            if (response.resultHash)
            {
                console.log('�ⷿĢɼ��Ͽ-���ڷ�� OK');

                // ��ǧ����URL
                var confirmURL = '/mr/confirm/mr_confirm.php?strSessionID=' + $.cookie('strSessionID') + "&resultHash=" + response.resultHash

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
                                !/\/mr\/confirm\/mr_confirm.php/.test(this.contentWindow.location.href)){
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
                        $($('input[name="ProductCode"]')[1]).focus();

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

                // ξ�������Ƥ�Ʃ���ˤ��ư�ö����
                divHeader.css('opacity', 0.0);
                divDetail.css('opacity', 0.0);

                // alert��ɽ���������å�������
                var alertMessages = '';

                // ���顼��å������Υե����ɥХå�
                $.each(response, function(name, msgError){
                    var element = $('[name="' + name + '"]');

                    // name°�������פ�����
                    if (1 <= element.length){
                        // �إå�DIV�λ����Ǥξ��
                        if(1 <= element.parents('div.regist-tab-header').length){
                            // ɽ�����֤�header�Ǥʤ����
                            if(tabs.prop('displayTab') != 'header'){
                                tabHeader.click();
                            }
                        }
                        // �ܺ�DIV�λ����Ǥξ��
                        else if(1 <= element.parents('div.regist-tab-detail').length){
                            // ɽ�����֤�detail�Ǥʤ����
                            if(tabs.prop('displayTab') != 'detail'){
                                tabDetail.click();
                            }
                        }

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

                // ξ���֤�Ʃ��������
                divHeader.css('opacity', '');
                divDetail.css('opacity', '');

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
            console.log('�ⷿĢɼ��Ͽ-���� fail');
            console.log(response.responseText);
            
            alert(
                "�ꥯ�����Ȥν�����˥��顼��ȯ�����ޤ�����" + "\r\n" +
                "���Υ��顼����ä���ʤ����ϥ����ƥ�ô���Ԥˤ�Ϣ��������"
            );

            // ��Ͽ�ܥ��󲡲���������
            btnRegist.on('click', function(){
                    clickRegist(this);
            });
        });
    };
})();
