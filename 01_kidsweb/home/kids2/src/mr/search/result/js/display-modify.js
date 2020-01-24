
(function(){
    $('img.modify.button').on('click', function(){
        url = '/mr/modify/display-modify.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-detail', 'width=1000, height=570,resizable=yes, scrollbars=yes, menubar=no');
    });
})();
