
(function(){
    // フォーム
    var form = $('form');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // エラーメッセージ(日付)
    var msgDateFormat = "yyyy/mm/dd形式かつ有効な日付を入力してください。";
    // 日付フォーマット yyyy/mm/dd形式
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;

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
            }return true;
        },
        msgDateFormat
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
            // 依頼日
            From_RequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_RequestDate"]').get(0).checked;
                }
            },
            To_RequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_RequestDate"]').get(0).checked;
                }
            },
            // 希望日
            From_ActionRequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionRequestDate"]').get(0).checked;
                }
            },
            To_ActionRequestDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionRequestDate"]').get(0).checked;
                }
            },
            // 返却予定日
            From_ReturnSchedule: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ReturnSchedule"]').get(0).checked;
                }
            },
            To_ReturnSchedule: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ReturnSchedule"]').get(0).checked;
                }
            },
            // 登録日
            From_Created: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Created"]').get(0).checked;
                }
            },
            To_Created: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Created"]').get(0).checked;
                }
            },
            // 更新日
            From_Updated: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Updated"]').get(0).checked;
                }
            },
            To_Updated: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_Updated"]').get(0).checked;
                }
            }
        }
    });
})();
