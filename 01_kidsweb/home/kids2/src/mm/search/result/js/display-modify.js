
(function(){
    $('img.modify.button').on('click', function(){
        url = '/mm/modify/displayModify.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version, 'display-detail', 'width=972, height=520,resizable=yes, scrollbars=yes, menubar=no');
    });
})();
