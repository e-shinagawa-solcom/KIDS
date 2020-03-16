
(function(){
    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function(){
            parent.$('iframe.regist-confirm').prev().find('.ui-dialog-titlebar-close').click();
        }
    });
    
    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            $(this).find('img').click();
        }
    });
})();
