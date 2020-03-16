(function () {

    // 登録ボタンのイベント
    $('#regist').on('click', function () {
        var params = new Array();
        var len = 0;
        $("#table_decide_detail tbody tr").each(function (i, e) {
            params[len] = {
                "strCompanyDisplayCode": $(this).find('td:nth-child(2)').text(),
                "strReceiveCode": $(this).find('td:nth-child(3)').text(),
                "strCustomerReceiveCode": $(this).find('td:nth-child(4)').text(),
                "strProductCode": $(this).find('td:nth-child(5)').text(),
                "strGoodsCode": $(this).find('td:nth-child(6)').text(),
                "dtmDeliveryDate": $(this).find('td:nth-child(7)').text(),
                "lngSalesClassCode": $(this).find('td:nth-child(8)').text(),
                "curProductPrice": $(this).find('td:nth-child(9)').text(),
                "strProductUnitName": $(this).find('td:nth-child(10)').text(),
                "lngUnitQuantity": $(this).find('td:nth-child(11)').text(),
                "lngProductQuantity": $(this).find('td:nth-child(12)').text(),
                "curSubtotalPrice": $(this).find('td:nth-child(13)').text(),
                "strDetailNote": $(this).find('td:nth-child(14)').text(),
                "lngReceiveNo": $(this).find('td:nth-child(15)').text(),
                "lngReceiveDetailNo": $(this).find('td:nth-child(16)').text(),
                "lngRevisionNo": $(this).find('td:nth-child(17)').text(),
                "lngProductUnitCode": $(this).find('td:nth-child(18)').text(),
                "strReviseCode": $(this).find('td:nth-child(19)').text(),
                "strProductCode_product": $(this).find('td:nth-child(20)').text()
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
                'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
                'detailData': params,
                'lngProductNo': $('input[name="lngProductNo"]').val(),
                'lngProductRevisionNo': $('input[name="lngProductRevisionNo"]').val(),
                'strReviseCode': $('input[name="strReviseCode"]').val(),
                'strGoodsCode': $('input[name="strGoodsCode"]').val()
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
    $('#return').on('click', function () {
                
        // 親ウィンドウのロックを解除する
        if (window.opener.$('#lockId').length) {
            window.opener.$('#lockId').remove();
        }
        //ウィンドウを閉じる
        window.close();
    });

    // ウィンドウを閉じる前のイベント
    $(window).on("beforeunload", function(e) {
        // 親ウィンドウのロックを解除する
        if (window.opener.$('#lockId').length) {
            window.opener.$('#lockId').remove();
        }
    });

})();