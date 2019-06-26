
//修正画面表示

(function(){
    $('img.renew.button').on('click', function(){
        url = '/pc/regist/renew.php';
        lngstockno = 'lngStockNo=' + $(this).attr('lngstockno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngstockno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
