(function () {
    // 履歴ボタンのイベント
    $('img.history.button').on('click', function () {
        var strSalesCode = $(this).attr('id');
        var lngRevisionNo = $(this).attr('lngrevisionno');
        var maxdetailno = $(this).attr('maxdetailno');
        var rownum = $(this).attr('rownum');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');
        var removeFlag = false;
        
        var row = $('tr[id="' + strSalesCode + '"]');
        var detailnos = row.attr('detailnos').split(",");
        $('tr[id^="' + strSalesCode + '_"]')
            .each(function () {
                var isMaxData = false;
                var id = $(this).attr('id');
                for (var i=0; i < detailnos.length; i++) {
                    if (id == strSalesCode || id == (strSalesCode + "_" + lngRevisionNo + "_" + detailnos[i])) {
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
                url: '/sc/search/result/index2.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strSalesCode': strSalesCode,
                    'lngRevisionNo': lngRevisionNo,
                    'rownum': rownum,
                    'displayColumns': displayColumns,
                }
            })
                .done(function (response) {
                    console.log(response);
                    if ($('tr[id="' + strSalesCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').length) {
                        $('tr[id="' + strSalesCode + "_" + lngRevisionNo + "_" + maxdetailno + '"]').after(response);
                    } else {
                        $('tr[id="' + strSalesCode + '"]').after(response);
                    }
                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/sc/detail/index.php';
                        sessionID = 'strSessionID=' + $.cookie('strSessionID');
                        lngSalesNo = 'lngSalesNo=' + $(this).attr('id');
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lngSalesNo + '&' + lngRevisionNo, 'display-detail', 'width=1001, height=649, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
        }
    });
})();