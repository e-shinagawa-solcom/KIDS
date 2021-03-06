
(function () {
    // フォーム
    var form = $('form[name="Invoice"]');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';

    // エラーメッセージ(必須項目)
    var msgRequired = "入力必須項目です。";
    // エラーメッセージ(必須項目)
    var msgEmpty = "が未入力です。";
    // エラーメッセージ(選択してください)
    var msgTaxEmpty = "消費税区分が未入力です";

    // エラーメッセージ(日付)
    var msgDateFormat = "yyyy/mm/dd形式かつ有効な日付を入力してください。";
    var msgGreaterThanToday = "現在より先の日付しか入力できません。";
    // エラーメッセージ(移動先が保管元と同一工場)
    var msgSameFactory = "移動先工場に保管元工場と同じ工場を指定することはできません。";
    // yyyy/mm/dd フォーマット
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;
    // 日付フォーマット yyyy/mm(m)/dd(d)形式
    var regDate2 = /([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})/;
    // validationキック
    $('.hasDatepicker').on({
        'change': function () {
            $(this).blur();
        }
    })

    // 課税区分
    $.validator.addMethod(
        "checkTax",
        function (value, element, params) {
            return value != 0;
        },
        msgTaxEmpty
    );

    // 日付がyyyy/mm/dd形式にマッチしているか,有効な日付か
    $.validator.addMethod(
        "checkDateFormat",
        function (value, params) {
            if (!value) { return true; }
            if (params) {
                if (value.length == 8) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
                // yyyy/mm(m)/dd(d)形式か
                if (!(regDate2.test(value))) {
                    return false;
                }
                var regResult = regDate2.exec(value);
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

    // 日付が過去でないか ActionDate
    $.validator.addMethod(
        "equalsOrGreaterThanToday",
        function (value, element, params) {
            if (params) {
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // 現在の日時と比較
                var nowDi = new Date();
                // 入力した年が現在より小さければエラー
                if (nowDi.getFullYear() > di.getFullYear()) {
                    return false;
                    // 入力した年が現在より大きければ正
                } else if (nowDi.getFullYear() < di.getFullYear()) {
                    return true;
                    // 入力した年が現在と同じ場合
                } else if (nowDi.getFullYear() == di.getFullYear()) {
                    // 入力した月が現在より小さければエラー
                    if (nowDi.getMonth() > di.getMonth()) {
                        return false;
                        // 入力した月が現在より大きければ正
                    } else if (nowDi.getMonth() < di.getMonth()) {
                        return true;
                    } else if (nowDi.getMonth() == di.getMonth()) {
                        // 入力した日が現在と同じかそれより小さければエラー
                        if (nowDi.getDate() > di.getDate()) {
                            return false;
                        }
                    }
                    return true;
                }
            } return true;
        },
        msgGreaterThanToday
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
                    position: 'absolute',
                    top: $(element).position().top,
                    left: $(element).position().left - 20,
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
            // 顧客コード
            lngCustomerCode: {
                required: true
            },
            // 課税区分
            lngTaxClassCode: {
                //                required: true,
                checkTax: true
            },
            // 納品日From
            From_dtmDeliveryDate: {
                checkDateFormat: true
            },
            // 納品日To
            To_dtmDeliveryDate: {
                checkDateFormat: true
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // 顧客コード
            lngCustomerCode: {
                required: '顧客コード' + msgEmpty
            },
            // 課税区分
            lngTaxClassCode: {
                required: '課税区分' + msgEmpty
            },
            // 納品日From
            From_dtmDeliveryDate: {
                required: + msgDateFormat
            },
            // 納品日To
            To_dtmDeliveryDate: {
                required: + msgDateFormat
            }
        }
    });
})();
