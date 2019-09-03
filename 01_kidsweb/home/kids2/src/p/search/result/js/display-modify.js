(function () {
    // 確定ボタンのイベント
    $('img.modify.button').on('click', function () {
        url = '/p/modify/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngProductNo = 'lngProductNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + lngProductNo, 'display-detail', 'width=1001, height=649, resizable=yes, scrollbars=yes, menubar=no');
    });
})();