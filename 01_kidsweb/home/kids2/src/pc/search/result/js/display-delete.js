(function(){
    // 削除ボタンのイベント
    $('img.delete.button').on('click', function(){
        url = '/pc/delete/delete_confirm.php';
        sessionID = 'strSessionID=' + getUrlVars(location)["strSessionID"];
        lngStockNo = 'lngStockNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead').eq(0).find("tr:first th"));
        childSortList = 'childSortList=' + setSortList($(".tablesorter-child thead").eq(0).find("tr:first th"));

        // 別ウィンドウで表示
        var w = window.open(url + '?' + sessionID + '&' + lngStockNo + '&' + lngRevisionNo + '&' + sortList + '&' + childSortList, 'display-detail', 'width=800, height=670, resizable=yes, scrollbars=yes, menubar=no');
        // w.onunload = function () {
        //     window.opener.location.reload();
        // }
    });
})();
