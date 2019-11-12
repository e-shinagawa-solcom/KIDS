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
        $('input[name$="From_dtmDeliveryDate"]'),
        $('input[name$="To_dtmDeliveryDate"]'),
    ];
    $.each(datePickerTargets, function(i, v){
        $(v).datepicker();
    });

    $('input[name="Option_admin"]').prop('checked', false);

    // ��Ͽ�ܥ��󥤥٥�Ȳ����
    var events = $._data($('img.search').get(0), 'events');
    var originalHandler = [];
    for(var i = 0; i < events.click.length; i++){
        originalHandler[i] = events.click[i].handler;
    }
    // ���ߤΥ��٥�Ȥ��Ǥ��ä�
    $('img.search').off('click');
    $('img.search').on('click', {next:originalHandler}, function(event){
        if(!$('input.is-search:checked').length){
            alert("�����������å��ܥå��������򤵤�Ƥ��ޤ���");
            return false;
        }
        if(!$('input.is-display:checked').length){
            alert("ɽ�����ܥ����å��ܥå��������򤵤�Ƥ��ޤ���");
            return false;
        }

        // ���Ϲ��ܥ����å�
        if(!checkValues()){
            return false;
        }

        // ��α���Ƥ������٥�Ȥ�¹�
        for(var i = 0; i < event.data.next.length; i++){
            event.data.next[i]();
        }
    });
    function checkValues(){
        var result = true;
        var targets = $('input.is-search:checked');
        $.each(targets, function(){
            var parent = $(this).parent().parent();
            var target = $(parent).children('span.regist-label');
            if($(target).find($('input[name="From_dtmInsertDate"]')).length){
                if(!checkValueInsertDate($(parent))){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="From_dtmExpirationDate"]')).length){
                if(!checkValueExpirationDate($(parent))){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="From_dtmDeliveryDate"]')).length){
                if(!checkValueDeliveryDate($(parent))){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngInputUserCode"]')).length){
                if(!checkValuePresent("���ϼ�", $('input[name="lngInputUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="From_strOrderCode"]')).length){
                if(!checkValueOrderCode(parent)){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="From_strProductCode"]')).length){
                if(!checkValueProductCode(parent)){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="strProductName"]')).length){
                if(!checkValuePresent("����̾", $('input[name="strProductName"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="strProductEnglishName"]')).length){
                if(!checkValuePresent("����̾(�Ѹ�)", $('input[name="strProductEnglishName"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngInChargeGroupCode"]')).length){
                if(!checkValuePresent("�Ķ�����", $('input[name="lngInChargeGroupCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngInChargeUserCode"]')).length){
                if(!checkValuePresent("��ȯô����", $('input[name="lngInChargeUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngCustomerCode"]')).length){
                if(!checkValuePresent("������", $('input[name="lngCustomerCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('select[name="lngStockSubjectCode"]')).length){
                var val = $('select[name="lngStockSubjectCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("���ϲ��ܤ����Ϥ���Ƥ��ޤ���");
                    result = false;
                    return false;
                }
            }
            if($(target).find($('select[name="lngStockItemCode"]')).length){
                var val = $('select[name="lngStockItemCode"]').find('option:selected').val();
                if(val === "1224-0"){
                    alert("�������ʤ����Ϥ���Ƥ��ޤ���");
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngOrderStatusCode[]"]')).length){
                if(!$(target).find('input:checked').length){
                    alert("���֤����Ϥ���Ƥ��ޤ���");
                    result = false;
                    return false;
                }
            }        
        });

        return result;
    }
    // ȯ���������å�
    function checkValueInsertDate(parent){
        var from = $(parent).find($('input[name="From_dtmInsertDate"]')).val();
        var to = $(parent).find($('input[name="To_dtmInsertDate"]')).val();
        return checkValueDate("��Ͽ��", from, to);
    }
    // ȯ��ͭ�������������å�
    function checkValueExpirationDate(parent){
        var from = $(parent).find($('input[name="From_dtmExpirationDate"]')).val();
        var to = $(parent).find($('input[name="To_dtmExpirationDate"]')).val();
        return checkValueDate("ȯ��ͭ��������", from, to);
    }
    // Ǽ�������å�
    function checkValueDeliveryDate(parent){
        var from = $(parent).find($('input[name="From_dtmDeliveryDate"]')).val();
        var to = $(parent).find($('input[name="To_dtmDeliveryDate"]')).val();
        return checkValueDate("Ǽ��", from, to);
    }
    // ���մ�Ϣ�����å�
    function checkValueDate(target, from, to){
        console.log(target);
        if(!from.length && !to.length){
            alert(target + "�����Ϥ���Ƥ��ޤ���");
            return false;
        }
        if(from.length){
            if(!isDateFormat(from)){
                alert(target + "FROM�ν񼰤˸�꤬����ޤ�");
                return false;
            }
            if(!isValidDate(from)){
                alert(target + "FROM��¸�ߤ��ʤ����դ����ꤵ��ޤ���");
                return false;
            }
        }
        if(to.length){
            if(!isDateFormat(to)){
                alert(target + "TO�ν񼰤˸�꤬����ޤ�");
                return false;
            }
            if(!isValidDate(to)){
                alert(target + "TO��¸�ߤ��ʤ����դ����ꤵ��ޤ���");
                return false;
            }
        }
        if(from.length && to.length){
            if(new Date(from) > new Date(to)){
                alert(target + "FROM��" + target + "TO���̤������դ����ꤵ��ޤ���")
                return false;
            }
        }
        if(target === "��Ͽ��"){
            if(new Date(from) > new Date()){
                alert(target + "FROM��̤������դ����ꤵ��ޤ���");
                return false;
            }
        }
        return true;
    }
    // ȯ��NO�����å�
    function checkValueOrderCode(parent){
        var result = true;
        var from = $(parent).find($('input[name="From_strOrderCode"]')).val();
        var to = $(parent).find($('input[name="To_strOrderCode"]')).val();
        if(!from.length && !to.length){
            alert("ȯ��NO.�����Ϥ���Ƥ��ޤ���");
            result = false;
            return false;
        }
        if(from.length){
            if(!from.match(/^\d{8}(_\d{2})?$/) && !from.match(/^\d{9}(_\d{2})?$/)){
                alert("ȯ��NO.FROM�ν񼰤˸�꤬����ޤ�");
                result = false;
                return false;
            }
        }
        if(to.length){
            if(!to.match(/^\d{8}(_\d{2})?$/) && !to.match(/^\d{9}(_\d{2})?$/)){
                alert("ȯ��NO.TO�ν񼰤˸�꤬����ޤ�");
                result = false;
                return false;
            }
        }

        return result;
    }
    // ���ʥ����ɥ����å�
    function checkValueProductCode(parent){
        var result = true;
        var from = $(parent).find($('input[name="From_strProductCode"]')).val();
        var to = $(parent).find($('input[name="To_strProductCode"]')).val();
        if(!from.length && !to.length){
            alert("���ʥ����ɤ����Ϥ���Ƥ��ޤ���");
            result = false;
            return false;
        }
        if(from.length){
            if(!from.match(/^\d{5}(_\d{2})?$/)){
                alert("���ʥ�����FROM�ν񼰤˸�꤬����ޤ�");
                result = false;
                return false;
            }
        }
        if(to.length){
            if(!to.match(/^\d{5}(_\d{2})?$/)){
                alert("���ʥ����ɤν񼰤˸�꤬����ޤ�");
                result = false;
                return false;
            }
        }

        return result;
    }
    // �����ϥ����å�
    function checkValuePresent(target, value){
        console.log(target);
        var result = true;
        if(!value.length){
            alert(target + "�����Ϥ���Ƥ��ޤ���");
            result = false;
            return false;
        }

        return result;
    }
    function changeAdmin(mode){
        var targets = [
            $('input[name="IsDisplay_strProductName"]'),
            $('input[name="IsDisplay_strProductEnglishName"]'),
            $('input[name="IsDisplay_lngInChargeGroupCode"]'),
            $('input[name="IsDisplay_lngInChargeUserCode"]'),
            $('input[name="IsDisplay_lngStockSubjectCode"]'),
            $('input[name="IsDisplay_lngStockItemCode"]'),
            $('input[name="IsDisplay_dtmDeliveryDate"]'),
            $('input[name="IsDisplay_lngRecordNo"]'),
            $('input[name="IsDisplay_curProductPrice"]'),
            $('input[name="IsDisplay_lngProductQuantity"]'),
            $('input[name="IsDisplay_curSubTotalPrice"]'),
            $('input[name="IsDisplay_strDetailNote"]'),
        ];

        if(mode){
            $.each(targets, function(){
                $(this).prop('checked', false);
                $(this).attr('disabled', 'disabled');
            });
            // console.log("checked");
        } else {
            $.each(targets, function(){
                $(this).attr('disabled', false);
            });
            $('input[name="IsDisplay_lngRecordNo"]').prop('checked', true);
            // console.log("unchecked");
        }
    }

    // events
    $('input[name="Option_admin"]').on('change', function(){
        changeAdmin($(this).prop('checked'));
    });
});
function isDate(d){
    if(d == "") { return false; }
    if(!isDateFormat(d)) { return false; }
    if(!isValidDate(d)){ return false; }
    // var date = new Date(d);
    // if(date.getFullYear() != d.split("/")[0]
    //     || date.getMonth() != d.split("/")[1] - 1
    //     || date.getDate() != d.split("/")[2]
    // ){
    //     return false;
    // }
    return true;
}
function isDateFormat(d){
    return d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/);
}
function isValidDate(d){
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
