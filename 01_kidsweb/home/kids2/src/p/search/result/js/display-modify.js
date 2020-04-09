(function () {
    // 確定ボタンのイベント
    $('img.modify.button').on('click', function () {
        url = '/p/modify/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngProductNo = 'lngProductNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + lngProductNo + '&' + lngRevisionNo, 'display-detail', 'width=1001, height=670, resizable=yes, scrollbars=yes, menubar=no');
    });
})();