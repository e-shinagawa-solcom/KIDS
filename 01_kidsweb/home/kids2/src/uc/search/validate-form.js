
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
            if (params) {
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
            if (params) {
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
            if (params) {
                return this.optional(element) || /\d{5}(_\d{2})?$/.test(value);
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
                } else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)) {
                    if (value.length == 7) {
                        var str = value.trim();
                        var y = str.substr(0, 4);
                        var m = str.substr(5, 2);
                        var d = '01';
                        value = y + "/" + m + "/" + d;
                    }
                } else if (/(19[0-9]{2}|2[0-9]{3})(0[1-9]|1[0-2])/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = '01';
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
                } else {
                    return false;
                }
            } return true;
        },
        msgDateFormat
    );

    // 日付が未来日でないか ActionDate
    $.validator.addMethod(
        "isLessThanToday",
        function (value, element, params) {
            if (params) {
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
            if (params[0]) {
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
            // ログイン許可
            bytInvalidFlag: {
                required: function () {
                    return $('input[name="IsSearch_bytInvalidFlagConditions"]').get(0).checked;
                }
            },
            // ユーザーコード
            lngUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngUserCodeConditions"]').get(0).checked;
                }
            },
            // ユーザーID
            strUserID: {
                required: function () {
                    return $('input[name="IsSearch_strUserIDConditions"]').get(0).checked;
                }
            },
            // メール配信許可
            bytMailTransmitFlag: {
                required: function () {
                    return $('input[name="IsSearch_bytMailTransmitFlagConditions"]').get(0).checked;
                }
            },
            // メールアドレス
            strMailAddress: {
                required: function () {
                    return $('input[name="IsSearch_strMailAddressConditions"]').get(0).checked;
                }
            },
            // ユーザー表示
            bytUserDisplayFlag: {
                required: function () {
                    return $('input[name="IsSearch_bytUserDisplayFlagConditions"]').get(0).checked;
                }
            },
            // 表示ユーザーコード
            strUserDisplayCode: {
                required: function () {
                    return $('input[name="IsSearch_strUserDisplayCodeConditions"]').get(0).checked;
                }
            },
            // 表示ユーザー名
            strUserDisplayName: {
                required: function () {
                    return $('input[name="IsSearch_strUserDisplayNameConditions"]').get(0).checked;
                }
            },
            // フルネーム
            strUserFullName: {
                required: function () {
                    return $('input[name="IsSearch_strUserFullNameConditions"]').get(0).checked;
                }
            },
            // 会社
            lngCompanyCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngCompanyCodeConditions"]').get(0).checked;
                }
            },
            // グループ
            lngGroupCode: {
                checkSelect: function () {
                    return $('input[name="IsSearch_lngGroupCodeConditions"]').get(0).checked;
                }
            },
            // 権限グループ
            lngAuthorityGroupCode: {
                required: function () {
                    return $('input[name="IsSearch_lngAuthorityGroupCodeConditions"]').get(0).checked;
                }
            },
            // アクセスIPアドレス
            lngAccessIPAddressCode: {
                required: function () {
                    return $('input[name="IsSearch_lngAccessIPAddressCodeConditions"]').get(0).checked;
                }
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // ログイン許可
            bytInvalidFlag: {
                required: msgRequired
            },
            // ユーザーコード
            lngUserCode: {
                required: msgRequired
            },
            // ユーザーID
            strUserID: {
                required: msgRequired
            },
            // メール配信許可
            bytMailTransmitFlag: {
                required: msgRequired
            },
            // メールアドレス
            strMailAddress: {
                required: msgRequired
            },
            // ユーザー表示
            bytUserDisplayFlag: {
                required: msgRequired
            },
            // 表示ユーザーコード
            strUserDisplayCode: {
                required: msgRequired
            },
            // 表示ユーザー名
            strUserDisplayName: {
                required: msgRequired
            },
            // フルネーム
            strUserFullName: {
                required: msgRequired
            },
            // 会社
            lngCompanyCode: {
                required: msgRequired
            },
            // グループ
            lngGroupCode: {
                required: msgRequired
            },
            // 権限グループ
            lngAuthorityGroupCode: {
                required: msgRequired
            },
            // アクセスIPアドレス
            lngAccessIPAddressCode: {
                required: msgRequired
            }
        }
    });
})();
