(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/pc/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngStockNo = 'lngStockNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngStockNo + '&' + lngRevisionNo, 'display-detail', 'width=800, height=728, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
