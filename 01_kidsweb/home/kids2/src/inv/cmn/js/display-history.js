(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strInvoiceCode = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var maxdetailno = $(this).attr('maxdetailno');
        var rownum = $(this).attr('rownum');
        var removeFlag = false;
        var row = $('tr[id="' + strInvoiceCode + '"]');
        var detailnos = row.attr('detailnos').split(",");
        $('tr[id^="' + strInvoiceCode + '_"]')
            .each(function () {
                var isMaxData = false;
                var id = $(this).attr('id');
                for (var i=0; i < detailnos.length; i++) {
                    if (id == strInvoiceCode || id == (strInvoiceCode + "_" + lngRevisionNo + "_" + detailnos[i])) {
                        isMaxData = true;
                    }
                }
                if (!isMaxData) {
                    $(this).remove();
                    removeFlag = true;
                }
            });
        if (!removeFlag) {
            // リクエスト送信
            $.ajax({
                url: '/inv/result/index4.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strInvoiceCode': strInvoiceCode,
                    'lngRevisionNo': lngRevisionNo,
                    'rownum': rownum,
                }
            })
                .done(function (response) {
                    console.log(response);
                    if ($('tr[id="' + strInvoiceCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').length) {
                        $('tr[id="' + strInvoiceCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').after(response);
                    } else {
                        $('tr[id="' + strInvoiceCode + '"]').after(response);
                    }

                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/inv/result/index2.php';
                        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];
                        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('lnginvoiceno');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lnginvoiceno + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
    });
})();