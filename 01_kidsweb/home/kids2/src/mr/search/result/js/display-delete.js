
(function(){
    $('img.delete.button').on('click', function(){
        url = '/mr/delete/confirm/confirm-delete.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
