//
// regist.js
//

// ------------------------------------------------------------------
//   Enterキー押下イベント
// ------------------------------------------------------------------
window.document.onkeydown=fncEnterKeyDown;

function fncEnterKeyDown( e )
{
    // Enterキー押下で明細追加
	if( window.event.keyCode == 13)
	{
        if (document.activeElement.id == "BaseBack"){
            $("#AddBt").trigger('click');
        }
	}
}

// ------------------------------------------------------------------
//   明細選択エリアの選択処理
// ------------------------------------------------------------------
var lastSelectedRow;
function RowClick(currenttr, lock) {
    if (window.event.ctrlKey) {
        toggleRow(currenttr);
    }

    if (window.event.button === 0) {
        if (!window.event.ctrlKey && !window.event.shiftKey) {
            clearAllSelected();
            toggleRow(currenttr);
        }

        if (window.event.shiftKey) {
            selectRowsBetweenIndexes([lastSelectedRow.rowIndex, currenttr.rowIndex])
        }
    }
}

function toggleRow(row) {
    row.className = row.className == 'selected' ? '' : 'selected';
    // TODO:同じ行のチェックボックスのON/OFFを切り替える
    var checked = $(row).find('input[name="edit"]').prop('checked');
    $(row).find('input[name="edit"]').prop('checked', !checked);
    
    lastSelectedRow = row;
}

function selectRowsBetweenIndexes(indexes) {
    var trs = document.getElementById("DetailTable").tBodies[0].getElementsByTagName("tr");
    indexes.sort(function(a, b) {
        return a - b;
    });

    for (var i = indexes[0]; i <= indexes[1]; i++) {
        trs[i-1].className = 'selected';
        var checked = $(trs[i-1]).find('input[name="edit"]').prop('checked');
        $(trs[i-1]).find('input[name="edit"]').prop('checked', !checked);
    }
}

function clearAllSelected() {
    var trs = document.getElementById("DetailTable").tBodies[0].getElementsByTagName("tr");
    for (var i = 0; i < trs.length; i++) {
        trs[i].className = '';
    }
}
// ------------------------------------------------------------------

// ＜検索条件入力画面から呼ばれる関数その１＞
// 検索条件入力画面で入力された値の設定
function SetSearchConditionWindowValue(search_condition) {
    // 顧客
    $('input[name="lngCustomerCode"]').val(search_condition.strCompanyDisplayCode);
    $('input[name="strCustomerName"]').val(search_condition.strCompanyDisplayName);

    // 顧客に紐づく国コードによって消費税区分のプルダウンを変更する
    if(search_condition.strCompanyDisplayCode != ""){
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {
                strMode : "get-lngcountrycode",
                strSessionID: $('input[name="strSessionID"]').val(),
                strcompanydisplaycode: search_condition.strCompanyDisplayCode,
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

// ＜検索条件入力画面から呼ばれる関数その２＞
// 明細検索
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
        // jQueryUIのtablesorterでソート設定
        $('#DetailTable').tablesorter({
            headers: {
                'not-sortable': { sorter: false },
            }            
        });

    }).fail(function(error){
        console.log("fail:search-detail");
        console.log(error);
    });
    
}

// ＜検索条件入力画面から呼ばれる関数その３＞
// 出力明細をすべてクリア
function ClearAllEditDetail() {
    // 全削除ボタンクリックを手動で起動
    $("#AllDeleteBt").trigger('click');
}

jQuery(function($){
    $("#EditTableBody").sortable();
    $("#EditTableBody").on('sortstop', function(){
        changeRowNum();
    });

    // ------------------------------------------
    //   datepicker設定
    // ------------------------------------------
    // datepicker対象要素
    var dateElements = [
        // 納品日
        $('input[name="dtmDeliveryDate"]'),
        // 支払期限
        $('input[name="dtmPaymentLimit"]'),
    ];
    // datepickerの設定
    $.each(dateElements, function(){
        this.datepicker({
                showButtonPanel: true,
                dateFormat: "yy/mm/dd",
                onClose: function(){
                    this.focus();
                }
            }).attr({
                maxlength: "10"
        });
    });

    // ------------------------------------------
    //   functions
    // ------------------------------------------
    // 追加バリデーションチェック
    function validateAdd(tr){
        //明細選択エリアの納期
        var detailDeliveryDate = new Date($(tr).children('td.detailDeliveryDate').text());

        //ヘッダ・フッタ部の納品日と比較（同月以外は不正）
        var headerDeliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());
        var sameMonth = (headerDeliveryDate.getYear() == detailDeliveryDate.getYear())
                        && (headerDeliveryDate.getMonth() == detailDeliveryDate.getMonth());
        if (!sameMonth){
            alert("受注確定時の納期と納品日と一致しません。受注データを修正してください。");
            return false;
        }

        //出力明細の先頭行があればその納期と比較（同月以外は不正）
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length){
            var firstRowDate = new Date($(firstTr).children('td.detailDeliveryDate').text());
            var sameMonthDetail = (firstRowDate.getYear() == detailDeliveryDate.getYear())
                                && (firstRowDate.getMonth() == detailDeliveryDate.getMonth());
            if (!sameMonthDetail){
                alert("出力明細と納品月が異なる明細は選択できません。");
                return false;
            }
        }

        //重複する明細の追加を禁止（重複判定：受注明細のキー）
        var existsSameKey = false;
        var rn1 = $(tr).children('td.detailReceiveNo').text();
        var dn1 = $(tr).children('td.detailReceiveDetailNo').text();
        var rev1 = $(tr).children('td.detailReceiveRevisionNo').text();
    
        $("#EditTableBody tr").each(function(){
            var rn2 = $(this).children('td.detailReceiveNo').text();
            var dn2 = $(this).children('td.detailReceiveDetailNo').text();
            var rev2 = $(this).children('td.detailReceiveRevisionNo').text();

            var isSame = (rn1 == rn2) && (dn1 == dn2) && (rev1 == rev2);
            existsSameKey = existsSameKey || isSame;
        });

        if (existsSameKey){
            alert("重複する明細は選択できません。");
            return false;
        }

        return true;
    }
    
    //出力明細一覧エリアに選択した明細を追加
    function setEdit(tr){

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
        //受注番号（明細登録用）
        td = $(tr).find($('td.detailReceiveNo')).clone();
        $(editTr).append($(td));
        //受注明細番号（明細登録用）
        td = $(tr).find($('td.detailReceiveDetailNo')).clone();
        $(editTr).append($(td));
        //リビジョン番号（明細登録用）
        td = $(tr).find($('td.detailReceiveRevisionNo')).clone();
        $(editTr).append($(td));
        //再販コード（明細登録用）
        td = $(tr).find($('td.detailReviseCode')).clone();
        $(editTr).append($(td));
        //売上区分コード（明細登録用）
        td = $(tr).find($('td.detailSalesClassCode')).clone();
        $(editTr).append($(td));
        //製品単位コード（明細登録用）
        td = $(tr).find($('td.detailProductUnitCode')).clone();
        $(editTr).append($(td));
        //備考（明細登録用）
        td = $(tr).find($('td.detailNote')).clone();
        $(editTr).append($(td));
        //通貨単位コード（明細登録用）
        td = $(tr).find($('td.detailMonetaryUnitCode')).clone();
        $(editTr).append($(td));
        //通貨レートコード（明細登録用）
        td = $(tr).find($('td.detailMonetaryRateCode')).clone();
        $(editTr).append($(td));
        //通貨単位記号（明細登録用）
        td = $(tr).find($('td.detailMonetaryUnitSign')).clone();
        $(editTr).append($(td));
        //明細統一フラグ（明細登録用）
        td = $(tr).find($('td.detailUnifiedFlg')).clone();
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

    // ヘッダ部・フッタ部入力エリアのPOST用データ収集
    function getUpdateHeader(){
        
        var result = {
            //起票者
            strdrafteruserdisplaycode:  $('input[name="lngInsertUserCode"]').val(),
            strdrafteruserdisplayname:  $('input[name="strInsertUserName"]').val(),
            //顧客
            strcompanydisplaycode:     $('input[name="lngCustomerCode"]').val(),
            strcompanydisplayname:     $('input[name="strCustomerName"]').val(),
            //顧客担当者
            strcustomerusername:       $('input[name="strCustomerUserName"]').val(),
            //納品日
            dtmdeliverydate:           $('input[name="dtmDeliveryDate"]').val(),
            //納品先
            strdeliveryplacecompanydisplaycode: $('input[name="lngDeliveryPlaceCode"]').val(),
            strdeliveryplacename:      $('input[name="strDeliveryPlaceName"]').val(),
            //納品先担当者
            strdeliveryplaceusername:  $('input[name="strDeliveryPlaceUserName"]').val(),
            //備考
            strnote:                   $('input[name="strNote"]').val(),
            //消費税区分
            lngtaxclasscode:           $('select[name="lngTaxClassCode"]').children('option:selected').val(),
            strtaxclassname:           $('select[name="lngTaxClassCode"]').children('option:selected').text(),
            //消費税率
            lngtaxcode:                $('select[name="lngTaxRate"]').children('option:selected').val(),
            lngtaxrate:                $('select[name="lngTaxRate"]').children('option:selected').text(),
            //消費税額
            strtaxamount:              $('input[name="strTaxAmount"]').val(),
            //支払期限
            dtmpaymentlimit:           $('input[name="dtmPaymentLimit"]').val(),
            //支払方法
            lngpaymentmethodcode:      $('select[name="lngPaymentMethodCode"]').children('option:selected').val(),
            //合計金額
            curtotalprice:             $('input[name="strTotalAmount"]').val(),
        };

        return result;
    }

    // 出力明細一覧エリアのPOST用データ収集
    function getUpdateDetail(){
        var result = [];
        $.each($('#EditTableBody tr'), function(i, tr){
            var param ={
                //No.（行番号）
                rownumber: $(tr).children('td[name="rownum"]').text(),
                //顧客発注番号
                strcustomerreceivecode: $(tr).children('.detailCustomerReceiveCode').text(),
                //受注番号
                strreceivecode: $(tr).children('.detailReceiveCode').text(),
                //顧客品番
                strgoodscode: $(tr).children('.detailGoodsCode').text(),
                //製品コード
                strproductcode: $(tr).children('.detailProductCode').text(),
                //製品名
                strproductname: $(tr).children('.detailProductName').text(),
                //製品名（英語）
                strproductenglishname: $(tr).children('.detailProductEnglishName').text(),
                //営業部署
                strsalesdeptname: $(tr).children('.detailSalesDeptName').text(),
                //売上区分
                strsalesclassname: $(tr).children('.detailSalesClassName').text(),
                //納期
                dtmdeliverydate: $(tr).children('.detailDeliveryDate').text(),
                //入数
                lngunitquantity: $(tr).children('.detailUnitQuantity').text(),
                //単価　※カンマ除去
                curproductprice: $(tr).children('.detailProductPrice').text().split(',').join(''),
                //単位
                strproductunitname: $(tr).children('.detailProductUnitName').text(),
                //数量　※カンマ除去
                lngproductquantity: $(tr).children('.detailProductQuantity').text().split(',').join(''),
                //税抜金額　※カンマ除去
                cursubtotalprice: $(tr).children('.detailSubTotalPrice').text().split(',').join(''),
                //受注番号（明細登録用）
                lngreceiveno: $(tr).children('.detailReceiveNo').text(),
                //受注明細番号（明細登録用）
                lngreceivedetailno: $(tr).children('.detailReceiveDetailNo').text(),
                //リビジョン番号（明細登録用）
                lngreceiverevisionno: $(tr).children('.detailReceiveRevisionNo').text(),
                //再販コード（明細登録用）
                strrevisecode: $(tr).children('.detailReviseCode').text(),
                //売上区分コード（明細登録用）
                lngsalesclasscode: $(tr).children('.detailSalesClassCode').text(),
                //製品単位コード（明細登録用）
                lngproductunitcode: $(tr).children('.detailProductUnitCode').text(),
                //備考（明細登録用）
                strnote: $(tr).children('.detailNote').text(),
                //通貨単位コード（明細登録用）
                lngmonetaryunitcode: $(tr).children('.detailMonetaryUnitCode').text(),
                //通貨レートコード（明細登録用）
                lngmonetaryratecode: $(tr).children('.detailMonetaryRateCode').text(),
                //通貨単位記号（明細登録用）
                strmonetaryunitsign: $(tr).children('.detailMonetaryUnitSign').text(),
                //明細統一フラグ（明細登録用）
                bytdetailunifiedflg: $(tr).children('.detailUnifiedFlg').text(),
            };
            result.push(param);
        });
        return result;
    }

    // 別ウィンドウを開いてPOSTする（検索条件入力画面を開くときだけ使用）
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

    // --------------------------------------------------------------------------------
    //   日付計算ヘルパ関数
    // --------------------------------------------------------------------------------
    // nヶ月前後の年月日を取得する
    function getAddMonthDate(year,month,day,add){
        var addMonth = month + add;
        var endDate = getEndOfMonth(year,addMonth);//add分を加えた月の最終日を取得

        //引数で渡された日付がnヶ月後の最終日より大きければ日付を次月最終日に合わせる
        //5/31→6/30のように応当日が無い場合に必要
        if(day > endDate){
            day = endDate;
        }else{
            day = day - 1;
        }

        var addMonthDate = new Date(year,addMonth - 1,day);
        return addMonthDate;
    }
    //今月の月末日を取得
    //※次月の0日目＝今月の末日になる
    function getEndOfMonth(year,month){
        var endDate = new Date(year,month,0);
        return endDate.getDate();
    }

    // 締め日をもとに月度を計算する
    function getMonthlyBasedOnClosedDay(targetDate, closedDay)
    {
        var targetYear = targetDate.getFullYear();
        var targetMonth = targetDate.getMonth()+1;
        var targetDay = targetDate.getDate();

        if (targetDay > closedDay){
            // 対象日 > 締め日 なら翌月度
            return getAddMonthDate(targetYear, targetMonth, 1, +1);
        }else{
            // 対象日 <= 締め日 なら当月度
            return new Date(targetYear, targetMonth, 1);
        }
    }
    // --------------------------------------------------------------------------------

    // ------------------------------------------
    //    バリデーション関連
    // ------------------------------------------
    // 出力明細エリアに明細が1行以上存在するなら true
    function existsEditDetail(){
        return $("#EditTableBody tr").length > 0;
    }

    // 納品日の月が締済みであるなら true
    function isClosedMonthOfDeliveryDate(deliveryDate, closedDay)
    {
        // システム日付
        var nowDate = new Date();
        // 顧客の月度
        var customerMonthly = getMonthlyBasedOnClosedDay(nowDate, closedDay);
        // 納品日の月度
        var deliveryMonthly = getMonthlyBasedOnClosedDay(deliveryDate, closedDay);
        // 納品日の月度＜顧客の月度 なら、納品日の月は締め済
        var isClosed = (deliveryMonthly.getTime() < customerMonthly.getTime());

        return isClosed;
    }

    // 対象日付がシステム日付の前後一ヶ月以内なら true
    function withinOneMonthBeforeAndAfter(targetDate)
    {
        // システム日付
        var nowDate = new Date();
        var nowYear = nowDate.getFullYear();
        var nowMonth = nowDate.getMonth()+1;
        var nowDay = nowDate.getDate();

        // ひと月前
        var aMonthAgo = getAddMonthDate(nowYear, nowMonth, nowDay, -1);
        // ひと月後
        var aMonthLater = getAddMonthDate(nowYear, nowMonth, nowDay, +1);

        // 前後一ヶ月以内ならtrue
        var valid = (aMonthAgo.getTime() <= targetDate.getTime()) &&
                    (aMonthLater.getTime() >= targetDate.getTime());

        return valid;
    }

    // 出力明細一覧エリアの明細に、ヘッダ部の納品日の月度と同月度ではない納期の明細が存在するなら true
    function existsInDifferentDetailDeliveryMonthly(deliveryDate, closedDay){

        // 納品日の月度
        var deliveryMonthly = getMonthlyBasedOnClosedDay(deliveryDate, closedDay);

        // 各明細の納期の月度を取得する
        var aryDetailDeliveryMonthly = [];
        $("#EditTableBody tr").each(function(){
            // 明細の納期を取得
            var detailDeliveryDate = $(this).children('td.detailDeliveryDate').text();
            // 納期の月度
            var detailDeliveryMonthly = getMonthlyBasedOnClosedDay(detailDeliveryDate, closedDay);
            // 配列に追加
            aryDetailDeliveryMonthly.push(detailDeliveryMonthly);
        });

        // 納期の月度が納品日の月度と一致しない明細が１つでもあったらエラー
        var indifferentDetailExists = aryDetailDeliveryMonthly.some(function(element){ 
            return (element.getTime() != deliveryMonthly.getTime());
        });

        return !indifferentDetailExists;
    }

    // 出力明細エリアの各明細の売上区分の統一性をチェック。チェックOKならtrue、NGならfalse
    function checkEditDetailsAreSameSalesClass()
    {
        // 出力明細エリアにあるすべての明細の明細統一フラグと売上区分コードを取得する
        var aryDetailUnifiedFlg = [];
        var aryDetailSalesClassCode = [];
        $("#EditTableBody tr").each(function(){
            // 明細統一フラグを取得して配列に追加
            aryDetailUnifiedFlg .push($(this).children('td.detailUnifiedFlg').text());
            // 売上区分コードを取得して配列に追加
            aryDetailSalesClassCode .push($(this).children('td.detailSalesClassCode').text());
        });

        // １．全行の売上区分マスタの明細統一フラグがfalse -> OKとしてチェック終了。そうでないなら２．へ
        var allDetailUnifiedFlgIsFalse = aryDetailUnifiedFlg.every(function(element){ 
            return (element == false);
        });
        if (allDetailUnifiedFlgIsFalse){
            return true;
        }
        
        // ２．売上区分マスタの明細統一フラグがtrueである明細の件数 != 出力明細一覧エリアの明細行数 を満たすなら NGとしてチェック終了。そうでないなら３．へ
        var aryDetailUnifiedFlgIsTrue = aryDetailUnifiedFlg.filter(function(element){
            return (element == true);
        });
        if (aryDetailUnifiedFlgIsTrue.length != $("#EditTableBody tr").length){
            return false;
        }

        // ３．出力明細一覧エリアの明細の売上区分コードがすべて同じ値 -> OKとしてチェック終了。そうでないなら NGとしてチェック終了
        var allDetailSalesClassCodeHasSameValue = aryDetailSalesClassCode.every(function(element){ 
            return (element == aryDetailSalesClassCode[0]);
        });

        return allDetailSalesClassCodeHasSameValue;
    }

    // プレビュー前バリデーションチェック
    function varidateBeforePreview(closedDay)
    {
        // 出力明細エリアに明細が一行もない
        if (!existsEditDetail()){
            alert("出力明細が選択されていません");
            return false;
        }

        // ヘッダ・フッタ部の納品日を取得
        var deliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());

        // 納品日の月が締済みである
        if (isClosedMonthOfDeliveryDate(deliveryDate, closedDay)){
            alert("締済みのため、指定された納品日は無効です");
            return false;
        }

        // 納品日がシステム日付の前後一ヶ月以内にない
        if (!withinOneMonthBeforeAndAfter(deliveryDate)){
            alert("納品日は今月の前後1ヶ月の間を指定してください");
            return false;
        }

        // 出力明細一覧エリアの明細に、ヘッダ部の納品日の月度と同月度ではない納期の明細が存在する
        if (existsInDifferentDetailDeliveryMonthly(deliverDate, closedDay)){
            alert("出力明細には、入力された納品日と異なる月に納品された明細を指定できません");
            return false;
        }

        // 出力明細一覧エリアの明細各行の売上区分統一性チェック
        if (!checkEditDetailsAreSameSalesClass()){
            alert("売上区分の混在ができない明細が選択されています");
            return false;
        }

        // バリデーション成功
        return true;
    }

    // プレビュー画面を表示する（バリデーションあり）
    function displayPreview(){

        // プレビュー画面のウィンドウ属性の定義
        var target = "previewWin";
        var features = "width=900,height=800,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";

        // 先に空のウィンドウを開いておく
        var emptyWin = window.open('', target, features);

        // POSTデータ構築
        var data = {
            strMode :      "display-preview",
            strSessionID:  $('input[name="strSessionID"]').val(),
            aryHeader:     getUpdateHeader(),
            aryDetail:     getUpdateDetail(),
        };

        $.ajax({
            type: 'POST',
            url: 'preview.php',
            data: data,
            async: true,
        }).done(function(data){
            console.log("done");
            
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
            console.log(data);

            //消費税率の選択項目更新
            $('select[name="lngTaxRate"] > option').remove();
            $('select[name="lngTaxRate"]').append(data);

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



    // 検索条件入力ボタン押下
    $('#SearchBt').on('click', function(){

        //出力明細一覧エリアの1行目の売上区分コードを取得する
        var firstRowSalesClassCode = "";
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length){
            firstRowSalesClassCode = $(firstTr).children('.detailSalesClassCode').text();
        }
        
        // 納品書明細検索条件入力画面を別窓で開く
        var url = "/sc/regist2/condition.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
        var data = {
            strSessionID: $('input[name="strSessionID"]').val(),
            //顧客コード（表示用会社コード）
            strcompanydisplaycode:   $('input[name="lngCustomerCode"]').val(),
            //出力明細一覧エリアの1行目の売上区分コード
            lngsalesclasscode:       firstRowSalesClassCode,
          };
        
        var features = "width=710,height=460,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        post_open(url, data, "conditionWin", features);
    });
    
    // 追加ボタン
    $('#AddBt').on('click', function(){
        
        var trArray = [];

        // 選択行の追加
        $("#DetailTableBody tr").each(function(index, tr){
            if ($(tr).attr('class') == "selected" || 
                $(tr).find('input[name="edit"]').prop('checked') == true){
                trArray.push(tr);
            }
        });

        if(trArray.length < 1){
            //alert("明細行が選択されていません。");
            return false;
        }

        // 追加バリデーションチェック
        var invalid = false;
        $.each($(trArray), function(i, v){
            if (!invalid){
                invalid = !validateAdd($(v));
            }
        });
        if (invalid)
        {
            return false;
        }

        // 明細追加
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

    $('#DateBtB').on('click', function(){
        $('input[name="dtmDeliveryDate"]').focus();
    });
    $('#DateBtC').on('click', function(){
        $('input[name="dtmPaymentLimit"]').focus();
    });

    // プレビューボタン押下
    $('#PreviewBt').on('click', function(){

        // POSTデータ構築
        var data = {
            strMode :      "get-closedday",
            strSessionID:  $('input[name="strSessionID"]').val(),
            strcompanydisplaycode:   $('input[name="lngCustomerCode"]').val(),
        };
        
        // プレビュー前のバリデーションに「締め日」が必要なのでajaxで取得する
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: data,
            async: true,
        }).done(function(data){
            console.log("done:get-closedday");
            console.log(data);

            // 締め日
            var closedDay = data;

            // 顧客コードに対応する締め日が取得できない
            if (!closedDay){
                alert("締め日が取得できません。");
                return false;
            }
            
            if (closedDay < 0){
                alert("締め日が負の値です。");
                return false;
            }
            
            // プレビュー画面表示前のバリデーションチェック
            if (!varidateBeforePreview(closedDay)){
                return false;
            }

            // プレビュー画面表示
            displayPreview();
            
        }).fail(function(error){
            console.log("fail:get-closedday");
            console.log(error);
        });

    });
});