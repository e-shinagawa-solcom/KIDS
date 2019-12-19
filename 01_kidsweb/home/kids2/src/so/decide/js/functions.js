(function () {
    var lockId = "lockId";

    // チェックボックスの切り替え処理のバインド
    $('input[type="checkbox"][name="allSel"]').on({
        'click': function () {
            var status = this.checked;
            $('input[type="checkbox"]')
                .each(function () {
                    this.checked = status;
                });
        }
    });


    // 確定ボタンのイベント
    $('img.decide').on('click', function () {
        // 選択した行のリビジョン番号を取得する
        var strId = "";
        $('input[type="checkbox"]')
            .each(function () {
                if (this.checked) {
                    if ($(this).attr('name') != "allSel") {
                        if (strId == "") {
                            strId = $(this).attr('id');
                        } else {
                            strId = strId + "," + $(this).attr('id');
                        }
                    }
                }
            });

        // 行が選択されてない場合
        if (strId == "") {
            alert("受注確定する明細データが指定されていません。");
            return;
        }

        $.ajax({
            url: '/so/decide/decide.php',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'strId': strId
            }
        })
            .done(function (data) {
                console.log("取得データ：" + data);
                // Ajaxリクエストが成功
                var data = JSON.parse(data);
                //既存データの削除
                $("#table_decide_no").empty();
                $("#table_decide_body").empty();

                // 一度設定したthedaのwidthを削除
                for (var i = 1; i <= 14; i++) {
                    var width = $(".table-decide-description thead tr th:nth-child(" + i + ")").css('width', '');
                }

                for (var i = 0; i < data.receiveDetail.length; i++) {
                    var rowNum = i + 1;
                    var id = "decide_no_" + rowNum;
                    var decide_no = '<tr id="' + id + '" rownum="' + rowNum + '"><td style="height: 20px;width: 20px;">' + rowNum + '</td></tr>';
                    $("#table_decide_no").append(decide_no);
                    //行データ
                    var row = data.receiveDetail[i];
                    var detail_id = "decide_detail_" + rowNum;
                    var select = '<select style="width:90px;" onchange="resetData(this)">';
                    for (var j = 0; j < data.productUnit.length; j++) {
                        var productunit = data.productUnit[j];
                        if (productunit.lngproductunitcode == row.lngproductunitcode) {
                            select += '<option value="' + productunit.lngproductunitcode + '" selected>' + productunit.strproductunitname + '</option>';
                        } else {
                            select += '<option value="' + productunit.lngproductunitcode + '">' + productunit.strproductunitname + '</option>';
                        }
                    }
                    select += '</select>';
                    // 入数・数量計算
                    var lngunitquantity = 1;
                    var lngproductquantity = convertNullToZero(row.lngproductquantity) / lngunitquantity;
                    if (productunit.lngproductunitcode == 2) {
                        lngunitquantity = convertNullToZero(row.lngcartonquantity);
                        lngproductquantity = convertNullToZero(row.lngproductquantity) / lngunitquantity;
                    }
                    // var decide_body = '<tr id="' + detail_id + '" rownum="' + rowNum + '" onclick="rowSelect(this);">'
                    var decide_body = '<tr id="' + detail_id + '" rownum="' + rowNum + '" >'
                        + '<td id="lngreceivedetailno">' + row.lngreceivedetailno + '</td>' // 明細行番号
                        + '<td id="strcustomerreceivecode"><input type="text" class="form-control form-control-sm txt-kids" style="width:90px;" value="' + convertNull(row.strcustomerreceivecode) + '"></td>' //顧客受注番号
                        + '<td id="strcompanydisplaycode" style="white-space: nowrap;">[' + convertNull(row.strcompanydisplaycode) + '] ' + convertNull(row.strcompanydisplayname) + '</td>' //顧客
                        + '<td id="strgoodscode">' + convertNull(row.strgoodscode) + '</td>'//顧客品番
                        + '<td id="dtmdeliverydate">' + convertNull(row.dtmdeliverydate) + '</td>'//納期
                        + '<td id="lngsalesdivisioncode" style="white-space: nowrap;" >[' + convertNull(row.lngsalesdivisioncode) + '] ' + convertNull(row.strsalesdivisionname) + '</td>'//売上分類
                        + '<td id="lngsalesclasscode" style="white-space: nowrap;" >[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'//売上区分
                        + '<td id="curproductprice">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice) + '</td>'//単価
                        + '<td id="lngproductunitcode">' + select + '</td>' //単位
                        + '<td id="lngunitquantity">' + lngunitquantity + '</td>'//入数
                        + '<td style="text-align: center;"><img class="button" src="/img/type01/so/product_off_ja_bt.gif" onclick="showProductInfo(this)" lngproductno="' + row.lngproductno + '" lngrevisionno="' + row.lngproductrevisionno + '"></td>'//製品
                        + '<td>' + lngproductquantity + '</td>'//数量
                        + '<td id="cursubtotalprice" style="white-space: nowrap;">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice) + '</td>'//小計
                        + '<td id="strdetailnote"><input type="text" class="form-control form-control-sm txt-kids" style="width:240px;" value="' + convertNull(row.strdetailnote) + '"></td>'//明細備考
                        + '<td id="strproductcode" style="display:none">' + row.strproductcode + '</td>'//製品コード
                        + '<td id="strproductname" style="display:none">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname) + '</td>'//製品コード
                        + '<td id="lngreceiveno" style="display:none">' + row.lngreceiveno + '</td>'//受注番号
                        + '<td id="strreceivecode" style="display:none">' + row.strreceivecode + '</td>'//受注コード
                        + '<td id="lngrevisionno" style="display:none">' + row.lngrevisionno + '</td>'//リビジョン番号
                        + '<td id="lngcartonquantity" style="display:none">' + convertNullToZero(row.lngcartonquantity) + '</td>'//カートン入数
                        + '<td id="lngproductquantity" style="display:none">' + convertNullToZero(row.lngproductquantity) + '</td>'//数量
                        + '<td id="strrevisecode" style="display:none">' + row.strrevisecode + '</td>'//製品リビジョン番号
                        + '</tr>';
                    $("#table_decide_body").append(decide_body);
                }

                var row = $(".table-decide-description tbody tr");
                var columnNum = row.find('td').length;
                var thwidthArry = [];
                var tdwidthArry = [];
                for (var i = 1; i <= 14; i++) {
                    var width = $(".table-decide-description thead tr th:nth-child(" + i + ")").width();
                    thwidthArry.push(width);
                    var tdwidth = $(".table-decide-description tbody tr td:nth-child(" + i + ")").width();
                    tdwidthArry.push(tdwidth);
                }
                for (var i = 1; i <= 14; i++) {
                    if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                        $(".table-decide-description thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    } else {
                        $(".table-decide-description thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                    }
                }
                for (var i = 1; i <= 14; i++) {
                    if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                        $(".table-decide-description tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    } else {
                        $(".table-decide-description tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);

                    }
                }
                var rows = $('.table-decide-description tbody tr');
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
                        $(".table-decide-description tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
                        
                    } else if (e.shiftKey) {
                        /* If pressed highlight the other row that was clicked */
                        var indexes = [lastSelectedRow.index(), row.index()];
                        indexes.sort(function (a, b) {
                            return a - b;
                        });
                        for (var i = indexes[0]; i <= indexes[1]; i++) {
                            $(".table-decide-description tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
                        }
                    } else {
                        /* Otherwise just highlight one row and clean others */
                        $(".table-decide-description tbody tr").css("background-color", "#ffffff");
                        $(".table-decide-description tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
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

            })
            .fail(function (data) {
                console.log("処理結果：" + data);
                alert("fail");
                // Ajaxリクエストが失敗
            });
    });

    // 全削除ボタンのイベント
    $('img.alldelete').on('click', function () {
        $("#table_decide_no").empty();
        $("#table_decide_body").empty();
    });

    // 削除ボタンのイベント
    $('img.delete').on('click', function () {
        var rownum = 0;
        $("#table_decide_no tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            } else {
                rownum += 1;
                $(this).attr('id', 'decide_no_' + rownum);
                $(this).attr('rownum', rownum);
                $(this).find('td').first().text(rownum);
            }


        });

        rownum = 0;
        $("#table_decide_body tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            } else {
                rownum += 1;
                $(this).attr('id', 'decide_detail_' + rownum);
                $(this).attr('rownum', rownum);
            }
        });
    });

    // 検索条件変更ボタンのイベント
    $('img.search').on('click', function () {

        // 画面操作を無効する
        lockScreen(lockId);

        url = '/so/decide/search_init.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
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

        resetRowid();

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

        resetRowid();

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

        resetRowid();

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

        resetRowid();
    });

    // 確定登録イベント
    $('img.decideRegist').on('click', function () {
        
        var params = new Array();
        var len = 0;
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
                exit;
            }
            var lngproductquantity = $(this).find('#lngproductquantity').text();
            if (lngproductquantity.indexOf('.') != -1) {
                alert(len + "行目の見積原価計算書上の数量が入数で割り切れません。");
                exit;
            }

            var strDetailNote = $(this).find('#strdetailnote').find('input:text').val();
            var strProductUnitName = $(this).find('#lngproductunitcode').find('select option:selected').text();
            var lngProductUnitCode = $(this).find('#lngproductunitcode').find('select option:selected').val();

            params[len - 1] = {
                "strReceiveCode": strReceiveCode,
                "lngReceiveDetailNo": lngReceiveDetailNo,
                "strCustomerReceiveCode": strCustomerReceiveCode,
                "strProductCode": $(this).find('#strproductname').text(),
                "strGoodsCode": $(this).find('#strgoodscode').text(),
                "dtmDeliveryDate": $(this).find('#dtmdeliverydate').text(),
                "lngSalesClassCode": $(this).find('#lngsalesclasscode').text(),
                "curProductPrice": $(this).find('#curproductprice').text(),
                "strProductUnitName": strProductUnitName,
                "lngUnitQuantity": $(this).find('#lngunitquantity').text(),
                "lngProductQuantity": $(this).find('#lngproductquantity').text(),
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
            exit;
        }
        // 画面操作を無効する
        // lockScreen("lockId");

        // リクエスト送信
        $.ajax({
            url: '/so/decide/decide_confirm.php',
            type: 'post',
            // dataType: 'json',
            type: 'POST',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'detailData': params
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

function showProductInfo(objID) {
    url = '/p/detail/index.php';
    sessionID = 'strSessionID=' + $.cookie('strSessionID');
    lngProductNo = 'lngProductNo=' + objID.getAttribute('lngproductno');
    lngRevisionNo = 'lngRevisionNo=' + objID.getAttribute('lngrevisionno');
    // 別ウィンドウで表示
    window.open(url + '?' + sessionID + '&' + lngProductNo + '&' + lngRevisionNo, '_blank', 'height=510, width=600, resizable=yes, scrollbars=yes, menubar=no');

}

// 行選択イベント
function rowSelect(objID) {
    var rownum = objID.getAttribute('rownum');
    var backgroud = $("#decide_detail_" + rownum).css("background-color");
    if (backgroud == "rgb(255, 255, 255)") {
        $("#decide_detail_" + rownum).css("background-color", "#bbbbbb");
        $("#decide_no_" + rownum).css("background-color", "#bbbbbb");
    } else {
        $("#decide_detail_" + rownum).css("background-color", "#ffffff");
        $("#decide_no_" + rownum).css("background-color", "#ffffff");
    }
}

// 行IDの再設定
function resetRowid() {
    var rownum = 0;
    $("#table_decide_body tr").each(function (i, e) {
        rownum += 1;
        $(this).attr('id', 'decide_detail_' + rownum);
        $(this).attr('rownum', rownum);
    });

    rownum = 0;
    $("#table_decide_no tr").each(function (i, e) {
        rownum += 1;
        $(this).attr('id', 'decide_no_' + rownum);
        $(this).attr('rownum', rownum);
        $(this).find('td').first().text(rownum);
    });
}

// 単位セレクトボックスの変更イベント
function resetData(objID) {
    var val = objID.value;
    var children = objID.parentNode.parentNode.children;
    // カートン入数の取得
    var lngcartonquantity = children[19].innerHTML;
    // 製品数量の取得
    var lngproductquantity = children[20].innerHTML;
    // 入数・数量計算
    var lngunitquantitynew = 1;
    var lngproductquantitynew = lngproductquantity / lngunitquantitynew;
    // 単位が[c/t]の場合、
    if (val == 2) {
        // 入数 = カートン入数
        lngunitquantitynew = lngcartonquantity;
        // 数量 = 製品数量/カートン入数
        lngproductquantitynew = lngproductquantity / lngunitquantitynew;
    }
    children[9].innerText = lngunitquantitynew;
    children[11].innerText = lngproductquantitynew;


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
