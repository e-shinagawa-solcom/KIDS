(function () {
    $(window).on("beforeunload", function(e) {
        window.opener.location.reload();
    });
})();