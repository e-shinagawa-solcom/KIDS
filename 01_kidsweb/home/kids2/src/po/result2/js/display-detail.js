
(function(){
    $('img.detail.button').on('click', function(){
        url = '/po/result2/index2.php';
        lngPurchaseOrderNo = 'lngPurchaseOrderNo=' + $(this).attr('id');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + lngPurchaseOrderNo + '&' + lngrevisionno + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });
})();