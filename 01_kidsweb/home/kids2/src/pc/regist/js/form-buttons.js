var curtaxList;
(function () {
    // フォーム
    var workForm = $('form');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // クリアボタン
    var btnClear = $('img.clear');
    // 登録ボタン
    var btnRegist = $('img.regist');
    // 発注情報取得ボタン
    var btnGetPoInfo = $('img.getpoinfo');


    // フォームサブミット抑止
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    btnGetPoInfo.on('click', function () {
        // 発注NO.の取得
        var strOrderCode = $('input[name="strOrderCode"]').val();
        var strReviseCode = $('input[name="strReviseCode"]').val();
        var dtmStockAppDate = $('input[name="dtmStockAppDate"]').val();
        if (strOrderCode != "" && /^[a-zA-Z0-9]+$/.test(strOrderCode)) {
            // リクエスト送信
            $.ajax({
                url: '/pc/regist/getPoInfo.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'strOrderCode': strOrderCode,
                    'strReviseCode': strReviseCode,
                    'dtmStockAppDate': dtmStockAppDate
                }
            })
                .done(function (response) {
                    var data = JSON.parse(response);
                    if (data.orderdetail.length == 0) {
                        alert("該当する発注データがありません。");
                        exit;
                    }

                    $("#tbl_order_detail").empty();
                    for (var i = 0; i < data.orderdetail.length; i++) {
                        var row = data.orderdetail[i];
                        // 発注ステータスが納品済の場合、エラーを出す
                        if (row.lngorderstatuscode == 4) {
                            alert("指定された発注番号は「納品済み」です。");
                            exit;
                        }
                    }

                    for (var i = 0; i < data.orderdetail.length; i++) {
                        var rowNum = i + 1;
                        var row = data.orderdetail[i];
                        // 通貨単位コード
                        var lngmonetaryunitcode = row.lngmonetaryunitcode;
                        // 通貨レートコード
                        var lngmonetaryratecode = row.lngmonetaryratecode;
                        $('input[name="lngMonetaryUnitCode"]').val(lngmonetaryunitcode);
                        $('input[name="strMonetaryUnitName"]').val(row.strmonetaryunitname);
                        $('select[name="lngMonetaryRateCode"]').val(lngmonetaryratecode);
                        $('input[name="lngOrderStatusCode"]').val(row.strorderstatusname);
                        $('input[name="curConversionRate"]').val(row.curconversionrate);
                        $('input[name="lngPayConditionCode"]').val(row.lngpayconditioncode);
                        $('input[name="strPayConditionName"]').val(row.strpayconditionname);
                        $('input[name="lngCustomerCode"]').val(row.strcompanydisplaycode);
                        $('input[name="strCustomerName"]').val(row.strcompanydisplayname);
                        $('input[name="strReviseCode"]').val(row.strrevisecode);
                        $('input[name="lngLocationCode"]').val(row.lnglocationcode);
                        $('input[name="strLocationName"]').val(row.strlocationname);
                        $('input[name="dtmExpirationDate"]').val(row.dtmexpirationdate);
                        $('input[name="lngOrderNo"]').val(row.lngorderno);
                        // 国コードの取得
                        var lngcountrycode = row.lngcountrycode;
                        var curtax = 0;
                        var lngtaxclasscode = 0;
                        var lngtaxcode = row.lngtaxcode;
                        // 国コード：81日本の場合、「外税」となる、それ以外の場合、非課税
                        if (lngcountrycode == 81) {
                            if (data.tax == null) {
                                alert("消費税情報の取得に失敗しました。");
                                exit;
                            } else {
/*
                                curtax = data.tax.curtax * 100;
                                lngtaxcode = data.tax.lngtaxcode
*/
                                curtaxList = '<select style="width:90px;" onchange="resetTaxPrice(this)">';
                                for (var j = 0; j < data.tax.length; j++) {
                                    var taxRow = data.tax[j];
                                    if (j == 0) {
                                        curtaxList += '<option value="' + taxRow.lngtaxcode + '" selected>' + taxRow.curtax * 100 + '</option>';
                                        curtax =  taxRow.curtax * 100;
                                    } else {
                                        curtaxList += '<option value="' + taxRow.lngtaxcode + '">' + taxRow.curtax * 100 + '</option>';

                                    }
                                }
                                curtaxList += '</select>';
                            }
                            lngtaxclasscode = 2;
                        } else {
                            curtax = 0;
                            curtaxList = 0;
                            lngtaxclasscode = 1;
                        }

                        var curtaxprice = 0;
                        // １：非課税
                        if (lngtaxclasscode == 1) {
                            curtaxprice = 0;
                            //　2:外税
                        } else if (lngtaxclasscode == 2) {
                            curtaxprice = Math.floor(row.cursubtotalprice * (curtax/100));
                            // 3:内税
                        } else {
                            curtaxprice = Math.floor((row.cursubtotalprice / (1 + (curtax/100))) * (curtax / 100));
                        }

                        var select = '<select style="width:90px;" onchange="resetTaxPrice(this)">';
                        for (var j = 0; j < data.taxclass.length; j++) {
                            var taxclassRow = data.taxclass[j];
                            if (taxclassRow.lngtaxclasscode == lngtaxclasscode) {
                                select += '<option value="' + taxclassRow.lngtaxclasscode + '" selected>' + taxclassRow.strtaxclassname + '</option>'
                            } else {
                                select += '<option value="' + taxclassRow.lngtaxclasscode + '">' + taxclassRow.strtaxclassname + '</option>'

                            }
                        }
                        select += '</select>';
                        var detail_body = '<tr class="row'+ rowNum + '">'
                            + '<td class="col1">' + rowNum + '</td>'
                            + '<td class="col2"><input type="checkbox" style="width:10px;"></td>'
                            + '<td class="col3">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname).substring(0, 28) + '</td>'
                            + '<td class="col4">[' + convertNull(row.lngstocksubjectcode) + '] ' + convertNull(row.strstocksubjectname) + '</td>'
                            + '<td class="col5">[' + convertNull(row.lngstockitemcode) + '] ' + convertNull(row.strstockitemname) + '</td>'
                            + '<td class="col6">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice) + '</td>'
                            + '<td class="col7">' + row.strmonetaryunitname + '</td>'
                            + '<td class="col8">' + convertNumber(row.lngproductquantity, 0) + '</td>'
                            + '<td class="col9">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice) + '</td>'
                            // 消費税区分
                            + '<td class="col10">' + select + '</td>'
                            // 消費税率
                            + '<td class="col11">' + curtaxList + '</td>'
                            // 消費税額
                            + '<td class="col12">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, curtaxprice) + '</td>'
                            + '<td class="col13">' + row.dtmdeliverydate + '</td>'
                            + '<td class="col14">' + convertNull(row.strnote) + '</td>'
                            + '<td style="display:none">' + row.cursubtotalprice + '</td>'
                            + '<td style="display:none">' + curtax + '</td>'
                            + '<td style="display:none">' + row.lngmonetaryunitcode + '</td>'
                            + '<td style="display:none">' + row.strmonetaryunitsign + '</td>'
                            + '<td style="display:none">' + curtaxprice + '</td>'
                            + '<td style="display:none">' + row.lngorderno + '</td>'
                            + '<td style="display:none">' + row.lngrevisionno + '</td>'
                            + '<td style="display:none">' + row.lngorderdetailno + '</td>'
                            + '<td style="display:none">' + lngtaxcode + '</td>'
                            + '<td style="display:none">' + lngtaxclasscode + '</td>'
                            + '</tr>';
                        $("#tbl_order_detail").append(detail_body);
                    }

                    var row = $(".table-description tbody tr:nth-child(1)");
                    var columnNum = row.find('td').length;
                    var widthArry = [];
                    var theadwidth = $(".table-description tbody").width();
                    for (var i = 1; i <= columnNum; i++) {
                        var width = $(".table-description tbody tr:nth-child(1) td:nth-child(" + i + ")").width();
                        widthArry.push(width);
                    }
                    $(".table-description thead").width($(".table-description tbody").width() + columnNum + 10);
                    for (var i = 1; i <= columnNum; i++) {
                        $(".table-description thead tr th:nth-child(" + i + ")").width(widthArry[i - 1] + 1);
                    }
                    $('select[name="lngMonetaryRateCode"]').change();

                })
                .fail(function (response) {
                    alert(response);
                    alert("fail");
                })
        }

    });


    // 通貨変更イベント
    $('input[name="lngMonetaryUnitCode"]').on('change', function () {
        // リクエスト送信
        $.ajax({
            url: '/pc/regist/getMonetaryRate.php',
            type: 'post',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngMonetaryUnitCode': $(this).val(),
                'lngMonetaryRateCode': $('select[name="lngMonetaryRateCode"]').val(),
                'dtmStockAppDate': $('input[name="dtmStockAppDate"]').val()
            }
        })
            .done(function (response) {
                var data = JSON.parse(response);
                $('input[name="curConversionRate"]').val(data.curconversionrate);
            })
            .fail(function (response) {
                alert(response);
                alert("fail");
            })
    });

    // 通貨レート変更イベント
    $('select[name="lngMonetaryRateCode"]').on('change', function () {
        // リクエスト送信
        $.ajax({
            url: '/pc/regist/getMonetaryRate.php',
            type: 'post',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'lngMonetaryUnitCode': $('input[name="lngMonetaryUnitCode"]').val(),
                'lngMonetaryRateCode': $(this).val(),
                'dtmStockAppDate': $('input[name="dtmStockAppDate"]').val()
            }
        })
            .done(function (response) {
                var data = JSON.parse(response);
                $('input[name="curConversionRate"]').val(data.curconversionrate);
            })
            .fail(function (response) {
                alert("fail");
            })
    });

    // 登録ボタン押下時の処理
    btnRegist.on('click', function () {

        if (workForm.valid()) {

            var dtmExpirationDate = $('input[name="dtmExpirationDate"]').val();

            var detaildata = new Array();
            var len = 0;
            $("#tbl_order_detail tr").each(function (i, e) {
                // 明細行番号
                var strReceiveCode = $(this).find('td:nth-child(1)').text();
                // 発注明細行番号
                // 受注明細番号
                var chkbox = $(this).find('td:nth-child(2)').find('input:checkbox');
                if (chkbox.prop("checked")) {
                    len += 1;
                    // 発注番号                
                    var lngOrderNo = $(this).find('td:nth-child(20)').text();
                    // 発注リビジョン番号                
                    var lngRevisionNo = $(this).find('td:nth-child(21)').text();
                    // 発注明細番号                
                    var lngOrderDetailNo = $(this).find('td:nth-child(22)').text();
                    // 仕入明細番号
                    var lngStockDetailNo = len;
                    // 消費税区分
                    var lngTaxClassCode = $(this).find('td:nth-child(10)').find('select').val();
                    var strTaxClassName = $(this).find('td:nth-child(10)').find('select option:selected').text();
                    // 消費率
                    var curTax = 0;
                    var lngTaxCode = null;
                    if( lngTaxClassCode == "1")
                    {
                        lngTaxCode = null;
                        curTax = 0;
                    }
                    else
                    {
                        lngTaxCode = $(this).find('td:nth-child(11)').find('select').val();
                        curTax = $(this).find('td:nth-child(11)').find('select option:selected').text();
                    }
                    // 消費税額                
                    var curTaxPrice = $(this).find('td:nth-child(19)').text();
                    // 納期
                    var dtmDeliveryDate = $(this).find('td:nth-child(13)').text();
                    // 消費税コード                
//                    var lngTaxCode = $(this).find('td:nth-child(23)').text();

                    // 納期がヘッダ部で入力した製品到着日と同月でない行が存在した場合
                    if (dtmDeliveryDate.substring(1, 7) != dtmExpirationDate.substring(1, 7)) {
                        alert("発注確定時の納期と納品日と一致しません。発注データを修正してください。");
                        exit;
                    }
                    detaildata[len - 1] = {
                        "lngOrderNo": lngOrderNo,
                        "lngOrderDetailNo": lngOrderDetailNo,
                        "lngRevisionNo": lngRevisionNo,
                        "lngStockDetailNo": lngStockDetailNo,
                        "lngTaxClassCode": lngTaxClassCode,
                        "strTaxClassName": strTaxClassName,
                        "curTax": curTax / 100,
                        "curTaxPrice": curTaxPrice,
                        "lngTaxCode": lngTaxCode
                    };
                }
            });

            if (len == 0) {
                exit;
            }
            var formData = workForm.serializeArray();
            formData.push({ name: "detailData", value: JSON.stringify(detaildata) });
            formData.push({ name: "strSessionID", value: $.cookie('strSessionID') });
            formData.push({ name: "strMonetaryRateName", value: $('select[name="lngMonetaryRateCode"] option:selected').text() });
            formData.push({ name: "lngMonetaryUnitCode", value: $('input[name="lngMonetaryUnitCode"]').val() });
            formData.push({ name: "lngPayConditionCode", value: $('input[name="lngPayConditionCode"]').val() });
            formData.push({ name: "strMonetaryUnitName", value: $('input[name="strMonetaryUnitName"]').val() });
            formData.push({ name: "strPayConditionName", value: $('input[name="strPayConditionName"]').val() });

            var actionUrl = workForm.attr('action');
//            alert(actionUrl);
            // リクエスト送信
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData
            })
                .done(function (response) {
//                    alert(response);
                    var w = window.open();
                    w.document.open();
                    w.document.write(response);
                    w.document.close();
                    w.onunload = function () {
                        window.opener.location.reload();
                    }
                })
                .fail(function (response) {
                    alert(response);
//                    alert("fail");
                    // Ajaxリクエストが失敗
                });
        }
        else {
            // バリデーションのキック
            workForm.find(':submit').click();
        }
    });


})();

/**
 * 税額再計算
 * @param {*} objID 
 */
function resetTaxPrice(objID) {

    var children = objID.parentNode.parentNode.children;
    var rowClass = objID.parentNode.parentNode.className;
    var lngtaxclasscode = $('.'+rowClass).find('.col10').find('option:selected').val();
    
    if(!curtaxList)
    {
        curtaxList = $("#taxList").html();
    }

    var lngoldtaxclasscode = children[23].innerText;
    // カートン入数の取得
    var cursubtotalprice = children[14].innerHTML;
    var curtax;
    var lngmonetaryunitcode = children[16].innerHTML;
    var strmonetaryunitsign = children[17].innerHTML;

    // 課税区分が変わったら消費税率も変わる
    if ( lngtaxclasscode != lngoldtaxclasscode ){
        if( lngtaxclasscode == 1 )
        {
            // 外税・内税→非課税
            children[10].innerText = 0;
        }
        else if( lngoldtaxclasscode == 1 )
        {
            // 非課税→外税・内税
            children[10].innerHTML = curtaxList;
        }
        // 外税⇔内税の場合は税率変わらない
    }
    if( lngtaxclasscode == 1 ){
        curtax = children[10].innerHTML;
    }
    else{
        curtax = $('.'+rowClass).find('.col11').find('option:selected').text();
    }
    if (lngtaxclasscode == 1) {
        curtaxprice = 0;
        curtax = 0;
        //　2:外税
    } else if (lngtaxclasscode == 2) {
        curtaxprice = Math.floor(cursubtotalprice * (curtax / 100));
        // 3:内税
    } else {
        curtaxprice = Math.floor((cursubtotalprice / (1 + (curtax/100))) * (curtax / 100));
    }
    children[11].innerText = money_format(lngmonetaryunitcode, strmonetaryunitsign, String(curtaxprice));
    children[15].innerText = curtax;
    children[18].innerText = curtaxprice;
    children[23].innerText = lngtaxclasscode
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
        return '\xA5' + " " + convertNumber(price, 4);
    } else {
        return strmonetaryunitsign + " " + convertNumber(price, 4);
    }
}

function convertNumber(str, fracctiondigits) {
    if (str != "" && str != undefined && str != "null") {
        return Number(str).toLocaleString(undefined, {
            minimumFractionDigits: fracctiondigits,
            maximumFractionDigits: fracctiondigits
        });
    } else {
        return "";
    }
}