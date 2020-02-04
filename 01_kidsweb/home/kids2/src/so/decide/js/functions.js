(function () {
    resetTableADisplayStyle();
    resetTableAWidth();
    resetTableBWidth();
    selectRow($("#tbl_detail_chkbox"), $("#tbl_detail"));
    selectRow($("#tbl_decide_no"), $("#tbl_decide_body"));

    if ($('input[name="strGoodsCode"]').val() != "") {
        $('input[name="strGoodsCode"]').attr('readonly', true);
    } else {
        $('input[name="strGoodsCode"]').attr('readonly', false);
    }

    var lockId = "lockId";
    // ウィンドウクローズ処理
    window.onbeforeunload = unLock;

    // チェックボックスの切り替え処理のバインド
    $('input[type="checkbox"][name="allSel"]').on({
        'click': function () {
            var status = this.checked;
            $('input[type="checkbox"]')
                .each(function () {
                    this.checked = status;
                    if (status) {
                        $("#tbl_detail_chkbox tbody tr").css("background-color", "#bbbbbb");
                        $("#tbl_detail tbody tr").css("background-color", "#bbbbbb");
                    } else {

                        $("#tbl_detail_chkbox tbody tr").css("background-color", "#ffffff");
                        $("#tbl_detail tbody tr").css("background-color", "#ffffff");
                    }
                });
        }
    });

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

        $('#tbl_detail_chkbox tbody tr').each(function (i, e) {
            var rownum = i + 1;
            var chkbox = $(this).find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                var lngreceivedetailnoA = $('#tbl_detail tbody tr:nth-child(' + rownum + ')').find('#lngreceivedetailno').text();
                var addObj = true;
                $('#tbl_decide_body tbody tr').each(function (i, e) {
                    var lngreceivedetailnoB = $(this).find('#lngreceivedetailno').text();
                    if (lngreceivedetailnoA == lngreceivedetailnoB) {
                        addObj = false;
                        return false;
                    }
                });
                if (addObj) {
                    // tableBの追加
                    $("#tbl_decide_body tbody").append('<tr>' + $('#tbl_detail tbody tr:nth-child(' + rownum + ')').html() + '</tr>');
                    var no = $("#tbl_decide_no tbody").find('tr').length + 1;
                    $("#tbl_decide_no tbody").append('<tr><td>' + no + '</td></tr>');

                    var lasttr = $("#tbl_decide_body").find('tr').last();
                    lasttr.find('td:nth-child(1)').css('display', 'none');
                }

            }
        });

        // tableBのリセット
        for (var i = $('#tbl_detail_chkbox tbody tr').length; i > 0; i--) {
            var row = $('#tbl_detail_chkbox tbody tr:nth-child(' + i + ')');
            var chkbox = row.find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                row.remove();
                $('#tbl_detail tbody tr:nth-child(' + i + ')').remove();
            }
        }

        resetTableBRowid();

        resetTableBWidth();
        
        resetTableBDisplayStyle();

        selectRow($("#tbl_decide_no"), $("#tbl_decide_body"));

        selectChange();

        scanAllCheckbox();
    });

    // 全削除ボタンのイベント
    $('img.alldelete').on('click', function () {

        $("#table_decide_body tr").each(function (i, e) {
            removeTableBToTableA($(this));
        });

        $("#table_decide_no").empty();

        resetTableARowid();

        resetTableAWidth();
        
        resetTableADisplayStyle();

        resetTableBWidth();

        scanAllCheckbox();
        
        selectRow($("#tbl_detail_chkbox"), $("#tbl_detail"));
    });

    // 削除ボタンのイベント
    $('img.delete').on('click', function () {
        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            }
        });
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                removeTableBToTableA($(this));
            }
        });

        resetTableARowid();

        resetTableBRowid();

        resetTableAWidth();
        
        resetTableADisplayStyle();

        resetTableBWidth();

        scanAllCheckbox();
        
        selectRow($("#tbl_detail_chkbox"), $("#tbl_detail"));
    });

    function removeTableBToTableA(tableBRow) {
        var trhtml = tableBRow.html();
        var detailnoB = tableBRow.find('#lngreceivedetailno').text();
        var rownum = 0;
        $("#tbl_detail tbody tr").each(function (i, e) {
            var detailnoA = $(this).find('#lngreceivedetailno').text();
            console.log("detailnoA:" + detailnoA);
            console.log("detailnoB:" + detailnoB);
            console.log(parseInt(detailnoA) > parseInt(detailnoB));
            if (parseInt(detailnoA) > parseInt(detailnoB)) {
                rownum = i + 1;
                return false;
            }
        });
        if (rownum == 0) {
            $('#tbl_detail tbody').append('<tr>' + trhtml + '</tr>');
            $('#tbl_detail_chkbox tbody').append('<tr><td style="text-align:center;"><input type="checkbox" style="width: 10px;"></td></tr>');
            rownum = $("#tbl_detail tbody tr").length;
        } else {
            $('#tbl_detail tbody tr:nth-child(' + rownum + ')').before('<tr>' + trhtml + '</tr>');
            $('#tbl_detail_chkbox tbody tr:nth-child(' + rownum + ')').before('<tr><td style="text-align:center;"><input type="checkbox" style="width: 10px;"></td></tr>');
        }

        $('#tbl_detail tbody tr:nth-child(' + (rownum) + ')').find('td:nth-child(1)').css('display', '');

        tableBRow.remove();
    }

    // 検索条件変更ボタンのイベント
    $('img.search').on('click', function () {

        // 画面操作を無効する
        lockScreen(lockId);

        url = '/so/decide/search_init.php';
        sessionID = 'strSessionID=' + $('input[type="hidden"][name="strSessionID"]').val();
        param = 'strproductcode=' + $('input[name="strProductCode"]').val();
        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + param, '_blank', 'width=730, height=570, resizable=yes, scrollbars=yes, menubar=no');
    });

    // 行を一つ上に移動するボタン
    $('img.rowup').click(function () {
        var len = $("#table_decide_body").children().length;
        for (var i = 1; i <= len; i++) {
            var row = $("#decide_detail_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i - 1; j >= 1; j--) {
                    var row_prev = $("#decide_detail_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertBefore(row_prev);
                        break;
                    }
                }
            }
        }

        var len = $("#table_decide_no").children().length;
        for (var i = 1; i <= len; i++) {
            var row = $("#decide_no_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i - 1; j >= 1; j--) {
                    var row_prev = $("#decide_no_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertBefore(row_prev);
                        break;
                    }
                }
            }
        }

        resetTableBRowid();

    });

    // 行を一つ下に移動するボタン
    $('img.rowdown').click(function () {
        var len = $("#table_decide_body").children().length;
        for (var i = len; i >= 1; i--) {
            var row = $("#decide_detail_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i + 1; j <= len; j++) {
                    var row_prev = $("#decide_detail_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertAfter(row_prev);
                        break;
                    }
                }
            }
        }


        var len = $("#table_decide_no").children().length;
        for (var i = len; i >= 1; i--) {
            var row = $("#decide_no_" + i);
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i + 1; j <= len; j++) {
                    var row_prev = $("#decide_no_" + j);
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertAfter(row_prev);
                        break;
                    }
                }
            }
        }

        resetTableBRowid();

    });

    // 行を一番上に移動する
    $('img.rowtop').click(function () {
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertBefore($("#decide_detail_1"));
            }
        });

        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertBefore($("#decide_no_1"));
            }
        });

        resetTableBRowid();

    });

    // 行を一番下に移動する
    $('img.rowbottom').click(function () {
        var lasttr = $("#table_decide_body").find('tr').last();
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertAfter(lasttr);
            }
        });

        lasttr = $("#table_decide_no").find('tr').last();
        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertAfter(lasttr);
            }
        });

        resetTableBRowid();
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
            // 顧客受注番号
            var strCustomerReceiveCode = $(this).find('#strcustomerreceivecode').find('input:text').val();

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
                var w = window.open("", 'Decide Confirm', "width=1011px, height=600px, scrollbars=yes, resizable=yes");
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

// 行IDの再設定
function resetTableBRowid() {
    var rownum = 0;
    $("#table_decide_no tr").each(function (i, e) {
        rownum += 1;
        $(this).find('td').first().text(rownum);
    });
}



// 行IDの再設定
function resetTableARowid() {
    var rownum = 0;
    $("#tbl_detail tbody tr").each(function (i, e) {
        rownum += 1;
        $(this).find('td').first().text(rownum);
    });
}

selectChange();

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
                var html = '<input type="text" class="form-control form-control-sm txt-kids" style="width:90px;" value="' + lngunitquantitynew + '">';
                $(this).parent().parent().find('#lngunitquantity').html(html);
            } else {
                $(this).parent().parent().find('#lngunitquantity').text(lngunitquantitynew);
            }
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

function resetAllTableColumnWidth() {
    // $(".table-decide-description").css('table-layout', '');
    var row = $(".table-decide-description tbody tr");
    var columnNum = row.find('td').length;
    var thwidthArry = [];
    var tdwidthArry = [];
    for (var i = 1; i <= columnNum; i++) {
        thwidthArry.push($(".table-decide-description thead tr th:nth-child(" + i + ")").width());
        tdwidthArry.push($(".table-decide-description tbody tr td:nth-child(" + i + ")").width());
    }
    for (var i = 1; i <= columnNum; i++) {
        if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
            $(".table-decide-description thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1] + 10);
            $(".table-decide-description tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1] + 10);
        } else {
            $(".table-decide-description thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1] + 10);
            $(".table-decide-description tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1] + 10);
        }
    }
    // $(".table-decide-description").css('table-layout', 'fixed');
}

function resetTableADisplayStyle() {
    $("#tbl_detail tbody tr").each(function (i, e) {
        $(this).find("#strcustomerreceivecode").find('input:text').prop('disabled', true);
        $(this).find("#lngproductunitcode").find('select').prop('disabled', true);
        $(this).find("#lngunitquantity").find('input:text').prop('disabled', true);
        $(this).find("#strdetailnote").find('input:text').prop('disabled', true);
    });

    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(1)").css('display', '');

    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(3)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(3)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(8)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(8)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(9)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(9)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(10)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(10)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(11)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(11)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(12)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(12)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(13)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(13)").css('display', 'none');
}

function resetTableBDisplayStyle() {
    $("#tbl_decide_body tbody tr").each(function (i, e) {
        $(this).find("#strcustomerreceivecode").find('input:text').prop('disabled', false);
        $(this).find("#lngproductunitcode").find('select').prop('disabled', false);
        $(this).find("#lngunitquantity").find('input:text').prop('disabled', false);
        $(this).find("#strdetailnote").find('input:text').prop('disabled', false);
    });

    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(1)").css('display', '');

    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(3)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(3)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(8)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(8)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(9)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(9)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(10)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(10)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(11)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(11)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(12)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(12)").css('display', 'none');
    // $(".table-decide-description").eq(0).find("thead tr th:nth-child(13)").css('display', 'none');
    // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(13)").css('display', 'none');
}

// function resetTableAWidth() {
//     var width = 0;
//     var columnNum = $(".table-decide-description").eq(0).find("thead tr th").length;
//     for (var i = 1; i <= columnNum; i++) {
//         if ($(".table-decide-description").eq(0).find("thead tr th:nth-child(" + i + ")").css('display') == "none") {
//             // $(".table-decide-description").eq(0).find("thead tr th:nth-child(" + i + ")").css('width', '');
//             // $(".table-decide-description").eq(2).find("tbody tr td:nth-child(" + i + ")").css('width', '');
//         } else {
//             width += $(".table-decide-description").eq(0).find("thead tr th:nth-child(" + i + ")").width();
//         }
//     }
//     $(".table-decide-description").eq(0).width(width + 25);
//     $(".table-decide-description").eq(2).width(width + 25);
// }


function resetTableAWidth() {
    $("#tbl_detail_chkbox tbody tr td").width($("#tbl_detail_chkbox_head tr th").width());
    $("#tbl_detail thead").css('display', '');
    $("#tbl_detail tbody tr td").width('');
    $("#tbl_detail thead tr th").width('');
    $("#tbl_detail_head thead tr th").width('');
    var thwidthArry = [];
    var tdwidthArry = [];
    var width = 0;
    var columnNum = $('#tbl_detail_head thead tr th').length;
    for (var i = 1; i <= columnNum; i++) {
        var thwidth = $('#tbl_detail_head thead tr th:nth-child(' + i + ')').width();
        var tdwidth = $('#tbl_detail tbody tr td:nth-child(' + i + ')').width();
        thwidthArry.push(thwidth + 20);
        tdwidthArry.push(tdwidth + 20);
    }

    for (var i = 1; i <= columnNum; i++) {
        if ($("#tbl_detail_head thead tr th:nth-child(" + i + ")").css("display") != "none") {
            if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                $("#tbl_detail_head thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                $("#tbl_detail tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                width += thwidthArry[i - 1];
            } else {
                $("#tbl_detail_head thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                $("#tbl_detail tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                width += tdwidthArry[i - 1];
            }
        }
    }
    $("#tbl_detail_head").width(width + 100);
    $("#tbl_detail").width(width + 100);

    $("#tbl_detail thead").css('display', 'none');
}


function resetTableBWidth() {
    $("#tbl_decide_no tbody tr td").width($("#tbl_decide_no_head thead tr th").width());
    $("#tbl_decide_body tbody tr td").width('');
    $("#tbl_decide_head thead tr th").width('');
    var thwidthArry = [];
    var tdwidthArry = [];
    var columnNum = $('#tbl_decide_head thead tr th').length;
    console.log(columnNum);
    var width = 0;
    for (var i = 1; i <= columnNum; i++) {
        var thwidth = $('#tbl_decide_head thead tr th:nth-child(' + i + ')').width();
        var tdwidth = $('#tbl_decide_body tbody tr td:nth-child(' + i + ')').width();
        thwidthArry.push(thwidth + 20);
        tdwidthArry.push(tdwidth + 20);
    }

    for (var i = 1; i <= columnNum; i++) {
        if ($("#tbl_decide_head thead tr th:nth-child(" + i + ")").css("display") != "none") {
            if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                $("#tbl_decide_head thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                $("#tbl_decide_body tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                width += thwidthArry[i - 1];
            } else {
                $("#tbl_decide_head thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                $("#tbl_decide_body tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                width += tdwidthArry[i - 1];
            }
        }
    }

    $("#tbl_decide_head").width(width + 100);
    $("#tbl_decide_body").width(width + 100);
}
// function resetTableBWidth() {
//     var width = 0;
//     var columnNum = $(".table-decide-description").eq(3).find("thead tr th").length;
//     for (var i = 1; i <= columnNum; i++) {
//         if ($(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").css('display') == "none") {
//             // $(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").css('width', '');
//             // $(".table-decide-description").eq(5).find("tbody tr td:nth-child(" + i + ")").css('width', '');
//         } else {
//             console.log(i + ":" + $(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").width());
//             width += $(".table-decide-description").eq(3).find("thead tr th:nth-child(" + i + ")").width();
//         }
//     }
//     console.log(width);
//     $(".table-decide-description").eq(3).width(width + 50);
//     $(".table-decide-description").eq(5).width(width + 50);
// }


  /**
   * @method scanAllCheckbox スキャンチェックボックス
   */
  function scanAllCheckbox() {

    var $all_rows = $('#tbl_detail tbody tr');
    var $all_chkbox_rows = $('#tbl_detail_chkbox tbody tr');
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');

    // 有効 <tr> ＊選択可能行
    var count_checked = 0;
    var count_disabled = 0;

    // data がない場合、全選択／解除チェックボックスを寝かせて無効化
    if (!$all_rows.length) {
      $('#allChecked').prop({ 'checked': false, 'disabled': true });
    } else {
      $('#allChecked').prop('disabled', false);
    }

    $.each($all_checkbox, function (i) {
      // チェックボックスがひとつでも外れている場合、全選択／解除チェックボックスを寝かす
      if (!($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)')) {
        $('#allChecked').prop('checked', false);
      }

      // チェックボックスがすべてチェックされた場合、全選択／解除チェックボックスを立てる
      if ($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)') {
        ++count_checked;
      }
      if ($all_rows.length === count_checked) {
        $('#allChecked').prop('checked', true);
      }

      // すべてのチェックボックスが無効化された場合、全選択／解除チェックボックスを寝かせて無効化
      if ($(this).prop('disabled')) {
        ++count_disabled;
      }
      if (data.length === count_disabled) {
        $('#allChecked').prop({ 'checked': false, 'disabled': true });
      }
    });
  };


function selectRow(objA, objB) {
    var rows = objA.find('tbody tr');
    var rows = objB.find('tbody tr');
    var lastSelectedRow;
    /* Create 'click' event handler for rows */
    objA.find('tbody tr').on('click', function (e) {
        lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
    });


    /* Create 'click' event handler for rows */
    objB.find('tbody tr').on('click', function (e) {
        lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
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
}

function trClickEvent(row, lastSelectedRow, e, objA, objB) {

    /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
     * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
     * 'Shift' => is represented by 'e.shiftKey' */
    if (e.ctrlKey || e.metaKey) {
        /* If pressed highlight the other row that was clicked */
        objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        objB.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");

    } else if (e.shiftKey) {
        /* If pressed highlight the other row that was clicked */
        var indexes = [lastSelectedRow.index(), row.index()];
        indexes.sort(function (a, b) {
            return a - b;
        });
        for (var i = indexes[0]; i <= indexes[1]; i++) {
            objA.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
            objA.find("tbody tr:nth-child(" + (i + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
            objB.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
        }
    } else {
        /* Otherwise just highlight one row and clean others */
        objA.find("tbody tr").css("background-color", "#ffffff");
        objA.find("tbody tr").find('input[type="checkbox"]').prop('checked', false);
        objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        objB.find("tbody tr").css("background-color", "#ffffff");
        objB.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        lastSelectedRow = row;
    }

    return lastSelectedRow;
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

    return false;
}