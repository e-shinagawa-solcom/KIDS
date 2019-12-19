(function () {
    $(window).on("beforeunload", function(e) {
        if(window.opener.locaton.pathname.indexof("/list") >= 0){
            window.opener.location.reload();
        }
    });
})();