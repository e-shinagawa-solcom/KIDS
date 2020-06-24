(function () {
    var mswBox = $('.msw-box');
    var btnClose = mswBox.find('.msw-box__header__close-btn');
    var inputCode = mswBox.find('.input-code');
    var inputName = mswBox.find('.input-name');
    var resultSelectBox = mswBox.find('.result-select');
    var resultCount = mswBox.find('.counter');
    var btnClear = mswBox.find('.clear');

    // 閉じるボタン押下処理
    btnClose.on({
        'click': function () {
            clear();
            $(window.frameElement).toggle();
            $("body").css('top', '0px');
            $("body").css('left', '0px');
        }
    });

    // クリアボタン押下処理
    btnClear.on({
        // クリック
        'click': function () {
            clear();
            // msw内の最初のinputにフォーカス
            mswBox.find('input').eq(0).focus();
            $("body").css('top', '0px');
            $("body").css('left', '0px');
        },
        // EnterKey
        'keypress': function (e) {
            if (e.which == 13) {
                clear();
                // msw内の最初のinputにフォーカス
                mswBox.find('input').eq(0).focus();
                $("body").css('top', '0px');
                $("body").css('left', '0px');
            }
        }
    })

    var clear = function () {
        inputCode.val('');
        inputName.val('');
        resultSelectBox.empty();
        resultCount.val('');
    }



})();

// iframe要素のdraggable実装
(function () {
    var iframe = $(window.frameElement);
    var winHeight = 670;
    var height = iframe.parents('body').outerHeight(true);
    console.log(height);
    if (height < winHeight) {
        height = winHeight;
    }

    var iframe_top = iframe.css('top');
    var iframe_left = iframe.css('left');
    var iframe_height = iframe.css('height');
    var iframe_width = iframe.css('width');
    console.log(iframe_top);
    $("body").on("dragstart", function (event, ui) {
        iframe.css('top', '0px');
        iframe.css('left', '0px');
        iframe.css('height', height - 10);
        iframe.css('width', iframe.parents('body').outerWidth(true));
    });
    $('body').draggable({
        cursor: "move",
        containment: [0, 0, iframe.parents('body').outerWidth(true), iframe.parents('body').outerHeight(true)]
    });
})();
