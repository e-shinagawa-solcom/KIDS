
(function () {
    // �ե�����
    var workForm = $('form');
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
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // �Ĥ����ݤν���
    $(window).on('beforeunload', function () {
        $(window.opener.opener.document).find('form').submit();
    });

    // ���ꥢ�ܥ���
    btnClear.on('click', function () {
        window.location.reload();
    });

    // ��Ͽ�ܥ��󲡲����ν���
    btnRegist.on('click', function () {
        clickRegist(this);
    });

    // ��Ͽ�ܥ��󥯥�å����˸ƤӽФ�function
    var clickRegist = function (invoker) {

        // �ե����ม��
        if (validate(invoker)) {
            // ���֥ߥåȽ���
            submitProduct(invoker);
        }
    }

    // ���ڽ����Υ��å�
    var validate = function (invoker) {
        // ���ڷ��
        var result = false;

        // ξ�������Ƥ�Ʃ���ˤ��ư�ö����
        divHeader.css('opacity', 0.0);
        divDetail.css('opacity', 0.0);

        // �إå����ָ���
        tabHeader.click();
        
        // �إå����֤θ��ڷ�̤�OK�ξ��
        if (workForm.valid()) {
            // �ܺ٥��ָ���
            tabDetail.click();
            // �ܺ٥��ָ��ڷ�̤�OK�ξ��
            if (workForm.valid()) {
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

    // ���ʥ��֥ߥå�
    var submitProduct = function (invoker) {
        // ����å����٥��̵����
        // $(invoker).off('click');

        var formData = workForm.serializeArray();
        formData.push({ name: "strSessionID", value: $.cookie('strSessionID') });
        // �ꥯ����������
        $.ajax({
            url: '/p/modify/modify_confirm.php',
            type: 'POST',
            data: formData
        })
            .done(function (response) {
                var w = window.open();
                w.document.open();
                w.document.write(response);
                w.document.close();
                w.onunload = function () {
                    window.opener.location.reload();
                }
            })
            .fail(function (response) {
                alert("fail");
                alert(response);

            });
    };

    $("img.edit").on('click', function () {
        var display = $('#EditFrame').css('display');
        if (display == "block") {
            $("#EditFrame").css("display", "none");
        } else {
            $("#EditFrame").css("display", "block");
        }
        
        window.editWin.fncEditParentToHtmltext();
    });
})();
