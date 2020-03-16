(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/sc/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngSalesNo = 'lngSalesNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngSalesNo + '&' + lngRevisionNo, 'display-detail', 'width=800, height=768, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
