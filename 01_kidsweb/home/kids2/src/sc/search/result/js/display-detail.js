(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/sc/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngSalesNo = 'lngSalesNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngSalesNo, 'display-detail', 'width=1001, height=649, resizable=yes, scrollbars=yes, menubar=no');
    });
})();