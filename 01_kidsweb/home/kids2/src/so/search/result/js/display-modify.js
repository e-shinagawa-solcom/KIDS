(function () {
    // 詳細ボタンのイベント
    $('img.modify.button').on('click', function () {
        url = '/so/modify/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo, 'display-detail', 'width=900, height=528, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
