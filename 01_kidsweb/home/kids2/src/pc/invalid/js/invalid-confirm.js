(function () {

    // 無効ボタンのイベント
    $('img.invalid').on('click', function () {
        var lngStockNo = $(this).attr('id');
        // リクエスト送信
        $.ajax({
            url: '/pc/invalid/invalid_finish.php',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngStockNo': lngStockNo
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
    $('img.close').on('click', function () {
        //ウィンドウを閉じる
        window.close();
    });
})();