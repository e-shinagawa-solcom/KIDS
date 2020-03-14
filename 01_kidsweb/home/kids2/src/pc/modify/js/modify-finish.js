(function () {
    // 閉じるボタンのイベント
    $('#close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
        // 親ウィンドウを閉じる
        window.opener.location.reload();
    });
    
    $(window).on("beforeunload", function(e) {
        window.opener.location.reload();
    });
})();