(function () {
    // // 詳細ボタンのイベント
    // $('img.history.button').on('click', function () {
    //     var strProductCode = $(this).attr('id');
    //     var revisionNos = $(this).attr('revisionnos').split(",");
    //     for (var i = revisionNos.length - 1; i >= 0; i--) {
    //         var row = $('tr[id="' + strProductCode + '_' + revisionNos[i] + '"]');
    //         var display = row.css('display');
    //         if (display == 'none') {
    //             row.css("display", "");
    //         } else {
    //             row.css("display", "none");
    //         }
    //         row.insertAfter($('tr[id="' + strProductCode + '"]'));
    //     }
    // });

    // 詳細ボタンのイベント
    $('img.history.button').on('click', function (e) {
        // e.stopPropagation();
        var strProductCode = $(this).attr('id').substr(0, 5);
        var lngrevisionno = $(this).attr('lngrevisionno');
        var strReviseCode = $(this).attr('strrevisecode');

        var rownum = $(this).attr('rownum');
        var displayColumns = $('input[name="displayColumns"]').val().split(',');

        if ($('tr[id^="' + strProductCode + '_' + strReviseCode + '_"]').length) {
            $(this).attr('src', '/img/type01/cmn/seg/p_history_open_off.gif');
            $('tr[id^="' + strProductCode + '_' + strReviseCode + '_"]').remove();
            $("#result").trigger("update");
        } else {

            // リクエスト送信
            $.ajax({
                url: '/p/search/result/index2.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strProductCode': strProductCode,
                    'strReviseCode': strReviseCode,
                    'lngRevisionNo': lngrevisionno,
                    'rownum': rownum,
                    'displayColumns': displayColumns,
                }
            })
                .done(function (response) {
                    console.log(response);
                    var row = $('tr[id="' + strProductCode + '_' + strReviseCode + '"]');
                    row.after(response);

                    historyTrClickSelectRow();
                    
                    $('a').on('keydown', function (e) {
                        e.stopPropagation();
                        if (e.which == 13) {
                            $(this).find('img').click();
                        }
                    });
                    
                    // 詳細ボタンのイベント
                    $('img.detail.button').on('click', function () {
                        url = '/p/detail/index.php';
                        sessionID = 'strSessionID=' + $.cookie('strSessionID');
                        lngProductNo = 'lngProductNo=' + $(this).attr('id').substr(0, 5);
                        lngRevisionNo = 'lngRevisionNo=' + $(this).attr('revisionno');

                        // 別ウィンドウで表示
                        open(url + '?' + sessionID + '&' + lngProductNo + '&' + lngRevisionNo, 'display-detail', 'width=1001, height=649, resizable=yes, scrollbars=yes, menubar=no');
                    });
                })
                .fail(function (response) {
                    console.log(response);
                    alert("fail");
                })
            $(this).attr('src', '/img/type01/cmn/seg/p_history_close_off.gif');

        }
    });
})();