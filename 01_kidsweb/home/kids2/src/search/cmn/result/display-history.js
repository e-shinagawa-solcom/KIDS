(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var id = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var type = $(this).attr('type');
        var rownum = $(this).attr('rownum');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');
        var removeFlag = false;
        if (type == 'sc' || type == 'slip' || type == 'pc' || type == 'inv') {
            var strCode = id;
            var maxdetailno = $(this).attr('maxdetailno');
            var row = $('tr[id="' + id + '"]');
            var detailnos = row.attr('detailnos').split(",");
            $('tr[id^="' + id + '_"]')
                .each(function () {
                    var isMaxData = false;
                    for (var i = 0; i < detailnos.length; i++) {
                        if ($(this).attr('id') == id || $(this).attr('id') == (id + "_" + lngRevisionNo + "_" + detailnos[i])) {
                            isMaxData = true;
                        }
                    }
                    if (!isMaxData) {
                        $(this).remove();
                        removeFlag = true;
                    }
                });
        } else if (type == 'so' || type == 'po' || type == 'purchaseorder') {
            if ($('tr[id^="' + id + '_"]').length) {
                $('tr[id^="' + id + '_"]').remove();
                removeFlag = true;
            } else {
                var strReceiveCode = id.split('_');
                var strCode = strReceiveCode[0];
                var lngDetailNo = strReceiveCode[1];
            }
        }
        if (!removeFlag) {
            // リクエスト送信
            $.ajax({
                url: '/search/result/history/index.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strCode': strCode,
                    'lngDetailNo': lngDetailNo,
                    'lngRevisionNo': lngRevisionNo,
                    'rownum': rownum,
                    'type': type,
                    'displayColumns': displayColumns,
                },
                // async: true,s
            })
                .done(function (response) {
                    console.log(response);
                    if (type == 'so' || type == 'po' || type == 'purchaseorder') {
                        var row = $('tr[id="' + id + '"]');
                        row.after(response);
                    } else if (type == 'sc' || type == 'slip' || type == 'pc' || type == 'inv') {
                        if ($('tr[id="' + id + "_" + lngRevisionNo + "_" + maxdetailno + '"]').length) {
                            $('tr[id="' + id + "_" + lngRevisionNo + "_" + maxdetailno + '"]').after(response);
                        } else {
                            $('tr[id="' + id + '"]').after(response);
                        }
                    }

                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        if (type == 'purchaseorder') { // 発注書
                            url = '/po/result2/index2.php';
                            lngPkNo = 'lngPurchaseOrderNo=' + $(this).attr('id');
                        } else if (type == 'po') { // 発注
                            url = '/po/result/index2.php';
                            lngPkNo = 'lngOrderNo=' + $(this).attr('id');
                        } else if (type == 'so') { // 受注
                            url = '/so/detail/index.php';
                            lngPkNo = 'lngReceiveNo=' + $(this).attr('id');
                        } else if (type == 'sc') { // 売上
                            url = '/sc/detail/index.php';
                            lngPkNo = 'lngSalesNo=' + $(this).attr('id');
                        } else if (type == 'slip') { //納品書                            
                            url = '/sc/result2/index2.php';
                            lngPkNo = 'lngSlipNo=' + $(this).attr('id');
                        } else if (type == 'pc') { // 仕入                            
                            url = '/pc/detail/index.php';
                            lngPkNo = 'lngStockNo=' + $(this).attr('id');
                        } else if (type == 'inv') {           
                            url = '/inv/result/index2.php';
                            lngPkNo = 'lngInvoiceNo=' + $(this).attr('id');
                        } else if (type == 'estimate') {
                        }
                        sessionID = 'strSessionID=' + $.cookie('strSessionID');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');
                        url = url + '?' + sessionID + '&' + lngPkNo + '&' + lngRevisionNo;
                        // 別ウィンドウで表示
                        open(url, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
        // ヘッダーをクリックする時、明細行を削除する
        $('th').on('click', function () {
            $('tr.detail').remove();
        });
    });
})();