
(function () {
    // フォーム
    var form = $('form');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // エラーメッセージ(必須項目)
    var msgRequired = "入力必須項目です。";
    // エラーメッセージ(日付)
    var msgDateFormat = "yyyy/mm/dd形式かつ有効な日付を入力してください。";
    // 日付フォーマット yyyy/mm/dd形式
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;
    // エラーメッセージ（書式誤り）
    var msgSpecialFormat = "書式に誤りがあります。"

    // validationキック
    $('.hasDatepicker').on({
        'change': function () {
            $(this).blur();
        }
    });

    // 発注NO.に半角英数字以外の文字が入力できない
    $.validator.addMethod(
        "checkStrOrderCode",
        function (value, element, params) {
            if (params) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            } 
            return true;
        },
        msgSpecialFormat
    );

    // 日付がyyyy/mm/dd形式にマッチしているか,有効な日付か
    $.validator.addMethod(
        "checkDateFormat",
        function (value, element, params) {
            if (params) {
                // yyyy/mm/dd形式か
                if (!(regDate.test(value))) {
                    return false;
                }
                // 日付文字列の字句分解
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // 日付の有効性チェック
                if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
                    return true;
                } else {
                    return false;
                }
            } return true;
        },
        msgDateFormat
    );


    // 検証設定
    form.validate({
        // -----------------------------------------------
        // エラー表示処理
        // -----------------------------------------------
        errorPlacement: function (error, element) {
            invalidImg = $('<img>')
                .attr('class', classNameErrorIcon)
                .attr('src', urlErrorIcon)
                // CSS設定(表示位置)
                .css({
                    position: 'relative',
                    top: -1,
                    left: -2,
                    opacity: 'inherit'
                })
                // ツールチップ表示
                .tooltipster({
                    trigger: 'hover',
                    onlyone: false,
                    position: 'top',
                    content: error.text()
                });

            // エラーアイコンが存在しない場合
            if ($(element).prev('img.' + classNameErrorIcon).length <= 0) {
                // エラーアイコンを表示
                $(element).before(invalidImg);
            }
            // エラーアイコンが存在する場合
            else {
                // 既存のエラーアイコンのツールチップテキストを更新
                $(element).prev('img.' + classNameErrorIcon)
                    .tooltipster('content', error.text());
            }
        },
        // -----------------------------------------------
        // 検証OK時の処理
        // -----------------------------------------------
        unhighlight: function (element) {
            // エラーアイコン削除
            $(element).prev('img.' + classNameErrorIcon).remove();
        },
        // -----------------------------------------------
        // 検証ルール
        // -----------------------------------------------
        rules: {
            // 仕入日
            dtmStockAppDate: {
                required: true,
                checkDateFormat: true
            },
            // 発注NO.
            strOrderCode: {
                required: true,
                checkStrOrderCode: true              
            },
            // 納品書NO.            
            strSlipCode: {
                required: true
            },
            // 製品到着日
            dtmExpirationDate: {
                checkDateFormat: function () {
                    return $('input[name="dtmExpirationDate"]').val() != "";
                }
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // 仕入日
            dtmStockAppDate: {
                required: msgRequired
            },
            // 発注NO.
            strOrderCode: {
                required: msgRequired
            },
            // 納品書NO.           
            strSlipCode: {
                required: msgRequired
            }
        }
    });
})();
