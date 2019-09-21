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
        if(!checkValues()){
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
    function checkValues(){
        var result = true;
        var targets = $('input.is-search:checked');
        $.each(targets, function(){
            var parent = $(this).parent().parent();
            var target = $(parent).children('span.regist-label');
            // 登録日
            if($(target).find($('input[name="From_dtmInsertDate"]')).length){
                if(!checkValueInsertDate($(parent))){
                    result = false;
                    return false;
                }
            }
            // 入力者
            if($(target).find($('input[name="lngInputUserCode"]')).length){
                if(!checkValuePresent("入力者", $('input[name="lngInputUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // 発注有効期限日
            if($(target).find($('input[name="From_dtmExpirationDate"]')).length){
                if(!checkValueExpirationDate($(parent))){
                    result = false;
                    return false;
                }
            }
            // 発注書NO
            if($(target).find($('input[name="strOrderCode"]')).length){
                if(!checkValueOrderCode(parent)){
                    result = false;
                    return false;
                }
            }
            // 製品
            if($(target).find($('input[name="strProductCode"]')).length){
                if(!checkValueProductCode(parent)){
                    result = false;
                    return false;
                }
            }
            // 営業部署
            if($(target).find($('input[name="lngInChargeGroupCode"]')).length){
                if(!checkValuePresent("営業部署", $('input[name="lngInChargeGroupCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // 開発担当者
            if($(target).find($('input[name="lngInChargeUserCode"]')).length){
                if(!checkValuePresent("開発担当者", $('input[name="lngInChargeUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // 仕入先
            if($(target).find($('input[name="lngCustomerCode"]')).length){
                if(!checkValuePresent("仕入先", $('input[name="lngCustomerCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // 納品部署
            if($(target).find($('input[name="lngDeliveryPlaceCode"]')).length){
                if(!checkValuePresent("納品部署", $('input[name="lngDeliveryPlaceCode"]').val())){
                    result = false;
                    return false;
                }
            }
            // 通貨
            if($(target).find($('select[name="lngMonetaryunitCode"]')).length){
                var val = $('select[name="lngMonetaryunitCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("通貨が入力されていません");
                    result = false;
                    return false;
                }
            }
            // 通貨レート
            if($(target).find($('select[name="lngMonetaryRateCode"]')).length){
                var val = $('select[name="lngMonetaryRateCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("通貨レートが入力されていません");
                    result = false;
                    return false;
                }
            }
            // 支払条件
            if($(target).find($('select[name="lngPayConditionCode"]')).length){
                var val = $('select[name="lngPayConditionCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("支払条件が入力されていません");
                    result = false;
                    return false;
                }
            }
        });
        return result;
    }
    // 登録日チェック
    function checkValueInsertDate(parent){
        var from = $(parent).find($('input[name="From_dtmInsertDate"]')).val();
        var to = $(parent).find($('input[name="To_dtmInsertDate"]')).val();
        return checkValueDate("登録日", from, to);
    }
    // 発注有効期限日チェック
    function checkValueExpirationDate(parent){
        var from = $(parent).find($('input[name="From_dtmExpirationDate"]')).val();
        var to = $(parent).find($('input[name="To_dtmExpirationDate"]')).val();
        return checkValueDate("発注有効期限日", from, to);
    }
    // 日付関連チェック
    function checkValueDate(target, from, to){
        console.log(target);
        if(!from && !to){
            alert(target + "が入力されていません");
            return false;
        }
        if(from.length){
            if(!isDateFormat(from)){
                alert(target + "FROMの書式に誤りがあります");
                return false;
            }
            if(!isValidDate(from)){
                alert(target + "FROMに存在しない日付が指定されました");
                return false;
            }
        }
        if(to.length){
            if(!isDateFormat(to)){
                alert(target + "TOの書式に誤りがあります");
                return false;
            }
            if(!isValidDate(to)){
                alert(target + "TOに存在しない日付が指定されました");
                return false;
            }
        }
        if(from.length && to.length){
            if(new Date(from) > new Date(to)){
                alert(target + "FROMに" + target + "TOより未来の日付が指定されました")
                return false;
            }
        }
        if(target === "登録日"){
            if(new Date(from) > new Date()){
                alert(target + "FROMに未来の日付が指定されました");
                return false;
            }
        }
        return true;
    }
    // 発注書NOチェック
    function checkValueOrderCode(parent){
        var result = true;
        var orderCode = $(parent).find($('input[name="strOrderCode"]')).val();
        if(!checkValuePresent("発注書NO.", orderCode)){
            result = false;
            return false;
        }
        if(orderCode.length){
            if(!orderCode.match(/^\d{8}(_\d{2})?$/)){
                alert("発注書NO.の書式に誤りがあります");
                result = false;
                return false;
            }
        }

        return result;
    }
    // 製品コードチェック
    function checkValueProductCode(parent){
        var result = true;
        var productCode = $(parent).find($('input[name="strProductCode"]')).val();
        if(!productCode.length){
            alert("製品コードが入力されていません");
            result = false;
            return false;
        }
        if(productCode.length){
            if(!productCode.match(/^\d{5}(_\d{2})?$/)){
                alert("製品コードの書式に誤りがあります");
                result = false;
                return false;
            }
        }

        return result;
    }
    // 各入力チェック
    function checkValuePresent(target, value){
        // console.log(target);
        var result = true;
        if(!value.length){
            alert(target + "が入力されていません");
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
        console.log("日付チェックエラー：["  + date + "]");
    } else {
        console.log("日付チェックOK");
    }
}
