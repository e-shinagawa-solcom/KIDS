// (function () {
//     // 詳細ボタンのイベント
//     $('img.history.button').on('click', function () {
//         var id = $(this).attr('id');
//         var revisionNos = $(this).attr('revisionnos').split(",");
//         var maxdetailno = $(this).attr('maxdetailno');
//         var revisionno = $(this).attr('revisionno');

//         for (var i = revisionNos.length - 1; i >= 0; i--) {
//             var row = $('tr[id="' + id + '_' + revisionNos[i] + '"]');
//             var detailnos = row.attr('detailnos').split(",");
//             for (var j = 0; j < detailnos.length; j++) {
//                 var subrow = $('tr[id="' + id + '_' + revisionNos[i] + '_' + detailnos[j] + '"]');
//                 var display = subrow.css('display');
//                 if (display == 'none') {
//                     subrow.css("display", "");
//                 } else {
//                     subrow.css("display", "none");
//                 }
//                 subrow.insertAfter($('tr[id="' + id + "_" + revisionno + "_" + maxdetailno + '" ]'));
//             }

//             var display = row.css('display');
//                 if (display == 'none') {
//                     row.css("display", "");
//                 } else {
//                     row.css("display", "none");
//                 }
//             row.insertAfter($('tr[id="' + id + "_" + revisionno + "_" + maxdetailno + '" ]'));

//         }
//     });
// })();

(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strStockCode = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var maxdetailno = $(this).attr('maxdetailno');
        var rownum = $(this).attr('rownum');
        var type = $(this).attr('type');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');
        var removeFlag = false;
        var row = $('tr[id="' + strStockCode + '"]');
        var detailnos = row.attr('detailnos').split(",");
        $('tr[id^="' + strStockCode + '_"]')
            .each(function () {
                var isMaxData = false;
                var id = $(this).attr('id');
                for (var i=0; i < detailnos.length; i++) {
                    if (id == strStockCode || id == (strStockCode + "_" + lngRevisionNo + "_" + detailnos[i])) {
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
            // $.ajax({
            //     url: '/pc/search/result/index2.php',
            //     type: 'post',
            //     data: {
            //         'strSessionID': $.cookie('strSessionID'),
            //         'strStockCode': strStockCode,
            //         'lngRevisionNo': lngRevisionNo,
            //         'rownum': rownum,
            //         'displayColumns': displayColumns,
            //     }

                $.ajax({
                    url: '/search/result/history/index.php',
                    type: 'post',
                    data: {
                        'strSessionID': $.cookie('strSessionID'),
                        'strCode': strStockCode,
                        'lngRevisionNo': lngRevisionNo,
                        'rownum': rownum,
                        'type': type,
                        'displayColumns': displayColumns,
                    },
                    async: true,
            })
                .done(function (response) {
                    console.log(response);
                    if ($('tr[id="' + strStockCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').length) {
                        $('tr[id="' + strStockCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').after(response);
                    } else {
                        $('tr[id="' + strStockCode + '"]').after(response);
                    }

                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/pc/detail/index.php';
                        sessionID = 'strSessionID=' + $.cookie('strSessionID');
                        lngStockNo = 'lngStockNo=' + $(this).attr('id');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lngStockNo + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
                .always(function (response) {
                    console.log(response);
                    alert("test1");
                })
        }
    });
})();