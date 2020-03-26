
function validate_checkDateFormat(value, params) {
    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;
    if (!value) {
        return true;
    }

    if (params) {
        if (value.length == 8) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            var d = str.substr(6, 2);
            value = y + "/" + m + "/" + d;
        }
        // yyyy/mm(m)/dd(d)形式か
        if (!(regDate.test(value))) {
            return false;
        }

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
    }
    return true;
}


// エラーアイコンクラス名
var classNameErrorIcon = 'error-icon';
// エラーアイコンリソースURL
var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';

function validate_errorPlacement(error, element) {
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
}

function validate_unhighlight(element) {
    // エラーアイコン削除
    $(element).prev('img.' + classNameErrorIcon).remove();
}
