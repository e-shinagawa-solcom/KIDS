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
        $('input[name$="From_dtmDeliveryDate"]'),
        $('input[name$="To_dtmDeliveryDate"]'),
    ];
    $.each(datePickerTargets, function(i, v){
        $(v).datepicker();
    });

    $('input[name="Option_admin"]').prop('checked', false);

    // 登録ボタンイベント横取り
    var events = $._data($('img.search').get(0), 'events');
    var originalHandler = [];
    for(var i = 0; i < events.click.length; i++){
        originalHandler[i] = events.click[i].handler;
    }
    // 現在のイベントを打ち消す
    $('img.search').off('click');
    $('img.search').on('click', {next:originalHandler}, function(event){
        if(!$('input.is-search:checked').length){
            alert("検索条件チェックボックスが選択されていません。");
            return false;
        }
        if(!$('input.is-display:checked').length){
            alert("表示項目チェックボックスが選択されていません。");
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
                if(!checkValuePresent("入力者", $('input[name="lngInputUserCode"]').val())){
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
                if(!checkValuePresent("製品名", $('input[name="strProductName"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="strProductEnglishName"]')).length){
                if(!checkValuePresent("製品名(英語)", $('input[name="strProductEnglishName"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngInChargeGroupCode"]')).length){
                if(!checkValuePresent("営業部署", $('input[name="lngInChargeGroupCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngInChargeUserCode"]')).length){
                if(!checkValuePresent("開発担当者", $('input[name="lngInChargeUserCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngCustomerCode"]')).length){
                if(!checkValuePresent("仕入先", $('input[name="lngCustomerCode"]').val())){
                    result = false;
                    return false;
                }
            }
            if($(target).find($('select[name="lngStockSubjectCode"]')).length){
                var val = $('select[name="lngStockSubjectCode"]').find('option:selected').val();
                if(val === "0"){
                    alert("入力科目が入力されていません");
                    result = false;
                    return false;
                }
            }
            if($(target).find($('select[name="lngStockItemCode"]')).length){
                var val = $('select[name="lngStockItemCode"]').find('option:selected').val();
                if(val === "1224-0"){
                    alert("仕入部品が入力されていません");
                    result = false;
                    return false;
                }
            }
            if($(target).find($('input[name="lngOrderStatusCode[]"]')).length){
                if(!$(target).find('input:checked').length){
                    alert("状態が入力されていません");
                    result = false;
                    return false;
                }
            }        
        });

        return result;
    }
    // 発注日チェック
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
    // 納期チェック
    function checkValueDeliveryDate(parent){
        var from = $(parent).find($('input[name="From_dtmDeliveryDate"]')).val();
        var to = $(parent).find($('input[name="To_dtmDeliveryDate"]')).val();
        return checkValueDate("納期", from, to);
    }
    // 日付関連チェック
    function checkValueDate(target, from, to){
        console.log(target);
        if(!from.length && !to.length){
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
    // 発注NOチェック
    function checkValueOrderCode(parent){
        var result = true;
        var from = $(parent).find($('input[name="From_strOrderCode"]')).val();
        var to = $(parent).find($('input[name="To_strOrderCode"]')).val();
        if(!from.length && !to.length){
            alert("発注NO.が入力されていません");
            result = false;
            return false;
        }
        if(from.length){
            if(!from.match(/^\d{8}(_\d{2})?$/) && !from.match(/^\d{9}(_\d{2})?$/)){
                alert("発注NO.FROMの書式に誤りがあります");
                result = false;
                return false;
            }
        }
        if(to.length){
            if(!to.match(/^\d{8}(_\d{2})?$/) && !to.match(/^\d{9}(_\d{2})?$/)){
                alert("発注NO.TOの書式に誤りがあります");
                result = false;
                return false;
            }
        }

        return result;
    }
    // 製品コードチェック
    function checkValueProductCode(parent){
        var result = true;
        var from = $(parent).find($('input[name="From_strProductCode"]')).val();
        var to = $(parent).find($('input[name="To_strProductCode"]')).val();
        if(!from.length && !to.length){
            alert("製品コードが入力されていません");
            result = false;
            return false;
        }
        if(from.length){
            if(!from.match(/^\d{5}(_\d{2})?$/)){
                alert("製品コードFROMの書式に誤りがあります");
                result = false;
                return false;
            }
        }
        if(to.length){
            if(!to.match(/^\d{5}(_\d{2})?$/)){
                alert("製品コードの書式に誤りがあります");
                result = false;
                return false;
            }
        }

        return result;
    }
    // 各入力チェック
    function checkValuePresent(target, value){
        console.log(target);
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
        console.log("日付チェックエラー：["  + date + "]");
    } else {
        console.log("日付チェックOK");
    }
}
