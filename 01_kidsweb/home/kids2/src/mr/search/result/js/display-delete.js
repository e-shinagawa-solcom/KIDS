
(function(){
    $('img.delete.button').on('click', function(){
        url = '/mr/delete/confirm/confirm-delete.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
