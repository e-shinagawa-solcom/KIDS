(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/pc/detail/index.php';
        sessionID = 'strSessionID=' + getUrlVars(location)["strSessionID"];
        lngStockNo = 'lngStockNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngStockNo + '&' + lngRevisionNo + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
