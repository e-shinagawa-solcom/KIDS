
(function(){
    // 閉じた際の処理
    $(window).on('beforeunload', function(){
        $(window.opener.opener.document).find('form').submit();
    });

    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function (){
            window.close();
        }
    });
    // 削除ボタン押下時の挙動
    $('img.delete-action').on({
        'click' : function (){
            url = '/mr/delete/deleteMoldReport.php';
            sessionID = 'strSessionID=' + $.cookie('strSessionID');
            moldReportID = 'MoldReportId=' + $(this).attr('id');
            revision = 'Revision=' + $(this).attr('revision');
            // 削除リクエスト
            window.location.href = url + '?' + sessionID + '&' + moldReportID + '&' + revision;
        }
    });
})();
