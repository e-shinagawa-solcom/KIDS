
(function(){
    // �ץ�ӥ塼ɽ��
    $('img.preview.button').on('click', function(){
        url = '/mold/list/displayMoldReport.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-report', 'resizable=yes, scrollbars=yes, menubar=no');
    });
    // COPY�ץ�ӥ塼ɽ��
    $('img.copy-preview.button').on('click', function(){
        url = '/mold/list/displayMoldReport.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');
        copy= 'isCopy';

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version + '&' + copy, 'display-report', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
