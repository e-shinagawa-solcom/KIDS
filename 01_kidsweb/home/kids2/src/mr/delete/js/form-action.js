
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
            url = '/mr/delete/deleteMoldReport.php';
            sessionID = 'strSessionID=' + $.cookie('strSessionID');
            moldReportID = 'MoldReportId=' + $(this).attr('id');
            revision = 'Revision=' + $(this).attr('revision');
            // ����ꥯ������
            window.location.href = url + '?' + sessionID + '&' + moldReportID + '&' + revision;
        }
    });
})();
