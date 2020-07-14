
(function(){
    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function (){
            window.close();
        }
    });
    // 削除ボタン押下時の挙動
    $('img.delete-action').on({
        'click' : function (){
            url = '/mm/delete/deleteMoldHistory.php';
            sessionID = 'strSessionID=' + $.cookie('strSessionID');
            moldNo = 'MoldNo=' + $(this).attr('MoldNo');
            historyNo = 'HistoryNo=' + $(this).attr('HistoryNo');
            version = 'Version=' + $(this).attr('Version');
            sortLit = 'sortList=' + getUrlVars(location)["sortList"];

            // 削除リクエスト
            window.location.href = url + '?' + sessionID + '&' + moldNo + '&' + historyNo + '&' + version + '&' + sortLit;
        }
    });
    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            $(this).find('img').click();
        }
    });
})();
