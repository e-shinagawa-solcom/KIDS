
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

    // 区分の必須チェック
    $.validator.addMethod(
        "checkSelect",
        function (value, element, params) {
            return value != "";
        },
        msgRequired
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
            if (params) {
                if (/^[0-9]{8}$/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                } else if (/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(5, 2);
                    var d = '01';
                    value = y + "/" + m + "/" + d;
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
            // 作成日時
            From_dtmInsertDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked && $('input[name="To_dtmInsertDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                }
            },
            To_dtmInsertDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked && $('input[name="From_dtmInsertDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmInsertDate"]').get(0).checked;
                }
                
            },
            // 仕入日
            From_dtmOrderAppDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmOrderAppDate"]').get(0).checked && $('input[name="To_dtmOrderAppDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmOrderAppDate"]').get(0).checked;
                }
            },
            To_dtmOrderAppDate: {
                required: function () {
                    return $('input[name="IsSearch_dtmOrderAppDate"]').get(0).checked && $('input[name="From_dtmOrderAppDate"]').val() == "";
                },
                checkDateFormat: function () {
                    return $('input[name="IsSearch_dtmOrderAppDate"]').get(0).checked;
                }
                
            },
            // 入力者            
            lngInputUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInputUserCode"]').get(0).checked;
                }
            },
            // 発注NO.            
            From_strOrderCode: {
                required: function () {
                    return $('input[name="IsSearch_strOrderCode"]').get(0).checked;
                }
            },
            To_strOrderCode: {
                required: function () {
                    return $('input[name="IsSearch_strOrderCode"]').get(0).checked;
                }
            },
            // 製品コード            
            From_strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                },
                checkStrProductCode: function() {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                }
            },
            To_strProductCode: {
                required: function () {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                },
                checkStrProductCode: function() {
                    return $('input[name="IsSearch_strProductCode"]').get(0).checked;
                }
            },
            // 営業部署            
            lngInChargeGroupCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeGroupCode"]').get(0).checked;
                }
            },
            // 担当者            
            lngInChargeUserCode: {
                required: function () {
                    return $('input[name="IsSearch_lngInChargeUserCode"]').get(0).checked;
                }
            },
            // 仕入先            
            lngCustomerCompanyCode: {
                required: function () {
                    return $('input[name="IsSearch_lngCustomerCompanyCode"]').get(0).checked;
                }
            },
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
            // 仕入日
            From_dtmOrderAppDate: {
                required: msgRequired
            },
            To_dtmOrderAppDate: {
                required: msgRequired                
            },
            // 入力者            
            lngInputUserCode: {
                required: msgRequired
            },
            // 製品コード            
            From_strProductCode: {
                required: msgRequired
            },
            To_strProductCode: {
                required: msgRequired
            },
            // 発注NO.            
            From_strOrderCode: {
                required: msgRequired
            },
            To_strOrderCode: {
                required: msgRequired
            },
            // 営業部署            
            lngInChargeGroupCode: {
                required: msgRequired
            },
            // 担当者            
            lngInChargeUserCode: {
                required: msgRequired
            },
            // 仕入先            
            lngCustomerCompanyCode: {
                required: msgRequired
            }
        }
    });
})();
