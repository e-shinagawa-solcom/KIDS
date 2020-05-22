(function () {
    // フォームサブミット抑止
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // ToDo  msw.jsと内容はほぼ同じなので1つにまとめたい。
    var apply = function (handleName, docMsw) {
        var code = $('input[name=' + handleName + ']');
        code.val(docMsw.find('select.result-select').find('option:selected').attr('code'));
        // mswの非表示
        invokeMswClose(docMsw);
        // 顧客コードチェンジイベントキック
        code.trigger('change');
        // 顧客が変更された場合
        if (handleName == 'lngCustomerCode') {
            customerChangeReset();
        }
    };

    // 顧客が変更された場合は明細関連をリセットする
    var customerChangeReset = function () {
        // 出力明細一覧を削除
        $('#btnAllDelete').trigger('click');
        // 前月請求残額(消費税込み)
        $('input[name="curlastmonthbalance"]').val(0).change();
        // 消費税
        $('input[name="curtaxprice"]').val(0).change();
        // 当月請求額
        $('input[name="curthismonthamount"]').val(0).change();
        // 差引合計額
        $('input[name="notaxcurthismonthamount"]').val(0).change();
        // 締め日の計算
        btnbilling.trigger('click');
    }

    // 閉じるボタン処理の呼び出し
    var invokeMswClose = function (msw) {
        msw.find('.msw-box__header__close-btn').trigger('click');
    };


    // 検索結果ダブルクリックで適用する
    $(".result-select").on("dblclick", function () {
        mswBox.find('img.apply').trigger('click');
        mswBox.find('img.msw-box__header__close-btn').trigger('click');
    });

    // 請求日変更の処理
    $('input[name="ActionDate"]').on("change", function () {
        selectClosedDay();
    });

    // 請求モード変更の処理
    $('input[name="invoiceMode"]:radio').on("change", function () {
        console.log("change");
        selectClosedDay();
    });

    // 開始日時フォーカスを取ったときの処理
    $('input[name="ActionDate"]').on('blur', function () {
        blurDate($(this));
    });

    // 顧客コード変更の処理
    $('input[name="lngCustomerCode"]').on("change", function () {
        var msg = '請求対象の明細をすべてクリアします。\nよろしいですか？';
        var $tableA_rows = $('#tableA tbody tr');
        var $tableA_rows_length = $tableA_rows.length;

        var warn = ($tableA_rows_length > 0) ? true : false;

        if (warn && window.confirm(msg) === false) {
            return;
        }

        $tableA_rows.remove();
        $('#tableA_chkbox tbody tr').remove();

        selectClosedDay();
    });

    // 締め日を取得する
    var isCloseDay;
    var selectClosedDay = function () {

        var customerCode = $('input[name="lngCustomerCode"]');
        var customerName = $('input[name="strCustomerName"]');
        var billingDate = $('input[name="ActionDate"]');
        // 請求日が未入力
        if (isEmpty(billingDate.val()) == '0') {
            console.log('none 請求日');
            return;
        }

        switch (isEmpty(customerCode.val()) + isEmpty(customerName.val())) {
            // どちらも未入力
            case '00':
                return;
                break;

            // いずれかが何かが入力されている
            case '01':
            case '10':
            case '11':
                var condition = {
                    data: {
                        QueryName: 'selectClosedDayByCodeAndName',
                        Conditions: {
                            customerCode: customerCode.val(),
                        }
                    }
                };
                break;
            default:
                break;
        }
        // マスター検索実行
        queryMasterData(condition, setResult, setNodata);
    };

    // 真偽値の文字列表現を取得
    function isEmpty(val) {
        if (val) {
            return '1';
        } else {
            return '0';
        }
    }

    // 検索結果から自/至を算出しセット
    function setResult(response) {

        console.log(response);
        if (isEmpty(response[0].lngclosedday) == 0) {
            alert('締め日の取得に失敗しました。');
            return false;
        }

        // 自/至を取得
        var [start, end] = getClosedDay(response[0].lngclosedday);
        // 自/至セット
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd = $('input[name="dtmchargeternend"]');
        var billingMonth = $('#invoiceMonth');

        billingStart.val(start).change();
        billingEnd.val(end).change();
        // 請求月セット
        billingMonth.val(end.split('/')[1]).change();
        return true;


    }

    // 検索結果0件の時optionにNoDataをセット
    function setNodata(response) {
        console.log('0件');
        console.log(response.responseText);
    }

    // 締め日から自至を算出する
    function getClosedDay(close) {
        var billingDate = $('input[name="ActionDate"]').val();
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd = $('input[name="dtmchargeternend"]');
        if (billingDate.length == 8) {
            var y = billingDate.substr(0, 4);
            var m = billingDate.substr(4, 2);
            var d = billingDate.substr(6, 2);
            billingDate = y + "/" + m + "/" + d;
        }
        var dateLength = splitDate(billingDate);
        // 請求モード
        var invoiceMode = $('input[name="invoiceMode"]:checked').val();
        console.log("モード：" + invoiceMode);
        console.log(invoiceMode == '1');
        // 請求日が未入力
        if (isEmpty(billingDate) == '0') {
            return [false, false];
        }

        // 請求日の形式がおかしい
        if (dateLength == false) {
            return [false, false];
        }

        // 請求モードが請求日モードの場合
        if (invoiceMode == '1') {
            var start = billingDate;
            var end = billingDate;
        } else {
            if (close == 0) {
                var date1 = new Date(billingDate + ' 02:00');
                // 今月初日
                var first_date = new Date(date1.getFullYear(), date1.getMonth(), 1);
                // 今月末日
                var last_date = new Date(date1.getFullYear(), date1.getMonth() + 1, 0);
                // 自の取得
                var start = first_date.getFullYear() + '/' + ("00" + (first_date.getMonth() + 1)).slice(-2) + '/' + ("00" + first_date.getDate()).slice(-2);
                // 至の取得
                var end = last_date.getFullYear() + '/' + ("00" + (last_date.getMonth() + 1)).slice(-2) + '/' + ("00" + last_date.getDate()).slice(-2);
            } else {
                var date1 = new Date(billingDate + ' 00:00');
                console.log(date1.getDate());
                if (date1.getDate() <= close) {
                    // 今月の取得
                    var curr_month = new Date(date1.getFullYear(), date1.getMonth(), 1);
                    // 先月の取得
                    var last_month = new Date(date1.getFullYear(), date1.getMonth() - 1, close);
                    last_month.setDate(last_month.getDate() + 1);

                    // 自の取得
                    var start = last_month.getFullYear() + '/' + ("00" + (last_month.getMonth() + 1)).slice(-2) + '/' + ("00" + last_month.getDate()).slice(-2);
                    // 至の取得
                    var end = curr_month.getFullYear() + '/' + ("00" + (curr_month.getMonth() + 1)).slice(-2) + '/' + ("00" + close).slice(-2);
                } else {
                    // 今月の取得
                    var curr_month = new Date(date1.getFullYear(), date1.getMonth(), close);
                    curr_month.setDate(curr_month.getDate() + 1);
                    // 自の取得
                    var start = curr_month.getFullYear() + '/' + ("00" + (curr_month.getMonth() + 1)).slice(-2) + '/' + ("00" + curr_month.getDate()).slice(-2);
                    // 至の取得
                    var end = date1.getFullYear() + '/' + ("00" + (date1.getMonth() + 2)).slice(-2) + '/' + ("00" + close).slice(-2);

                    console.log(start);
                    console.log(end);
                }
            }
        }
        // 返却
        return [start, end];
    }

    // 請求日の日付をチェックして正しければ「/」で分割
    function splitDate(str) {

        // 日付フォーマット yyyy/mm(m)/dd(d)形式
        var regDate = /(\d{4})\/(\d{1,2})\/(\d{1,2})/;

        // yyyy/mm/dd形式か
        if (!(regDate.test(str))) {
            return false;
        }

        // 日付文字列の字句分解
        var regResult = regDate.exec(str);
        var yyyy = regResult[1];
        var mm = regResult[2];
        var dd = regResult[3];
        var di = new Date(yyyy, mm - 1, dd);
        // 日付の有効性チェック
        if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
            return regResult;
        }

        return false;
    };

    // 納品書明細検索ボタン押下処理
    $('#search-condition').on({
        'click': function () {
            // validationキック
            var form = $('form[name="Invoice"]');
            // From/Toが入力されていたらチェック
            let $dtmFrom = $('input[name="From_dtmDeliveryDate"]').val();
            let $dtmTo = $('input[name="To_dtmDeliveryDate"]').val();
            if ($dtmFrom && $dtmTo) {
                let d1 = new Date($dtmFrom);
                let d2 = new Date($dtmTo);
                if (d1 > d2) {
                    alert('納品日（TO）が納品日（FROM）より過去の日です');
                    return false;
                }
            }

            // クリアチェック (顧客コードまたは通貨または課税区分が初期値と異なる場合)
            let bReset = false;
            let changeCode = false;
            // 親顧客コード
            var parentCustomerCode = window.opener.$('input[name="lngCustomerCode"]').val();
            var parentCustomerName = window.opener.$('input[name="strCustomerName"]').val();
            // 顧客コード
            let customerCode = $('input[name="lngCustomerCode"]').val();
            let customerName = $('input[name="strCustomerName"]').val();


            if (parentCustomerCode != customerCode) {
                bReset = true;
                changeCode = true;
            }

            let $tableB = window.opener.$('#tableB');
            // テーブルB <tbody>, <tr>
            let $tableB_tbody = $('tbody', $tableB);
            let $tableB_row = $('tr', $tableB_tbody);
            if ($tableB_row.length > 0) {
                for (var i = 0, rowlen = $tableB_row.length; i < rowlen; i++) {
                    for (var j = 0, collen = $tableB_row[i].cells.length; j < collen; j++) {
                        if ($tableB_row[i].cells[j].className == 'taxclass') {
                            // 課税区分
                            var parentTaxCode = $tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
                        }
                    }
                }

                let taxClassCode = $('select[name="lngTaxClassCode"]').val();
                if (taxClassCode != parentTaxCode) {
                    bReset = true;
                }
            }

            if (form.valid() == false) {
                return;
            }

            // 入力値を取得
            // 顧客
            var params = {
                mode: 'ajax',
                strSessionID: $('input[name="strSessionID"]').val(),
                QueryName: 'selectClosedDayByCodeAndName',
                conditions: {
                    customerCode: customerCode,
                    customerName: customerName,
                    strSlipCode: $('input[name="strSlipCode"]').val(),
                    deliveryFrom: $('input[name="From_dtmDeliveryDate"]').val(),
                    deliveryTo: $('input[name="To_dtmDeliveryDate"]').val(),
                    dtmChargeternStart: $('input[name="dtmChargeternStart"]').val(),
                    dtmChargeternEnd: $('input[name="dtmChargeternEnd"]').val(),
                    deliveryPlaceCode: $('input[name="lngDeliveryPlaceCode"]').val(),
                    deliveryPlaceName: $('input[name="strDeliveryPlaceName"]').val(),
                    moneyClassCode: $('select[name="lngMoneyClassCode"]').val(),
                    taxClassCode: $('select[name="lngTaxClassCode"]').val(),
                    inChargeUserCode: $('input[name="lngInChargeUserCode"]').val(),
                    inChargeUserName: $('input[name="strInChargeUserName"]').val(),
                    inputUserCode: $('input[name="lngInputUserCode"]').val(),
                    inputUserName: $('input[name="lngInputUserName"]').val(),
                }
            }

            // 検索する
            var search = {
                url: '/inv/regist/condition.php?strSessionID=' + $.cookie('strSessionID'),
                type: 'post',
                dataType: 'json',
                data: params,
            };

            $.ajax(search)
                .done(function (response) {
                    console.log(response);
                    // 親ウィンドウの存在チェック
                    if (!window.opener || window.opener.closed) {
                        // 親ウィンドウが存在しない場合
                        window.alert('メインウィンドウが見当たりません。');
                    }
                    else {
                        if (response.Message) {
                            alert(response.Message);
                            return;
                        }
                        var msg = '選択された明細を全てクリアしますが、よろしいですか？';
                        var $tableA_rows = window.opener.$('#tableA tbody tr');
                        var $tableA_rows_length = $tableA_rows.length;
                        var warn = ($tableA_rows_length > 0) ? true : false;
                        if (warn && window.confirm(msg) === false) {
                            return;
                        }

                        // 顧客コード変更
                        if (changeCode == true) {
                            window.opener.$('input[name="lngCustomerCode"]').val(customerCode).change();
                            window.opener.$('input[name="strCustomerName"]').val(customerName);
                        }
                        // TABLE作成
                        window.opener.$.createTable(response);

                        // テーブル初期化
                        if (bReset == true) {
                            // 出力明細一覧を削除
                            window.opener.$('#alldelete').trigger('click');
                            // 前月請求残額(消費税込み)
                            window.opener.$('input[name="curlastmonthbalance"]').val(0).change();
                            // 消費税
                            window.opener.$('input[name="curtaxprice"]').val(0).change();
                            // 当月請求額
                            window.opener.$('input[name="curthismonthamount"]').val(0).change();
                            // 差引合計額
                            window.opener.$('input[name="notaxcurthismonthamount"]').val(0).change();
                        }

                        // 通貨変更
                        var lngMoneyClassCode = $('select[name="lngMoneyClassCode"] option:selected').val();
                        window.opener.$('input[name="lngmonetaryunitcode"]').val(lngMoneyClassCode);
                        if (lngMoneyClassCode == '1') {
                            window.opener.$('span.moneyclass').text("\xA5");
                        } else {
                            window.opener.$('span.moneyclass').text($('select[name="lngMoneyClassCode"] option:selected').text());

                        }


                        window.close();
                    }
                })
                .fail(function (response) {
                    console.log(response);
                    alert('検索に失敗しました。\n条件を変更して下さい。');
                    return;
                });
            return false;
        }
    });

    // PREVIEWボタン押下処理 (preview)
    $('#preview').on({
        'click': function () {
            console.log("previw valid");
            // validationキック
            if ($('form[name="Invoice"]').valid() == false) {
                return;
            }
            // // 金額計算
            // billingAmount();
            // プレビュー画面呼び出し (遅延させないとINPUT取得できない)
            var prev = setTimeout(previewDrow, 800);
        }
    });


    var previewDrow = function () {
        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // 納品書番号を取得する。slipcode
        var slipCodeList = [];
        var customerNoList = [];
        var slipNoList = [];
        var revisionNoList = [];
        // 納品日を格納する
        var deliveryDate = [];
        // 最初の課税区分を取得する。
        var taxclass = false;
        // 最初の税率を取得する。
        var tax = false;
        // 税率が同じかをチェックするフラグ
        var isSameTax = true;
        var isError = false;
        var len = 0;
        tableB_row.each(function () {
            slipNoList.push($(this).attr('slipno'));
            revisionNoList.push($(this).attr('revisionno'));
            len += 1;
            var strCustomerNo = $(this).find('.customerno').find('input:text').val();
            if (strCustomerNo == "") {
                alert(len + "行目の顧客NO.が入力されていません。");
                isError = true;
                return false;
            }
            if (strCustomerNo.length > 10) {
                alert(len + "行目の顧客NO.が10桁まで入力してください。");
                isError = true;
                return false;
            }
        });
        
        if (isError) {
            return;
        }

        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                // if (!tableB_row[i].cells[j].innerText) continue;

                if (tableB_row[i].cells[j].className.substr(0, 'slipcode'.length) == 'slipcode') {
                    // 納品書No
                    slipCodeList.push(tableB_row[i].cells[j].innerText);
                }
                if (tableB_row[i].cells[j].className.substr(0, 'customerno'.length) == 'customerno') {
                    console.log(tableB_row[i].cells[j].querySelector('input').value);
                    // 顧客No
                    customerNoList.push(tableB_row[i].cells[j].querySelector('input').value);
                }
                if (tableB_row[i].cells[j].className.substr(0, 'tax right'.length) == 'tax right' && !tax) {
                    // 消費税
                    tax = tableB_row[i].cells[j].innerText;
                    console.log('消費税セット');
                }
                if (tableB_row[i].cells[j].className.substr(0, 'taxclass'.length) == 'taxclass' && !taxclass) {
                    // 税抜き金額
                    taxclass = tableB_row[i].cells[j].innerText;
                }
                if (tableB_row[i].cells[j].className.substr(0, 'deliverydate'.length) == 'deliverydate') {
                    // 納品日
                    deliveryDate.push(tableB_row[i].cells[j].innerText);
                }
                if (tableB_row[i].cells[j].className.substr(0, 'tax right'.length) == 'tax right' && tax) {
                    console.log('消費税率チェック');
                    if (tableB_row[i].cells[j].innerText != tax) {
                        console.log('消費税NG');
                        isSameTax = false;
                    }
                }
            }
        }
        console.log(customerNoList);

        // エラーチェックで問題なければ確認画面表示

        // プレビューバリデーションチェック
        // 出力明細一覧エリアに明細が1行も存在しない場合
        if (slipCodeList.length === 0) {
            alert('出力明細が選択されていません');
            return false;
        }
        // 出力明細一覧エリアに選択された納品書の消費税率がすべて同一ではない場合
        if (isSameTax == false) {
            alert('消費税率の異なる納品書は請求書の明細に混在できません');
            return false;
        }
        // 請求日が「自」より前の日付を指定する場合、
        let activeDate = new Date($('input[name="ActionDate"]').val());
        // 2システム日付と顧客の締め日から、当日の締め月を求める。
        let systemDate = new Date();
        if (isCloseDay != 0) {
            if (systemDate.getDate() > isCloseDay) {
                var ternstart = new Date(systemDate.getFullYear(), systemDate.getMonth(), isCloseDay + 1);
            } else {
                systemDate.setMonth(systemDate.getMonth() - 1);
                var ternstart = new Date(systemDate.getFullYear(), systemDate.getMonth(), isCloseDay);
            }
        } else {
            var ternstart = new Date(systemDate.getFullYear(), systemDate.getMonth(), 1);
        }
        if (activeDate < ternstart) {
            alert('締済みのため、指定された請求日は無効です');
            return false;
        }

        // 納品日が「自」の1ヶ月前から「至」までの期間以外ない場合
        let start = new Date($('input[name="dtmchargeternstart"]').val());
        start.setMonth(start.getMonth() - 1);
        let end = new Date($('input[name="dtmchargeternend"]').val());
        let isDeliveryDate = true;

        $.each(deliveryDate, function (i, v) {
            let deliDate = new Date(v);

            if (deliDate < start || deliDate > end) {
                isDeliveryDate = false;
            }
        });

        if (isDeliveryDate == false) {
            alert('出力明細には、入力された請求月およびその前月に納品された明細のみ指定してください');
            return false;
        }

        var strMode = $('input[name="strMode"]').val();

        // 既存フォーム削除
        var delold = document.getElementsByName('slipCodeList');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        var delold = document.getElementsByName('customerNoList');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('slipNoList');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('revisionNoList');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('mode');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('taxclass');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        delold = document.getElementsByName('tax');
        if (delold.length > 0) {
            for (var i = 0; i < delold.length; i++) {
                delold[i].parentNode.removeChild(delold[i]);
            }
        }
        // フォーム追加
        var ele1 = document.createElement('input');
        // データを設定
        ele1.setAttribute('type', 'hidden');
        ele1.setAttribute('name', 'slipCodeList');
        ele1.setAttribute('value', slipCodeList);
        // 要素を追加
        document.Invoice.appendChild(ele1);
        // フォーム追加
        var ele2 = document.createElement('input');
        // データを設定
        ele2.setAttribute('type', 'hidden');
        ele2.setAttribute('name', 'mode');
        ele2.setAttribute('value', 'prev');
        // 要素を追加
        document.Invoice.appendChild(ele2);
        // フォーム追加
        var ele3 = document.createElement('input');
        // データを設定
        ele3.setAttribute('type', 'hidden');
        ele3.setAttribute('name', 'taxclass');
        ele3.setAttribute('value', taxclass);
        // 要素を追加
        document.Invoice.appendChild(ele3);
        // フォーム追加
        var ele4 = document.createElement('input');
        // データを設定
        ele4.setAttribute('type', 'hidden');
        ele4.setAttribute('name', 'tax');
        ele4.setAttribute('value', tax);
        // 要素を追加
        document.Invoice.appendChild(ele4);

        // フォーム追加
        var ele5 = document.createElement('input');
        // データを設定
        ele5.setAttribute('type', 'hidden');
        ele5.setAttribute('name', 'slipNoList');
        ele5.setAttribute('value', slipNoList);
        // 要素を追加
        document.Invoice.appendChild(ele5);

        // フォーム追加
        var ele6 = document.createElement('input');
        // データを設定
        ele6.setAttribute('type', 'hidden');
        ele6.setAttribute('name', 'revisionNoList');
        ele6.setAttribute('value', revisionNoList);
        // 要素を追加
        document.Invoice.appendChild(ele6);

        var ele7 = document.createElement('input');
        // データを設定
        ele7.setAttribute('type', 'hidden');
        ele7.setAttribute('name', 'customerNoList');
        ele7.setAttribute('value', customerNoList);
        // 要素を追加
        document.Invoice.appendChild(ele7);

        var invForm = $('form[name="Invoice"]');

        if (invForm.valid()) {

            var windowName = 'registPreview';
            if (strMode == 'renewPrev') {
                // 修正
                url = '/inv/regist/renew.php?strSessionID=' + $('input[name="strSessionID"]').val();
            } else {
                // 登録
                url = '/inv/regist/index.php?strSessionID=' + $('input[name="strSessionID"]').val();
            }

            // フォーム設定
            var windowPrev = open('about:blank', windowName, 'width=900, height=728, scrollbars=yes, resizable=yes');
            invForm.attr('action', url);
            invForm.attr('method', 'post');
            invForm.attr('target', windowName);


            // サブミット
            invForm.submit();

            return false;

        }
        else {
            // バリデーションのキック
            invForm.find(':submit').click();
        }

        return true;

    }

    // 登録処理 (insert)
    var insertCheck = function () {

        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // 納品書番号を取得する。slipcode
        var slipCodeList = [];
        // 最初の課税区分を取得する。
        var taxclass = false;
        // 最初の税率を取得する。
        var tax = false;

        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                if (!tableB_row[i].cells[j].innerText) continue;

                if (tableB_row[i].cells[j].className == 'slipcode') {
                    // 納品書No
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].innerText);
                    slipCodeList.push(tableB_row[i].cells[j].innerText);
                }
                if (tableB_row[i].cells[j].className == 'customerno') {
                    // 顧客No
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].querySelector('input').value);
                    customerNoList.push(tableB_row[i].cells[j].querySelector('input').value);
                }
                if (tableB_row[i].cells[j].className == 'tax right' && !tax) {
                    // 消費税
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].innerText);
                    tax = tableB_row[i].cells[j].innerText;
                }
                if (tableB_row[i].cells[j].className == 'taxclass' && !taxclass) {
                    // 税抜き金額
                    console.log(tableB_row[i].cells[j].className);
                    console.log(tableB_row[i].cells[j].innerText);
                    taxclass = tableB_row[i].cells[j].innerText;
                }
            }
        }

        // エラーチェックで問題なければ確認画面表示

        // フォーム追加
        var ele1 = document.createElement('input');
        // データを設定
        ele1.setAttribute('type', 'hidden');
        ele1.setAttribute('name', 'slipCodeList');
        ele1.setAttribute('value', slipCodeList);
        // 要素を追加
        document.Invoice.appendChild(ele1);
        // フォーム追加
        var ele2 = document.createElement('input');
        // データを設定
        ele2.setAttribute('type', 'hidden');
        ele2.setAttribute('name', 'mode');
        ele2.setAttribute('value', 'prev');
        // 要素を追加
        document.Invoice.appendChild(ele2);
        // フォーム追加
        var ele3 = document.createElement('input');
        // データを設定
        ele3.setAttribute('type', 'hidden');
        ele3.setAttribute('name', 'taxclass');
        ele3.setAttribute('value', taxclass);
        // 要素を追加
        document.Invoice.appendChild(ele3);
        // フォーム追加
        var ele4 = document.createElement('input');
        // データを設定
        ele4.setAttribute('type', 'hidden');
        ele4.setAttribute('name', 'tax');
        ele4.setAttribute('value', tax);
        // 要素を追加
        document.Invoice.appendChild(ele4);

        // フォーム追加
        var ele5 = document.createElement('input');
        // データを設定
        ele5.setAttribute('type', 'hidden');
        ele5.setAttribute('name', 'customerNoList');
        ele5.setAttribute('value', customerNoList);
        // 要素を追加
        document.Invoice.appendChild(ele5);

        var invForm = $('form[name="Invoice"]');
        if (invForm.valid()) {

            var windowName = 'registPreview';
            url = '/inv/regist/index.php?strSessionID=' + $.cookie('strSessionID');
            // フォーム設定
            var windowPrev = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
            invForm.attr('action', url);
            invForm.attr('method', 'post');
            invForm.attr('target', windowName);
            // サブミット
            invForm.submit();
            return false;
        }
        else {
            // バリデーションのキック
            form.find(':submit').click();
        }
        return true;
    };


    // function convertNumber(str) {
    //     if (str != "" && str != undefined && str != "null") {
    //         return Number(str).toLocaleString(undefined, {
    //             minimumFractionDigits: 0,
    //             maximumFractionDigits: 0
    //         });
    //     } else if (str == "0") {
    //         return str;
    //     } else {
    //         return "";
    //     }
    // }
})();
