(function () {
    // 修正ボタンのイベント
    $('img.fix.button').on('click', function () {
        url = '/pc/modify/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngStockNo = 'lngStockNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        var w = window.open(url + '?' + sessionID + '&' + lngStockNo + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=768, resizable=yes, scrollbars=yes, menubar=no');
        // w.onunload = function () {
        //     window.opener.location.reload();
        // }
    });
})();