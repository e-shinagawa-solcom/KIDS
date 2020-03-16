
(function(){

    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            console.log($(this).find('img.msw-button'));
            $(this).find('img').click();
        }
    });
        
    $('img.modify.button').on('click', function(){
        url = '/mm/modify/displayModify.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        moldNo = 'MoldNo=' + $(this).attr('id');
        historyNo = 'HistoryNo=' + $(this).attr('historyno');
        version = 'Version=' + $(this).attr('version');

        // 別ウィンドウで表示
        open(url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version, 'display-detail', 'width=972, height=520,resizable=yes, scrollbars=yes, menubar=no');
    });
})();
