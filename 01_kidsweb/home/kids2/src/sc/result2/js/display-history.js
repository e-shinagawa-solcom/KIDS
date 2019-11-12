(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strSlipCode = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var maxdetailno = $(this).attr('maxdetailno');
        var rownum = $(this).attr('rownum');
        var removeFlag = false;
        var row = $('tr[id="' + strSlipCode + '"]');
        var detailnos = row.attr('detailnos').split(",");
        $('tr[id^="' + strSlipCode + '_"]')
            .each(function () {
                var isMaxData = false;
                var id = $(this).attr('id');
                for (var i=0; i < detailnos.length; i++) {
                    if (id == strSlipCode || id == (strSlipCode + "_" + lngRevisionNo + "_" + detailnos[i])) {
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
                url: '/sc/result2/index4.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strSlipCode': strSlipCode,
                    'lngRevisionNo': lngRevisionNo,
                    'rownum': rownum,
                }
            })
                .done(function (response) {
                    console.log(response);
                    if ($('tr[id="' + strSlipCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').length) {
                        $('tr[id="' + strSlipCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').after(response);
                    } else {
                        $('tr[id="' + strSlipCode + '"]').after(response);
                    }

                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/sc/result2/index2.php';
                        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];
                        lngslipno = 'lngSlipNo=' + $(this).attr('lngslipNo');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lngslipno + '&' + lngRevisionNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
    });
})();