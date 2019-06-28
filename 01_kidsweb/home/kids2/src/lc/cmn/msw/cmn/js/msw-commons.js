(function() {
    var mswBox = $('.msw-box');
    var btnClose = mswBox.find('.msw-box__header__close-btn');
    var inputCode = mswBox.find('.input-code');
    var inputName = mswBox.find('.input-name');
    var resultSelectBox = mswBox.find('.result-select');
    var resultCount = mswBox.find('.counter');
    var btnClear = mswBox.find('.clear');

    // 閉じるボタン押下処理
    btnClose.on({
        'click': function() {
            clear();
            $(window.frameElement).toggle();
        }
    });

    // クリアボタン押下処理
    btnClear.on({
        // クリック
        'click': function(){
            clear();
            // msw内の最初のinputにフォーカス
            mswBox.find('input').eq(0).focus();
        },
        // EnterKey
        'keypress': function(e) {
            if(e.which == 13){
                clear();
                // msw内の最初のinputにフォーカス
                mswBox.find('input').eq(0).focus();
            }
        }
    })

    var clear = function(){
        inputCode.val('');
        inputName.val('');
        resultSelectBox.empty();
        resultCount.val('');
    }



})();

// iframe要素のdraggable実装
(function(){
    var dragging = null;
    var start = null;
    var iframe = $(window.frameElement);

    $('body').on('mousemove', function(e){
        if(dragging && start){
            var offsetTop = e.offsetY - start.top;
            var offsetLeft = e.offsetX - start.left;

            iframe.offset({
                top: iframe.offset().top + offsetTop,
                left: iframe.offset().left + offsetLeft
            });
        }
    });

    $('body').on('mousedown', 'div.msw-box__header', function(e){
        dragging = $(e.target);
        start = {top: e.offsetY, left: e.offsetX};
    });

    $('body').on('mouseup', function(e){
        dragging = null;
    });
})();
