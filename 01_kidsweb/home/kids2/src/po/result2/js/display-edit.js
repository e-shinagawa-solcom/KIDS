
(function(){
    $('img.fix.button').on('click', function(){
//        url = '/po/regist/renew.php';
        url = '/po/regist/modify.php';
        lngorderno = 'lngPurchaseOrderNo=' + $(this).attr('id');
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + '&' + lngrevisionno + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=996, height=689,resizable=yes, scrollbars=yes, menubar=no');
    });
})();

