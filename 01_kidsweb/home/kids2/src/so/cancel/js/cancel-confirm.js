(function () {

    // 登録ボタンのイベント
    $('img.cancel').on('click', function () {
        var lngReceiveNo = $(this).attr('id');
        // リクエスト送信
        $.ajax({
            url: '/so/cancel/cancel_finish.php',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngReceiveNo': lngReceiveNo
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