(function() {
    var apply = function(handleName, docMsw){
        var code = $('input[name=' + handleName + ']');
        code.val(docMsw.find('select.result-select').find('option:selected').attr('code'));
        // msw����ɽ��
        invokeMswClose(docMsw);
        // �ܵҥ����ɥ����󥸥��٥�ȥ��å�
        code.trigger('change');
    };

    // �Ĥ���ܥ�������θƤӽФ�
    var invokeMswClose = function(msw){
        msw.find('.msw-box__header__close-btn').trigger('click');
    };

    // M�ܥ��󲡲�����
    $('img.msw-button').on({
        'click': function() {

            var mswName = $(this).attr('invokeMSWName');
            var ifmMsw = $('iframe.' + mswName);
            var docMsw = $(ifmMsw.get(0).contentWindow.document);

            // iframe�Υݥ������,����������
            // iframe��ɽ���ΰ��ɽ��ʪ(msw-box)�Υ������˹�碌��
            var mswBox = docMsw.find('.msw-box');
            var ifmHeight = mswBox.offset().top + mswBox.outerHeight(true);
            var ifmWidth = mswBox.offset().top + mswBox.outerWidth(true);
            var pos = setPosition(this, docMsw);
            ifmMsw.css({
                'position': 'absolute',
                'top': pos.top,
                'left': pos.left,
                'height': ifmHeight,
                'width': ifmWidth,
                'z-index': '9999'
            });

            var handleName = $(this).prev().prev().attr('name');
            ifmMsw.get(0).handler = handleName;

            docMsw.off('click', 'img.apply');
            docMsw.off('keydown', 'img.apply');
            docMsw.on('click', 'img.apply', function() {
                    apply(handleName, docMsw);
                }
            );
            docMsw.on('keydown', 'img.apply', function(e){
                    if(e.which == 13){
                        apply(handleName, docMsw);
                    }
                }
            );

            // MSWɽ��ľ���˼¹Ԥ�����������
            var mswBrfore = $(this).attr('msw-before');
            if(mswBrfore){
                eval(mswBrfore + '(handleName);');
            }

            // msw��ɽ��
            invokeMswClose(docMsw);

            // �إå���������
            var headerWidth = docMsw.find('.msw-box__header').width();
            var btnCloseWidth = docMsw.find('.msw-box__header__close-btn').width();
            var btnCloseHeight = docMsw.find('.msw-box__header__close-btn').height();
            var headerbar = docMsw.find('.msw-box__header__bar');
            headerbar.css({
                'height': btnCloseHeight,
                'width': headerWidth - btnCloseWidth,
                'background-color': '#5495c8',
                'line-height': btnCloseHeight + 'px',
                'color': 'white',
                'font-size': '12px',
                'font-weight': 'bold',
                'text-indent': '1em'
            });

            // msw��κǽ��input�˥ե�������
            docMsw.find('input[tabindex="1"]').focus();
        }
    });

    // msw��position����
    var setPosition = function(btn, docMsw) {
        // �ܥ���οƤΥ饤��
        var line = $(btn).parents('[class*="regist-line"]');
        var lineOffset = line.offset();

        var mswBox = docMsw.find('.msw-box');
        var mswBoxHeight = mswBox.outerHeight(true);
        var mswBoxWidth = mswBox.outerWidth(true);
        // msw�������
        var position = {top: line.position().top + line.height(), left: line.position().left};

        // msw��ɽ�������̤˼��ޤ�ʤ����
        if(lineOffset.top + line.height() + mswBoxHeight > $(document).height() && $(document).height() > mswBoxHeight){
            // ���̤ι⤵�˼��ޤ�ʤ��⤵ʬ�����
            position.top -= $('[class^="form-box--"], [class="form-box"]').offset().top + position.top + line.height() + mswBoxHeight - $(document).height();
        }

        // msw���������̤˼��ޤ�ʤ����
        position.left -= Math.min(position.left, (position.left + mswBoxWidth > $(document).width() && $(document).width() > mswBoxWidth)?
        Math.abs(position.left + mswBoxWidth - $(document).width()) : 0);

        return position;
	}

})();
