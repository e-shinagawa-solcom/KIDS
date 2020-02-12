
(function(){
    $('img.detail.button').on('click', function(){
        url = '/mr/detail/detailMoldReport.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-detail', 'width=800, height=900, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
