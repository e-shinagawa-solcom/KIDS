//
// regist.js
//
jQuery(function ($) {
    $("#EditTableBody").sortable();
    $("#EditTableBody").on('sortstop', function () {
        changeRowNum();
    });
    // $('input[name="dtmExpirationDate"]').datepicker();
    function checkPresentRow(tr) {
        var orderNo = $(tr).children('td.detailOrderCode').text();
        var orderDetailNo = $(tr).children('td.detailOrderDetailNo').text();
        var result = false;
        $.each($('#EditTableBody tr'), function (i, tr) {
            var od = $(tr).children('.detailOrderCode').text();
            var odd = $(tr).children('td.detailOrderDetailNo').text();
            if (orderNo === od && orderDetailNo === odd) {
                result = true;
            }
        });
        return result;
    }
    function setEdit(tr) {
        if (checkPresentRow(tr)) {
            return false;
        }

        var editTable = $('#EditTable');
        var tbody = $('#EditTableBody');
        var i = $(tbody).find('tr').length;

        var editTr = $('<tr></tr>');
        var td = $('<td></td>').text(i + 1);
        $(td).attr('name', 'rownum');
        $(editTr).append($(td));
        td = $(tr).find($('td.detailOrderCode')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailOrderDetailNo')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailStockSubjectName')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailStockItemName')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailCompanyDisplayCode')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailDeliveryMethod')).clone();
        $(editTr).append($(td).removeClass('forEdit'));
        td = $(tr).find($('td.detailProductPrice')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailProductQuantity')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailSubtotalPrice')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailDeliveryDate')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailNote')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailProductUnitCode')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailOrderNo')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailRevisionNo')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailStockSubjectCode')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailStockItemCode')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailMonetaryUnitCode')).clone();
        $(editTr).append($(td));
        td = $(tr).find($('td.detailCustomerCompanyCode')).clone();
        $(editTr).append($(td));

        $(tbody).append($(editTr));
        $(editTable).append($(tbody));
        //addHiddenValue(i, $(editTr));
    }
    function addHiddenValue(i, tr) {
        var hidden = $('#SegHidden');
        var orderNo = $(tr).find($('td.detailOrderCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderNo]").val(orderNo));
        var orderDetailNo = $(tr).find($('td.detailOrderDetailNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderDetailNo]").val(orderDetailNo));
        var stockSubjectName = $(tr).find($('td.detailStockSubjectName')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][stockSubjectName]").val(stockSubjectName));
        var stockItenName = $(tr).find($('td.detailStockItemName')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][stockItenName]").val(stockItenName));
        var companyDisplayCode = $(tr).find($('td.detailCompanyDisplayCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][companyDisplayCode]").val(companyDisplayCode));
        var deliveryMethod = $(tr).find('option:selected').val();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][deliveryMethod]").val(deliveryMethod));
        var productPrice = $(tr).find($('td.detailProductPrice')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][productPrice]").val(productPrice));
        var productQuantity = $(tr).find($('td.detailProductQuantity')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][productQuantity]").val(productQuantity));
        var subTotalPrice = $(tr).find($('td.detailSubtotalPrice')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][subTotalPrice]").val(subTotalPrice));
        var deliveryDate = $(tr).find($('td.detailDeliveryDate')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][deliveryDate]").val(deliveryDate));
        var detailNote = $(tr).find($('td.detailNote')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][detailNote]").val(detailNote));
        var productUnitCode = $(tr).find($('td.detailProductUnitCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][productUnitCode]").val(productUnitCode));
        var orderNo = $(tr).find($('td.detailOrderNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderNo]").val(orderNo));
        var revisionNo = $(tr).find($('td.detailRevisionNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][revisionNo]").val(revisionNo));
        var subjectCode = $(tr).find($('td.detailStockSubjectCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][subjectCode]").val(subjectCode));
        var stockItemCode = $(tr).find($('td.detailStockItemCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][stockItemCode]").val(stockItemCode));
        var monetaryUnitCode = $(tr).find($('td.detailMonetaryUnitCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][monetaryUnitCode]").val(monetaryUnitCode));
        var customerCompanyCode = $(tr).find($('td.detailCustomerCompanyCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][customerCompanyCode]").val(customerCompanyCode));

    }
    function getCheckedRows() {
        var selected = getSelectedRows();
        var cnt = $(selected).length;
        if (cnt === 0) {
            console.log("なにもしない");
            return false;
        }
        if (cnt > 1) {
            //console.log("なにもしない。ただし後で処理が追加になるかもしれない。");
            alert("移動対象は1行のみ選択してください");
            return false;
        }
        return true;
    }
    function getSelectedRows() {
        return $('#EditTableBody .selected');
    }
    function executeSort(mode) {
        var row = $('#EditTableBody').children('.selected');
        switch (mode) {
            case 0:
                $('#EditTableBody tr:first').before($(row));
                break;
            case 1:
                var rowPreview = $(row).prev('tr');
                if (row.prev.length) {
                    row.insertBefore(rowPreview);
                    var td = rowPreview.children('td[name="rownum"]')
                }
                break;
            case 2:
                var rowNext = $(row).next('tr');
                if (rowNext.length) {
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
    function changeRowNum() {
        $('#EditTableBody').find('[name="rownum"]').each(function (idx) {
            $(this).html(idx + 1);
        });
    }
    function validationCheck2() {
        var result = true;
        if (!getSelectedRows().length) {
            // 発注明細行が1件も選択されていない場合
            alert("発注確定する明細行が選択されていません。");
            result = false;
        }
        var expirationDate = $('input[name="dtmExpirationDate"]').val();
        if (!expirationDate) {
            // 発注有効期限日が未入力の場合
            alert("発注有効期限日が指定されていません。");
            result = false;
        }
        if (!expirationDate.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/g)) {
            // 発注有効期限日が正規表現「^\d{4}\/\d{1,2}\/\d{1,2}$」に一致しない場合
            alert("発注有効期限日の書式に誤りがあります。");
            result = false;
        }
        if (!isDate(expirationDate)) {
            // 発注有効期限日が存在しない日付(2/31等)の場合
            alert("発注有効期限日に存在しない日付が指定されました。");
            result = false;
        }
        var countryCode = $('input[name="lngCountryCode"]').val();
        if (countryCode !== '81') {
            var selected = $('select[name="optPayCondition"]').children('option:selected').val();
            if (selected === '0') {
                // 仕入先のm_company.lngcountrycodeが「81(日本)」以外かつ支払条件が未選択の場合
                alert('仕入先が海外の場合、支払い条件を指定してください。');
                result = false;
            }
        }
        var locationCode = $('input[name="lngLocationCode"]').val();
        if (!locationCode) {
            // 納品場所が未入力の場合
            alert('納品場所が指定されていません。');
            result = false;
        }
        var details = getSelectedRows();
        /* 運搬方法は"-"も可とする
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
        */
        return result;
    }
    function isDate(d) {
        if (d == "") { return false; }
        if (!d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) { return false; }

        var date = new Date(d);
        if (date.getFullYear() != d.split("/")[0]
            || date.getMonth() != d.split("/")[1] - 1
            || date.getDate() != d.split("/")[2]
        ) {
            return false;
        }
        return true;
    }
    function getUpdateDetail() {
        var result = [];
        $.each(getSelectedRows(), function (i, tr) {
            var param = {
                lngOrderDetailNo: $(tr).children('.detailOrderDetailNo').text(),
                lngSortKey: i + 1,
                lngDeliveryMethodCode: $(tr).find('[name="optDelivery"] option:selected').val(),
                strDeliveryMethodName: $(tr).find('[name="optDelivery"] option:selected').text(),
                lngProductUnitCode: $(tr).find('.detailProductUnitCode').text(),
                lngOrderNo: $(tr).find('.detailOrderNo').text(),
                lngRevisionNo: $(tr).find('.detailRevisionNo').text(),
                lngStockSubjectCode: $(tr).find('.detailStockSubjectCode').text(),
                strStockItemCode: $(tr).find('.detailStockItemCode').text(),
                lngMonetaryUnitCode: $(tr).find('.detailMonetaryUnitCode').text(),
                lngCustomerCompanyCode: $(tr).find('.detailCustomerCompanyCode').text(),
                curProductPrice: $(tr).find('.detailProductPrice').text(),
                lngProductQuantity: $(tr).find('.detailProductQuantity').text(),
                curSubtotalPrice: $(tr).find('.detailSubtotalPrice').text(),
                dtmDeliveryDate: $(tr).find('.detailDeliveryDate').text(),
                strDetailNote: $(tr).find('.detailNote').text(),
            };
            result.push(param);
        });
        return result;
    }


    // events
    $('#DetailTableBodyAllCheck').on('change', function () {
        $('input[name="edit"]').prop('checked', this.checked);
    });
    $('#FixBt').on('click', function () {
        //console.log("ぽちっとな");
        var cb = $('#DetailTableBody').find('input[name="edit"]');
        var checked = false;
        var trArray = [];
        $.each(cb, function (i, v) {
            if ($(v).prop('checked')) {
                checked = true;
                trArray.push($(v).parent().parent());
            }
        });
        if (!checked) {
            alert("発注確定する明細行が選択されていません。");
            return false;
        }

        $.each($(trArray), function (i, v) {
            setEdit($(v));
        });

        var rows = $('#EditTable tbody tr');
        var lastSelectedRow;
        /* Create 'click' event handler for rows */
        rows.on('click', function (e) {
            /* Get current row */
            var row = $(this);

            /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
             * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
             * 'Shift' => is represented by 'e.shiftKey' */
            if (e.ctrlKey || e.metaKey) {
                /* If pressed highlight the other row that was clicked */
                $("#EditTable tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
 
            } else if (e.shiftKey) {
                /* If pressed highlight the other row that was clicked */
                var indexes = [lastSelectedRow.index(), row.index()];
                indexes.sort(function (a, b) {
                    return a - b;
                });
                for (var i = indexes[0]; i <= indexes[1]; i++) {
                    $("#EditTable tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
                }
            } else {
                /* Otherwise just highlight one row and clean others */
                $("#EditTable tbody tr").css("background-color", "#ffffff");
                $("#EditTable tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
                lastSelectedRow = row;
            }

        });

        /* This 'event' is used just to avoid that the table text 
         * gets selected (just for styling). 
         * For example, when pressing 'Shift' keyboard key and clicking 
         * (without this 'event') the text of the 'table' will be selected.
         * You can remove it if you want, I just tested this in 
         * Chrome v30.0.1599.69 */
        $(document).bind('selectstart dragstart', function (e) {
            e.preventDefault(); return false;
        });
    });
    /*
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
    */
    $('#selectup').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(0);
    });
    $('#selectup1').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(1);
    });
    $('#selectdown1').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(2);
    });
    $('#selectdown').on('click', function () {
        var selected = getCheckedRows();
        if (!selected) { return false; }
        executeSort(3);
    });
    $("#AddBt").on('click', function () {
        // var selected = getSelectedRows();
        // alert(selected);
        // if (!selected.length) { return false; }
        // $(selected).remove();

        $("#EditTableBody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            }
        });

        changeRowNum();
    });
    $('#AlladdBt').on('click', function () {
        $('#EditTableBody').empty();
    });
    $('#FixEntryBtn').on('click', function () {
        if (!validationCheck2()) {
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '/po/confirm/index.php?strSessionID=' + $('input[name="strSessionID"]').val(),
            data: {
                strSessionID: $('input[name="strSessionID"]').val(),
                lngOrderNo: $('input[name="lngOrderNo"]').val(),
                strMode: $('input[name="strMode"]').val(),
                lngRevisionNo: $('input[name="lngRevisionNo"]').val(),
                lngPayConditionCode: $('select[name="lngPayConditionCode"]').val(),
                dtmExpirationDate: $('input[name="dtmExpirationDate"]').val(),
                lngLocationCode: $('input[name="lngLocationCode"]').val(),
                strNote: $('input[name="strNote"]').val(),
                strProductCode: $('input[name="strProductCode"]').val(),
                strReviseCode: $('input[name="strReviseCode"]').val(),
                aryDetail: getUpdateDetail(),
            },
            async: true,
        }).done(function (data) {
            console.log("done");
            console.log(data);
            //$('html').html(data);
            document.write(data);
        }).fail(function (error) {
            console.log("fail");
            console.log(error);
        });
    });
});