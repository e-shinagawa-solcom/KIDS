
(function(){
    // フォーム
    var form = $('form');
    // ヘッダタブ
    var header = $('div.regist-tab-header');
    // 詳細タブ
    var detail = $('div.regist-tab-detail');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';

    // エラーメッセージ(必須項目)
    var msgRequired = "入力必須項目です。";
    // エラーメッセージ(日付)
    var msgDateFormat = "yyyy/mm/dd形式かつ有効な日付を入力してください。";
    // 帳票その他欄の最大入力可能文字数
    var noteMaxLen = 38;
    // エラーメッセージ(その他の最大文字数まで)
    var msgNote = noteMaxLen + "文字までしか入力できません。"

    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/;

    // validationキック
    $('.hasDatepicker').on({
        'change': function(){
            $(this).blur();
        }
    });

    // 日付がyyyy/mm/dd形式にマッチしているか,有効な日付か
    $.validator.addMethod(
        "checkDateFormat",
        function(value, element, params) {
            if(params){
                // yyyy/mm/dd形式か
                if (!(regDate.test(value))) {
                    return false;
                }

                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = "01";
                var di = new Date(yyyy, mm - 1, dd);
                // 日付の有効性チェック
                if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
                    return true;
                }
            }
            return true;
        },
        msgDateFormat
    );

    $.validator.addMethod(
        "maxlength",
        function (value, element, params) {
            // 未入力の場合チェックしない
            return !value ? true : (value.length <= params) ? true : false;
        },
        msgNote
    );

    // 検証設定
    form.validate({
        // -----------------------------------------------
        // エラー表示処理
        // -----------------------------------------------
        errorPlacement: function (error, element){
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
            if ($(element).prev('img.' + classNameErrorIcon).length <= 0){
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
        unhighlight: function(element){
                // エラーアイコン削除
                $(element).prev('img.' + classNameErrorIcon).remove();
        },
        // -----------------------------------------------
        // 検証ルール
        // -----------------------------------------------
        rules:{
            // 製品名称（日本語）
            strProductName: {
                required: true
            },
            // 製品名称（英語）
            strProductEnglishName: {
                required: true
            },
            // 営業部署
            lngInchargeGroupCode: {
                required: true
            },
            // 担当者
            lngInchargeUserCode: {
                required: true
            },
            // 開発担当者
            lngDevelopUserCode: {
                required: true
            },
            // 顧客
            lngCustomerCompanyCode: {
                required: true
            },
            // 顧客担当者
            lngCustomerUserCode: {
                required: true
            },
            // 商品形態
            lngProductFormCode: {
                required: true
            },
            // カートン入数
            lngCartonQuantity: {
                required: true
            },
            // 生産予定数
            lngProductionQuantity: {
                required: true
            },
            // 初回納品数
            lngFirstDeliveryQuantity: {
                required: true
            },
            // 納期
            dtmDeliveryLimitDate: {
                required: true,
                checkDateFormat: true
            },
            // 納価(pcs単価)
            curProductPrice: {
                required: true
            },
            // 上代(pcs単価)
            curretailPrice: {
                required: true
            },
            // 製品構成
            strProductComposition: {
                required: true
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // 製品名称（日本語）
            strProductName: {
                required: msgRequired
            },
            // 製品名称（英語）
            strProductEnglishName: {
                required: msgRequired
            },
            // 営業部署
            lngInchargeGroupCode: {
                required: msgRequired
            },
            // 担当者
            lngInchargeUserCode: {
                required: msgRequired
            },
            // 開発担当者
            lngDevelopUserCode: {
                required: msgRequired
            },
            // 顧客
            lngCustomerCompanyCode: {
                required: msgRequired
            },
            // 顧客担当者
            lngCustomerUserCode: {
                required: msgRequired
            },
            // 商品形態
            lngProductFormCode: {
                required: msgRequired
            },
            // カートン入数
            lngCartonQuantity: {
                required: msgRequired
            },
            // 生産予定数
            lngProductionQuantity: {
                required: msgRequired
            },
            // 初回納品数
            lngFirstDeliveryQuantity: {
                required: msgRequired
            },
            // 納期
            dtmDeliveryLimitDate: {
                required: msgRequired
            },
            // 納価(pcs単価)
            curProductPrice: {
                required: msgRequired
            },
            // 上代(pcs単価)
            curretailPrice: {
                required: msgRequired
            },
            // 製品構成
            strProductComposition: {
                required: msgRequired
            }
        }
    });
})();
