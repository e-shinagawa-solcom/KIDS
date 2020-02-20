$(document).ready(function () {


    // 開始日時フォーカスを失ったときの処理
    $("input[name='objectYm'], input[name='openYm'], input[name='shipYm']").on('blur', function () {
        console.log($(this).val());
        var value = $(this).val();
        if (/^[0-9]{6}$/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            $(this).val(y + "/" + m);
        }
        if (/^[0-9]{5}$/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 1);
            $(this).val(y + "/0" + m);
        }
    });

    // 開始日時フォーカスを取ったときの処理
    $('input[name="objectYm"], input[name="openYm"], input[name="shipYm"]').on('focus', function () {
        var chgVal = $(this).val().replace(/\//g, "");
        $(this).val(chgVal);
        $(this).select();
    });

});