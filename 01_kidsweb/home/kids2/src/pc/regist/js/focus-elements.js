// �Ƽ�ܥ����ե���������ǽ�ˤ���
$('img.msw-button, img.list-add, img.list-del').attr({
    tabindex: 0
});

$('.form-box__contents').on(
    'keydown', 'img.msw-button, img.list-add, img.list-del', function(e){
        // 13:Enter����, 32:Space����
        if(e.which == 13 || e.which == 32){
            $(this).click();
            return false;
        }
    }
);
