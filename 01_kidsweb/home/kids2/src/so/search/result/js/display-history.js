// (function () {
//     // 詳細ボタンのイベント
//     $('img.history.button').on('click', function () {
//         var strReceiveCode = $(this).attr('id');
//         var revisionNos = $(this).attr('revisionnos').split(",");
//         for (var i = revisionNos.length - 1; i >= 0; i--) {
//             var row = $('tr[id="' + strReceiveCode + '_' + revisionNos[i] + '"]');
//             var display = row.css('display');
//             if (display == 'none') {
//                 row.css("display", "");
//             } else {
//                 row.css("display", "none");
//             }
//             row.insertAfter($('tr[id="' + strReceiveCode + '"]'));
//         }
//     });
// })();


(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strReceiveCodeAndDetailNo = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var rownum = $(this).attr('rownum');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');
        if ($('tr[id^="' + strReceiveCodeAndDetailNo + '_"]').length) {
            $('tr[id^="' + strReceiveCodeAndDetailNo + '_"]').remove();
        } else {
            var strReceiveCode = strReceiveCodeAndDetailNo.split('_');
            // リクエスト送信
            $.ajax({
                url: '/so/search/result/index2.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strCode': strReceiveCode[0],
                    'lngDetailNo': strReceiveCode[1],
                    'lngRevisionNo': lngRevisionNo,
                    'type': 'so',
                    'rownum': rownum,
                    'displayColumns': displayColumns,
                }
            })
                .done(function (response) {
                    console.log(response);
                    var row = $('tr[id="' + strReceiveCodeAndDetailNo + '"]');
                    row.after(response);

                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/so/detail/index.php';
                        sessionID = 'strSessionID=' + $.cookie('strSessionID');
                        lngReceiveNo = 'lngReceiveNo=' + $(this).attr('id');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lngReceiveNo + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
    });
})();