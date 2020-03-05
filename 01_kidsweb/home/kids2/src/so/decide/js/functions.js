(function () {
    resetTableADisplayStyle();
    // テーブルAの幅をリセットする
    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));
    // テーブルBの幅をリセットする
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));
    // テーブルAの行クリックイベントの設定
    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
    // テーブルB行イベントの追加
    selectRow("", $("#tableB_no"), $("#tableB"), "");

    if ($('input[name="strGoodsCode"]').val() != "") {
        $('input[name="strGoodsCode"]').attr('readonly', true);
        $('input[name="strGoodsCode"]').removeClass('TxtStyle05L');
        $('input[name="strGoodsCode"]').addClass('disTxt05L');
    } else {
        $('input[name="strGoodsCode"]').attr('readonly', false);
        $('input[name="strGoodsCode"]').removeClass('disTxt05L');
        $('input[name="strGoodsCode"]').addClass('TxtStyle05L');
    }

    var lockId = "lockId";
    // ウィンドウクローズ処理
    window.onbeforeunload = unLock;

    // チェックボックスの切り替え処理のバインド
    setAllCheckClickEvent($("#allChecked"), $("#tableA"), $("#tableA_chkbox"));

    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));

    // 追加ボタンのイベント
    $('img.add').on('click', function () {
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
            alert("受注確定する明細データが指定されていません。");
            return;
        }

        $('#tableA_chkbox tbody tr').each(function (i, e) {
            var rownum = i + 1;
            var chkbox = $(this).find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                var lngreceivedetailnoA = $('#tableA tbody tr:nth-child(' + rownum + ')').find('#lngreceivedetailno').text();
                var addObj = true;
                $('#tableB tbody tr').each(function (i, e) {
                    var lngreceivedetailnoB = $(this).find('#lngreceivedetailno').text();
                    if (lngreceivedetailnoA == lngreceivedetailnoB) {
                        addObj = false;
                        return false;
                    }
                });
                if (addObj) {
                    // tableBの追加
                    $("#tableB tbody").append('<tr>' + $('#tableA tbody tr:nth-child(' + rownum + ')').html() + '</tr>');
                    var no = $("#tableB_no tbody").find('tr').length + 1;
                    $("#tableB_no tbody").append('<tr><td>' + no + '</td></tr>');

                    var lasttr = $("#tableB").find('tr').last();
                    lasttr.find('td:nth-child(1)').css('display', 'none');
                }

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


        resetTableBDisplayStyle();

        resetTableRowid($('#tableB_no'));
        // テーブルBの幅をリセットする
        resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));
        // テーブルB行イベントの追加
        selectRow("", $("#tableB_no"), $("#tableB"), "");

        selectChange();

        unitQuantityChange();

        scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));

        // チェックボックスクリックイベントの設定
        setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));

    });

    // 全削除ボタンのイベント
    $('img.alldelete').on('click', function () {

        // テーブルBのデータをすべてテーブルAに移動する
        deleteAllRows($("#tableA"), $("#tableA_head"), $("#tableA_chkbox"), $("#tableA_chkbox_head"), $("#tableB"), $("#tableB_no"), $("#allChecked"), '#lngreceivedetailno');

        resetTableRowid($("#tableA"));

        resetTableADisplayStyle();

        $("#tableA_head").trigger("update");

        $("#tableA").trigger("update");
    });

    // 削除ボタンのイベント
    $('img.delete').on('click', function () {
        // テーブルBの選択されたデータをテーブルAに移動する
        deleteRows($("#tableA"), $("#tableA_head"), $("#tableA_chkbox"), $("#tableA_chkbox_head"), $("#tableB"), $("#tableB_no"), $("#allChecked"), '#lngreceivedetailno');

        resetTableRowid($("#tableA"));

        $("#tableA_head").trigger("update");

        $("#tableA").trigger("update");

        resetTableADisplayStyle();
    });

    // 検索条件変更ボタンのイベント
    $('img.search').on('click', function () {

        // 画面操作を無効する
        lockScreen(lockId);

        url = '/so/decide/search_init.php';
        sessionID = 'strSessionID=' + $('input[type="hidden"][name="strSessionID"]').val();
        param = 'strproductcode=' + $('input[name="strProductCode"]').val();
        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + param, '_blank', 'width=730, height=768, resizable=yes, scrollbars=yes, menubar=no');
    });

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

    // 確定登録イベント
    $('img.decideRegist').on('click', function () {
        // 顧客品番が空の場合、エラー
        if ($('input[name="strGoodsCode"]').val() == "") {
            alert("顧客品番が入力されていません。");
            return;
        }
        var params = new Array();
        var len = 0;
        var isError = false;
        $("#table_decide_body tr").each(function (i, e) {
            len += 1;
            // // 受注番号
            var strReceiveCode = $(this).find('#strreceivecode').text();
            // 受注明細番号
            var lngReceiveDetailNo = $(this).find('#lngreceivedetailno').text();
            console.log(lngReceiveDetailNo);
            // 顧客受注番号
            var strCustomerReceiveCode = $(this).find('#strcustomerreceivecode').find('input:text').val();
console.log(strCustomerReceiveCode);
            if (strCustomerReceiveCode == "") {
                alert(len + "行目の顧客受注番号が入力されていません。");
                isError = true;
                return false;
            }
            var lngproductquantity = $(this).find('#lngproductquantity_re').text();
            if (lngproductquantity.indexOf('.') != -1) {
                alert(len + "行目の見積原価計算書上の数量が入数で割り切れません。");
                isError = true;
                return false;
            }
            var lngunitquantity = $(this).find('#lngunitquantity').find('input:text').val();
            if (lngunitquantity != undefined) {                
                if (!lngunitquantity.match(/^[0-9]+$/)) {
                    alert(len + "行目の入数は半角数字で入力してください。");
                    isError = true;
                    return false;
                }
            }

            var strDetailNote = $(this).find('#strdetailnote').find('input:text').val();
            var strProductUnitName = $(this).find('#lngproductunitcode').find('select option:selected').text();
            var lngProductUnitCode = $(this).find('#lngproductunitcode').find('select option:selected').val();
            var lngunitquantity = 0;
            if ($(this).find('#lngunitquantity').find('input:text').length != 0) {
                lngunitquantity = $(this).find('#lngunitquantity').find('input:text').val();
            } else {
                lngunitquantity = $(this).find('#lngunitquantity').text();
            }

            params[len - 1] = {
                "strReceiveCode": strReceiveCode,
                "lngReceiveDetailNo": lngReceiveDetailNo,
                "strCustomerReceiveCode": strCustomerReceiveCode,
                "strProductCode": $(this).find('#strproductname').text(),
                "strGoodsCode": $('input[name="strGoodsCode"]').val(),
                "dtmDeliveryDate": $(this).find('#dtmdeliverydate').text(),
                "lngSalesClassCode": $(this).find('#lngsalesclasscode').text(),
                "curProductPrice": $(this).find('#curproductprice').text(),
                "strProductUnitName": strProductUnitName,
                "lngUnitQuantity": lngunitquantity,
                "lngProductQuantity": $(this).find('#lngproductquantity_re').text(),
                "curSubtotalPrice": $(this).find('#cursubtotalprice').text(),
                "strDetailNote": strDetailNote,
                "strCompanyDisplayCode": $(this).find('#strcompanydisplaycode').text(),
                "lngReceiveNo": $(this).find('#lngreceiveno').text(),
                "lngRevisionNo": $(this).find('#lngrevisionno').text(),
                "strReviseCode": $(this).find('#strrevisecode').text(),
                "strProductCode_product": $(this).find('#strproductcode').text(),
                "lngProductUnitCode": lngProductUnitCode
            };
        });

        if (len == 0) {
            alert("受注確定する明細行が選択されていません。");
            isError = true;
            return false;
        }

        if (isError) {
            return;
        }
        // 画面操作を無効する
        // lockScreen("lockId");
        var strGoodsCode = "";
        if (!$('input[name="strGoodsCode"]').attr('readonly')) {
            strGoodsCode = $('input[name="strGoodsCode"]').val();
        }

        // リクエスト送信
        $.ajax({
            url: '/so/decide/decide_confirm.php',
            type: 'post',
            // dataType: 'json',
            type: 'POST',
            data: {
                'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
                'detailData': params,
                'lngProductNo': $('input[name="lngProductNo"]').val(),
                'lngProductRevisionNo': $('input[name="lngProductRevisionNo"]').val(),
                'strReviseCode': $('input[name="strReviseCode"]').val(),
                'strGoodsCode': strGoodsCode
            }
        })
            .done(function (response) {
                var w = window.open("", 'Decide Confirm', "width=1011, height=600, scrollbars=yes, resizable=yes");
                w.document.open();
                w.document.write(response);
                w.document.close();
            })
            .fail(function (response) {
                console.log("処理結果：" + JSON.stringify(response));
                alert("fail");

                // 画面操作を有効にする
                unlockScreen("lockId");
            });
    });

})();


selectChange();

unitQuantityChange();

function selectChange() {
    // 単位セレクトボックスの変更イベント
    $('select').on({
        'change': function () {
            var lngcartonquantity = $(this).parent().parent().find('#lngcartonquantity').text();
            var lngproductquantity = $(this).parent().parent().find('#lngproductquantity').text();
            var val = $(this).val();
            // 入数・数量計算
            var lngunitquantitynew = 1;
            var lngproductquantitynew = lngproductquantity / lngunitquantitynew;
            // 単位が[c/t]の場合、
            if (val == 2) {
                // 入数 = カートン入数
                lngunitquantitynew = lngcartonquantity;
                // 数量 = 製品数量/カートン入数
                lngproductquantitynew = lngproductquantity / lngunitquantitynew;
                var html = '<input type="text" name="unitQuantity" class="form-control form-control-sm txt-kids" style="width:90px;" value="' + lngunitquantitynew + '">';
                $(this).parent().parent().find('#lngunitquantity').html(html);
            } else {
                $(this).parent().parent().find('#lngunitquantity').text(lngunitquantitynew);
            }
            $(this).parent().parent().find('#lngproductquantity_re').text(lngproductquantitynew);

            // テーブルBの幅をリセットする
            resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

            unitQuantityChange();
        }
    });
}

function unitQuantityChange() {
    $('input[name="unitQuantity"]').on({
        'change': function () {
            var val = $(this).val();
            if (!val.match(/^[0-9]+$/)) {
                alert("入数は半角数字で入力してください。");
                return false;
            }
            var lngproductquantity = $(this).parent().parent().find('#lngproductquantity').text();
            
            console.log(lngproductquantity);
            // 数量 = 製品数量/カートン入数
            var lngproductquantitynew = lngproductquantity / val;
            $(this).parent().parent().find('#lngproductquantity_re').text(lngproductquantitynew);
        }
    });
}


/**
 * 文字変換（nullの場合、""に変換）
 * @param {} str 
 */
function convertNull(str) {
    if (str != "" && str != undefined && str != "null") {
        return str;
    } else {
        return "";
    }
}

/**
 * 文字変換（nullの場合、"0"に変換）
 * @param {} str 
 */
function convertNullToZero(str) {
    if (str != "" && str != undefined && str != "null") {
        return str;
    } else {
        return 0;
    }
}

function money_format(lngmonetaryunitcode, strmonetaryunitsign, price) {
    if (lngmonetaryunitcode == 1) {
        return '\xA5' + " " + price;
    } else {
        return strmonetaryunitsign + " " + price;
    }
}
/*
 * 画面操作を無効にする
 */
function lockScreen(id) {

    /*
     * 現在画面を覆い隠すためのDIVタグを作成する
     */
    var divTag = $('<div />').attr("id", id);

    /*
     * スタイルを設定
     */
    divTag.css("z-index", "999")
        .css("position", "absolute")
        .css("top", "0px")
        .css("left", "0px")
        .css("right", "0px")
        .css("bottom", "0px")
        .css("background-color", "gray")
        .css("opacity", "0.8");

    /*
     * BODYタグに作成したDIVタグを追加
     */
    $('body').append(divTag);
}

/*
 * 画面操作無効を解除する
 */
function unlockScreen(id) {
    /*
     * 画面を覆っているタグを削除する
     */
    $("#" + id).remove();
}

function resetTableADisplayStyle() {
    $("#tableA tbody tr").each(function (i, e) {
        $(this).find("td:nth-child(1)").css("display", "");
        $(this).find("#strcustomerreceivecode").find('input:text').prop('disabled', true);
        $(this).find("#lngproductunitcode").find('select').prop('disabled', true);
        $(this).find("#lngunitquantity").find('input:text').prop('disabled', true);
        $(this).find("#strdetailnote").find('input:text').prop('disabled', true);
    });
}

function resetTableBDisplayStyle() {
    $("#tableB tbody tr").each(function (i, e) {
        $(this).find("td:nth-child(1)").css("display", "none");
        $(this).find("#strcustomerreceivecode").find('input:text').prop('disabled', false);
        $(this).find("#lngproductunitcode").find('select').prop('disabled', false);
        $(this).find("#lngunitquantity").find('input:text').prop('disabled', false);
        $(this).find("#strdetailnote").find('input:text').prop('disabled', false);
    });
}

function unLock() {
    $.ajax({
        url: '/so/decide/index.php',
        type: 'post',
        type: 'POST',
        data: {
            'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
            'mode': 'cancel',
        }
    })
        .done(function (response) {
        })
        .fail(function (response) {
        });

}