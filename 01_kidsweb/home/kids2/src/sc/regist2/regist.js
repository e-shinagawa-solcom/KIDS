//
// regist.js
//

// 検索条件入力画面で入力された値の設定（検索条件入力画面より呼び出される）
function SetSearchConditionWindowValue(search_condition) {
    // 顧客
    $('input[name="lngCustomerCode"]').val(search_condition.lngCustomerCode);
    $('input[name="strCustomerName"]').val(search_condition.strCustomerName);

    // 顧客に紐づく国コードによって消費税区分のプルダウンを変更する
    if(search_condition.lngCustomerCode != ""){
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {
                strMode : "get-lngcountrycode",
                strSessionID: $('input[name="strSessionID"]').val(),
                strcompanydisplaycode: search_condition.lngCustomerCode,
            },
            async: true,
        }).done(function(data){
            console.log("done:get-lngcountrycode");
            if (data == "81"){
                // 81：「外税」を選択（他の項目も選択可能）
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
                $("select[name='lngTaxClassCode']").val("2");
    
            }else{
                // 81以外：「非課税」固定
                $("select[name='lngTaxClassCode']").val("1");
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', true);
            }
        }).fail(function(error){
            console.log("fail:get-lngcountrycode");
            console.log(error);
        });
    }else{
        // 顧客コードが空なら固定解除
        $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
    }
}

// 明細検索（検索条件入力画面より呼び出される）
function SearchReceiveDetail(search_condition) {
 
    // 部分書き換えのためajaxでPOST
    $.ajax({
        type: 'POST',
        url: 'index.php',
        data: {
            strMode : "search-detail",
            strSessionID: $('input[name="strSessionID"]').val(),
            condition: search_condition,
        },
        async: true,
    }).done(function(data){
        console.log("done:search-detail");
        // 検索結果をテーブルにセット
        $('#DetailTableBody').html(data);

    }).fail(function(error){
        console.log("fail:search-detail");
        console.log(error);
    });
    
}

jQuery(function($){
    $("#EditTableBody").sortable();
    $("#EditTableBody").on('sortstop', function(){
        changeRowNum();
    });
    // $('input[name="dtmExpirationDate"]').datepicker();

    // ------------------------------------------
    //   functions
    // ------------------------------------------
    function checkPresentRow(tr){
        var orderNo = $(tr).children('td.detailOrderCode').text();
        var orderDetailNo = $(tr).children('td.detailOrderDetailNo').text();
        var result = false;
        $.each($('#EditTableBody tr'), function(i, tr){
            var od = $(tr).children('.detailOrderCode').text();
            var odd = $(tr).children('td.detailOrderDetailNo').text();
            if(orderNo === od && orderDetailNo === odd){
                result = true;
            }
        });
        return result;
    }
    
    //出力明細一覧エリアに選択した明細を追加
    function setEdit(tr){

        //DEBUG:一旦重複チェック外す
        //if(checkPresentRow(tr)){
        //   return false;
        //}

        var editTable = $('#EditTable');
        var tbody = $('#EditTableBody');
        var i = $(tbody).find('tr').length;
        
        var editTr = $('<tr></tr>');
        var td = $('<td></td>').text(i + 1);
        
        // No.
        $(td).attr('name', 'rownum');
        $(editTr).append($(td));
        // 顧客受注番号
        td = $(tr).find($('td.detailCustomerReceiveCode')).clone();
        $(editTr).append($(td));
        // 受注番号
        td = $(tr).find($('td.detailReceiveCode')).clone();
        $(editTr).append($(td));
        // 顧客品番
        td = $(tr).find($('td.detailGoodsCode')).clone();
        $(editTr).append($(td));
        // 製品コード
        td = $(tr).find($('td.detailProductCode')).clone();
        $(editTr).append($(td));
        // 製品名
        td = $(tr).find($('td.detailProductName')).clone();
        $(editTr).append($(td));
        // 製品名（英語）
        td = $(tr).find($('td.detailProductEnglishName')).clone();
        $(editTr).append($(td));
        // 営業部署
        td = $(tr).find($('td.detailSalesDeptName')).clone();
        $(editTr).append($(td));
        // 売上区分
        td = $(tr).find($('td.detailSalesClassName')).clone();
        $(editTr).append($(td));
        // 納期
        td = $(tr).find($('td.detailDeliveryDate')).clone();
        $(editTr).append($(td));
        // 入数
        td = $(tr).find($('td.detailUnitQuantity')).clone();
        $(editTr).append($(td));
        // 単価
        td = $(tr).find($('td.detailProductPrice')).clone();
        $(editTr).append($(td));
        // 単位
        td = $(tr).find($('td.detailProductUnitName')).clone();
        $(editTr).append($(td));
        // 数量
        td = $(tr).find($('td.detailProductQuantity')).clone();
        $(editTr).append($(td));
        // 税抜金額
        td = $(tr).find($('td.detailSubTotalPrice')).clone();
        $(editTr).append($(td));
        
        // 出力明細テーブルに明細を追加
        $(tbody).append($(editTr));
        $(editTable).append($(tbody));
    }

    
    // 合計金額・消費税額の更新
    function updateAmount(){

        // 税抜金額を取得して配列に格納
        var aryPrice = [];
        $("#EditTableBody tr").each(function() {
            //カンマをはじいてから数値に変換
            var price = Number($(this).find($('td.detailSubTotalPrice')).html().split(',').join(''));
            aryPrice.push(price);
        });

        // ----------------
        // 合計金額の算出
        // ----------------
        var totalAmount = 0;
        aryPrice.forEach(function(price) {
            totalAmount += price;
        });

        // ----------------
        // 消費税額の算出
        // ----------------
        // 消費税区分を取得
        var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();

        // 消費税率を取得
        var taxRate = Number($('select[name="lngTaxRate"]').children('option:selected').text());

        console.log(taxRate);

        // 消費税額の計算
        var taxAmount = 0;
        if (taxClassCode == "1")
        {
            // 1:非課税
            taxAmount = 0;
        }
        else if (taxClassCode == "2")
        {
            // 2:外税
            taxAmount = Math.floor(totalAmount * taxRate);
        }
        else if (taxClassCode == "3")
        {
            // 3:内税
            aryPrice.forEach(function(price) {
                taxAmount += Math.floor( (price / (1+taxRate)) * taxRate );
            });
        }

        // ------------------
        // フォームに値を設定
        // ------------------
        $('input[name="strTotalAmount"]').val(totalAmount);
        $('input[name="strTaxAmount"]').val(taxAmount);
    }

    function addHiddenValue(i, tr){
        var hidden = $('#SegHidden');
        var orderNo = $(tr).find($('td.detailOrderCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][orderNo]").val(orderNo));
        var orderDetailNo = $(tr).find($('td.detailOrderDetailNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][orderDetailNo]").val(orderDetailNo));
        var stockSubjectName = $(tr).find($('td.detailStockSubjectName')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][stockSubjectName]").val(stockSubjectName));
        var stockItenName = $(tr).find($('td.detailStockItemName')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][stockItenName]").val(stockItenName));
        var companyDisplayCode = $(tr).find($('td.detailCompanyDisplayCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][companyDisplayCode]").val(companyDisplayCode));
        var deliveryMethod = $(tr).find('option:selected').val();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][deliveryMethod]").val(deliveryMethod));
        var productPrice = $(tr).find($('td.detailProductPrice')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][productPrice]").val(productPrice));
        var productQuantity = $(tr).find($('td.detailProductQuantity')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][productQuantity]").val(productQuantity));
        var subTotalPrice = $(tr).find($('td.detailSubtotalPrice')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][subTotalPrice]").val(subTotalPrice));
        var deliveryDate = $(tr).find($('td.detailDeliveryDate')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][deliveryDate]").val(deliveryDate));
        var detailNote = $(tr).find($('td.detailNote')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][detailNote]").val(detailNote));
        var productUnitCode = $(tr).find($('td.detailProductUnitCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][productUnitCode]").val(productUnitCode));
        var orderNo = $(tr).find($('td.detailOrderNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][orderNo]").val(orderNo));
        var revisionNo = $(tr).find($('td.detailRevisionNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][revisionNo]").val(revisionNo));
        var subjectCode = $(tr).find($('td.detailStockSubjectCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][subjectCode]").val(subjectCode));
        var stockItemCode = $(tr).find($('td.detailStockItemCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][stockItemCode]").val(stockItemCode));
        var monetaryUnitCode = $(tr).find($('td.detailMonetaryUnitCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][monetaryUnitCode]").val(monetaryUnitCode));
        var customerCompanyCode = $(tr).find($('td.detailCustomerCompanyCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name","detail["+ (i + 1) +"][customerCompanyCode]").val(customerCompanyCode));

    }
    function getCheckedRows(){
        var selected = getSelectedRows();
        var cnt = $(selected).length;
        if(cnt === 0) {
            console.log("なにもしない");
            return false;
        }
        if(cnt > 1) {
            //console.log("なにもしない。ただし後で処理が追加になるかもしれない。");
            alert("移動対象は1行のみ選択してください");
            return false;
        }
        return true;
    }
    function getSelectedRows(){
        return $('#EditTableBody tr.selected');
    }
    function executeSort(mode){
        var row = $('#EditTableBody').children('.selected');
        switch(mode) {
            case 0:
                $('#EditTableBody tr:first').before($(row));
                break;
            case 1:
                var rowPreview = $(row).prev('tr');
                if(row.prev.length) {
                    row.insertBefore(rowPreview);
                    var td = rowPreview.children('td[name="rownum"]')
                }
                break;
            case 2:
                var rowNext = $(row).next('tr');
                if(rowNext.length) {
                    row.insertAfter(rowNext);
                    var td = rowNext.children('td[name="rownum"]')
                }
                break;
            case 3:
                $('#EditTableBody').append($(row));
                break;
            default:
                break;
        }
        changeRowNum();
    }
    function changeRowNum(){
        $('#EditTableBody').find('[name="rownum"]').each(function(idx){
            $(this).html(idx + 1);
        });
    }
    function validationCheck2(){
        var result = true;
        if(!getSelectedRows().length){
            // 発注明細行が1件も選択されていない場合
            alert("発注確定する明細行が選択されていません。");
            result = false;
        }
        var expirationDate = $('input[name="dtmExpirationDate"]').val();
        if(!expirationDate){
            // 発注有効期限日が未入力の場合
            alert("発注有効期限日が指定されていません。");
            result = false;
        }
        if(!expirationDate.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/g)){
            // 発注有効期限日が正規表現「^\d{4}\/\d{1,2}\/\d{1,2}$」に一致しない場合
            alert("発注有効期限日の書式に誤りがあります。");
            result = false;
        }
        if(!isDate(expirationDate)){
            // 発注有効期限日が存在しない日付(2/31等)の場合
            alert("発注有効期限日に存在しない日付が指定されました。");
            result = false;
        }
        var countryCode = $('input[name="lngCountryCode"]').val();
        if(countryCode !== '81'){
            var selected = $('select[name="optPayCondition"]').children('option:selected').val();
            if(selected === '0'){
                // 仕入先のm_company.lngcountrycodeが「81(日本)」以外かつ支払条件が未選択の場合
                alert('仕入先が海外の場合、支払い条件を指定してください。');
                result = false;
            }
        }
        var locationCode = $('input[name="lngLocationCode"]').val();
        if(!locationCode){
            // 納品場所が未入力の場合
            alert('納品場所が指定されていません。');
            result = false;
        }
        var details = getSelectedRows();
        var message = [];
        $.each(details, function(i, tr){
            var selected = $(tr).find('option:selected').val();
            if(selected === "0"){
                var row = $(tr).children('td[name="rownum"]').text();
                message.push(row + '行目の運搬方法が指定されていません。');
            }
        });
        if(message.length){
            // 運搬方法が1件でも未選択の場合
            alert(message.join('\n'));
            result = false;
        }

        return result;
    }
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
    function getUpdateDetail(){
        var result = [];
        $.each(getSelectedRows(), function(i, tr){
            var param ={
                lngOrderDetailNo: $(tr).children('.detailOrderDetailNo').text(),
                lngSortKey: i + 1,
                lngDeliveryMethodCode: $(tr).find('option:selected').val(),
                strDeliveryMethodName: $(tr).find('option:selected').text(),
                lngProductUnitCode: $(tr).find('.detailProductUnitCode').text(),
                lngOrderNo: $(tr).find('.detailOrderNo').text(),
                lngRevisionNo: $(tr).find('.detailRevisionNo').text(),
                lngStockSubjectCode: $(tr).find('.detailStockSubjectCode').text(),
                lngStockItemCode: $(tr).find('.detailStockItemCode').text(),
                lngMonetaryUnitCode: $(tr).find('.detailMonetaryUnitCode').text(),
                lngCustomerCompanyCode: $(tr).find('.detailCustomerCompanyCode').text(),
                curProductPrice: $(tr).find('.detailProductPrice').text(),
                lngProductQuantity: $(tr).find('.detailProductQuantity').text(),
                curSubtotalPrice: $(tr).find('.detailSubtotalPrice').text(),
                dtmDeliveryDate: $(tr).find('.detailDeliveryDate').text(),
            };
            result.push(param);
        });
        return result;
    }


    // 別ウィンドウを開いてPOSTする
    function post_open(url, data, target, features) {

        window.open('', target, features);
       
        // フォームを動的に生成
        var html = '<form id="temp_form" style="display:none;">';
        for(var x in data) {
          if(data[x] == undefined || data[x] == null) {
            continue;
          }
          var _val = data[x].replace(/'/g, "\'");
          html += "<input type='hidden' name='" + x + "' value='" + _val + "' >";
        }
        html += '</form>';
        $("body").append(html);
       
        $('#temp_form').attr("action",url);
        $('#temp_form').attr("target",target);
        $('#temp_form').attr("method","POST");
        $('#temp_form').submit();
       
        // フォームを削除
        $('#temp_form').remove();
    }
    
    // ------------------------------------------
    //   events
    // ------------------------------------------
    $("select[name='lngTaxClassCode']").on('change', function(){
        updateAmount();
    });

    $('select[name="lngTaxRate"]').on('change', function(){
        updateAmount();
    });

    $('input[name="dtmDeliveryDate"]').on('change', function(){
        //TODO:changeイベントを拾えないので要問題解決。素直に日付入力をdateTimePickerに替えたほうがいい
        
        //消費税率の選択項目変更
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {
                strMode : "change-deliverydate",
                strSessionID: $('input[name="strSessionID"]').val(),
                dtmDeliveryDate: $(this).val(),
            },
            async: true,
        }).done(function(data){
            console.log("done:change-deliverydate");
            //TODO:消費税率の選択項目更新
            console.log(data);
            
            //金額の更新
            updateAmount();

        }).fail(function(error){
            console.log("fail:change-deliverydate");
            console.log(error);
        });

    });

    $('#DetailTableBodyAllCheck').on('change', function(){
        $('input[name="edit"]').prop('checked', this.checked);
    });
    $('#SearchBt').on('click', function(){
        
        var url = "/sc/regist2/condition.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
        var data = {
            strSessionID: $('input[name="strSessionID"]').val(),
            param1: 'test'
          };
        
        var features = "width=710,height=460,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        post_open(url, data, "conditionWin", features);

    });
    
    // 追加ボタン
    $('#AddBt').on('click', function(){
        
        var cb = $('#DetailTableBody').find('input[name="edit"]');
        var checked = false;
        var trArray = [];
        $.each(cb, function(i, v){
            if($(v).prop('checked')){
                checked = true;
                trArray.push($(v).parent().parent());
            }
        });
        if(!checked){
            //alert("明細行が選択されていません。");
            return false;
        }
        
        // 出力明細追加
        $.each($(trArray), function(i, v){
            setEdit($(v));
        });

        // 合計金額・消費税額の更新
        updateAmount();
    });


    $('body').on('click', '#EditTableBody tr', function(e){
        var tds = $(e.currentTarget).children('td');
        var checked = $(tds).hasClass('selected');
        if(checked){
            $(tds).removeClass('selected');
            $(this).removeClass('selected');
        } else {
            $(tds).addClass('selected');
            $(this).addClass('selected');
        }
    });
    $('#selectup').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(0);
    });
    $('#selectup1').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(1);
    });
    $('#selectdown1').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(2);
    });
    $('#selectdown').on('click', function(){
        var selected = getCheckedRows();
        if(!selected){ return false; }
        executeSort(3);
    });
    $("#DeleteBt").on('click', function(){
        var selected = getSelectedRows();
        if(!selected.length) { return false; }
        $(selected).remove();
        changeRowNum();
        updateAmount();
    });
    $('#AllDeleteBt').on('click', function(){
        $('#EditTableBody').empty();
        updateAmount();
    });


    $('#PreviewBt').on('click', function(){

        
        var tempdata = {
            data1: '日本語データ１',
            data2: '日本語データ２',
            data3: 'value3'
        };
        
        var target = "previewWin";
        var features = "width=800,height=800,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        // 先に空のウィンドウを開いておく
        var emptyWin = window.open('', target, features);

        var data = {
            headerData: tempdata,
            strSessionID:         $('input[name="strSessionID"]').val(),
            lngOrderNo:           $('input[name="lngOrderNo"]').val(),
            strMode:              $('input[name="strMode"]').val(),
            lngRevisionNo:        $('input[name="lngRevisionNo"]').val(),
            lngPayConditionCode:  $('select[name="optPayCondition"]').children('option:selected').val(),
            dtmExpirationDate:    $('input[name="dtmExpirationDate"]').val(),
            lngLocationCode:      $('input[name="lngLocationCode"]').val(),
            strNote:              $('input[name="strNote"]').text(),
            aryDetail:            getUpdateDetail(),
        };

        $.ajax({
            type: 'POST',
            url: 'preview.php',
            data: data,
            async: true,
        }).done(function(data){
            console.log("done");
            console.log(data);
            
            var url = "/sc/regist2/preview.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
            var previewWin = window.open('' , target , features );
            previewWin.document.write(data);
            previewWin.document.close();
            
            //再読み込みなしでアドレスバーのURLのみ変更
            previewWin.history.pushState(null,null,url);
            
        }).fail(function(error){
            console.log("fail");
            console.log(error);
            emptyWin.close();
        });

    });
});