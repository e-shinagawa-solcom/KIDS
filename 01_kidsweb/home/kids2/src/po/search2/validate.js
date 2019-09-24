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
        if(!checkValues()){
            return false;
        }

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
    function checkValues(){
        var result = true;
        var targets = $('input.is-search:checked');
        $.each(targets, function(){
            var parent = $(this).parent().parent();
            var target = $(parent).children('span.regist-label');
            // ��Ͽ��
            if($(target).find($('input[name="From_dtmInsertDate"]')).length){
                if(!checkValueInsertDate($(parent))){
                    result = false;
                    return false;
                }
            }
            // ���ϼ�
            if($(target).find($('input[name="lngInputUserCode"]')).length){
                if(!checkValuePresent("���ϼ�", $('input[name="lngInputUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // ȯ��ͭ��������
            if($(target).find($('input[name="From_dtmExpirationDate"]')).length){
                if(!checkValueExpirationDate($(parent))){
                    result = false;
                    return false;
                }
            }
            // ȯ���NO
            if($(target).find($('input[name="strOrderCode"]')).length){
                if(!checkValueOrderCode(parent)){
                    result = false;
                    return false;
                }
            }
            // ����
            if($(target).find($('input[name="strProductCode"]')).length){
                if(!checkValueProductCode(parent)){
                    result = false;
                    return false;
                }
            }
            // �Ķ�����
            if($(target).find($('input[name="lngInChargeGroupCode"]')).length){
                if(!checkValuePresent("�Ķ�����", $('input[name="lngInChargeGroupCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // ��ȯô����
            if($(target).find($('input[name="lngInChargeUserCode"]')).length){
                if(!checkValuePresent("��ȯô����", $('input[name="lngInChargeUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // ������
            if($(target).find($('input[name="lngCustomerCode"]')).length){
                if(!checkValuePresent("������", $('input[name="lngCustomerCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // Ǽ������
            if($(target).find($('input[name="lngDeliveryPlaceCode"]')).length){
                if(!checkValuePresent("Ǽ������", $('input[name="lngDeliveryPlaceCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // �̲�
            if($(target).find($('select[name="lngMonetaryunitCode"]')).length){
                var val = $('select[name="lngMonetaryunitCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("�̲ߤ����Ϥ���Ƥ��ޤ���");
                    result = false;
                    return false;
                }
            }
            // �̲ߥ졼��
            if($(target).find($('select[name="lngMonetaryRateCode"]')).length){
                var val = $('select[name="lngMonetaryRateCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("�̲ߥ졼�Ȥ����Ϥ���Ƥ��ޤ���");
                    result = false;
                    return false;
                }
            }
            // ��ʧ���
            if($(target).find($('select[name="lngPayConditionCode"]')).length){
                var val = $('select[name="lngPayConditionCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("��ʧ��郎���Ϥ���Ƥ��ޤ���");
                    result = false;
                    return false;
                }
            }
        });
        return result;
    }
    // ��Ͽ�������å�
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
    // ���մ�Ϣ�����å�
    function checkValueDate(target, from, to){
        console.log(target);
        if(!from && !to){
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
    // ȯ���NO�����å�
    function checkValueOrderCode(parent){
        var result = true;
        var orderCode = $(parent).find($('input[name="strOrderCode"]')).val();
        if(!checkValuePresent("ȯ���NO.", orderCode)){
            result = false;
            return false;
        }
        if(orderCode.length){
            if(!orderCode.match(/^\d{8}(_\d{2})?$/)){
                alert("ȯ���NO.�ν񼰤˸�꤬����ޤ�");
                result = false;
                return false;
            }
        }

        return result;
    }
    // ���ʥ����ɥ����å�
    function checkValueProductCode(parent){
        var result = true;
        var productCode = $(parent).find($('input[name="strProductCode"]')).val();
        if(!productCode.length){
            alert("���ʥ����ɤ����Ϥ���Ƥ��ޤ���");
            result = false;
            return false;
        }
        if(productCode.length){
            if(!productCode.match(/^\d{5}(_\d{2})?$/)){
                alert("���ʥ����ɤν񼰤˸�꤬����ޤ�");
                result = false;
                return false;
            }
        }

        return result;
    }
    // �����ϥ����å�
    function checkValuePresent(target, value){
        // console.log(target);
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
            $('input[name="IsDisplay_strProductCode"]'),
            $('input[name="IsDisplay_lngInChargeGroupCode"]'),
            $('input[name="IsDisplay_lngInChargeUserCode"]'),
            $('input[name="IsDisplay_strNote"]'),
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
    $('input[name="IsDisplay_btnAdmin"]').on('change', function(){
        changeAdmin($(this).prop('checked'));
    });
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
