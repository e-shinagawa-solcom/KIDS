
(function(){
    url = '/mold/list/frameset.php';
    sessionID = 'strSessionID=' + $.cookie('strSessionID');
    moldReportID = 'MoldReportId=' + $('#preview').attr('moldreportid');
    revision = 'Revision=' + $('#preview').attr('revision');
    version = 'Version=' + $('#preview').attr('version');
    isRegist = 'isRegist';

    $('.report-box__wrap-box-button #preview').on('click', function(){
        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + moldReportID  + '&' + revision  + '&' +  version + '&' + isRegist, 'display-report', 'width=1011, height=670,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no');
    });
})();
