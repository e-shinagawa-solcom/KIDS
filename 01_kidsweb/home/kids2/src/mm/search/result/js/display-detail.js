
(function(){
    $('img.detail.button').on('click', function(){
        url = '/mm/detail/detailMoldHistory.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version, 'display-detail', 'width=800, height=768, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
