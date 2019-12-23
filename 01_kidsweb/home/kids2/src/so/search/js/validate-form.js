
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
    var msgLessThanToday = "未来の日付が指定されました。";
    var msgLessThantToDate = "FROMにTOより未来の日付が指定されました。";

    // validationキック
    $('.hasDatepicker').on({
        'change': function () {
            $(this).blur();
        }
    });

    // 区分の必須チェック
    $.validator.addMethod(
        "checkSelect",
        function (value, element, params) {
            return value != "0";
        },
        msgRequired
    );

    // 顧客受注番号に半角英数字、[-],[,],[ ]以外の文字が入力できない
    $.validator.addMethod(
        "checkStrCustomerReceiveCode",
        function (value, element, params) {
            if (params && value != "") {
                return this.optional(element) || /^[a-zA-Z0-9-, ]+$/.test(value);
            }
            return true;
        },
        msgSpecialFormat
    );

    // 受注コードの書式チェック
    $.validator.addMethod(
        "checkStrReceiveCode",
        function (value, element, params) {
            if (params && value != "") {
                return this.optional(element) || /^d\d{8}(_\d{2})?$/.test(value);
            }
            return true;
        },
        msgSpecialFormat
    );
    　　// 製品コードの書式チェック
    $.validator.addMethod(
        "checkStrProductCode",
        function (value, element, params) {
            if (params && value != '') {
                var codeList = value.split(",");                
                var result = true;
                $.each(codeList, function (ind, val) {
                    if (val.indexOf('-') !== -1) {
                        var val1 = val.split("-")[0];
                        var val2 = val.split("-")[1];
                        if (!val1.match(/^\d{5}(_\d{2})?$/) || !val2.match(/^\d{5}(_\d{2})?$/)) {
                            result = false;
                            return false;
                        }
                    } else if (val.length) {
                        if (!val.match(/^\d{5}(_\d{2})?$/)) {
                            result = false;
                            return false;
                        }
                    }
                });
                if (!result) {
                    return false;
                }
            }
            return true;
        },
        msgSpecialFormat
    );

    // 日付がyyyy/mm/dd形式にマッチしているか,有効な日付か
    $.validator.addMethod(
        "checkDateFormat",
        function (value, element, params) {
            if (params && value != "") {
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
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
                }
            } return true;
        },
        msgDateFormat
    );

    // 日付が未来日でないか ActionDate
    $.validator.addMethod(
        "isLessThanToday",
        function (value, element, params) {
            if (params && value != "") {                
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // 現在の日時と比較
                var nowDi = new Date();
                // 入力した年が現在より小さければ正
                if (nowDi.getFullYear() > di.getFullYear()) {
                    return true;
                    // 入力した年が現在より大きければエラー
                } else if (nowDi.getFullYear() < di.getFullYear()) {
                    return false;
                    // 入力した年が現在と同じ場合
                } else if (nowDi.getFullYear() == di.getFullYear()) {
                    // 入力した月が現在より小さければ正
                    if (nowDi.getMonth() > di.getMonth()) {
                        return true;
                        // 入力した月が現在より大きければエラー
                    } else if (nowDi.getMonth() < di.getMonth()) {
                        return false;
                    } else if (nowDi.getMonth() == di.getMonth()) {
                        // 入力した日が現在と同じかそれより小さければ正
                        if (nowDi.getDate() >= di.getDate()) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } return true;
        },
        msgLessThanToday
    );

    // FROM_XXXXがTO_XXXXより小さいか(同日不可)
    $.validator.addMethod(
        "isGreaterThanFromDate",
        function (value, element, params) {
            if (params[0] && value != "") {                
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
                var params1 = $(params[1]).val();
                // FROM_XXXXが入力された場合、                
                if (params1 != "") {                    
                    if (/^[0-9]{8}$/.test(params1)) {
                        var str = params1.trim();
                        var y = str.substr(0, 4);
                        var m = str.substr(4, 2);
                        var d = str.substr(6, 2);
                        params1 = y + "/" + m + "/" + d;
                    }
                    var regResult = regDate.exec(params1);
                    var yyyy = regResult[1];
                    var mm = regResult[2];
                    var dd = regResult[3];
                    var fromDate = new Date(yyyy, mm, dd);
                    regResult = regDate.exec(value);
                    yyyy = regResult[1];
                    mm = regResult[2];
                    dd = regResult[3];
                    var di = new Date(yyyy, mm, dd);
                    // 入力した年がFROM_XXXXより小さければエラー
                    if (fromDate.getFullYear() > di.getFullYear()) {
                        return false;
                        // 入力した年がFROM_XXXXより大きければ正
                    } else if (fromDate.getFullYear() < di.getFullYear()) {
                        return true;
                        // 入力した年がFROM_XXXXと同じ場合
                    } else if (fromDate.getFullYear() == di.getFullYear()) {
                        // 入力した月がFROM_XXXXより小さければエラー
                        if (fromDate.getMonth() > di.getMonth()) {
                            return false;
                            // 入力した月がFROM_XXXXより大きければ正
                        } else if (fromDate.getMonth() < di.getMonth()) {
                            return true;
                            // 入力した月がFROM_XXXXと同じ場合
                        } else if (fromDate.getMonth() == di.getMonth()) {
                            // 入力した日がFROM_XXXXより小さければエラー
                            if (fromDate.getDate() > di.getDate()) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
            return true;
        },
        msgLessThantToDate
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
            // 登録日
            From_dtmInsertDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked && $('input[name="To_dtmInsertDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                },
                isLessThanToday: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                }
            },
            To_dtmInsertDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked && $('input[name="From_dtmInsertDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmInsertDate"]').get(0).checked, 'input[name="From_dtmInsertDate"]'];
                }

            },
            // 入力者            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
                }
            },
            // 顧客受注番号                        
            From_strCustomerReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked && $('input[name="To_strCustomerReceiveCode"]').val() == "";
                },
                checkStrCustomerReceiveCode: function () {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked;
                }
            },
            To_strCustomerReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked && $('input[name="From_strCustomerReceiveCode"]').val() == "";
                },
                checkStrCustomerReceiveCode: function () {
                    return $('input[name="IsSearch_strCustomerReceiveCode"]').get(0).checked;
                }
            },
            // 受注No.            
            From_strReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked && $('input[name="To_strReceiveCode"]').val() == "";
                },
                checkStrReceiveCode: function () {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked;
                }
            },
            To_strReceiveCode: {
                required: function () {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked && $('input[name="From_strReceiveCode"]').val() == "";
                },
                checkStrReceiveCode: function () {
                    return $('input[name="IsSearch_strReceiveCode"]').get(0).checked;
                }
            },
            // 製品コード            
            strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                },
                checkStrProductCode: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                }
            },
            // 製品名            
            strProductName: {
                required: function () {
                    return $('input[name="IsSearch_strProductName"]').get(0).checked;
                }
            },
            // 製品名（英語）            
            strProductEnglishName: {
                required: function () {
                    return $('input[name="IsSearch_strProductEnglishName"]').get(0).checked;
                }
            },
            // 営業部署            
            lngInChargeGroupCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeGroupCode"]').get(0).checked;
                }
            },
            // 開発担当者            
            lngInChargeUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeUserCode"]').get(0).checked;
                }
            },
            // 売上区分            
            lngSalesClassCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngSalesClassCode"]').get(0).checked;
                }
            },
            // 顧客品番            
            strGoodsCode: {
                required: function () {
                    return $('input[name="IsSearch_strGoodsCode"]').get(0).checked;
                }
            },
            // 顧客            
            lngCustomerCompanyCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCompanyCode"]').get(0).checked;
                }
            },
            // 納期            
            From_dtmDeliveryDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked && $('input[name="To_dtmDeliveryDate"]').val() == "";
                }
            },
            To_dtmDeliveryDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked && $('input[name="From_dtmDeliveryDate"]').val() == "";
                },
                isGreaterThanFromDate: function () {
                    return [$('input[name="IsSearch_dtmDeliveryDate"]').get(0).checked, 'input[name="From_dtmDeliveryDate"]'];
                }
            },
            // 状態            
            'lngReceiveStatusCode[]': {
                required: function () {
                    return $('input[name="IsSearch_lngReceiveStatusCode"]').get(0).checked;
                }
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // 登録日
            From_dtmInsertDate: {
                required: msgRequired
            },
            To_dtmInsertDate: {
                required: msgRequired
            },
            // 入力者            
            lngInputUserCode: {
                required: msgRequired
            },
            // 顧客受注番号                        
            From_strCustomerReceiveCode: {
                required: msgRequired
            },
            To_strCustomerReceiveCode: {
                required: msgRequired
            },
            // 受注No.            
            From_strReceiveCode: {
                required: msgRequired
            },
            To_strReceiveCode: {
                required: msgRequired
            },
            // 製品コード            
            From_strProductCode: {
                required: msgRequired
            },
            To_strProductCode: {
                required: msgRequired
            },
            // 製品名            
            strProductName: {
                required: msgRequired
            },
            // 製品名（英語）            
            strProductEnglishName: {
                required: msgRequired
            },
            // 営業部署            
            lngInChargeGroupCode: {
                required: msgRequired
            },
            // 開発担当者            
            lngInChargeUserCode: {
                required: msgRequired
            },
            // 売上区分            
            lngSalesClassCode: {
                required: msgRequired
            },
            // 顧客品番            
            strGoodsCode: {
                required: msgRequired
            },
            // 顧客            
            lngCustomerCompanyCode: {
                required: msgRequired
            },
            // 納期            
            From_dtmDeliveryDate: {
                required: msgRequired
            },
            To_dtmDeliveryDate: {
                required: msgRequired
            },
            // 状態            
            'lngReceiveStatusCode[]': {
                required: msgRequired
            }
        }
    });
})();
