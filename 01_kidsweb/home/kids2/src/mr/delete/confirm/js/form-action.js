
(function(){
    // �Ĥ���ܥ��󲡲����ε�ư
    $('img.close-action').on({
        'click' : function (){
            window.close();
        }
    });
    // �Ĥ���ܥ��󲡲����ε�ư
    $('img.delete-action').on({
        'click' : function (){
            url = '/mr/delete/deleteMoldReport.php';
            sessionID = 'strSessionID=' + $.cookie('strSessionID');
            moldReportID = 'MoldReportId=' + $(this).attr('id');
            revision = 'Revision=' + $(this).attr('revision');
            version = 'Version=' + $(this).attr('version');
            // ����ꥯ������
            window.location.href = url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version;
        }
    });
})();
