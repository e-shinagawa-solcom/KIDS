(function(){
    // 確定取消ボタンのイベント
    $('img.cancel.button').on('click', function(){
        url = '/so/cancel/cancel-confirm.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo + '&' + sortList, 'display-detail', 'width=800, height=670, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
