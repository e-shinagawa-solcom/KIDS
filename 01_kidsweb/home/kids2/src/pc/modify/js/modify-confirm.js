(function () {

    // 登録ボタンのイベント
    $('img.regist').on('click', function () {
        var formData = new Array();
        formData.push({ name: "strSessionID", value: $.cookie('strSessionID') });
        $("#tbl_stock_info tbody tr").each(function (i, e) {
            formData.push({ name: $(this).find('td:nth-child(1)').text(), value: $(this).find('td:nth-child(2)').text() });
        });

        // リクエスト送信
        $.ajax({
            url: '/pc/modify/modify_finish.php',
            type: 'POST',
            data: formData
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