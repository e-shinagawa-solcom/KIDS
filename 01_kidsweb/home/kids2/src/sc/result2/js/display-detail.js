
(function(){
    $('img.detail.button').on('click', function(){
        url = '/sc/result2/index2.php';
        lngslipno = 'lngSlipNo=' + $(this).attr('id');
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars(window.location)["strSessionID"];
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));
        console.log(sortList);
        console.log(childSortList);

        // 別ウィンドウで表示
        open(url + '?' + lngslipno + '&' + lngrevisionno + '&' + sessionID + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728,resizable=yes, scrollbars=yes, menubar=no');
    });
})();