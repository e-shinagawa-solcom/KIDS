
(function(){
    $('img.delete.button').on('click', function(){
        url = '/mm/delete/confirm/confirm-delete.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version, 'display-delete', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
