
var rateEditInfoArry = [];
(function () {
    // Mボタン押下処理
    $('#rate_edit').on({
        'click': function () {
            showIFrame();
        }
    });
})();


var apply = function (docMsw) {
    var rate = docMsw.find('input[name="rate"]').val();
    var monetaryUnit = docMsw.find('select[name="monetaryUnit"] option:selected').text();
    var deliveryYm = docMsw.find('input[name="deliveryYm"]').val();
    console.log(deliveryYm);

    if (deliveryYm == "") {
        alert("納期年月を設定してください。");
        return false;
    }
    if (deliveryYm != "" && !isDate(deliveryYm + "/01")) {
        alert("納期年月の形式が不正です。例：2019/01\r\n");
        return false;
    }

    if (rate == "") {
        alert("適用レートを設定してください。");
        return false;
    }
    if (rate != "" && !$.isNumeric(rate)) {
        alert("円価換算額は半角数字で入力してください。");
        return false;
    }

    var rateEditInfo = {'rate': rate, 'monetaryUnit': monetaryUnit, 'deliveryYm':deliveryYm};
    console.log(rateEditInfo);
    // var rateEditInfoArry_pre = $('input[name="rateEditInfoArry"]').val();
    var rateEditInfoArry_pre = rateEditInfoArry;
    var isExist = false;
    var removeIndex = 0;
    console.log(rateEditInfoArry_pre);
    if (rateEditInfoArry_pre.length == 0) {
        rateEditInfoArry = [];
        rateEditInfoArry.push(rateEditInfo);
    } else {
        var filtered = $.grep(rateEditInfoArry_pre, function(elem, index) {
            if (monetaryUnit == elem.monetaryUnit && deliveryYm == elem.deliveryYm)
            {
                isExist = true;
                removeIndex = index;
            }
        });
        if (isExist) {            
            rateEditInfoArry.splice(removeIndex, 1); 
        }
        rateEditInfoArry.push(rateEditInfo);
    }
    console.log(rateEditInfoArry);
    $('input[name="deliveryYm"]').val(deliveryYm);
    
    $('input[name="rate"]').val(rate);
    
    $('input[name="monetaryUnit"]').val(monetaryUnit);

    $('input[name="rate"]').trigger('change');
    // mswの非表示
    invokeMswClose(docMsw);
};
// 閉じるボタン処理の呼び出し
var invokeMswClose = function (msw) {
    msw.find('.msw-box__header__close-btn').trigger('click');
};

var showIFrame = function () {
    var ifmMsw = $('iframe');
    var docMsw = $(ifmMsw.get(0).contentWindow.document);

    var height = $('body').outerHeight(true);
    if (height < $(window).height()) {
        height = $(window).height();
    }

    // 画面の縦横
    var w_height = $(window).height();
    var w_width = $(window).width();

    // 要素の縦横
    var mswBox = docMsw.find('.msw-box');
    var el_height = mswBox.offset().top + mswBox.outerHeight(true);
    var el_width = mswBox.offset().top + mswBox.outerWidth(true);

    // 最上部からの距離
    var scroll_height = $(window).scrollTop();

    // 高さは画面の上部（最上部）からの距離なのでスクロールの距離を加算
    var position_h = scroll_height + (w_height - el_height) / 2;

    // 横は画面左からの距離なので、そのまま画面の横幅と要素の横幅を減算して半分の距離
    var position_w = (w_width - el_width) / 2;
    console.log(w_width);
    console.log($(window).height());
    console.log(el_height);
    console.log(position_h);
    console.log(position_w);
    ifmMsw.css({
        'position': 'absolute',
        'top': position_h + 'px',
        'left': position_w + 'px',
        'height': height - position_h,
        'width': $('body').outerWidth(true),
        'z-index': '9999',
    });


    docMsw.off('click', '#apply');
    docMsw.off('keydown', '#apply');
    docMsw.on('click', '#apply', function () {
        apply(docMsw);
    });
    docMsw.on('keydown', '#apply', function (e) {
        if (e.which == 13) {
            apply(docMsw);
        }
    });
    console.log(docMsw);

    // mswの表示
    invokeMswClose(docMsw);

    // ヘッダーの設定
    var headerWidth = docMsw.find('.msw-box__header').width();
    var btnCloseWidth = docMsw.find('.msw-box__header__close-btn').width();
    var btnCloseHeight = docMsw.find('.msw-box__header__close-btn').height();
    var headerbar = docMsw.find('.msw-box__header__bar');
    headerbar.css({
        'height': btnCloseHeight,
        'width': headerWidth - btnCloseWidth,
        'background-color': '#5495c8',
        'line-height': btnCloseHeight + 'px',
        'color': 'white',
        'font-size': '12px',
        'font-weight': 'bold',
        'text-indent': '1em'
    });
}



function isDate(d) {
    if (d == "") { return false; }
    if (!isDateFormat(d)) { return false; }
    if (!isValidDate(d)) { return false; }
    return true;
}
function isDateFormat(d) {
    return d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/);
}
function isValidDate(d) {
    var date = new Date(d);
    if (date.getFullYear() != d.split("/")[0]
        || date.getMonth() != d.split("/")[1] - 1
        || date.getDate() != d.split("/")[2]
    ) {
        return false;
    }
    return true;
}