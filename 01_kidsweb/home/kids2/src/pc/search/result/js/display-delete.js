(function(){
    // 削除ボタンのイベント
    $('img.delete.button').on('click', function(){
        url = '/pc/delete/delete_confirm.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngStockNo = 'lngStockNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

        // 別ウィンドウで表示
        var w = window.open(url + '?' + sessionID + '&' + lngStockNo + '&' + lngRevisionNo, 'display-detail', 'width=800, height=768, resizable=yes, scrollbars=yes, menubar=no');
        // w.onunload = function () {
        //     window.opener.location.reload();
        // }
    });
})();
