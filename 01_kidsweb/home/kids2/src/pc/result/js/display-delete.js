
// 削除画面表示


(function(){
    $('img.delete.button').on('click', function(){
        // url = '/mm/delete/confirm/confirm-delete.php';
        url = '/pc/result/index3.php';
        lngstockno = 'lngStockNo=' + $(this).attr('lngstockno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngstockno + '&' + sessionID, 'display-delete', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
