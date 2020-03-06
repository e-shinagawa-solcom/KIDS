(function () {    

    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function(e) {
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウをリロードする
        window.opener.location.reload();
    });
})();