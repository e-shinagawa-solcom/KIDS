(function () {
    $('#cancel').on({
        'click': function () {
            $('.msw-box__header__close-btn').trigger('click');
        }
    });
    // 開始日時フォーカスを失ったときの処理
    $('input[name="deliveryYm"]').on('blur', function () {
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
    $('input[name="deliveryYm"]').on('focus', function () {
        console.log($(this).val());
        var chgVal = $(this).val().replace(/\//g, "");
        $(this).val(chgVal);
        $(this).select();
    });

    // 開始日時フォーカスを失ったときの処理
    $('input[name="rate"]').on('blur', function () {
        var val = $(this).val();
        console.log(val);
        val = Number(val).toLocaleString(undefined, {
            minimumFractionDigits: 4,
            maximumFractionDigits: 4
        });
        $(this).val(val);
    });

    $('input[name="rate"]').on('focus', function () {
        var val = $(this).val();
        console.log(val);
        $(this).val(val.replace(/,/g, ''));
    });

})();
