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
// mswのposition設定
var setPosition = function (parentObj, docMsw) {
    // ボタンの親のライン
    var line = parentObj;
    console.log(line);
    var lineOffset = line.offset();
    console.log(lineOffset);

    console.log(line.outerHeight(true));
    var mswBox = docMsw.find('.msw-box');
    var mswBoxHeight = mswBox.outerHeight(true);
    var mswBoxWidth = mswBox.outerWidth(true);
    // msw初期位置
    var position = { top: lineOffset.top - mswBoxHeight, left: lineOffset.left };
    if ((lineOffset.top - mswBoxHeight) < 0) {
        position.top = 0;
    }
//     if ((lineOffset.left - mswBoxWidth) < 0) {
//         position.left = 0;
//     }
// console.log(position);
    // //mswの表示が画面に収まらない場合
    // if (lineOffset.top + mswBoxHeight > $(document).height() && $(document).height() > mswBoxHeight) {
    //     // 画面の高さに収まらない高さ分を引く
    //     position.top -= position.top + mswBoxHeight - $(document).height();
    // }

    // // msw横幅が画面に収まらない場合
    // position.left -= Math.min(position.left, (position.left + mswBoxWidth > $(document).width() && $(document).width() > mswBoxWidth) ?
    //     Math.abs(position.left + mswBoxWidth - $(document).width()) : 0);

    return position;
}
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
                var mswBox = docMsw.find('.msw-box');
                // var ifmHeight = mswBox.offset().top + mswBox.outerHeight(true);
                // var ifmWidth = mswBox.offset().top + mswBox.outerWidth(true);
                // console.log($('body').outerHeight(true));
                // console.log($('body').offset.height);
                // console.log($('body').offset.width);
                console.log($('body').outerHeight(true));
                var pos = setPosition(parentObj, docMsw);
                console.log($(window).height());

                console.log(pos);
                var height = $('body').outerHeight(true);
                if (height < $(window).height()) {
                    height = $(window).height();
                }
                ifmMsw.css({
                    'position': 'absolute',
                    'top': pos.top,
                    'left': pos.left,
                    'height': height,
                    'width': $('body').outerWidth(true),
                    'z-index': '9999'
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