(function () {
    // 確定ボタンのイベント
    $('img.decide.button').on('click', function () {
        url = '/so/decide/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
            
        window.open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo, 'display-detail', 'width=1011, height=600, resizable=yes, scrollbars=yes, menubar=no');
    });
})();