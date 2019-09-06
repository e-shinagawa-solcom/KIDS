//
// validation.js
//
jQuery(function($){
    // datepicker����
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

    // ��Ͽ�ܥ��󥤥٥�Ȳ����
    var events = $._data($('img.search').get(0), 'events');
    var originalHandler = [];
    for(var i = 0; i < events.click.length; i++){
        originalHandler[i] = events.click[i].handler;
    }
    // ���ߤΥ��٥�Ȥ��Ǥ��ä�
    $('img.search').off('click');
    $('img.search').on('click', {next:originalHandler}, function(event){
        var result = checkedCheckbox($('input.is-search'), "�����������å��ܥå��������򤵤�Ƥ��ޤ���");
        if(!result){
            return false;
        }
        result = checkedCheckbox($('input.is-display'), "ɽ�����ܥ����å��ܥå��������򤵤�Ƥ��ޤ���");
        if(!result){
            return false;
        }

        // ���Ϲ��ܥ����å�
        result = checkValue();

        // ��α���Ƥ������٥�Ȥ�¹�
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
        console.log("��Ͽ��");
        var result = false;
        

        return result;
    }
    function checkValidateInputUser(){
        console.log("���ϼ�");
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
        console.log("���ե����å����顼��["  + date + "]");
    } else {
        console.log("���ե����å�OK");
    }
}
