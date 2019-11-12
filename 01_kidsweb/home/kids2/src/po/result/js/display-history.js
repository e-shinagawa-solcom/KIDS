(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strOrderCodeAndDetailNo = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var rownum = $(this).attr('rownum');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');
        if ($('tr[id^="' + strOrderCodeAndDetailNo + '_"]').length) {
            $('tr[id^="' + strOrderCodeAndDetailNo + '_"]').remove();
        } else {
            var strOrderCode = strOrderCodeAndDetailNo.split('_');
            // リクエスト送信
            $.ajax({
                url: '/po/result/index4.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strOrderCode': strOrderCode[0],
                    'lngOrderDetailNo': strOrderCode[1],
                    'lngRevisionNo': lngRevisionNo,
                    'rownum': rownum,
                    'displayColumns': displayColumns,
                }
            })
                .done(function (response) {
                    console.log(response);
                    var row = $('tr[id="' + strOrderCodeAndDetailNo + '"]');
                    row.after(response);

                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/po/result/index2.php';
                        sessionID = 'strSessionID=' + $.cookie('strSessionID');
                        lngOrderNo = 'lngOrderNo=' + $(this).attr('id');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lngOrderNo + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
    });
})();