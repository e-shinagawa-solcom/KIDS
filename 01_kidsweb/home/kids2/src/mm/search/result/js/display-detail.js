
(function(){
    $('img.detail.button').on('click', function(){
        url = '/mm/detail/detailMoldHistory.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
