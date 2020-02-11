(function () {
    $(window).on("beforeunload", function(e) {
        if(window.opener.location.pathname.indexOf("/list") >= 0){
            window.opener.location.reload();
        }
    });
})();