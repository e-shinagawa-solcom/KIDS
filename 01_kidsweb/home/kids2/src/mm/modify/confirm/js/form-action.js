
(function(){
    // 登録ボタン押下時の挙動
    $('img.regist-action').on({
        'click' : function (){
            // クリックイベントを削除
            $(this).off('click');

            // 登録処理実行
            window.location.href="/mm/modify/modifyMoldHistory.php?" +
                "strSessionID=" + $.cookie('strSessionID') + "&" +
                "resultHash=" +$.cookie('resultHash');
        }
    });

    // 閉じるボタン押下時の挙動
    $('img.close-action').on({
        'click' : function(){
            parent.$('iframe.modify-confirm').prev().find('.ui-dialog-titlebar-close').click();
        }
    });
})();
