//
// regist.js
//
jQuery(function ($) {
    // ウィンドウクローズ処理
    window.onbeforeunload = unLock;

    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));
    // テーブルBの幅をリセットする
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));
    resetTableADisplayStyle();
    resetTableBDisplayStyle();
    // テーブル行クリックイベントの設定
    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
    // テーブル行クリックイベントの設定
    selectRow('', $("#tableB_no"), $("#tableB"), '');
    
    resetTableRowid($('#tableB_no'));

    tableBSort();

    function tableBSort() {
        var sortval = 0;
        $('#tableB_head thead tr th').on('click', function () {
            var sortkey = $(this)[0].cellIndex;
            console.log(sortkey);
            if (sortval == 1) {
                sortval = 0;
            } else {
                sortval = 1;
            }
            var r = $('#tableB').tablesorter();
            r.trigger('sorton', [[[(sortkey), sortval]]]);
        });
    }

    function addHiddenValue(i, tr) {
        var hidden = $('#SegHidden');
        // var orderNo = $(tr).find($('td.detailOrderCode')).text();
        // $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderNo]").val(orderNo));
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

    function validationCheck2() {
        var result = true;
        if (!$('#tableB tbody tr').length) {
            // 発注明細行が1件も選択されていない場合
            alert("発注確定する明細行が選択されていません。");
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
        /* 運搬方法は"-"も可とする
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
        $('#tableB tbody tr').each(function (i, tr) {
            var param = {
                lngOrderDetailNo: $(tr).children('.detailOrderDetailNo').text(),
                lngSortKey: i + 1,
                lngDeliveryMethodCode: $(tr).find('[name="optDelivery"] option:selected').val(),
                strDeliveryMethodName: $(tr).find('[name="optDelivery"] option:selected').text(),                
                lngProductUnitCode: $(tr).find('[name="optProductUnit"] option:selected').val(),
                strProductUnitName: $(tr).find('[name="optProductUnit"] option:selected').text(),
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
                strDetailNote: $(tr).find('.detailNote').find('input:text').val(),
                strMoldNo: $(tr).find('.detailStrMoldNo').text(),
            };
            result.push(param);
        });
        return result;
    }


    // チェックボックスの切り替え処理のバインド
    setAllCheckClickEvent($("#allChecked"), $("#tableA"), $("#tableA_chkbox"));

    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));

    // 行を一つ上に移動するボタン
    $('img.rowup').click(function () {
        rowUp($("#tableB"), $("#tableB_no"));
    });

    // 行を一つ下に移動するボタン
    $('img.rowdown').click(function () {
        rowDown($("#tableB"), $("#tableB_no"));
    });

    // 行を一番上に移動する
    $('img.rowtop').click(function () {
        rowTop($("#tableB"), $("#tableB_no"));
    });

    // 行を一番下に移動する
    $('img.rowbottom').click(function () {
        rowBottom($("#tableB"), $("#tableB_no"));
    });

    // 追加ボタンのイベント
    $('#add').on('click', function () {
        var isChecked = false;
        $('input[type="checkbox"]')
            .each(function () {
                if (this.checked) {
                    if ($(this).attr('name') != "allSel") {
                        isChecked = true;
                        return false;
                    }
                }
            });

        // 行が選択されてない場合
        if (!isChecked) {
            alert("発注確定する明細データが指定されていません。");
            return;
        }

        $('#tableA_chkbox tbody tr').each(function (i, e) {
            var rownum = i + 1;
            var chkbox = $(this).find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                // tableBの追加
                $("#tableB tbody").append('<tr>' + $('#tableA tbody tr:nth-child(' + rownum + ')').html() + '</tr>');
                var no = $("#tableB_no tbody").find('tr').length + 1;
                $("#tableB_no tbody").append('<tr><td>' + no + '</td></tr>');
            }

        });

        // tableBのリセット
        for (var i = $('#tableA_chkbox tbody tr').length; i > 0; i--) {
            var row = $('#tableA_chkbox tbody tr:nth-child(' + i + ')');
            var chkbox = row.find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                row.remove();
                $('#tableA tbody tr:nth-child(' + i + ')').remove();
            }
        }

        resetTableRowid($('#tableB_no'));

        // テーブルBの幅をリセットする
        resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

        resetTableBDisplayStyle();

        // テーブル行クリックイベントの設定
        selectRow('', $("#tableB_no"), $("#tableB"), '');

        scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));

        $("#tableA").trigger("update");
        $("#tableB").trigger("update");

        tableBSort();
    });

    // 全削除ボタンのイベント
    $('#alldelete').on('click', function () {

        // テーブルBのデータをすべてテーブルAに移動する
        deleteAllRows($("#tableA"), $("#tableA_head"), $("#tableA_chkbox"), $("#tableA_chkbox_head"), $("#tableB"), $("#tableB_no"), $("#allChecked"), '.detailOrderDetailNo');

        $("#tableA_head").trigger("update");

        $("#tableA").trigger("update");

        $("#tableB_no").trigger("update");

        $("#tableB").trigger("update");

        resetTableADisplayStyle();

        tableBSort();
    });

    // 削除ボタンのイベント
    $('#delete').on('click', function () {

        // テーブルBの選択されたデータをテーブルAに移動する
        deleteRows($("#tableA"), $("#tableA_head"), $("#tableA_chkbox"), $("#tableA_chkbox_head"), $("#tableB"), $("#tableB_no"), $("#allChecked"), '.detailOrderDetailNo');

        $("#tableA_head").trigger("update");

        $("#tableA").trigger("update");

        $("#tableB_no").trigger("update");

        $("#tableB").trigger("update");

        resetTableADisplayStyle();

        tableBSort();
    });

    $('#decideRegist').on('click', function () {
        if (!validationCheck2()) {
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '/po/confirm/index.php?strSessionID=' + $('input[type="hidden"][name="strSessionID"]').val(),
            data: {
                strSessionID: $('input[type="hidden"][name="strSessionID"]').val(),
                strMode: $('input[name="strMode"]').val(),
                lngPayConditionCode: $('select[name="lngPayConditionCode"]').val(),
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

            var w = window.open("", 'Decide Confirm', "width=1011, height=600, scrollbars=yes, resizable=yes");
            w.document.open();
            w.document.write(data);
            w.document.close();
        }).fail(function (error) {
            console.log("fail");
            console.log(error);
        });
    });
    $('#clear').on('click', function () {
        window.location.reload();
    });
    function unLock() {
        $.ajax({
            url: '/po/regist/index.php',
            type: 'POST',
            data: {
                'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
                'strMode': 'cancel',
            },
            timeout: 10000
        })
            .done(function (response) {
            })
            .fail(function (response) {
            });

        window.opener.location.reload();
    }

    function resetTableADisplayStyle() {
        $("#tableA tbody tr").each(function (i, e) {
            $(this).find(".detailProductUnitCode").find('select').prop('disabled', true);
            $(this).find(".detailDeliveryMethodCode").find('select').prop('disabled', true);
            $(this).find(".detailNote").find('input:text').prop('disabled', true);
        });
    }

    function resetTableBDisplayStyle() {
        $("#tableB tbody tr").each(function (i, e) {
            $(this).find(".detailProductUnitCode").find('select').prop('disabled', false);
            $(this).find(".detailDeliveryMethodCode").find('select').prop('disabled', false);
            $(this).find(".detailNote").find('input:text').prop('disabled', false);
        });
    }
});