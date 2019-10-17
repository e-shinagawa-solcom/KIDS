(function () {
    
    // 閉じるボタンのイベント
    $('img.close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウを閉じる
        window.opener.close();
        //親ウィンドウの親ウィンドウをリロードする
        window.opener.opener.location.reload();
    });
})();