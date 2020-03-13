//
// regist2.js
//
jQuery(function ($) {

    window.onbeforeunload = unLock;
    // チェックボックスの切り替え処理のバインド
    setAllCheckClickEvent($("#allChecked"), $("#tableA"), $("#tableA_chkbox"));

    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));
    
    // テーブルAの幅をリセットする
    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));

    // テーブルBの幅をリセットする
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

    resetTableADisplayStyle();

    resetTableBDisplayStyle();

    // テーブル行クリックイベントの設定
    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));

    // テーブル行クリックイベントの設定
    selectRow('', $("#tableB_no"), $("#tableB"), '');

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

    function validationCheck() {
        var result = true;
        //        var selectedRows = getSelectedRows();
        var selectedRows = $('#tableB tbody tr');
        if (!selectedRows.length) {
            alert("発注書修正をおこなう明細行が選択されていません。");
            return false;
        }

        return result;
    }

    function validateDelivery(rows) {
        var result = true;
        var messages = [];
        $.each(rows, function (i, row) {
            // console.log($(row)[0]);
            var deliveryMethod = $(row).find('select[name="lngdeliverymethodcode"]').val();
            if (deliveryMethod === '0') {
                messages.push((i + 1) + "行目の運搬方法が指定されていません。");
                result = false;
            }
        });

        if (!result) {
            alert(messages.join("\n"));
        }
        return result;
    }
    function validatePayCondition() {
        var result = true;
        var payConditionOrign = $('input[name="lngPayConditionCodeOrign"]').val();
        var payCondition = $('select[name="lngPayConditionCode"]').val();
        if (payConditionOrign === payConditionOrign && payCondition === "1") {
            result = confirm("支払い方法はT/Tを推奨しますが、本当にL/Cに変更しますか?(変更後は戻せません)");
        }

        return result;
    }
    function getUpdateDetail(lngPurchaseOrderNo) {
        var result = [];
        //        $.each(getSelectedRows(), function(i, tr){
        $('#tableB tbody tr').each(function (i, tr) {
            var param = {
                lngPurchaseOrderNo: lngPurchaseOrderNo,
                lngSortKey: $(tr).find('td[name="rownum"]').text(),
                lngPurchaseOrderDetailNo: $(tr).find('.detailPurchaseorderDetailNo').text(),
                lngStockSubjectCode: $(tr).find('.detailStockSubjectCode').text().split("]")[0].replace("[", ""),
                lngStockItemCode: $(tr).find('.detailStockItemCode').text().split("]")[0].replace("[", ""),
                lngDeliveryMethodCode: $(tr).find('[name="lngdeliverymethodcode"] option:selected').val(),
                strDeliveryMethodName: $(tr).find('[name="lngdeliverymethodcode"] option:selected').text().trim(),
                lngProductUnitCode: $(tr).find('[name="lngproductunitcode"] option:selected').val(),
                strProductUnitName: $(tr).find('[name="lngproductunitcode"] option:selected').text().trim(),
                curProductPrice: $(tr).find('.detailProductPrice').text().split(" ")[1],
                lngProductQuantity: $(tr).find('.detailProductQuantity').text(),
                curSubtotalPrice: $(tr).find('.detailSubtotalPrice').text().split(" ")[1],
                dtmDeliveryDate: $(tr).find('.detailDeliveryDate').text(),
                strDetailNote: $(tr).find('.detailDetailNote').find('input:text').val(),
                lngOrderNo: $(tr).find('input[name="lngorderno"]').val(),
                lngOrderRevisionNo: $(tr).find('input[name="lngorderrevisionno"]').val(),
                lngOrderDetailNo: $(tr).find('input[name="lngorderdetailno"]').val(),
            };
            result.push(param);
        });

        return result;
    }

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

    $(document).on('click', '#btnClose', function () {
        window.open('about:blank', '_self').close();
    });
    $('#decideRegist').on('click', function () {
        // console.log('確定登録ボタンクリック');
        if (!validationCheck()) {
            console.log("バリデーションエラーのため処理継続中止。")
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/po/confirm2/index.php?strSessionID=' + $('input[name="strSessionID"]').val(),
            scriptCharset: 'UTF-8',
            data: {
                strSessionID: $('input[name="strSessionID"]').val(),
                strMode: 'renew',
                lngPurchaseOrderNo: $('input[name="lngPurchaseOrderNo"]').val(),
                lngRevisionNo: $('input[name="lngRevisionNo"]').val(),
                lngPayConditionCode: $('select[name="lngPayConditionCode"]').children('option:selected').val(),
                strPayConditionName: $('select[name="lngPayConditionCode"]').children('option:selected').text(),
                lngLocationCode: $('input[name="lngLocationCode"]').val(),
                strLocationName: $('input[name="strLocationName"]').val(),
                strNote: $('input[name="strNote"]').val(),
                strOrderCode: $('input[name="strOrderCode"]').val(),
                lngMonetaryUnitCode: $('select[name="lngMonetaryUnitCode"]').children('option:selected').val(),
                strMonetaryUnitName: $('select[name="lngMonetaryUnitCode"]').children('option:selected').text(),
                lngCustomerCompanyCode: $('input[name="strCustomerCode"]').val(),
                strProductCode: $('input[name="strProductCode"]').val(),
                strProductName: $('input[name="strProductName"]').val(),


                aryDetail: getUpdateDetail($('input[name="lngPurchaseOrderNo"]').val()),
            }
        }).done(function (data) {
            console.log("done");

            var w = window.open("", 'Renew Confirm', "width=1011, height=600, scrollbars=yes, resizable=yes");
            w.document.open();
            w.document.write(data);
            w.document.close();

        }).fail(function (error) {
            console.log("fail");
            console.log(error);
        });
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

});

function resetTableADisplayStyle() {
    $("#tableA tbody tr").each(function (i, e) {
        $(this).find(".detailProductUnitCode").find('select').prop('disabled', true);
        $(this).find(".detailDeliveryMethodCode").find('select').prop('disabled', true);
        $(this).find(".detailDetailNote").find('input:text').prop('disabled', true);
    });
}

function resetTableBDisplayStyle() {
    $("#tableB tbody tr").each(function (i, e) {
        $(this).find(".detailProductUnitCode").find('select').prop('disabled', false);
        $(this).find(".detailDeliveryMethodCode").find('select').prop('disabled', false);
        $(this).find(".detailDetailNote").find('input:text').prop('disabled', false);
    });
}
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

function unLock() {
    $.ajax({
        url: '/po/regist/modify.php',
        type: 'post',
        // dataType: 'json',
        type: 'POST',
        //            async: false,
        data: {
            'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
            'strMode': 'cancel',
        }
    })
        .done(function (response) {
        })
        .fail(function (response) {
        });
}
