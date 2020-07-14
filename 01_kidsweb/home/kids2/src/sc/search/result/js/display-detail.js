(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/sc/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngSalesNo = 'lngSalesNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngSalesNo + '&' + lngRevisionNo + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=728, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
