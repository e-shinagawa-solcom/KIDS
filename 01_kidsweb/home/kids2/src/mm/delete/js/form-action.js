
(function(){
    // �Ĥ����ݤν���
    $(window).on('beforeunload', function(){
        $(window.opener.opener.document).find('form').submit();
    });

    // �Ĥ���ܥ��󲡲����ε�ư
    $('img.close-action').on({
        'click' : function (){
            window.close();
        }
    });
    // ����ܥ��󲡲����ε�ư
    $('img.delete-action').on({
        'click' : function (){
            url = '/mm/delete/deleteMoldHistory.php';
            sessionID = 'strSessionID=' + $.cookie('strSessionID');
            moldNo = 'MoldNo=' + $(this).attr('MoldNo');
            historyNo = 'HistoryNo=' + $(this).attr('HistoryNo');
            version = 'Version=' + $(this).attr('Version');

            // ����ꥯ������
            window.location.href = url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version;
        }
    });
})();
