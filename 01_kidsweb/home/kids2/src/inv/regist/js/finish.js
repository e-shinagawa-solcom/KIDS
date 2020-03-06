
(function () {
    // 閉じた際の処理
    $(window).on('beforeunload', function () {
        if (window.opener.location.href.indexOf('renew') >= 0) {
            window.opener.opener.location.reload();
            window.opener.close();
        } else {
            window.opener.location.reload();
        }
        window.close();

    });
})();