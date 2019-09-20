(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/pc/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngStockNo = 'lngStockNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngStockNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
    });
})();