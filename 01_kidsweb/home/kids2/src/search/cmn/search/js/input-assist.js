///// FOCUS COLOR /////
var focuscolor = '#c7d0cb';

(function(){
    // FROMのinput要素の値をTOのinputに反映させる
    $('input[name^="From_"]').on({
        'change': function(){
            var toElement = $(this).parent().find('input[name^="To_"]');
            // FROMの値をTOに反映
            toElement.val($(this).val());
            // イベントキック
            toElement.change();
            toElement.blur();
        }
    });
})();
