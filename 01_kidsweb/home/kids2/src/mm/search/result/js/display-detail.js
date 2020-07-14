
(function(){
    $('img.detail.button').on('click', function(){
        url = '/mm/detail/detailMoldHistory.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version + '&' + sortList, 'display-detail', 'width=800, height=728, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
