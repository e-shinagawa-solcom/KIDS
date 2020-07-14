(function () {
    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function (e) {        
        // 親ウィンドウのロックを解除する
        if (window.opener.$('#lockId').length) {
            window.opener.$('#lockId').remove();
        }
        //ウィンドウを閉じる
        window.close();
        //親ウィンドウをリロードする
        openerReload();
    });
})();

function openerReload() {
    //親ウィンドウをリロードする
    if (window.opener != null) {
        if (window.opener.location.href.indexOf('result') > -1) {
            var hashParam = "";
            var urlVars = getUrlVars(location);
            if (urlVars["sortList"] != undefined && urlVars["sortList"] != "") {
                hashParam = hashParam + '&sortList=' + urlVars["sortList"];
            }
            if (urlVars["childSortList"] != undefined && urlVars["childSortList"] != "") {
                hashParam = hashParam + '&childSortList=' + urlVars["childSortList"];
            }
            window.opener.location.hash = hashParam;
        }
        window.opener.location.reload();
    }
}
