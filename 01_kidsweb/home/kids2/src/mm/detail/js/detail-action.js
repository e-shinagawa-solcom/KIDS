
(function(){
    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function (){
            window.close();
        }
    });

    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            $(this).find('img').click();
        }
    });
})();
