(function () {
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
                // Ajaxリクエストが成功
                var data = JSON.parse(data);
                //既存データの削除
                $("#table_decide_no").empty();
                $("#table_decide_body").empty();

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
                    var decide_body = '<tr id="' + detail_id + '" rownum="' + rowNum + '" onclick="rowSelect(this);">'
                        + '<td style="width: 100px;">' + row.strreceivecode + '</td>'
                        + '<td style="width: 50px;">' + row.lngreceivedetailno + '</td>'
                        + '<td style="width: 100px;"><input type="text" class="form-control form-control-sm txt-kids" style="width:90px;" value="' + row.strcustomerreceivecode + '"></td>'
                        + '<td style="width: 250px;">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname.substring(1, 28)) + '</td>'
                        + '<td style="width: 100px;">' + convertNull(row.strgoodscode) + '</td>'
                        + '<td style="width: 70px;">' + convertNull(row.dtmdeliverydate) + '</td>'
                        + '<td style="width: 70px;">[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'
                        + '<td style="width: 100px;">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice) + '</td>'
                        + '<td style="width: 100px;">' + select + '</td>' //単位
                        + '<td style="width: 70px;">' + lngunitquantity + '</td>'
                        + '<td style="width: 100px;text-align: center;"><img class="button" src="/img/type01/so/product_off_ja_bt.gif" onclick="showProductInfo(this)" strproductcode="' + row.strproductcode + '"></td>'
                        + '<td style="width: 70px;">' + lngproductquantity + '</td>'
                        + '<td style="width: 100px;">' +  money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice) + '</td>'
                        + '<td style="width: 250px;"><input type="text" class="form-control form-control-sm txt-kids" style="width:240px;" value="' + convertNull(row.strdetailnote) + '"></td>'
                        + '<td style="display:none">[' + convertNull(row.strcompanydisplaycode) + '] ' + convertNull(row.strcompanydisplayname) + '</td>'
                        + '<td style="display:none">' + row.lngreceiveno + '</td>'
                        + '<td style="display:none">' + row.lngrevisionno + '</td>'
                        + '<td style="display:none">' + convertNullToZero(row.lngcartonquantity) + '</td>'
                        + '<td style="display:none">' + convertNullToZero(row.lngproductquantity) + '</td>'
                        + '</tr>';
                    $("#table_decide_body").append(decide_body);
                }

            })
            .fail(function () {
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
        url = '/so/decide/search_init.php';
        sessionID = 'strSessionID=' + $.cookie('strSessionID');
        strReceiveCode = 'strReceiveCode=' + $(this).attr('code');
        // 別ウィンドウで表示
        window.open(url + '?' + sessionID + '&' + strReceiveCode, '_blank', 'width=730, height=570, resizable=yes, scrollbars=yes, menubar=no');
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

    $('img.regist').on('click', function () {
        var params = new Array();
        var len = 0;
        $("#table_decide_body tr").each(function (i, e) {
            len += 1;
            // 受注番号
            var strReceiveCode = $(this).find('td').first().text();
            // 受注明細番号
            var lngReceiveDetailNo = $(this).find('td:nth-child(2)').text();
            // 顧客受注番号
            var strCustomerReceiveCode = $(this).find('td:nth-child(3)').find('input:text').val();
            if (strCustomerReceiveCode == "") {
                alert(len + "行目の顧客受注番号が入力されていません。");
                exit;
            }
            var lngproductquantity = $(this).find('td:nth-child(12)').text();
            if (lngproductquantity.indexOf('.') != -1) {
                alert(len + "行目の見積原価計算書上の数量が入数で割り切れません。");
                exit;
            }

            var strDetailNote = $(this).find('td:nth-child(14)').find('input:text').val();
            var strProductUnitName = $(this).find('td:nth-child(9)').find('select option:selected').text();            
            var lngProductUnitCode = $(this).find('td:nth-child(9)').find('select option:selected').val();

            params[len - 1] = {
                "strReceiveCode": strReceiveCode,
                "lngReceiveDetailNo": lngReceiveDetailNo,
                "strCustomerReceiveCode": strCustomerReceiveCode,
                "strProductCode": $(this).find('td:nth-child(4)').text(),
                "strGoodsCode": $(this).find('td:nth-child(5)').text(),
                "dtmDeliveryDate": $(this).find('td:nth-child(6)').text(),
                "lngSalesClassCode": $(this).find('td:nth-child(7)').text(),
                "curProductPrice": $(this).find('td:nth-child(8)').text(),
                "strProductUnitName": strProductUnitName,
                "lngUnitQuantity": $(this).find('td:nth-child(10)').text(),
                "lngProductQuantity": $(this).find('td:nth-child(12)').text(),
                "curSubtotalPrice": $(this).find('td:nth-child(13)').text(),
                "strDetailNote": strDetailNote,
                "strCompanyDisplayCode": $(this).find('td:nth-child(15)').text(),
                "lngReceiveNo": $(this).find('td:nth-child(16)').text(),
                "lngRevisionNo": $(this).find('td:nth-child(17)').text(),
                "lngProductUnitCode": lngProductUnitCode
            };
        });

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
                var w = window.open();
                w.document.open();
                w.document.write(response);
                w.document.close();
            })
            .fail(function (response) {
                alert("fail");
                // Ajaxリクエストが失敗
            });
    });

})();

function showProductInfo(objID) {
    // url = '/p/result/index2.php';
    url = '/p/regist/renew.php';
    sessionID = 'strSessionID=' + $.cookie('strSessionID');
    strProductCode = 'strProductCode=' + objID.getAttribute('strproductcode');
    // 別ウィンドウで表示
    window.open(url + '?' + sessionID + '&' + strProductCode, '_blank', 'height=510, width=600, resizable=yes, scrollbars=yes, menubar=no');
    // var w = window.open(url + '?' + sessionID + '&' + lngReceiveNo, 'display-detail', 'width=1000, height=600, resizable=yes, scrollbars=yes, menubar=no');
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
    var lngcartonquantity = children[17].innerHTML;
    // 製品数量の取得
    var lngproductquantity = children[18].innerHTML;

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

function money_format(lngmonetaryunitcode, strmonetaryunitsign, price)
{
    if (lngmonetaryunitcode == 1) {
        return '\xA5' + " " + price;
    } else {
        return $strmonetaryunitsign + " " + $price;
    }
}
