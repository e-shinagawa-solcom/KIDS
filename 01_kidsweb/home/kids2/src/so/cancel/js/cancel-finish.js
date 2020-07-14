(function () {

    // 閉じるボタンのイベント
    $('#close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
        
        //親ウィンドウをリロードする
        openerReload();
    });
    
    // // ウィンドウを閉じる前のイベント
    // $(window).on("beforeunload", function(e) {        
    //     window.opener.location.reload();
    // });

})();