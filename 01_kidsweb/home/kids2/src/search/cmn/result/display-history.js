(function () {
    $('a').on('keydown', function (e) {
        if (e.which == 13) {
            e.stopPropagation();
            $(this).find('img').click();
        }
    });
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var id = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var type = $(this).attr('type');
        var rownum = $(this).attr('rownum');
        if (type == 'sc' || type == 'so' || type == 'pc' || type == 'po' || type == 'purchaseorder') {
            var displayColumns = $('input[name="displayColumns"]').val().split(',');
        }
        var removeFlag = false;
        // if (type == 'inv') {
        //     var strCode = id;
        //     var maxdetailno = $(this).attr('maxdetailno');
        //     var row = $('tr[id="' + id + '"]');
        //     var detailnos = row.attr('detailnos').split(",");
        //     $('tr[id^="' + id + '_"]')
        //         .each(function () {
        //             var isMaxData = false;
        //             for (var i = 0; i < detailnos.length; i++) {
        //                 if ($(this).attr('id') == id || $(this).attr('id') == (id + "_" + lngRevisionNo + "_" + detailnos[i])) {
        //                     isMaxData = true;
        //                 }
        //             }
        //             if (!isMaxData) {
        //                 $(this).remove();
        //                 removeFlag = true;
        //             }
        //         });
        // } else 
        if (type == 'so' || type == 'po' || type == 'pc' || type == 'purchaseorder' || type == 'slip' || type == 'sc' || type == 'inv') {
            if ($('tr[id^="' + id + '_"]').length) {
                $('tr[id^="' + id + '_"]').remove();

                $("#result").trigger("update");
                $(".tablesorter-child").trigger("update");

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
                    // if (type == 'so' || type == 'po' || type == 'purchaseorder') {
                    // }


                    var row = $('tr[id="' + id + '"]');
                    row.after(response);

                    historyTrClickSelectRow();

                    $("#result").trigger("update");
                    $(".tablesorter-child").trigger("update");

                    $(".tablesorter-child thead").css('display', '');
                    $(".tablesorter-child").css('table-layout', '');
                    $("#result").css('table-layout', '');
                    $("#result").css('width', '');
                    $(".tablesorter-child").css('width', '');

                    resetTable();

                    $('a').on('keydown', function (e) {
                        if (e.which == 13) {
                            e.stopPropagation();
                            $(this).find('img').click();
                        }
                    });

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
                        open(url, 'display-detail', 'width=800, height=768, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
        // ヘッダーをクリックする時、明細行を削除する
        $('th').on('click', function () {
            // $('tr.detail').remove();
        });
        if (!removeFlag) {
            $(this).attr('src', '/img/type01/cmn/seg/history_close_off.gif');
        }
        else {
            $(this).attr('src', '/img/type01/cmn/seg/history_open_off.gif');
        }

        window.resetTable = function () {
            var widthArry = [];
            var colnum = $("#result thead tr").eq(0).find("th").length;
            // ヘッダー部の各列の幅を配列に保存する
            for (var i = 1; i <= colnum; i++) {
                var width = $("#result thead tr th:nth-child(" + (i) + ")").eq(0).width();
                widthArry.push(Math.round(width));
            }

            // 明細部の各列の最大幅を取得し配列に保存する
            var childwidthArray = [];
            var tablechildcount = $(".tablesorter-child thead").length;
            console.log(tablechildcount);
            var detailcolcount = $(".tablesorter-child thead").eq(0).find("tr:first th").length;
            for (var i = 0; i < detailcolcount; i++) {
                var width = 0;
                for (var j = 0; j < tablechildcount; j++) {
                    var tmp = Math.round($(".tablesorter-child thead").eq(j).find("tr:first th").eq(i).width());
                    if (tmp > width) {
                        width = tmp;
                    }
                }
                childwidthArray.push(width);
            }

            // 明細部の各列の幅をリセットする
            var width = 0;
            var endindex = 0;
            $(".tablesorter-child thead").eq(0).find("tr:first th").each(function (i, e) {
                var index1 = $(this)[0].cellIndex;
                var index2 = $(".tablesorter-child").parent()[0].cellIndex;
                var index3 = index1 + index2 + 1;
                console.log(index3);
                if (endindex == 0) {
                    endindex = index3;
                }
                var child_width = childwidthArray[index1] + 20;
                var parent_width = widthArry[index3 - 1] + 20;
                if (child_width > widthArry[index3 - 1]) {
                    $("#result thead tr th:nth-child(" + (index3) + ")").eq(0).width(child_width);
                    $(".tablesorter-child tbody tr td:nth-child(" + (index1 + 1) + ")").width(child_width);
                    widthArry[index3 - 1] = child_width;
                } else {
                    $("#result thead tr th:nth-child(" + (index3) + ")").eq(0).width(parent_width);
                    $(".tablesorter-child tbody tr td:nth-child(" + (index1 + 1) + ")").width(parent_width);
                    widthArry[index3 - 1] = parent_width;
                }
            });

            // テーブルの幅をリセットする
            var parent_width = 0;
            for (var i = 0; i < widthArry.length; i++) {
                $("#result thead tr th:nth-child(" + (i + 1) + ")").eq(0).width(widthArry[i]);
                parent_width += widthArry[i];
            }
            $("#result").width(parent_width);

            // 明細部各テーブルのヘッダーを非表示に設定する
            $(".tablesorter-child thead").css('display', 'none');
            // テーブルのレイアウトを固定する
            $(".tablesorter-child").css('table-layout', 'fixed');
            $("#result").css('table-layout', 'fixed');
        }
    });

})();