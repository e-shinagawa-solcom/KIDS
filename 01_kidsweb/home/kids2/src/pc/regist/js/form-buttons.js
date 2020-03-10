var taxList;
var chkbox = [];
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
                    console.log(response);
                    var data = JSON.parse(response);
                    if (data.orderdetail.length == 0) {
                        alert("該当する発注データがありません。");
                        return false;
                    }

                    $("#tableB tbody").empty();
                    $("#tableB_chkbox tbody").empty();
                    for (var i = 0; i < data.orderdetail.length; i++) {
                        var row = data.orderdetail[i];
                        // 発注ステータスが納品済の場合、エラーを出す
                        if (row.lngorderstatuscode == 4) {
                            alert("指定された発注番号は「納品済み」です。");
                            return false;
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
                        $('input[name="strProductCode"]').val(row.strproductcode);
                        $('input[name="strProductName"]').val(row.strproductname);
                        $('input[name="lngCustomerCode"]').val(row.strcompanydisplaycode);
                        $('input[name="strCustomerName"]').val(row.strcompanydisplayname);
                        $('input[name="strReviseCode"]').val(row.strrevisecode);
                        $('input[name="lngLocationCode"]').val(row.lnglocationcode);
                        $('input[name="strLocationName"]').val(row.strlocationname);
                        $('input[name="dtmExpirationDate"]').val(row.dtmexpirationdate);
                        $('input[name="lngOrderNo"]').val(row.lngorderno);
                        $('input[name="lngPurchaseOrderNo"]').val(row.lngpurchaseorderno);
                        $('input[name="lngpurchaserevisionno"]').val(row.lngpurchaseorderrevisionno);
                        // 国コードの取得
                        var lngcountrycode = row.lngcountrycode;
                        var curtax = 0;
                        var lngtaxclasscode = 0;
                        var lngtaxcode = row.lngtaxcode;
                        if (data.tax == null) {
                            alert("消費税情報の取得に失敗しました。");
                            exit;
                        } else {
                            taxList = '<select style="width:90px;" onchange="resetTaxPrice(this, 2)">';
                            for (var j = 0; j < data.tax.length; j++) {
                                var taxRow = data.tax[j];
                                if (j == 0) {
                                    taxList += '<option value="' + taxRow.lngtaxcode + '" selected>' + taxRow.curtax * 100 + '</option>';
                                } else {
                                    taxList += '<option value="' + taxRow.lngtaxcode + '">' + taxRow.curtax * 100 + '</option>';

                                }
                            }
                            taxList += '</select>';
                        }

                        console.log(taxList);
                        // 国コード：81日本の場合、「外税」となる、それ以外の場合、非課税
                        if (lngcountrycode == 81) {
                            for (var j = 0; j < data.tax.length; j++) {
                                var taxRow = data.tax[j];
                                if (j == 0) {
                                    curtax = taxRow.curtax * 100;
                                }
                            }
                            curtaxList = taxList;
                            lngtaxclasscode = 2;

                            $("select[name='lngMonetaryRateCode']").val("0");
                            $('select[name="lngMonetaryRateCode"]').change();

                        } else {
                            curtax = 0;
                            curtaxList = 0;
                            lngtaxclasscode = 1;
                            console.log("tdd");
                            $("select[name='lngMonetaryRateCode']").val("1");
                        }

                        $('select[name="lngMonetaryRateCode"]').change();
                        $("select[name='lngMonetaryRateCode'] option:not(:selected)").prop('disabled', true);

                        var curtaxprice = 0;
                        // １：非課税
                        if (lngtaxclasscode == 1) {
                            curtaxprice = 0;
                            //　2:外税
                        } else if (lngtaxclasscode == 2) {
                            curtaxprice = Math.floor(row.cursubtotalprice * (curtax / 100));
                            // 3:内税
                        } else {
                            curtaxprice = Math.floor((row.cursubtotalprice / (1 + (curtax / 100))) * (curtax / 100));
                        }
                        console.log("消費税区分：" + lngtaxclasscode);
                        console.log("消費税額：" + curtaxprice);
                        console.log(money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, curtaxprice, 'taxprice'));
                        var select = '<select style="width:90px;" onchange="resetTaxPrice(this, 1)">';
                        for (var j = 0; j < data.taxclass.length; j++) {
                            var taxclassRow = data.taxclass[j];
                            if (taxclassRow.lngtaxclasscode == lngtaxclasscode) {
                                select += '<option value="' + taxclassRow.lngtaxclasscode + '" selected>' + taxclassRow.strtaxclassname + '</option>'
                            } else {
                                if (lngcountrycode == 81) {   // 海外は非課税固定
                                    select += '<option value="' + taxclassRow.lngtaxclasscode + '">' + taxclassRow.strtaxclassname + '</option>'
                                }

                            }
                        }
                        select += '</select>';
                        var detail_chkbox_body = '<tr class="row' + rowNum + '">'
                            + '<td style="text-align:center;"><input type="checkbox" name="edit" style="width:10px;"></td>'
                            + '</tr>';
                        var detail_body = '<tr class="row' + rowNum + '">'
                            + '<td class="col1">' + rowNum + '</td>'
                            // + '<td class="col2"><input type="checkbox" style="width:10px;"></td>'
                            // + '<td class="col3">[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname).substring(0, 28) + '</td>'
                            + '<td class="col4">[' + convertNull(row.lngstocksubjectcode) + '] ' + convertNull(row.strstocksubjectname) + '</td>'
                            + '<td class="col5">[' + convertNull(row.lngstockitemcode) + '] ' + convertNull(row.strstockitemname) + '</td>'
                            + '<td class="col6">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.curproductprice, 'unitprice') + '</td>'
                            + '<td class="col7">' + row.strproductunitname + '</td>'
                            + '<td class="col8">' + convertNumber(row.lngproductquantity, 0) + '</td>'
                            + '<td class="col9">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, row.cursubtotalprice, 'price') + '</td>'
                            // 消費税区分
                            + '<td class="col10">' + select + '</td>'
                            // 消費税率
                            + '<td class="col11">' + curtaxList + '</td>'
                            // 消費税額
                            + '<td class="col12">' + money_format(row.lngmonetaryunitcode, row.strmonetaryunitsign, curtaxprice, 'taxprice') + '</td>'
                            + '<td class="dtmdeliverydate">' + row.dtmdeliverydate + '</td>'
                            + '<td>' + convertNull(row.strnote) + '</td>'
                            + '<td class="cursubtotalprice" style="display:none">' + row.cursubtotalprice + '</td>'
                            + '<td class="curtax" style="display:none">' + curtax + '</td>'
                            + '<td class="lngmonetaryunitcode" style="display:none">' + row.lngmonetaryunitcode + '</td>'
                            + '<td class="strmonetaryunitsign" style="display:none">' + row.strmonetaryunitsign + '</td>'
                            + '<td class="curtaxprice" style="display:none">' + curtaxprice + '</td>'
                            + '<td class="lngorderno" style="display:none">' + row.lngorderno + '</td>'
                            + '<td class="lngrevisionno" style="display:none">' + row.lngrevisionno + '</td>'
                            + '<td class="lngorderdetailno" style="display:none">' + row.lngorderdetailno + '</td>'
                            + '<td class="lngtaxcode" style="display:none">' + lngtaxcode + '</td>'
                            + '<td class="lngtaxclasscode" style="display:none">' + lngtaxclasscode + '</td>'
                            + '</tr>';
                        $("#tableB tbody").append(detail_body);
                        $("#tableB_chkbox tbody").append(detail_chkbox_body);
                    }

                    // テーブル各セルの幅をリセットする
                    resetTableWidth($("#tableB_chkbox_head"), $("#tableB_chkbox"), $("#tableB_head"), $("#tableB"));
                    // テーブル行クリックイベントの設定
                    selectRow('hasChkbox', $("#tableB_chkbox"), $("#tableB"), $("#allChecked"));
                    // 対象チェックボックスチェック状態の設定
                    scanAllCheckbox($("#tableB_chkbox"), $("#allChecked"));
                    // チェックボックスクリックイベントの設定
                    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableB"), $("#tableB_chkbox"), $("#allChecked"));
                    // 対象チェックボックスクリックイベントの設定
                    setAllCheckClickEvent($("#allChecked"), $("#tableB"), $("#tableB_chkbox"));

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
                console.log(response);
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
                console.log(response);
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

            var dtmStockAppDate = $('input[name="dtmStockAppDate"]').val();

            // 仕入日が一年後である
            if (isOneYearLater(new Date(dtmStockAppDate))) {
                alert("仕入日はシステム日付の1年後以上となっている。");
                return false;
            }


            var detaildata = new Array();
            var len = 0;
            $("#tableB tbody tr").each(function (i, e) {
                // 発注明細行番号
                // 受注明細番号
                var chkbox = $("#tableB_chkbox tbody tr:nth-child(" + (i + 1) + ") td").find('input:checkbox');
                if (chkbox.prop("checked")) {
                    len += 1;
                    // 発注番号                
                    var lngOrderNo = $(this).find('.lngorderno').text();
                    // 発注リビジョン番号                
                    var lngRevisionNo = $(this).find('.lngrevisionno').text();
                    // 発注明細番号                
                    var lngOrderDetailNo = $(this).find('.lngorderdetailno').text();
                    // 仕入明細番号
                    var lngStockDetailNo = len;
                    // 消費税区分
                    var lngTaxClassCode = $(this).find('.col10').find('select').val();
                    var strTaxClassName = $(this).find('.col10').find('select option:selected').text();
                    // 消費率
                    var curTax = 0;
                    var lngTaxCode = null;
                    if (lngTaxClassCode == "1") {
                        lngTaxCode = null;
                        curTax = 0;
                    }
                    else {
                        lngTaxCode = $(this).find('.col10').find('select').val();
                        curTax = $(this).find('.col11').find('select option:selected').text();
                    }
                    // 消費税額                
                    var curTaxPrice = $(this).find('.curtaxprice').text();
                    // 納期
                    var dtmDeliveryDate = $(this).find('.dtmdeliverydate').text();
                    // 消費税コード                
                    //                    var lngTaxCode = $(this).find('td:nth-child(23)').text();

                    // 納期がヘッダ部で入力した仕入日と同月でない行が存在した場合
                    if (dtmDeliveryDate.substring(1, 7) != dtmStockAppDate.substring(1, 7)) {
                        alert("発注確定時の納期と仕入日と一致しません。発注データを修正してください。");
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
                alert("発注明細を一つ以上選択してください。")
                exit;
            }
            var formData = workForm.serializeArray();
            formData.push({ name: "detailData", value: JSON.stringify(detaildata) });
            formData.push({ name: "strSessionID", value: $.cookie('strSessionID') });
            formData.push({ name: "strMonetaryRateName", value: $('select[name="lngMonetaryRateCode"] option:selected').text() });
            formData.push({ name: "lngMonetaryUnitCode", value: $('input[name="lngMonetaryUnitCode"]').val() });
            formData.push({ name: "lngPayConditionCode", value: $('input[name="lngPayConditionCode"]').val() });
            formData.push({ name: "lngPurchaseOrderNo", value: $('input[name="lngPurchaseOrderNo"]').val() });
            formData.push({ name: "lngPurchaseOrderRevisionNo", value: $('input[name="lngpurchaserevisionno"]').val() });
            formData.push({ name: "lngStockNo", value: $('input[name="lngStockNo"]').val() });
            formData.push({ name: "lngStockRevisionNo", value: $('input[name="lngstockrevisionno"]').val() });

            var actionUrl = workForm.attr('action');
            //            alert(actionUrl);
            // リクエスト送信
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData
            })
                .done(function (response) {
                    console.log(response);
                    if (actionUrl.indexOf('modify') > -1) {
                        document.open();
                        document.write(response);
                        document.close();
                    } else {
                        var w = window.open("", 'Regist Confirm', "width=1011, height=700, scrollbars=yes, resizable=yes");
                        w.document.open();
                        w.document.write(response);
                        w.document.close();
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
function resetTaxPrice(objID, type) {

    if (!taxList) {
        taxList = $('#taxList').html();
    }
    console.log(taxList);
    var children = objID.parentNode.parentNode.children;
    var rowClass = objID.parentNode.parentNode.className;
    var curtax;
    var lngtaxclasscode = $('.' + rowClass).find('.col10').find('option:selected').val();
    var cursubtotalprice = $('.' + rowClass).find('.cursubtotalprice').text();
    var lngmonetaryunitcode = $('.' + rowClass).find('.lngmonetaryunitcode').text();
    var strmonetaryunitsign = $('.' + rowClass).find('.strmonetaryunitsign').text();
    if (type == 1) {
        var lngoldtaxclasscode = $('.' + rowClass).find('.lngtaxclasscode').text();
        // 課税区分が変わったら消費税率も変わる
        if (lngtaxclasscode == 1) {
            $('.' + rowClass).find('.col11').text(0);
            curtax = 0;
        }
        else {
            $('.' + rowClass).find('.col11').text('');
            $('.' + rowClass).find('.col11').append(taxList);
            curtax = $('.' + rowClass).find('.col11').find('option:selected').text();
            console.log(curtax);
            console.log(rowClass);
            console.log(taxList);
        }
    } else if (type == 2) {
        // 課税区分が変わったら消費税率も変わる
        if (lngtaxclasscode == 1) {
            curtax = 0;
        }
        else {
            curtax = $('.' + rowClass).find('.col11').find('option:selected').text();
        }
    }
    if (lngtaxclasscode == 1) {
        curtaxprice = 0;
        curtax = 0;
        //　2:外税
    } else if (lngtaxclasscode == 2) {
        curtaxprice = Math.floor(cursubtotalprice * (curtax / 100));
        // 3:内税
    } else {
        curtaxprice = Math.floor((cursubtotalprice / (1 + (curtax / 100))) * (curtax / 100));
    }
    $('.' + rowClass).find('.col12').text(money_format(lngmonetaryunitcode, strmonetaryunitsign, String(curtaxprice), 'taxprice'));
    $('.' + rowClass).find('.curtax').text(curtax);
    $('.' + rowClass).find('.curtaxprice').text(curtaxprice);
    $('.' + rowClass).find('.lngtaxclasscode').text(lngtaxclasscode);
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

function money_format(lngmonetaryunitcode, strmonetaryunitsign, price, type) {
    if (lngmonetaryunitcode == 1) {
        if (type == 'unitprice') {
            return '\xA5' + " " + convertNumber(price, 4);
        } else if (type == 'price') {
            return '\xA5' + " " + convertNumber(price, 0);
        } else if (type == 'taxprice') {
            return '\xA5' + " " + convertNumber(price, 0);
        }
        return '\xA5' + " " + convertNumber(price, 0);
    } else {
        if (type == 'unitprice') {
            return strmonetaryunitsign + " " + convertNumber(price, 4);
        } else if (type == 'price') {
            return strmonetaryunitsign + " " + convertNumber(price, 2);
        } else if (type == 'taxprice') {
            return strmonetaryunitsign + " " + convertNumber(price, 0);
        }
        return strmonetaryunitsign + " " + convertNumber(price, 2);
    }
}

function convertNumber(str, fracctiondigits) {
    console.log(str);
    if ((str != "" && str != undefined && str != "null") || str == 0) {
        console.log("null以外の場合：" + str);
        return Number(str).toLocaleString(undefined, {
            minimumFractionDigits: fracctiondigits,
            maximumFractionDigits: fracctiondigits
        });
    } else {
        console.log("nullの場合：" + str);
        return "";
    }
}

