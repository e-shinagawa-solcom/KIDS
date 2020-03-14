(function () {
    
    // 閉じるボタンのイベント
    $('#close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウを閉じる
        window.opener.close();
        //親ウィンドウの親ウィンドウをリロードする
        window.opener.opener.location.reload();
    });
    

    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function(e) {
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウを閉じる
        window.opener.close();
        //親ウィンドウの親ウィンドウをリロードする
        window.opener.opener.location.reload();
    });
})();