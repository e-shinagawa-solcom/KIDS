(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strOrderCode = $(this).attr('id');
        var lngrevisionno = $(this).attr('lngrevisionno');
        var rownum = $(this).attr('rownum');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');
        if ($('tr[id^="' + strOrderCode + '_"]').length) {
            $('tr[id^="' + strOrderCode + '_"]').remove();
        } else {

            // リクエスト送信
            $.ajax({
                url: '/po/result2/index2.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strOrderCode': strOrderCode,
                    'lngRevisionNo': lngrevisionno,
                    'rownum': rownum,
                    'displayColumns': displayColumns,
                }
            })
                .done(function (response) {
                    console.log(response);
                    var row = $('tr[id="' + strOrderCode + '"]');
                    row.after(response);
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
    });
})();