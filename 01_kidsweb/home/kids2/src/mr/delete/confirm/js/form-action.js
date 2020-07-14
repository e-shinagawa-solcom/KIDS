
(function(){
    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function (){
            window.close();
        }
    });
    // 閉じるボタン押下時の挙動
    $('img.delete-action').on({
        'click' : function (){
            url = '/mr/delete/deleteMoldReport.php';
            sessionID = 'strSessionID=' + $.cookie('strSessionID');
            moldReportID = 'MoldReportId=' + $(this).attr('id');
            revision = 'Revision=' + $(this).attr('revision');
            version = 'Version=' + $(this).attr('version');
            sortLit = 'sortList=' + getUrlVars(location)["sortList"];
            // 削除リクエスト
            window.location.href = url + '?' + sessionID + '&' + moldReportID + '&' + revision + '&' + version + '&' + sortLit;
        }
    });
    
    
    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            $(this).find('img').click();
        }
    });
})();
