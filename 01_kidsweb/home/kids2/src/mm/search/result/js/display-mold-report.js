
(function(){
    // プレビュー表示
    $('img.preview.button').on('click', function(){
        url = '/mold/list/displayMoldReport.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-report', 'resizable=yes, scrollbars=yes, menubar=no');
    });
    // COPYプレビュー表示
    $('img.copy-preview.button').on('click', function(){
        url = '/mold/list/displayMoldReport.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');
        copy= 'isCopy';

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version + '&' + copy, 'display-report', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
