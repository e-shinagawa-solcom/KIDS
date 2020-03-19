jQuery(document).ready(function () {
    setTextInputMode();
});


function setTextInputMode() {
    $("input[type='text']").focus(function () {
        // ime-mode:disabledを設定された場合
        var textStyle = $(this).attr('style');
        console.log(textStyle);
        if (textStyle != undefined && textStyle.length > 0) {
            textStyle = textStyle.replace(/:/g, '').replace(' ', '');
            if (textStyle.indexOf('ime-modedisabled') >= 0) {
                $(this).prop('type', 'tel');
                return;
            }
        }

        // コードテキストの場合
        var textName = $(this).attr('name');
        console.log(textName);
        if (textName != undefined && textName.length > 0) {
            if (textName.indexOf('code') >= 0 || textName.indexOf('Code') >= 0) {
                $(this).prop('type', 'tel');
                return;
            }

            if (textName.indexOf('date') >= 0 || textName.indexOf('Date') >= 0) {
                $(this).prop('type', 'tel');
                return;
            }
        }
    }).blur(function () {
        $(this).prop('type', 'text');
    })
}