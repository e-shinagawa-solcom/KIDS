(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/p/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngProductNo = 'lngProductNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngProductNo + '&' + lngRevisionNo, 'display-detail', 'width=1001, height=649, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
