(function () {
    
    // 閉じるボタンのイベント
    $('#close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウを閉じる
        if (window.opener != null) {
            window.opener.close();
        }
        //親ウィンドウの親ウィンドウをリロードする
        if (window.opener.opener != null) {
            if (window.opener.opener.location.href.indexOf('result') > -1) {
                // window.opener.opener.location.href = window.opener.opener.location.href + '&sortList=' + getUrlVars(window.opener.location)["sortList"];
                window.opener.opener.location.hash = '&sortList=' + getUrlVars(window.opener.location)["sortList"];
            }
            window.opener.opener.location.reload();
        }
    });
    

    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function(e) {
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウを閉じる
        if (window.opener != null) {
            window.opener.close();
        }
        //親ウィンドウの親ウィンドウをリロードする
        if (window.opener.opener != null) {
            window.opener.opener.location.reload();
        }
    });
})();