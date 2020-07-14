
(function(){
    // プレビュー表示
    $('img.preview.button').on('click', function(){
        url = '/mold/list/frameset.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version + '&' + sortList, 'display-report', 'width=1011, height=670,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no');
    });
    // COPYプレビュー表示
    $('img.copy-preview.button').on('click', function(){
        url = '/mold/list/frameset.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));
        copy= 'isCopy';

        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version + '&' + copy + '&' + sortList, 'display-report', 'width=1011, height=670,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no');
    });
})();
