(function () {

    // 登録ボタンのイベント
    $('img.cancel').on('click', function () {
        var lngReceiveNo = $(this).attr('id');
        var lngRevisionNo = $(this).attr('revisionno');
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
    $('img.close').on('click', function () {

        window.opener.location.reload();
        //ウィンドウを閉じる
        window.close();
    });

    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function(e) {  
        alert("test");      
        window.opener.location.reload();
    });
})();