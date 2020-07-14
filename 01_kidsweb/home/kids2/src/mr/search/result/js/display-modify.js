
(function(){
    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            console.log($(this).find('img.msw-button'));
            $(this).find('img').click();
        }
    });
    $('img.modify.button').on('click', function(){
        url = '/mr/modify/display-modify.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldReportID = 'MoldReportId=' + $(this).attr('id');
        revision = 'Revision=' + $(this).attr('revision');
        version = 'Version=' + $(this).attr('version');
        sortList = 'sortList=' + setSortList($('#result thead tr th'));

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version + '&' + sortList, 'display-detail', 'width=1000, height=570,resizable=yes, scrollbars=yes, menubar=no');
    });
})();
