
(function(){
    $('img.delete.button').on('click', function(){
        url = '/po/result2/index3.php';
        lngorderno = 'lngPurchaseOrderNo=' + $(this).attr('lngpurchaseorderno');
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + '&' + lngrevisionno + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });
})();