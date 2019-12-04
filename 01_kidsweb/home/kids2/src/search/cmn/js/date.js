(function(){
    // 開始日時フォーカスを失ったときの処理
    $('input[type="text"].is-from-date').on('blur', function () {
        var value = $(this).val();
        if (/^[0-9]{8}$/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            var d = str.substr(6, 2);
            $(this).val(y + "/" + m + "/" + d);
        } else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/.test(value)) {
            $(this).val(value.trim());
        }  else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(5, 2);
            var d = '01';
            $(this).val(y + "/" + m + "/" + d);
        } else if (/(19[0-9]{2}|2[0-9]{3})(0[1-9]|1[0-2])/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            var d = '01';
            $(this).val(y + "/" + m + "/" + d);
        }

        var obj = $(this).attr('alt');
        if ($('input[name="To_' + obj + '"]').val().length == 0) {
            $('input[name="To_' + obj + '"]').val($(this).val());
        }
    });

    // 開始日時フォーカスを取ったときの処理
    $('input[type="text"].is-from-date').on('focus', function () {
        var chgVal = $(this).val().replace(/\//g, "");
        $(this).val(chgVal);
        $(this).select();
    });

    // 開始日時フォーカスを失ったときの処理
    $('input[type="text"].is-date').on('blur', function () {
        var value = $(this).val();
        if (/^[0-9]{8}$/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            var d = str.substr(6, 2);
            $(this).val(y + "/" + m + "/" + d);
        } else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/.test(value)) {
            $(this).val(value.trim());
        }  else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(5, 2);
            var d = '01';
            $(this).val(y + "/" + m + "/" + d);
        } else if (/(19[0-9]{2}|2[0-9]{3})(0[1-9]|1[0-2])/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            var d = '01';
            $(this).val(y + "/" + m + "/" + d);
        }
    });

    // 開始日時フォーカスを取ったときの処理
    $('input[type="text"].is-date').on('focus', function () {
        var chgVal = $(this).val().replace(/\//g, "");
        $(this).val(chgVal);
        $(this).select();
    });
})();
