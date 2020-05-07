
$(function () {// フォーム
    var form = $('form');
    // エラーメッセージ(日付)
    var msgDateFormat = "yyyy/mm/dd形式かつ有効な日付を入力してください。";
    // エラーメッセージ(必須項目)
    var msgRequired = "入力必須項目です。";

    // validationキック
    $('.hasDatepicker').on({
        'change': function () {
            $(this).blur();
        }
    })

    // 日付がyyyy/mm/dd形式にマッチしているか,有効な日付か
    $.validator.addMethod(
        "checkDateFormat",
        function (value, params) {
            return validate_checkDateFormat(value, params);
        },
        msgDateFormat
    );

    // 検証設定
    form.validate({
        // -----------------------------------------------
        // エラー表示処理
        // -----------------------------------------------
        errorPlacement: function (error, element) {
            validate_errorPlacement(error, element);
        },
        // -----------------------------------------------
        // 検証OK時の処理
        // -----------------------------------------------
        unhighlight: function (element) {
            // エラーアイコン削除
            validate_unhighlight(element);
        },
        // -----------------------------------------------
        // 検証ルール
        // -----------------------------------------------
        rules: {
            // 納品日
            dtmDeliveryDate: {
                required: true,
                checkDateFormat: true
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // 納品日
            dtmDeliveryDate: {
                required: msgRequired
            },
        }
    });

    $('input[name="dtmDeliveryDate"]').on('change', function () {
        // POST先
        var postTarget = $('input[name="ajaxPostTarget"]').val();
        if ($('form').valid()) {
            //消費税率の選択項目変更
            $.ajax({
                type: 'POST',
                url: postTarget,
                data: {
                    strMode: "change-deliverydate",
                    strSessionID: $('input[name="strSessionID"]').val(),
                    dtmDeliveryDate: $(this).val(),
                },
                async: true,
            }).done(function (data) {
                console.log("done:change-deliverydate");
                console.log(data);
                var data = JSON.parse(data);
                if (data.error) {
                    alert("納品日の税率マスタが見つかりません。");
                }

                //消費税率の選択項目更新
                $('select[name="lngTaxRate"] > option').remove();
                $('select[name="lngTaxRate"]').append(data.strHtml);

                setTaxRate();        
                //金額の更新
                updateAmount();

            }).fail(function (error) {
                console.log("fail:change-deliverydate");
                console.log(error);
            });

            var lngMonetaryUnitCode = $('input[name="lngMonetaryUnitCode"]').val();
            var lngMonetaryRateCode = $('input[name="lngMonetaryRateCode"]').val();
            // 適用レートの取得
            if (lngMonetaryUnitCode != "" && lngMonetaryRateCode != "") {
                // リクエスト送信
                $.ajax({
                    url: '/pc/regist/getMonetaryRate.php',
                    type: 'post',
                    data: {
                        'strSessionID': $.cookie('strSessionID'),
                        'lngMonetaryUnitCode': lngMonetaryUnitCode,
                        'lngMonetaryRateCode': lngMonetaryRateCode,
                        'dtmStockAppDate': $('input[name="dtmDeliveryDate"]').val()
                    }
                })
                    .done(function (response) {
                        console.log(response);
                        var data = JSON.parse(response);
                        $('input[name="curConversionRate"]').val(data.curconversionrate);
                    })
                    .fail(function (response) {
                        alert("fail");
                    })
            }
        }

    });

});