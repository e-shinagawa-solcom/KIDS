(function () {

    // 登録ボタンのイベント
    $('img.regist').on('click', function () {
        alert("test");
        var params = new Array();
        var len = 0;
        $("#table_decide_detail tbody tr").each(function (i, e) {
            params[len] = {
                "strCompanyDisplayCode": $(this).find('td:nth-child(2)').text(),
                "strReceiveCode": $(this).find('td:nth-child(3)').text(),
                "lngReceiveDetailNo": $(this).find('td:nth-child(4)').text(),
                "strCustomerReceiveCode": $(this).find('td:nth-child(5)').text(),
                "strProductCode": $(this).find('td:nth-child(6)').text(),
                "strGoodsCode": $(this).find('td:nth-child(7)').text(),
                "dtmDeliveryDate": $(this).find('td:nth-child(8)').text(),
                "lngSalesClassCode": $(this).find('td:nth-child(9)').text(),
                "curProductPrice": $(this).find('td:nth-child(10)').text(),
                "strProductUnitName": $(this).find('td:nth-child(11)').text(),
                "lngUnitQuantity": $(this).find('td:nth-child(12)').text(),
                "lngProductQuantity": $(this).find('td:nth-child(13)').text(),
                "curSubtotalPrice": $(this).find('td:nth-child(14)').text(),
                "strDetailNote": $(this).find('td:nth-child(15)').text(),
                "lngReceiveNo": $(this).find('td:nth-child(16)').text(),
                "lngRevisionNo": $(this).find('td:nth-child(17)').text(),
                "lngProductUnitCode": $(this).find('td:nth-child(18)').text()
            };
            
            len += 1;
        });

        // リクエスト送信
        $.ajax({
            url: '/so/decide/decide_finish.php',
            type: 'post',
            // dataType: 'json',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'detailData': params
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