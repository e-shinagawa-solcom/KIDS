(function () {
    // 確定ボタンのイベント
    $('img.decide.button').on('click', function () {
        url = '/so/decide/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        var w = window.open(url + '?' + sessionID + '&' + lngReceiveNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
        w.onunload = function () {
            window.opener.location.reload();
        }
    });
})();