(function () {

    // 無効ボタンのイベント
    $('#invalid').on('click', function () {
        var lngStockNo = $(this).attr('lngStockNo');
        var lngRevisionNo = $(this).attr('lngRevisionNo');
        // リクエスト送信
        $.ajax({
            url: '/pc/invalid/invalid_finish.php',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngStockNo': lngStockNo,
                'lngRevisionNo': lngRevisionNo
            }
        })
            .done(function (response) {
                $('body').html(response);
                $('body').attr('class', 'finish-background');
            })
            .fail(function (response) {
                alert("fail");
                // Ajaxリクエストが失敗
            });
    });

    
    // 閉じるボタンのイベント
    $('#close').on('click', function () {
        window.opener.location.reload();
        //ウィンドウを閉じる
        window.close();
    });
    

    $(window).on("beforeunload", function(e) {
        window.opener.location.reload();
    });
})();