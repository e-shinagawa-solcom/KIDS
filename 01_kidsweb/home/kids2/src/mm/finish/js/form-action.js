
(function(){
    // �Ĥ���ܥ��󲡲����ε�ư
    $('img.close-action').on({
        'click' : function(){
            parent.$('iframe[class*=-confirm]').prev().find('.ui-dialog-titlebar-close').click();
        }
    });
})();
