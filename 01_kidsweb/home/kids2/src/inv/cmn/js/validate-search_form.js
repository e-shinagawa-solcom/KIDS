
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

    // 登録ボタンイベント横取り
    var events = $._data($('img.search').get(0), 'events');
    var originalHandler = [];
    for(var i = 0; i < events.click.length; i++){
        originalHandler[i] = events.click[i].handler;
    }
    // 現在のイベントを打ち消す
    $('img.search').off('click');
    $('img.search').on('click', {next:originalHandler}, function(event){
        var result = checkedCheckbox($('input.is-search'), "検索条件チェックボックスが選択されていません。");
        if(!result){
            return false;
        }


        // 保留していたイベントを実行
        for(var i = 0; i < event.data.next.length; i++){
            event.data.next[i]();
        }
    });
    function checkedCheckbox(e, msg){
        var result = isChecked(e);
        if(!result){
            alert(msg);
        }
        return result;
    }
    function isChecked(e){
        var result = false;
        $(e).each(function(){
            if($(this).prop('checked')){
                result = true;
                return false;
            }
        });
        return result;
    }

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
                } else {
                    return false;
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
            // 仕入計上日
            From_DtmAppropriationDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_DtmAppropriationDate"]').get(0).checked;
                }
            },
            To_DtmAppropriationDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_DtmAppropriationDate"]').get(0).checked;
                }
            },
            // 実施日
            From_ActionDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionDate"]').get(0).checked;
                }
            },
            To_ActionDate: {
                checkDateFormat: function(){
                    return $('input[name="IsSearch_ActionDate"]').get(0).checked;
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
