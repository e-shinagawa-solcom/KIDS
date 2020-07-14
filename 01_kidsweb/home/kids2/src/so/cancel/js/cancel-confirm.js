(function () {

    // 登録ボタンのイベント
    $('#cancel').on('click', function () {
        var lngReceiveNo = $(this).attr('lngReceiveNo');
        var lngRevisionNo = $(this).attr('lngRevisionNo');
        // リクエスト送信
        $.ajax({
            url: '/so/cancel/cancel_finish.php',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngReceiveNo': lngReceiveNo,
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
        //親ウィンドウをリロードする
        openerReload();
        //ウィンドウを閉じる
        window.close();
    });

    // // ウィンドウを閉じる前のイベント
    // $(window).on("beforeunload", function(e) {    
    //     // window.opener.location.reload();
    //     //親ウィンドウをリロードする
    //     openerReload();
    // });
})();