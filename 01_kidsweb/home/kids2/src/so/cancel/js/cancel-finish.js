(function () {

    // 閉じるボタンのイベント
    $('#close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
        
        //親ウィンドウをリロードする
        window.opener.location.reload();
    });
    
    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function(e) {        
        window.opener.location.reload();
    });

})();