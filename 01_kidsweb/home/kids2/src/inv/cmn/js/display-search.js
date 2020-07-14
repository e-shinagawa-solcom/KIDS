
(function(){
    $('img.detail.button').on('click', function(){
        url = '/inv/result/index2.php';
        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + lnginvoiceno + '&' + lngRevisionNo + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });

    $('img.fix.button').on('click', function(){
        url = '/inv/regist/renew.php';
        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + lnginvoiceno + '&' + lngRevisionNo + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=1000, height=550,resizable=yes, scrollbars=yes, menubar=no');
    });

    $('img.delete.button').on('click', function(){
        url = '/inv/result/index3.php';
        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('lnginvoiceno');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + lnginvoiceno + '&' + lngRevisionNo + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });
})();