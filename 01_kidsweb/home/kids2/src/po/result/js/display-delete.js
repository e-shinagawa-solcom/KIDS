
(function(){
    $('img.cancel.button').on('click', function(){
        url = '/po/result/index3.php';
        lngorderno = 'lngOrderNo=' + $(this).attr('id');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + '&' + sessionID + '&' + sortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });
})();