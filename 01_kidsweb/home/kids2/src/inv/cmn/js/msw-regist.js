(function() {
    // フォームサブミット抑止
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // ToDo  msw.jsと内容はほぼ同じなので1つにまとめたい。
    var apply = function(handleName, docMsw){
        var code = $('input[name=' + handleName + ']');
        code.val(docMsw.find('select.result-select').find('option:selected').attr('code'));
        // mswの非表示
        invokeMswClose(docMsw);
        // 顧客コードチェンジイベントキック
        code.trigger('change');
        // 顧客が変更された場合
        if(handleName == 'lngCustomerCode')
        {
            customerChangeReset();
        }
    };

    // 顧客が変更された場合は明細関連をリセットする
    var customerChangeReset = function(){
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
    var invokeMswClose = function(msw){
        msw.find('.msw-box__header__close-btn').trigger('click');
    };

    // Mボタン押下処理 (顧客変更)
    $('img.msw-inv-button').on({
        'click': function() {
            var msg = '請求対象の明細をすべてクリアします。\nよろしいですか？';
            var cc = isEmpty($('input[name="lngCustomerCode"]').val());
            var cn = isEmpty($('input[name="strCustomerName"]').val());
            var warn = (cc == 1 || cn == 1) ? true : false;

            if(warn && window.confirm(msg) === false ) {
                return;
            }

            var mswName = $(this).attr('invokeMSWName');
            var ifmMsw = $('iframe.' + mswName);
            var docMsw = $(ifmMsw.get(0).contentWindow.document);

            // iframeのポジション,サイズ設定
            // iframeの表示領域を表示物(msw-box)のサイズに合わせる
            var mswBox = docMsw.find('.msw-box');
            var ifmHeight = mswBox.outerHeight(true);
            if(typeof mswBox.offset() !== 'undefined' ) {
                var ifmHeight = mswBox.offset().top + mswBox.outerHeight(true);
            }
            var ifmWidth = mswBox.offset().top + mswBox.outerWidth(true);
            var pos = setPosition(this, docMsw);
            ifmMsw.css({
                'position': 'absolute',
                'top': pos.top,
                'left': pos.left,
                'height': ifmHeight,
                'width': ifmWidth,
                'z-index': '9999'
            });

            var handleName = $(this).prev().prev().attr('name');
            ifmMsw.get(0).handler = handleName;
            docMsw.off('click', 'img.apply');
            docMsw.off('keydown', 'img.apply');
            docMsw.on('click', 'img.apply', function() {
                    apply(handleName, docMsw);
                }
            );
            docMsw.on('keydown', 'img.apply', function(e){
                    if(e.which == 13){
                        apply(handleName, docMsw);
                    }
                }
            );

            // MSW表示直前に実行させたい処理
            var mswBrfore = $(this).attr('msw-before');
            if(mswBrfore){
                eval(mswBrfore + '(handleName);');
            }

            // mswの表示
            invokeMswClose(docMsw);

            // ヘッダーの設定
            var headerWidth = docMsw.find('.msw-box__header').width();
            var btnCloseWidth = docMsw.find('.msw-box__header__close-btn').width();
            var btnCloseHeight = docMsw.find('.msw-box__header__close-btn').height();
            var headerbar = docMsw.find('.msw-box__header__bar');
            headerbar.css({
                'height': btnCloseHeight,
                'width': headerWidth - btnCloseWidth,
                'background-color': '#5495c8',
                'line-height': btnCloseHeight + 'px',
                'color': 'white',
                'font-size': '12px',
                'font-weight': 'bold',
                'text-indent': '1em'
            });

            // msw内の最初のinputにフォーカス
            docMsw.find('input[tabindex="1"]').focus();
        }
    });

    // mswのposition設定
    var setPosition = function(btn, docMsw) {
        // ボタンの親のライン
        var line = $(btn).parents('[class*="regist-line"]');
        var lineOffset = line.offset();

        var mswBox = docMsw.find('.msw-box');
        var mswBoxHeight = mswBox.outerHeight(true);
        var mswBoxWidth = mswBox.outerWidth(true);
        // msw初期位置
        var position = {top: line.position().top + line.height(), left: line.position().left};

        // mswの表示が画面に収まらない場合
        if(lineOffset.top + line.height() + mswBoxHeight > $(document).height() && $(document).height() > mswBoxHeight){
            // 画面の高さに収まらない高さ分を引く
            position.top -= $('[class^="form-box--"], [class="form-box"]').offset().top + position.top + line.height() + mswBoxHeight - $(document).height();
        }

        // msw横幅が画面に収まらない場合
        position.left -= Math.min(position.left, (position.left + mswBoxWidth > $(document).width() && $(document).width() > mswBoxWidth)?
        Math.abs(position.left + mswBoxWidth - $(document).width()) : 0);

        return position;
    }

    // 検索結果ダブルクリックで適用する
    $(".result-select").on("dblclick",  function(){
        mswBox.find('img.apply').trigger('click');
        mswBox.find('img.msw-box__header__close-btn').trigger('click');
    });

    $('.TxtStyle05L.billing-date.hasDatepicker').on("change", function(){
            selectClosedDay();
    });
    // 請求日ボタン押下時の処理
    var btnbilling = $('.billing-button').find('img');
    btnbilling.on({
        // クリック
        'click': function() {
            selectClosedDay();
        },
        // EnterKey
        'keypress': function(e) {
           if(e.which == 13){
               selectClosedDay();
            }
        }
    });

    // 締め日を取得する
    var isCloseDay;
    var selectClosedDay = function() {
        var customerCode = $('input[name="lngCustomerCode"]');
        var customerName = $('input[name="strCustomerName"]');
        var billingDate  = $('input[name="ActionDate"]');

        // 請求日が未入力
        if(isEmpty(billingDate.val()) == '0') {
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
//        console.log(response);
        if (isEmpty(response[0].lngclosedday) == 0) {
            alert('締め日の取得に失敗しました。');
            return false;
        }

        // 自/至を取得
        var [start , end ] = getClosedDay(response[0].lngclosedday);
        // 自/至セット
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd   = $('input[name="dtmchargeternend"]');
        billingStart.val(start).change();
        billingEnd.val(end).change();
        // 請求月セット
        var billingMonth = $('input[name="From_strInvoiceMonth"]');
        billingMonth.val(end.split('/')[1]).change();
        return true;


    }

    // 検索結果0件の時optionにNoDataをセット
    function setNodata(response){
        console.log('0件');
        console.log(response.responseText);
    }

    // 締め日から自至を算出する
    function getClosedDay(close) {
        var billingDate  = $('input[name="ActionDate"]');
        var billingStart = $('input[name="dtmchargeternstart"]');
        var billingEnd   = $('input[name="dtmchargeternend"]');
        var dateLength   = splitDate(billingDate.val());

        // 請求日が未入力
        if(isEmpty(billingDate.val()) == '0') {
            return [false, false];
        }

        // 請求日の形式がおかしい
        if(dateLength == false) {
            return [false, false];
        }

        // 請求日
        var yyyy = parseInt(dateLength[1]);
        var mm   = parseInt(dateLength[2]);
        var dd   = parseInt(dateLength[3]);
        // 締め日 (close)
        close = parseInt(close);
        isCloseDay = close;
        if( close === 0 )
        {
	        // 末日締めの対応
	        var startDate = new Date(yyyy, mm - 1, 1);
	        var start = startDate.getFullYear() + '/' + (startDate.getMonth()+1) + '/' + startDate.getDate();
	        var endDate = new Date(yyyy, mm, 1);
	        endDate.setDate(endDate.getDate() - 1);
	        var end = endDate.getFullYear() + '/' + (endDate.getMonth()+1) + '/' + endDate.getDate();
        }
    	else
    	{
            // 至 ：請求日の日 <= 締め日の場合、当月の締め日、それ以外の場合は、翌月の締め日
            if (dd <= close) {
                var date1 = new Date(yyyy, mm - 1, close);
                var end = yyyy + '/' + mm + '/' + close;
            } else {
                var date1 = new Date(yyyy, mm - 1, close);
                date1.setMonth(date1.getMonth() + 1);
                var end = date1.getFullYear() + '/' + (date1.getMonth()+1) + '/' + date1.getDate();
            }

            // 自：至の1ヶ月前の翌日
            var startSplit = splitDate(end);
            var syyyy = parseInt(startSplit[1]);
            var smm   = parseInt(startSplit[2]);
            var sdd   = parseInt(startSplit[3]);
            var date2 = new Date(syyyy, smm - 1, sdd);
            // 一か月前
            date2.setMonth(date2.getMonth() - 1);
            // 翌日
            date2.setDate(date2.getDate() + 1);
            var start = date2.getFullYear() + '/' + (date2.getMonth()+1) + '/' + date2.getDate();
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
        var mm   = regResult[2];
        var dd   = regResult[3];
        var di   = new Date(yyyy, mm - 1, dd);
        // 日付の有効性チェック
        if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
            return regResult;
        }

        return false;
    };

    // 請求月のセレクトBOX
    function setMonthSelectBox() {
        var today = new Date();
        var mm = today.getMonth()+1;
        //月
        var month = "<select>";
        for (var i=1; i<=12; i++ ) {
            if( i == mm) {
                month += '<option value=\"' + i + '\" selected >' + i + '</option>';
            } else {
                month += '<option value=\"' + i + '\" >' + i + '</option>';
            }
        }
        month += '</select>';
        $('#invoiceMonth').html(month + "月");
    };
    setMonthSelectBox();

    // 納品書明細検索ボタン押下処理
    $('img.search-condition').on({
            'click': function() {
                // validationキック
                var form = $('form[name="Invoice"]');
                // From/Toが入力されていたらチェック
                let $dtmFrom = $('input[name="From_dtmDeliveryDate"]').val();
                let $dtmTo   = $('input[name="To_dtmDeliveryDate"]').val();
                if($dtmFrom && $dtmTo){
                    let d1 = new Date($dtmFrom);
                    let d2 = new Date($dtmTo);
                    if(d1 > d2){
                        alert('納品日（TO）が納品日（FROM）より過去の日です');
                        return false;
                    }
                }

                // クリアチェック (顧客コードまたは通貨または課税区分が初期値と異なる場合)
                let bReset     = false;
                let changeCode = false;
                // 親顧客コード
                var parentCustomerCode = window.opener.$('input[name="lngCustomerCode"]').val();
                var parentCustomerName = window.opener.$('input[name="strCustomerName"]').val();
                // 顧客コード
                let customerCode = $('input[name="lngCustomerCode"]').val();
                let customerName = $('input[name="strCustomerName"]').val();

                let $tableB = window.opener.$('#tableB');
                // テーブルB <tbody>, <tr>
                let $tableB_tbody = $('tbody', $tableB);
                let $tableB_row = $('tr', $tableB_tbody);
                if($tableB_row.length > 0){
                    for (var i = 0, rowlen = $tableB_row.length; i < rowlen; i++) {
                        for (var j = 0, collen = $tableB_row[i].cells.length; j < collen; j++) {
                            if($tableB_row[i].cells[j].className == 'taxclass') {
                                // 課税区分
                                var parentTaxCode  = $tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
                            }
                        }
                    }

                    if(parentCustomerCode != customerCode)
                    {
                        bReset     = true;
                        changeCode = true;
                    }
                    let taxClassCode = $('select[name="lngTaxClassCode"]').val();
                    if(taxClassCode != parentTaxCode){
                        bReset = true;
                    }
                }

                var msg = '選択された明細を全てクリアしますが、よろしいですか？';
                if(bReset == true && window.confirm(msg) === false ) {
                    return;
                }

                if(form.valid() == false){
                    return;
                }

                // 入力値を取得
                // 顧客
                var params = {
                        mode: 'ajax',
                        strSessionID: $('input[name="strSessionID"]').val(),
                        QueryName: 'selectClosedDayByCodeAndName',
                        conditions: {
                            customerCode:        customerCode,
                            customerName:        customerName,
                            strSlipCode:         $('input[name="strSlipCode"]').val(),
                            deliveryFrom:        $('input[name="From_dtmDeliveryDate"]').val(),
                            deliveryTo:          $('input[name="To_dtmDeliveryDate"]').val(),
                            deliveryPlaceCode:   $('input[name="lngDeliveryPlaceCode"]').val(),
                            deliveryPlaceName:   $('input[name="strDeliveryPlaceName"]').val(),
                            moneyClassCode:      $('select[name="lngMoneyClassCode"]').val(),
                            taxClassCode:        $('select[name="lngTaxClassCode"]').val(),
                            inChargeUserCode:    $('input[name="lngInChargeUserCode"]').val(),
                            inChargeUserName:    $('input[name="strInChargeUserName"]').val(),
                            inputUserCode:       $('input[name="lngInputUserCode"]').val(),
                            inputUserName:       $('input[name="lngInputUserName"]').val()
                        }
                    }

                // 検索する
                var search = {
                                url: '/inv/regist/condition.php?strSessionID=' + $.cookie('strSessionID'),
                                type: 'post',
                                dataType: 'json',
                                data:params,
                            };

                $.ajax( search )
                .done(function(response){
                    console.log(response);
                    // 親ウィンドウの存在チェック
                    if (!window.opener || window.opener.closed)
                    {
                        // 親ウィンドウが存在しない場合
                        window.alert('メインウィンドウが見当たりません。');
                    }
                    else
                    {
                    	if(response.Message)
                    	{
                    		alert(response.Message);
                    		return;
                    	}
                        // TABLE作成
                        window.opener.$.createTable(response);

                        // テーブル初期化
                        if(bReset == true) {
                            // 出力明細一覧を削除
                            window.opener.$('#btnAllDelete').trigger('click');
                            // 前月請求残額(消費税込み)
                            window.opener.$('input[name="curlastmonthbalance"]').val(0).change();
                            // 消費税
                            window.opener.$('input[name="curtaxprice"]').val(0).change();
                            // 当月請求額
                            window.opener.$('input[name="curthismonthamount"]').val(0).change();
                            // 差引合計額
                            window.opener.$('input[name="notaxcurthismonthamount"]').val(0).change();
                        }

                        // 顧客コード変更
                        if(changeCode == true) {
                            window.opener.$('input[name="lngCustomerCode"]').val(customerCode);
                            window.opener.$('input[name="strCustomerName"]').val(customerName);
                        }
                        window.close();
                    }
                })
                .fail(function(response){
                    console.log(response);
                    alert('検索に失敗しました。\n条件を変更して下さい。');
                    return;
                });
                return false;
            }
    });

    // 金額計算
    function billingAmount() {

        // 出力明細一覧取得
        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // 出力明細一覧エリアの1行目の消費税率を取得する
        let tax = false;
        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            if (tax !== false) continue;
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                if (tax !== false || !tableB_row[i].cells[j]) continue;
                if(tableB_row[i].cells[j].className == 'tax right') {
                    // 消費税率
                    console.log(tableB_row[i].cells[j].innerText);
                    strtax = tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
                    tax = Number(strtax)/100;
                }
            }
        }

        // 前月請求残額
        // 納品日が「自」以前である明細の税抜金額の合計+その合計に対して課税区分に応じて計算された消費税
        let lastMonthBalance = 0;
        let curLastMonthBalance = 0;
        // 当月請求額
        // 納品日が「自」以降である明細の税抜金額の合計
        let thisMonthAmount = 0;
        // 消費税
        // 当月請求額に対して課税区分に応じて計算
        let taxPrice = 0;
        // 差引合計額
        // 前月請求残額 + 当月請求額 + 消費税"
        let noTaxMonthAmount  = 0;

        // 「自」「至」を計算する
        selectClosedDay();

        var chargetern = function(){
            // 「自」取得
            let chargeternstart = $('input[name="dtmchargeternstart"]').val();
            let cs = isEmpty(chargeternstart);
            // 「至」取得
            let chargeternend = $('input[name="dtmchargeternend"]').val();
            let ce = isEmpty(chargeternend);

            if (cs == 0 || ce == 0) return false;

            startStamp = new Date(chargeternstart);
            endStamp   = new Date(chargeternend);

            for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
                let deliverydate = false;
                let price   = false;
                let data = false;

                for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                    if (!tableB_row[i].cells[j].innerText) continue;
                    if(tableB_row[i].cells[j].className == 'deliverydate') {
                        // 納品日
                        deliverydate = tableB_row[i].cells[j].innerText;
                    }
                    if(tableB_row[i].cells[j].className == 'price right') {
                        // 税抜金
                        price = tableB_row[i].cells[j].innerText;
                    }
                }

                if(!deliverydate || !price) continue;
                date = splitDate(deliverydate);
                deliverydateStamp = new Date(deliverydate);

                if(deliverydateStamp <= startStamp) {
                    // 前月請求残額
                    lastMonthBalance += Number(price);
                } else {
                    // 当月請求額
                    thisMonthAmount  += Number(price);
                }
            }

            // 前月請求残額(消費税込み)
            curLastMonthBalance = lastMonthBalance+(lastMonthBalance * (tax*100))/100;
            // 消費税計算
            // 当月請求額に対して課税区分に応じて計算
            taxPrice  = (thisMonthAmount*(tax*100))/100;
            // 差引合計額
            // 前月請求残額 + 当月請求額 + 消費税
            noTaxMonthAmount  = curLastMonthBalance + thisMonthAmount + taxPrice;
            // 結果を繁栄
            $('input[name="curlastmonthbalance"]').val(Math.round(curLastMonthBalance)).change();
            $('input[name="curthismonthamount"]').val(thisMonthAmount).change();
            $('input[name="curtaxprice"]').val(Math.round(taxPrice)).change();
            $('input[name="notaxcurthismonthamount"]').val(Math.round(noTaxMonthAmount)).change();
        };
        var result = setTimeout(chargetern, 500);

    }

    // PREVIEWボタン押下処理 (preview)
    $('img.preview-button').on({
        'click': function() {
            // validationキック
            if($('form[name="Invoice"]').valid()==false)
            {
                return;
            }
            // 金額計算
            billingAmount();
            // プレビュー画面呼び出し (遅延させないとINPUT取得できない)
            var prev = setTimeout(previewDrow, 800);
        }
    });


    var previewDrow = function(){
        tableB = $('#tableB');
        tableB_tbody = $('tbody', $tableB);
        tableB_row = $('tbody tr', $tableB);

        // 納品書番号を取得する。slipcode
        var slipCodeList = [];
        // 納品日を格納する
        var deliveryDate = [];
        // 最初の課税区分を取得する。
        var taxclass = false;
        // 最初の税率を取得する。
        var tax = false;
        // 税率が同じかをチェックするフラグ
        var isSameTax = true;

        for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
            for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
                if(!tableB_row[i].cells[j].innerText) continue;

                if(tableB_row[i].cells[j].className == 'slipcode') {
                    // 納品書No
                    slipCodeList.push(tableB_row[i].cells[j].innerText);
                }
                if(tableB_row[i].cells[j].className == 'tax right' && !tax) {
                    // 消費税
                    tax = tableB_row[i].cells[j].innerText;
                       console.log('消費税セット');
                }
                if(tableB_row[i].cells[j].className == 'taxclass' && !taxclass) {
                    // 税抜き金額
                    taxclass = tableB_row[i].cells[j].innerText;
                }
                if(tableB_row[i].cells[j].className == 'deliverydate') {
                    // 納品日
                    deliveryDate.push(tableB_row[i].cells[j].innerText);
                }
                   if(tableB_row[i].cells[j].className == 'tax right' && tax) {
                       console.log('消費税率チェック');
                       if(tableB_row[i].cells[j].innerText != tax){
                           console.log('消費税NG');
                           isSameTax = false;
                       }
                   }
           }
        }

        // エラーチェックで問題なければ確認画面表示

        // プレビューバリデーションチェック
        // 出力明細一覧エリアに明細が1行も存在しない場合
        if(slipCodeList.length === 0)
        {
            alert('出力明細が選択されていません');
            return false;
        }
        // 出力明細一覧エリアに選択された納品書の消費税率がすべて同一ではない場合
        if(isSameTax == false)
        {
            alert('消費税率の異なる納品書は請求書の明細に混在できません');
            return false;
        }
        // 納品日の月が締済みの場合
        // 1請求日と顧客の締め日から、請求日の締め月を求める。
        // 「至」取得
        let ternend = $('input[name="dtmchargeternend"]').val();
        let ternEndDate = new Date(ternend);
        // 2システム日付と顧客の締め日から、当日の締め月を求める。
        let systemDate = new Date();
        // 至 ：請求日の日 <= 締め日の場合、当月の締め日、それ以外の場合は、翌月の締め日
        let sysEndDate = new Date(systemDate.getFullYear(), systemDate.getMonth(), isCloseDay);
        if (systemDate.getDate() > isCloseDay) {
            sysEndDate.setMonth(sysEndDate.getMonth() + 1);
        }
        // 月比較(年跨ぎを考慮する)
        let d1 = new Date(ternEndDate.getFullYear(), ternEndDate.getMonth());
        let d2 = new Date(sysEndDate.getFullYear(), sysEndDate.getMonth());
        if(d1 < d2){
            alert('締済みのため、指定された納品日は無効です');
            return false;
        }

        // 納品日がシステム日付で算出した締め日の前後1ヶ月以内にない場合
        // ヘッダ部の請求月ではない納期の明細が存在した場合
        let start = new Date(sysEndDate.getTime());
        start.setMonth(start.getMonth() - 1);
        let end   = new Date(sysEndDate.getTime());
        end.setMonth(end.getMonth() + 1);
        let isDeliveryDate = true;

        // 請求月
        let invoicemonth = $('option:selected').val();
        let isSameMonth = true;

        $.each(deliveryDate, function(i, v) {
            let deliDate = new Date(v);

            if(deliDate < start || deliDate > end) {
                isDeliveryDate = false;
            }
            if(invoicemonth != deliDate.getMonth()+1){
                isSameMonth = false;
            }
        });

        if(isDeliveryDate == false) {
            alert('納品日は当月度の前後1ヶ月の間を指定してください');
            return false;
        }
        if(isSameMonth == false) {
            alert('出力明細には、入力された納品日と異なる月に納品された明細を指定できません');
            return false;
        }

        var strMode = $('input[name="strMode"]').val();

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

        var invForm = $('form[name="Invoice"]');

        if(invForm.valid()){

            var windowName = 'registPreview';
            if(strMode == 'renewPrev') {
                // 修正
                url = '/inv/regist/renew.php?strSessionID=' + $.cookie('strSessionID');
            }else{
                // 登録
                url = '/inv/regist/index.php?strSessionID=' + $.cookie('strSessionID');
            }

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
            invForm.find(':submit').click();
        }

        return true;

    }

    // 登録処理 (insert)
    var insertCheck = function() {

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
                    if(!tableB_row[i].cells[j].innerText) continue;

                    if(tableB_row[i].cells[j].className == 'slipcode') {
                        // 納品書No
                        console.log(tableB_row[i].cells[j].className);
                        console.log(tableB_row[i].cells[j].innerText);
                        slipCodeList.push(tableB_row[i].cells[j].innerText);
                    }
                    if(tableB_row[i].cells[j].className == 'tax right' && !tax) {
                        // 消費税
                        console.log(tableB_row[i].cells[j].className);
                        console.log(tableB_row[i].cells[j].innerText);
                        tax = tableB_row[i].cells[j].innerText;
                    }
                    if(tableB_row[i].cells[j].className == 'taxclass' && !taxclass) {
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

            var invForm = $('form[name="Invoice"]');
            if(invForm.valid()){

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

})();
