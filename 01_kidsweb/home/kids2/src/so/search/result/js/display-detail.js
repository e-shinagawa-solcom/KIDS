(function () {
    // 詳細ボタンのイベント
    $('img.detail.button').on('click', function () {
        url = '/so/detail/index.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo + '&' + sortList, 'display-detail', 'width=800, height=728, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
