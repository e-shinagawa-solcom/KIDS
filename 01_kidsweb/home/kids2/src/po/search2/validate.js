//
// validation.js
//
jQuery(function($){
    // datepicker設定
    var datePickerTargets = [
        $('input[name$="From_dtmInsertDate"]'),
        $('input[name$="To_dtmInsertDate"]'),
        $('input[name$="From_dtmExpirationDate"]'),
        $('input[name$="To_dtmExpirationDate"]'),
        $('input[name$="From_dtmDeliverDate"]'),
        $('input[name$="To_dtmDeliverDate"]'),
    ];
    $.each(datePickerTargets, function(i, v){
        $(v).datepicker();
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
        result = checkedCheckbox($('input.is-display'), "表示項目チェックボックスが選択されていません。");
        if(!result){
            return false;
        }

        // 入力項目チェック
        result = checkValue();

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
    function checkValue(){
        var result = true;
        if($('div.regist-line .regist-label').find($('input[name$="From_dtmInsertDate"]')).length){
            result = checkValueInsertDate();
        }
        if($('div.regist-line .regist-label').find($('input[name$="lngInputUserCode"]')).length){
            result = checkValidateInputUser();
        }
        return result;
    }
    function checkValueInsertDate(){
        console.log("登録日");
        var result = false;
        

        return result;
    }
    function checkValidateInputUser(){
        console.log("入力者");
    }
});
function isDate(d){
    if(d == "") { return false; }
    if(!d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) { return false; }

    var date = new Date(d);
    if(date.getFullYear() != d.split("/")[0]
        || date.getMonth() != d.split("/")[1] - 1
        || date.getDate() != d.split("/")[2]
    ){
        return false;
    }
    return true;
}
function fncCheckDate(e){
    var date = $(e).get(0).value;
    if(date && !isDate(date)){
        console.log("日付チェックエラー：["  + date + "]");
    } else {
        console.log("日付チェックOK");
    }
}
