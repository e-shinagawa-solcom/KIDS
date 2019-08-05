(function(){
    // 確定ボタンのイベント
    $('img.resale.button').on('click', function(){
        url = '/so/resale/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngReceiveNo, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
