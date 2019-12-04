
(function(){
    // 閉じた際の処理
    $(window).on('beforeunload', function(){
        $(window.opener.opener.document).find('form').submit();
    });
})();