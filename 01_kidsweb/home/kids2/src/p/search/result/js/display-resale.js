(function(){
    // 確定ボタンのイベント
    $('img.resale.button').on('click', function(){
        url = '/p/resale/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngProductNo = 'lngProductNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngProductNo, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
