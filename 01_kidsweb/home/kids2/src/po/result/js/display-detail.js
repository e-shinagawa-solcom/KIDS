
(function(){
    $('img.detail.button').on('click', function(){
        url = '/po/result/index2.php';
        lngorderno = 'lngOrderNo=' + $(this).attr('id');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + '&' + lngrevisionno + '&' + sessionID + '&' + sortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });
})();