(function () {
    // Mボタン押下処理
    $('img.decide.button').on({
        'click': function () {
            var lngestimateno = $(this).attr('lngestimateno');
            var lngestimaterevisionno = $(this).attr('revisionno');
            var lngorderno = $(this).attr('id');
            var strsessionid = $('input[name="strSessionID"]').val();
            var parentObj = $(this).parents('a');
            showIFrame(lngestimateno, lngestimaterevisionno, lngorderno, strsessionid, parentObj);
        }
    });
})();


var choice = function (lngestimateno, lngestimaterevisionno, lngorderno, strsessionid, docMsw) {
    var decidetype = docMsw.find('input[name="decidetype"]:checked').val();
    var returnurl = "";
    if (decidetype == 1) {
        var option = docMsw.find('select.select-result').find('option:selected');
        if (option.length == 0) {
            alert("発注書No候補リストから発注書Noを選択してください。");
            return;
        }
        var lngpurchaseorderno = option.attr('lngpurchaseorderno');
        var lngrevisionno = option.attr('lngrevisionno');
        returnurl = "/po/regist/modify.php?lngPurchaseOrderNo=" + lngpurchaseorderno + "&lngOrderNo=" + lngorderno
            + "&lngRevisionNo=" + lngrevisionno + "&strSessionID=" + strsessionid;
    } else if (decidetype == 2) {
        returnurl = "/po/regist/index.php?lngOrderNo=" + lngorderno + "&estimateNo=" + lngestimateno
            + "&revisionNo=" + lngestimaterevisionno + "&strSessionID=" + strsessionid;
    }

    open(returnurl, 'display-regist', 'width=996, height=689, resizable=yes, scrollbars=yes, menubar=no');

    // mswの非表示
    invokeMswClose(docMsw);
};
// 閉じるボタン処理の呼び出し
var invokeMswClose = function (msw) {
    msw.find('.msw-box__header__close-btn').trigger('click');
};

var showIFrame = function (lngestimateno, lngestimaterevisionno, lngorderno, strsessionid, parentObj) {
    var selectQuery = {
        url: '/po/cmn/getPoList.php?strSessionID=' + $('input[name="strSessionID"]').val(),
        type: 'post',
        dataType: 'json'
    };
    // 更新条件
    var condition = {
        data: JSON.stringify({
            lngestimateno: lngestimateno,
            lngestimaterevisionno: lngestimaterevisionno,
            lngorderno: lngorderno
        })
    };

    // リクエスト送信
    $.ajax($.extend({}, selectQuery, condition))
        .done(function (response) {
            console.log(response.length);
            if (response.length == 0) {
                returnurl = "/po/regist/index.php?lngOrderNo=" + lngorderno + "&estimateNo=" + lngestimateno
                    + "&revisionNo=" + lngestimaterevisionno + "&strSessionID=" + strsessionid;
                open(returnurl, 'display-regist', 'width=996, height=689, resizable=yes, scrollbars=yes, menubar=no');
                return;
            } else {
                var ifmMsw = $('iframe');
                var docMsw = $(ifmMsw.get(0).contentWindow.document);
                docMsw.find('.select-result option').remove();
                for (var i = 0; i < response.length; i++) {
                    console.log(response[i]);
                    docMsw.find('.select-result').append(
                        $('<option>')
                            .attr({
                                lngpurchaseorderno: response[i].lngpurchaseorderno,
                                lngrevisionno: response[i].lngrevisionno
                            })
                            .html(response[i].strpocode + '（明細行数' + response[i].detailnum + '）&nbsp;&nbsp;&nbsp;' + response[i].strcompanydisplayname)
                    );

                }

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
                    'height': height-position_h,
                    'width': $('body').outerWidth(true),
                    'z-index': '9999',
                });


                docMsw.off('click', '#choice');
                docMsw.off('keydown', '#choice');
                docMsw.on('click', '#choice', function () {
                    choice(lngestimateno, lngestimaterevisionno, lngorderno, strsessionid, docMsw);
                });
                docMsw.on('keydown', '#choice', function (e) {
                    if (e.which == 13) {
                        choice(lngestimateno, lngestimaterevisionno, lngorderno, strsessionid, docMsw);
                    }
                });
                docMsw.on('change', 'input[name="decidetype"]:radio', function () {
                    var decidetype = docMsw.find('input[name="decidetype"]:checked').val();
                    if (decidetype == 1) {
                        docMsw.find('select.select-result').prop('disabled', false);
                    } else if (decidetype == 2) {
                        docMsw.find('select.select-result').prop('disabled', true);
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
        })
        .fail(function (response) {
            console.log(response);
        });
}