
(function(){
    $('img.detail.button').on('click', function(){
        url = '/mr/detail/detailMoldReport.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // �̥�����ɥ���ɽ��
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
