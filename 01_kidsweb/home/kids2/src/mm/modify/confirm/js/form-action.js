
(function(){
    // ��Ͽ�ܥ��󲡲����ε�ư
    $('img.regist-action').on({
        'click' : function (){
            // ����å����٥�Ȥ���
            $(this).off('click');

            // ��Ͽ�����¹�
            window.location.href="/mm/modify/modifyMoldHistory.php?" +
                "strSessionID=" + $.cookie('strSessionID') + "&" +
                "resultHash=" +$.cookie('resultHash');
        }
    });

    // �Ĥ���ܥ��󲡲����ε�ư
    $('img.close-action').on({
        'click' : function(){
            parent.$('iframe.modify-confirm').prev().find('.ui-dialog-titlebar-close').click();
        }
    });
})();
