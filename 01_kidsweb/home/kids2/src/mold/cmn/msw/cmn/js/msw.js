(function() {
    var apply = function(handleName, docMsw){
        var code = $('input[name=' + handleName + ']');
        code.val(docMsw.find('select.result-select').find('option:selected').attr('code'));
        // mswの非表示
        invokeMswClose(docMsw);
        // 顧客コードチェンジイベントキック
        code.trigger('change');
    };

    // 閉じるボタン処理の呼び出し
    var invokeMswClose = function(msw){
        msw.find('.msw-box__header__close-btn').trigger('click');
    };

    // Mボタン押下処理
    $('img.msw-button').on({
        'click': function() {

            var mswName = $(this).attr('invokeMSWName');
            var ifmMsw = $('iframe.' + mswName);
            var docMsw = $(ifmMsw.get(0).contentWindow.document);

            // iframeのポジション,サイズ設定
            // iframeの表示領域を表示物(msw-box)のサイズに合わせる
            var mswBox = docMsw.find('.msw-box');
            var ifmHeight = mswBox.offset().top + mswBox.outerHeight(true);
            var ifmWidth = mswBox.offset().top + mswBox.outerWidth(true);
            var pos = setPosition(this, docMsw);
            ifmMsw.css({
                'position': 'absolute',
                'top': '0px',
                'left': '0px',
                'height': '100%',
                'width': '100%',
                'z-index': '9999'
            });

            var handleName = $(this).parents().parents().prev().prev().attr('name');
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

            // MSW表示直前に実行させたい処理
            var mswBrfore = $(this).attr('msw-before');
            if(mswBrfore){
                eval(mswBrfore + '(handleName);');
            }

            // mswの表示
            invokeMswClose(docMsw);

            // ヘッダーの設定
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

            // msw内の最初のinputにフォーカス
            docMsw.find('input[tabindex="1"]').focus();
        }
    });

    // mswのposition設定
    var setPosition = function(btn, docMsw) {
        // ボタンの親のライン
        var line = $(btn).parents('[class*="regist-line"]');
        var lineOffset = line.offset();

        var mswBox = docMsw.find('.msw-box');
        var mswBoxHeight = mswBox.outerHeight(true);
        var mswBoxWidth = mswBox.outerWidth(true);
        // msw初期位置
        var position = {top: line.position().top + line.height(), left: line.position().left};

        // mswの表示が画面に収まらない場合
        if(lineOffset.top + line.height() + mswBoxHeight > $(document).height() && $(document).height() > mswBoxHeight){
            // 画面の高さに収まらない高さ分を引く
            position.top -= $('[class^="form-box--"], [class="form-box"]').offset().top + position.top + line.height() + mswBoxHeight - $(document).height();
        }

        // msw横幅が画面に収まらない場合
        position.left -= Math.min(position.left, (position.left + mswBoxWidth > $(document).width() && $(document).width() > mswBoxWidth)?
        Math.abs(position.left + mswBoxWidth - $(document).width()) : 0);

        return position;
	}

})();
