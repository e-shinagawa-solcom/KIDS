(function () {

    // 削除ボタンのイベント
    $('img.delete').on('click', function () {
        var lngStockNo = $(this).attr('id');
        // リクエスト送信
        $.ajax({
            url: '/pc/delete/delete_finish.php',
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