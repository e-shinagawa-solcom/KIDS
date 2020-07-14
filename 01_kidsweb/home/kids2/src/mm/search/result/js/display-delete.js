
(function(){
    $('img.delete.button').on('click', function(){
        url = '/mm/delete/confirm/confirm-delete.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version + '&' + sortList, 'display-delete', 'width=800, height=728, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
