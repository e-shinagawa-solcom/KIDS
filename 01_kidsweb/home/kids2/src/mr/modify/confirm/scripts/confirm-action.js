
(function(){
    // ��Ͽ�ܥ��󲡲����ε�ư
    $('img.regist-action').on({
        'click' : function (){
            // ����å����٥�Ȥ���
            $(this).off('click');

            var url = "/mr/modify/modifyMoldReport.php";
            var sessionID = "strSessionID=" + $.cookie('strSessionID');
            var resultHash = "resultHash=" + $.cookie('resultHash');

            // ��Ͽ�����¹�
            window.location.href =
                url + '?' +
                sessionID + '&' +
                resultHash;
        }
    });
    
    // �Ĥ���ܥ��󲡲����ε�ư
    $('img.close-action').on({
        'click' : function(){
            parent.$('iframe.regist-confirm').prev().find('.ui-dialog-titlebar-close').click();
        }
    });
})();
