///// FOCUS COLOR /////
var focuscolor = '#c7d0cb';

(function(){
    // FROM��input���Ǥ��ͤ�TO��input��ȿ�Ǥ�����
    $('input[name^="From_"]').on({
        'change': function(){
            var toElement = $(this).parent().find('input[name^="To_"]');
            // FROM���ͤ�TO��ȿ��
            toElement.val($(this).val());
            // ���٥�ȥ��å�
            toElement.change();
            toElement.blur();
        }
    });
})();
