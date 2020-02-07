//
// regist.js
//

// ------------------------------------------------------------------
//   Enterキー押下イベント
// ------------------------------------------------------------------
window.document.onkeydown = fncEnterKeyDown;

function fncEnterKeyDown(e) {
    // Enterキー押下で明細追加
    if (window.event.keyCode == 13) {
        $("img.add").trigger('click');
    }
}

function clearAllSelected() {
    $("#tableA tbody tr").css("background-color", "#ffffff");
    $("#tableA_chkbox tbody tr").css("background-color", "#ffffff");
}
// ------------------------------------------------------------------

// ------------------------------------------------------------------
//
//  検索条件入力画面から呼ばれる関数（呼び出し元は condition.js を参照）
//
// ------------------------------------------------------------------
// 検索条件入力画面で入力された値の設定
function SetSearchConditionWindowValue(strCompanyDisplayCode, strCompanyDisplayName) {
    // console.log($('input[name="monetaryunitCount"]').val());
    // if ($('input[name="monetaryunitCount"]').val() != 1)
    // {
    //     return false;   
    // }
    // POST先
    var postTarget = $('input[name="ajaxPostTarget"]').val();

    // 顧客
    $('input[name="lngCustomerCode"]').val(strCompanyDisplayCode);
    $('input[name="strCustomerName"]').val(strCompanyDisplayName);

    // 顧客に紐づく国コードによって消費税区分のプルダウンを変更する
    if (strCompanyDisplayCode != "") {
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: {
                strMode: "get-lngcountrycode",
                strSessionID: $('input[name="strSessionID"]').val(),
                strcompanydisplaycode: strCompanyDisplayCode,
            },
            async: true,
        }).done(function (data) {
            console.log(data);
            console.log("done:get-lngcountrycode");
            if (data == "81") {
                console.log("81：「外税」");
                // 81：「外税」を選択（他の項目も選択可能）
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
                $("select[name='lngTaxClassCode']").val("2");
                $('select[name="lngTaxRate"]').prop("selectedIndex", 1);

                $("input[name='dtmPaymentLimit']").prop('disabled', true);
                $("input[name='dtmPaymentLimit']").val("");
                $("input[name='dtmPaymentLimit']").next("img").css("pointer-events", "none");
                $("select[name='lngPaymentMethodCode']").val("0");
                $("select[name='lngPaymentMethodCode'] option:not(:selected)").prop('disabled', true);

            } else {
                console.log("81以外：「非課税」固定");
                // 81以外：「非課税」固定
                $("select[name='lngTaxClassCode']").val("1");
                $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', true);
                $("select[name='lngPaymentMethodCode']").val("1");
                $("select[name='lngPaymentMethodCode'] option[value=0]").prop('disabled', true);

                $('select[name="lngTaxRate"]').val('');
                $("select[name='lngTaxRate'] option:not(:selected)").prop('disabled', true);
                // 支払期限の設定
                $("input[name='dtmPaymentLimit']").prop('disabled', false);
                $("input[name='dtmPaymentLimit']").next("img").css("pointer-events", "");
                var now = new Date();
                now.setMonth(now.getMonth() + 1);
                $('input[name="dtmPaymentLimit"]').val(now.getFullYear() + "/" + ("00" + (now.getMonth() + 1)).slice(-2) + "/" + ("00" + now.getDate()).slice(-2));
            }
        }).fail(function (error) {
            console.log("fail:get-lngcountrycode");
            console.log(error);
        });

    } else {
        // 顧客コードが空なら固定解除
        $("select[name='lngTaxClassCode'] option:not(:selected)").prop('disabled', false);
    }
}
// 国コードの取得
function GetLngCountryCode(postTarget, strCompanyDisplayCode, strSessionID) {
    console.log("国コード取得：" + strCompanyDisplayCode);
    if (strCompanyDisplayCode != "") {
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: {
                strMode: "get-lngcountrycode",
                strSessionID: strSessionID,
                strcompanydisplaycode: strCompanyDisplayCode,
            },
            async: true,
        }).done(function (data) {
            console.log(data);
            console.log("done:get-lngcountrycode");
            return data;
        }).fail(function (error) {
            console.log("fail:get-lngcountrycode");
            console.log(error);
        });
    } else {
        return "";
    }
}

// 明細検索
function SearchReceiveDetail(data) {
    $('#tableA_chkbox tbody tr').remove();
    $('#tableA tbody tr').remove();
    $('#tableA tbody tr td').width('');
    $('#tableA_head thead tr th').width('');

    $('#tableA_chkbox').append(data.chkbox_body);
    $('#tableA').append(data.detail_body);

    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));
    // テーブル行クリックイベントの設定
    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));
    // 対象チェックボックスクリックイベントの設定
    setAllCheckClickEvent($("#allChecked"), $("#tableA"), $("#tableA_chkbox"));

    $("#tableA_head").trigger("update");
    $("#tableA").trigger("update");
    // jQueryUIのtablesorterでソート設定
    $('#tableA_head').tablesorter({
        headers: {
            0: { sorter: false }
        }
    });

    $('input[name="strMonetaryUnitName"]').val(data.strmonetaryunitname);
    $('input[name="lngMonetaryUnitCode"]').val(data.lngmonetaryunitcode);
    $('input[name="strMonetaryRateName"]').val(data.strmonetaryratename);
    $('input[name="lngMonetaryRateCode"]').val(data.lngmonetaryratecode);
    $('input[name="curConversionRate"]').val(data.curconversionrate);

}
// 出力明細をすべてクリア
function ClearAllEditDetail() {
    // 全削除ボタンクリックを手動で起動
    $("#AllDeleteBt").trigger('click');
}
// ------------------------------------------------------------------

// ------------------------------------------
//   HTMLエレメント生成後の初期処理
// ------------------------------------------
jQuery(function ($) {

    $("#tableA thead").css('display', 'none');

    if ($('#tableB tbody tr').length > 0) {
        $('#tableB tbody tr td:nth-child(1)').css('display', 'none');
        $('#tableB_no tbody tr td').width($('#tableB_no_head thead tr th').width());
        resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

        // テーブル行クリックイベントの設定
        selectRow("", $("#tableB_no"), $("#tableB"), "");
    }

    var sortval = 0;
    $('#tableA_head thead tr th').on('click', function () {
        clearAllSelected();
        $('input[name="edit"]').prop('checked', false);
        var sortkey = $(this)[0].cellIndex;
        console.log(sortkey);
        if (sortval == 1) {
            sortval = 0;
        } else {
            sortval = 1;
        }
        var r = $('#tableA').tablesorter();
        r.trigger('sorton', [[[(sortkey), sortval]]]);

        resetTableRowid($('#tableA'));
    });

    // 消費税率の設定
    var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();
    if (taxClassCode == 1) {
        $('select[name="lngTaxRate"]').val('');
    } else {
        $('select[name="lngTaxRate"]').prop("selectedIndex", 1);
    }


    if ($('input[name="lngSlipNo"]').val().length > 0) {
        window.opener.$('input[name="locked"]').val("1");
    }

    // 画面名ヘッダ画像切り替え
    if ($('input[name="lngSlipNo"]').val()) {
        $('#SegAHeader').text('売上（納品書）修正');
    } else {
        $('#SegAHeader').text('売上（納品書）登録');
    }

    // datepicker対象要素
    var dateElements = [
        // 納品日
        $('input[name="dtmDeliveryDate"]'),
        // 支払期限
        $('input[name="dtmPaymentLimit"]'),
    ];
    // datepickerの設定
    $.each(dateElements, function () {
        this.datepicker({
            showButtonPanel: true,
            dateFormat: "yy/mm/dd",
            onClose: function () {
                this.focus();
            }
        }).attr({
            maxlength: "10"
        });
    });

    // 合計金額・消費税額の更新
    updateAmount();

    // ------------------------------------------
    //   functions
    // ------------------------------------------
    // 追加バリデーションチェック
    function validateAdd(tr) {
        console.log($(tr));
        console.log($(tr).children('td'));
        console.log($(tr).children('td.detailDeliveryDate').text());
        console.log($('input[name="dtmDeliveryDate"]').val());
        //明細選択エリアの納期
        var detailDeliveryDate = new Date($(tr).children('td.detailDeliveryDate').text());

        //ヘッダ・フッタ部の納品日と比較（同月以外は不正）
        var headerDeliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());
        console.log(detailDeliveryDate);
        console.log(headerDeliveryDate);


        console.log(headerDeliveryDate.getYear() + "/" + headerDeliveryDate.getMonth());
        console.log(detailDeliveryDate.getYear() + "/" + detailDeliveryDate.getMonth());
        var sameMonth = (headerDeliveryDate.getYear() == detailDeliveryDate.getYear())
            && (headerDeliveryDate.getMonth() == detailDeliveryDate.getMonth());
        if (!sameMonth) {
            alert("受注確定時の納期と納品日と一致しません。受注データを修正してください。");
            return false;
        }

        //出力明細の先頭行があればその納期と比較（同月以外は不正）
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length) {
            var firstRowDate = new Date($(firstTr).children('td.detailDeliveryDate').text());
            var sameMonthDetail = (firstRowDate.getYear() == detailDeliveryDate.getYear())
                && (firstRowDate.getMonth() == detailDeliveryDate.getMonth());
            if (!sameMonthDetail) {
                alert("出力明細と納品月が異なる明細は選択できません。");
                return false;
            }
        }

        return true;
    }


    // 合計金額・消費税額の更新
    function updateAmount() {

        // 税抜金額を取得して配列に格納
        var aryPrice = [];
        $("#EditTableBody tr").each(function () {
            //カンマをはじいてから数値に変換
            var price = Number($(this).find($('td.detailSubTotalPrice')).html().split(',').join(''));
            aryPrice.push(price);
        });

        // ----------------
        // 合計金額の算出
        // ----------------
        var totalAmount = 0;
        aryPrice.forEach(function (price) {
            totalAmount += price;
        });

        // ----------------
        // 消費税額の算出
        // ----------------
        // 消費税区分を取得
        var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();

        // 消費税率を取得
        var taxRate = Number($('select[name="lngTaxRate"]').children('option:selected').text().replace("%", "")) * 0.01;

        console.log(taxRate);

        // 消費税額の計算
        var taxAmount = 0;
        if (taxClassCode == "1") {
            // 1:非課税
            taxAmount = 0;
        }
        else if (taxClassCode == "2") {
            // 2:外税
            taxAmount = Math.floor(totalAmount * taxRate);
        }
        else if (taxClassCode == "3") {
            // 3:内税
            aryPrice.forEach(function (price) {
                taxAmount += Math.floor((price / (1 + taxRate)) * taxRate);
            });
        }

        // ------------------
        // フォームに値を設定
        // ------------------
        $('input[name="strTotalAmount"]').val(convertNumber(totalAmount, 4));
        $('input[name="strTaxAmount"]').val(convertNumber(taxAmount, 4));
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

    // ヘッダ部・フッタ部入力エリアのPOST用データ収集
    function getUpdateHeader() {

        var result = {
            //起票者
            strdrafteruserdisplaycode: $('input[name="lngInsertUserCode"]').val(),
            strdrafteruserdisplayname: $('input[name="strInsertUserName"]').val(),
            //顧客
            strcompanydisplaycode: $('input[name="lngCustomerCode"]').val(),
            strcompanydisplayname: $('input[name="strCustomerName"]').val(),
            //顧客担当者
            strcustomerusername: $('input[name="strCustomerUserName"]').val(),
            //納品日
            dtmdeliverydate: $('input[name="dtmDeliveryDate"]').val(),
            //納品先
            strdeliveryplacecompanydisplaycode: $('input[name="lngDeliveryPlaceCode"]').val(),
            strdeliveryplacename: $('input[name="strDeliveryPlaceName"]').val(),
            //納品先担当者
            strdeliveryplaceusername: $('input[name="strDeliveryPlaceUserName"]').val(),
            //備考
            strnote: $('input[name="strNote"]').val(),
            //消費税区分
            lngtaxclasscode: $('select[name="lngTaxClassCode"]').children('option:selected').val(),
            strtaxclassname: $('select[name="lngTaxClassCode"]').children('option:selected').text(),
            //消費税率
            lngtaxcode: $('select[name="lngTaxRate"]').children('option:selected').val(),
            curtax: $('select[name="lngTaxRate"]').children('option:selected').text().replace("%", "") * 0.01,
            //消費税額
            strtaxamount: Number(($('input[name="strTaxAmount"]').val()).split(',').join('')),
            //適用レート
            curconversionrate: $('input[name="curConversionRate"]').val(),
            //支払期限
            dtmpaymentlimit: $('input[name="dtmPaymentLimit"]').val(),
            //支払方法
            lngpaymentmethodcode: $('select[name="lngPaymentMethodCode"]').children('option:selected').val(),
            //支払方法
            strpaymentmethodname: $('select[name="lngPaymentMethodCode"]').children('option:selected').text(),
            //合計金額
            curtotalprice: Number(($('input[name="strTotalAmount"]').val()).split(',').join('')),
        };

        return result;
    }

    // 出力明細一覧エリアのPOST用データ収集
    function getUpdateDetail() {
        var result = [];

        $.each($('#EditTableBody tr'), function (i, tr) {

            var param = {
                //No.（行番号）
                rownumber: $(tr).children('[name="rownum"]').text(),
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
        for (var x in data) {
            if (data[x] == undefined || data[x] == null) {
                continue;
            }
            var _val = data[x].replace(/'/g, "\'");
            html += "<input type='hidden' name='" + x + "' value='" + _val + "' >";
        }
        html += '</form>';
        $("body").append(html);

        $('#temp_form').attr("action", url);
        $('#temp_form').attr("target", target);
        $('#temp_form').attr("method", "POST");
        $('#temp_form').submit();

        // フォームを削除
        $('#temp_form').remove();
    }

    // --------------------------------------------------------------------------------
    //   日付計算ヘルパ関数
    // --------------------------------------------------------------------------------
    // nヶ月前後の年月日を取得する
    function getAddMonthDate(year, month, day, add) {
        var addMonth = month + add;
        var endDate = getEndOfMonth(year, addMonth);//add分を加えた月の最終日を取得

        //引数で渡された日付がnヶ月後の最終日より大きければ日付を次月最終日に合わせる
        //5/31→6/30のように応当日が無い場合に必要
        if (day > endDate) {
            day = endDate;
        } else {
            day = day - 1;
        }

        var addMonthDate = new Date(year, addMonth - 1, day);
        return addMonthDate;
    }
    //今月の月末日を取得
    //※次月の0日目＝今月の末日になる
    function getEndOfMonth(year, month) {
        var endDate = new Date(year, month, 0);
        return endDate.getDate();
    }

    // 締め日をもとに月度を計算する
    function getMonthlyBasedOnClosedDay(targetDate, closedDay) {
        var targetYear = targetDate.getFullYear();
        var targetMonth = targetDate.getMonth() + 1;
        var targetDay = targetDate.getDate();

        if (targetDay > closedDay) {
            // 対象日 > 締め日 なら翌月度
            return getAddMonthDate(targetYear, targetMonth, 1, +1);
        } else {
            // 対象日 <= 締め日 なら当月度
            return new Date(targetYear, targetMonth, 1);
        }
    }
    // --------------------------------------------------------------------------------

    // ------------------------------------------
    //    バリデーション関連
    // ------------------------------------------
    // 出力明細エリアに明細が1行以上存在するなら true
    function existsEditDetail() {
        return $("#EditTableBody tr").length > 0;
    }

    // 納品日の月が締済みであるなら true
    function isClosedMonthOfDeliveryDate(deliveryDate, closedDay) {
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
    function withinOneMonthBeforeAndAfter(targetDate) {
        // システム日付
        var nowDate = new Date();
        var nowYear = nowDate.getFullYear();
        var nowMonth = nowDate.getMonth() + 1;
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
    function existsInDifferentDetailDeliveryMonthly(deliveryDate, closedDay) {

        // 納品日の月度
        var deliveryMonthly = getMonthlyBasedOnClosedDay(deliveryDate, closedDay);

        // 各明細の納期の月度を取得する
        var aryDetailDeliveryMonthly = [];
        $("#EditTableBody tr").each(function () {
            // 明細の納期を取得
            var arr = $(this).children('td.detailDeliveryDate').text().split('/');
            var detailDeliveryDate = new Date(arr[0], arr[1] - 1, arr[2]);

            // 納期の月度
            var detailDeliveryMonthly = getMonthlyBasedOnClosedDay(detailDeliveryDate, closedDay);
            // 配列に追加
            aryDetailDeliveryMonthly.push(detailDeliveryMonthly);
        });

        // 納期の月度が納品日の月度と一致しない明細が１つでもあったらエラー
        var indifferentDetailExists = aryDetailDeliveryMonthly.some(function (element) {
            return (element.getTime() != deliveryMonthly.getTime());
        });

        return indifferentDetailExists;
    }

    // 出力明細エリアの各明細の売上区分の統一性をチェック。チェックOKならtrue、NGならfalse
    function checkEditDetailsAreSameSalesClass() {
        // 出力明細エリアにあるすべての明細の明細統一フラグと売上区分コードを取得する
        var aryDetailUnifiedFlg = [];
        var aryDetailSalesClassCode = [];
        $("#EditTableBody tr").each(function () {
            // 明細統一フラグを取得して配列に追加
            aryDetailUnifiedFlg.push($(this).children('td.detailUnifiedFlg').text());
            // 売上区分コードを取得して配列に追加
            aryDetailSalesClassCode.push($(this).children('td.detailSalesClassCode').text());
        });

        // １．全行の売上区分マスタの明細統一フラグがfalse -> OKとしてチェック終了。そうでないなら２．へ
        var allDetailUnifiedFlgIsFalse = aryDetailUnifiedFlg.every(function (element) {
            return (element.toUpperCase() == 'F');
        });
        if (allDetailUnifiedFlgIsFalse) {
            return true;
        }

        // ２．売上区分マスタの明細統一フラグがtrueである明細の件数 != 出力明細一覧エリアの明細行数 を満たすなら NGとしてチェック終了。そうでないなら３．へ
        var aryDetailUnifiedFlgIsTrue = aryDetailUnifiedFlg.filter(function (element) {
            return (element.toUpperCase() == 'T');
        });
        if (aryDetailUnifiedFlgIsTrue.length != $("#EditTableBody tr").length) {
            return false;
        }

        // ３．出力明細一覧エリアの明細の売上区分コードがすべて同じ値 -> OKとしてチェック終了。そうでないなら NGとしてチェック終了
        var allDetailSalesClassCodeHasSameValue = aryDetailSalesClassCode.every(function (element) {
            return (element == aryDetailSalesClassCode[0]);
        });

        return allDetailSalesClassCodeHasSameValue;
    }

    // プレビュー前バリデーションチェック
    function varidateBeforePreview(closedDay) {
        // 出力明細エリアに明細が一行もない
        if (!existsEditDetail()) {
            alert("出力明細が選択されていません");
            return false;
        }

        // ヘッダ・フッタ部の納品日を取得
        var deliveryDate = new Date($('input[name="dtmDeliveryDate"]').val());

        // 納品日の月が締済みである
        if (isClosedMonthOfDeliveryDate(deliveryDate, closedDay)) {
            alert("締済みのため、指定された納品日は無効です");
            return false;
        }

        // 納品日がシステム日付の前後一ヶ月以内にない
        if (!withinOneMonthBeforeAndAfter(deliveryDate)) {
            alert("納品日は今月の前後1ヶ月の間を指定してください");
            return false;
        }

        // 出力明細一覧エリアの明細に、ヘッダ部の納品日の月度と同月度ではない納期の明細が存在する
        if (existsInDifferentDetailDeliveryMonthly(deliveryDate, closedDay)) {
            alert("出力明細には、入力された納品日と異なる月に納品された明細を指定できません");
            return false;
        }

        // 出力明細一覧エリアの明細各行の売上区分統一性チェック
        if (!checkEditDetailsAreSameSalesClass()) {
            alert("売上区分の混在ができない明細が選択されています");
            return false;
        }

        // バリデーション成功
        return true;
    }

    // プレビュー画面を表示する（バリデーションあり）
    function displayPreview() {

        // プレビュー画面のウィンドウ属性の定義
        var target = "previewWin";
        var features = "width=900,height=800,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";

        // 先に空のウィンドウを開いておく
        var emptyWin = window.open('', target, features);

        // POSTデータ構築
        var data = {
            strMode: "display-preview",
            strSessionID: $('input[name="strSessionID"]').val(),
            lngRenewTargetSlipNo: $('input[name="lngSlipNo"]').val(),
            lngRenewTargetRevisionNo: $('input[name="lngRevisionNo"]').val(),
            strRenewTargetSlipCode: $('input[name="strSlipCode"]').val(),
            lngRenewTargetSalesNo: $('input[name="lngSalesNo"]').val(),
            strRenewTargetSalesCode: $('input[name="strSalesCode"]').val(),
            aryHeader: getUpdateHeader(),
            aryDetail: getUpdateDetail(),
        };

        $.ajax({
            type: 'POST',
            url: 'preview.php',
            data: data,
            async: true,
        }).done(function (data) {
            console.log("done");
            console.log(data);

            var url = "/sc/regist2/preview.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
            var previewWin = window.open('', target, features);
            previewWin.document.open();
            previewWin.document.write(data);
            previewWin.document.close();

            //再読み込みなしでアドレスバーのURLのみ変更
            emptyWin.history.pushState(null, null, url);

        }).fail(function (error) {
            console.log("fail");
            console.log(error);
            emptyWin.close();
        });
    }

    // ------------------------------------------
    //   events
    // ------------------------------------------
    $("select[name='lngTaxClassCode']").on('change', function () {
        // 消費税区分を取得
        var taxClassCode = $('select[name="lngTaxClassCode"]').children('option:selected').val();
        if (taxClassCode == 1) {
            // $('select[name="lngTaxRate"]').append('<option value=""></option>');
            $('select[name="lngTaxRate"]').prop("selectedIndex", 0);
        } else {
            $('select[name="lngTaxRate"]').prop("selectedIndex", 1);
        }

        updateAmount();


    });

    $('select[name="lngTaxRate"]').on('change', function () {
        updateAmount();
    });

    $('input[name="dtmDeliveryDate"]').on('change', function () {

        // POST先
        var postTarget = $('input[name="ajaxPostTarget"]').val();

        //消費税率の選択項目変更
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: {
                strMode: "change-deliverydate",
                strSessionID: $('input[name="strSessionID"]').val(),
                dtmDeliveryDate: $(this).val(),
            },
            async: true,
        }).done(function (data) {
            console.log("done:change-deliverydate");
            console.log(data);

            //消費税率の選択項目更新
            $('select[name="lngTaxRate"] > option').remove();
            $('select[name="lngTaxRate"]').append(data);

            //金額の更新
            updateAmount();

        }).fail(function (error) {
            console.log("fail:change-deliverydate");
            console.log(error);
        });

        var lngMonetaryUnitCode = $('input[name="lngMonetaryUnitCode"]').val();
        var lngMonetaryRateCode = $('input[name="lngMonetaryUnitCode"]').val();
        // 適用レートの取得
        if (lngMonetaryUnitCode != "" && lngMonetaryRateCode != "") {
            // リクエスト送信
            $.ajax({
                url: '/pc/regist/getMonetaryRate.php',
                type: 'post',
                data: {
                    'strSessionID': $.cookie('strSessionID'),
                    'lngMonetaryUnitCode': lngMonetaryUnitCode,
                    'lngMonetaryRateCode': lngMonetaryRateCode,
                    'dtmStockAppDate': $('input[name="dtmDeliveryDate"]').val()
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
        }

    });


    // $('input[id="DetailTableBodyAllCheck"').on({
    //     'click': function () {
    //         alert("test");
    //         var status = this.checked;
    //         $('input[type="checkbox"][name="edit"]')
    //             .each(function () {
    //                 this.checked = status;
    //             });
    //     }
    // });


    // 検索条件入力ボタン押下
    $('img.search').on('click', function () {

        //出力明細一覧エリアの1行目の売上区分コードを取得する
        var firstRowSalesClassCode = "";
        var firstTr = $("#EditTableBody tr").eq(0);
        if (0 < firstTr.length) {
            firstRowSalesClassCode = $(firstTr).children('.detailSalesClassCode').text();
        }

        // 納品書明細検索条件入力画面を別窓で開く
        var url = "/sc/regist2/condition.php" + "?strSessionID=" + $('input[name="strSessionID"]').val();
        var data = {
            strSessionID: $('input[name="strSessionID"]').val(),
            //顧客コード（表示用会社コード）
            strcompanydisplaycode: $('input[name="lngCustomerCode"]').val(),
            //出力明細一覧エリアの1行目の売上区分コード
            lngsalesclasscode: firstRowSalesClassCode,
        };

        var features = "width=710,height=460,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no";
        post_open(url, data, "conditionWin", features);
    });

    // 追加ボタン
    $('img.add').on('click', function () {

        var trArray = [];

        // 選択行の追加
        $("#tableA_chkbox tbody tr").each(function (index, tr) {

            if ($(tr).find('input[name="edit"]').prop('checked') == true) {

                trArray.push($("#tableA tbody tr:nth-child(" + (index + 1) + ")"));
            }
        });

        if (trArray.length < 1) {
            // alert("明細行が選択されていません。");
            return false;
        }

        // DBG:一時コメントアウト対象
        // 追加バリデーションチェック
        var invalid = false;
        $.each($(trArray), function (i, v) {
            if (!invalid) {
                invalid = !validateAdd($(v));
            }
        });
        if (invalid) {
            return false;
        }

        // 明細追加        
        $('#tableA_chkbox tbody tr').each(function (i, e) {
            var rownum = i + 1;
            console.log(rownum);
            var chkbox = $(this).find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                var row = $('#tableA tbody tr:nth-child(' + rownum + ')');
                var rn1 = row.find('td.detailReceiveNo').text();
                var dn1 = row.find('td.detailReceiveDetailNo').text();
                var rev1 = row.find('td.detailReceiveRevisionNo').text();
                var addObj = true;
                $('#tableB tbody tr').each(function (i, e) {
                    var rn2 = $(this).find('td.detailReceiveNo').text();
                    var dn2 = $(this).find('td.detailReceiveDetailNo').text();
                    var rev2 = $(this).find('td.detailReceiveRevisionNo').text();
                    if ((rn1 == rn2) && (dn1 == dn2) && (rev1 == rev2)) {
                        addObj = false;
                        return false;
                    }
                });
                console.log(addObj);
                if (addObj) {
                    // tableBの追加
                    $("#tableB tbody").append('<tr>' + $('#tableA tbody tr:nth-child(' + rownum + ')').html() + '</tr>');
                    var no = $("#tableB_no tbody").find('tr').length + 1;
                    $("#tableB_no tbody").append('<tr><td>' + no + '</td></tr>');

                    var lasttr = $("#tableB tbody").find('tr').last();
                    lasttr.find('td:nth-child(1)').css('display', 'none');
                }

            }
        });

        $('#tableB_no tbody tr td').width($('#tableB_no_head thead tr th').width());

        resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

        // tableBのリセット
        for (var i = $('#tableA_chkbox tbody tr').length; i > 0; i--) {
            var row = $('#tableA_chkbox tbody tr:nth-child(' + i + ')');
            var chkbox = row.find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                row.remove();
                $('#tableA tbody tr:nth-child(' + i + ')').remove();
            }
        }

        $('#tableA tbody tr').each(function (i, e) {
            $(this).find('td:nth-child(1)').text(i + 1);
        });

        // selectRow($("#tableB_no"), $("#tableB"));
        selectRow("", $("#tableB_no"), $("#tableB"), "");
        // 合計金額・消費税額の更新
        updateAmount();


        $("#tableA_head").trigger("update");
        $("#tableA").trigger("update");

        if ($('#tableA_chkbox tbody tr').length == 0) {
            $("#tableA tbody tr td").width('');
            $("#tableA thead tr th").width('');
            $("#tableA_head tr th").width('');
        }
    });

    // 全削除ボタンのイベント
    $('img.alldelete').on('click', function () {

        // テーブルBのデータをすべてテーブルAに移動する
        deleteAllRows($("#tableA"), $("#tableA_head"), $("#tableA_chkbox"), $("#tableA_chkbox_head"), $("#tableB"), $("#tableB_no"), $("#allChecked"), '.detailReceiveNo')

        // テーブル行クリックイベントの設定
        selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));

        $("#tableA_head").trigger("update");
        $("#tableA").trigger("update");

        // 合計金額・消費税額の更新
        updateAmount();
    });

    // 削除ボタンのイベント
    $('img.delete').on('click', function () {

        // テーブルBの選択されたデータをテーブルAに移動する
        deleteRows($("#tableA"), $("#tableA_head"), $("#tableA_chkbox"), $("#tableA_chkbox_head"), $("#tableB"), $("#tableB_no"), $("#allChecked"), '.detailReceiveNo');
        $("#tableA_head").trigger("update");
        $("#tableA").trigger("update");
        // テーブル行クリックイベントの設定
        selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));

        // 合計金額・消費税額の更新
        updateAmount();
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


    // プレビューボタン押下
    $('img.preview').on('click', function () {
        // 納品先
        var lngDeliveryPlaceCode = $('input[name="lngDeliveryPlaceCode"]').val();
        if (lngDeliveryPlaceCode.length == 0) {
            alert("納品先を設定してください。");
            return;
        }
        // POST先
        var postTarget = $('input[name="ajaxPostTarget"]').val();

        // POSTデータ構築
        var data = {
            strMode: "get-closedday",
            strSessionID: $('input[name="strSessionID"]').val(),
            strcompanydisplaycode: $('input[name="lngCustomerCode"]').val(),
        };

        // プレビュー前のバリデーションに「締め日」が必要なのでajaxで取得する
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: data,
            async: true,
        }).done(function (data) {
            console.log("done:get-closedday");
            console.log(data);

            // 締め日
            var closedDay = data;

            // 顧客コードに対応する締め日が取得できないとそもそもバリデーションできない
            if (!closedDay) {
                alert("顧客コードに対応する締め日が取得できません。");
                return false;
            }

            if (closedDay < 0) {
                alert("顧客コードに対応する締め日が負の値です。");
                return false;
            }

            // DBG:一時コメントアウト対象
            // プレビュー画面表示前のバリデーションチェック
            if (!varidateBeforePreview(closedDay)) {
                return false;
            }

            // プレビュー画面表示
            displayPreview();

        }).fail(function (error) {
            console.log("fail:get-closedday");
            console.log(error);
        });

    });

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


    // 閉じた際の処理
    $(window).on('beforeunload', function () {
        window.opener.location.reload();
    });

    $("img.close").on('click', function () {
        window.close();
    });

});