
(function(){
    url = '/mold/list/displayMoldReport.php';
    sessionID = 'strSessionID=' + $.cookie('strSessionID');
    moldReportID = 'MoldReportId=' + $('#preview').attr('moldreportid');
    revision = 'Revision=' + $('#preview').attr('revision');
    version = 'Version=' + $('#preview').attr('version');
    isRegist = 'isRegist';

    $('.report-box__wrap-box-button #preview').on('click', function(){
        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID  + '&' + revision  + '&' +  version + '&' + isRegist, 'display-report', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
