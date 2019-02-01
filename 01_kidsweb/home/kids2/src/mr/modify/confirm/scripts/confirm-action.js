
(function(){
    // 登録ボタン押下時の挙動
    $('img.regist-action').on({
        'click' : function (){
            // クリックイベントを削除
            $(this).off('click');

            var url = "/mr/modify/modifyMoldReport.php";
            var sessionID = "strSessionID=" + $.cookie('strSessionID');
            var resultHash = "resultHash=" + $.cookie('resultHash');

            // 登録処理実行
            window.location.href =
                url + '?' +
                sessionID + '&' +
                resultHash;
        }
    });
    
    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function(){
            parent.$('iframe.regist-confirm').prev().find('.ui-dialog-titlebar-close').click();
        }
    });
})();
