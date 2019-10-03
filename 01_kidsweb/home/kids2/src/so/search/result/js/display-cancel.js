(function(){
    // 確定取消ボタンのイベント
    $('img.cancel.button').on('click', function(){
        url = '/so/cancel/cancel-confirm.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        var w = window.open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
        w.onunload = function () {
            window.opener.location.reload();
        }
    });
})();
