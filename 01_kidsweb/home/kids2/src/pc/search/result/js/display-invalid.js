(function(){
    // 無効ボタンのイベント
    $('img.invalid.button').on('click', function(){
        url = '/pc/invalid/invalid_confirm.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngStockNo = 'lngStockNo=' + $(this).attr('id');

        // 別ウィンドウで表示
        var w = window.open(url + '?' + sessionID + '&' + lngStockNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
        // w.onunload = function () {
        //     window.opener.location.reload();
        // }
    });
})();
