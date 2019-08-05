(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/so/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngReceiveNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
