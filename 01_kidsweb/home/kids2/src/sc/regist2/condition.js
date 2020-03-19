//
// condition.js
//
jQuery(function ($) {
    // マスタ検索共通
    var searchMaster = {
        url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
        type: 'post',
        dataType: 'json'
    };


    $('a').on('keydown', function (e) {
        e.stopPropagation();
        if (e.which == 13) {
            $(this).find('img').click();
        }
    });

    var postTarget = window.opener.$('input[name="ajaxPostTarget"]').val();
    var strSessionID = $('input[name="strSessionID"]').val();

    // 顧客-表示会社コード イベント登録
    $('input[name="lngCustomerCode"]').on({
        'change': function () {
            selectCustomerName($(this));
            // JQuery Validation Pluginで検知させる為イベントキック
            $(this).trigger('blur');
            // フォーカスを生産工場名に合わせる
            $('input[name="strCustomerName"]').focus();

            SetMonetaryUnitCode(postTarget, $('input[name="lngCustomerCode"]').val(), strSessionID);
        }
    });

    // --------------------------------------------------------------------------
    // 顧客-表示会社コードによるデータ索引
    // --------------------------------------------------------------------------
    // 顧客-表示会社コードから表示名を索引
    var selectCustomerName = function (invoker) {
        console.log("顧客-表示会社コード->表示名 change");
        // 索引結果のセット先CSSセレクタの作成
        var targetCssSelector = 'input[name="str' + $(invoker).attr('alt') + 'Name"]';
        // 索引結果0件の時のコード欄のCSSセレクタの作成
        var targetCodeCssSelector = 'input[name="lng' + $(invoker).attr('alt') + 'Code"]';
        // 検索条件
        var condition = {
            data: {
                QueryName: 'selectCustomerName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // リクエスト送信
        $.ajax($.extend({}, searchMaster, condition))
            .done(function (response) {
                console.log("工場-表示会社コード->表示名 done");
                // 工場-表示名に値をセット
                $(targetCssSelector).val(response[0].companydisplayname);
            })
            .fail(function (response) {
                console.log("工場-表示会社コード->表示名 fail");
                console.log(response.responseText);
                // 工場-コード、表示名の値をリセットし、コード欄にフォーカス
                $(targetCssSelector).val('');
                $(targetCodeCssSelector).val('').focus();
            });
    };
    // 親画面から引き継いだ顧客コードをセット
    var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
    var strCompanyDisplayCode = $('input[name="lngCustomerCode"]').val();
    var lngDefaultMonetaryUnitCode = $('#lngDefaultMonetaryUnitCode').val();
    // ------------------------------------
    //  events
    // ------------------------------------
    // OKボタン
    $('#OkBt').on('click', function () {

        // ---------------------------
        //  入力値（検索条件）の収集
        // ---------------------------
        var search_condition = {
            strCompanyDisplayCode: $('input[name="lngCustomerCode"]').val(),
            strCompanyDisplayName: $('input[name="strCustomerName"]').val(),
            strCustomerReceiveCode: $('input[name="strCustomerReceiveCode"]').val(),
            strReceiveCode: $('input[name="strReceiveCode"]').val(),
            strReceiveDetailProductCode: $('input[name="strReceiveDetailProductCode"]').val(),
            strGoodsCode: $('input[name="strGoodsCode"]').val(),
            lngInChargeGroupCode: $('input[name="lngInChargeGroupCode"]').val(),
            strInChargeGroupName: $('input[name="strInChargeGroupName"]').val(),
            lngSalesClassCode: $('select[name="lngSalesClassCode"]').children('option:selected').val(),
            lngMonetaryUnitCode: $('select[name="lngMonetaryUnitCode"]').children('option:selected').val(),
            From_dtmDeliveryDate: $('input[name="From_dtmDeliveryDate"]').val(),
            To_dtmDeliveryDate: $('input[name="To_dtmDeliveryDate"]').val(),
            dtmDeliveryDate: window.opener.$('input[name="dtmDeliveryDate"]').val(),
            strNote: $('input[name="strNote"]').val(),
            IsIncludingResale: $('input[name="IsIncludingResale"]').prop("checked") ? 'On' : 'Off',
        };

        // --------------------------------------------------------------
        //   入力値のバリデーション
        // --------------------------------------------------------------
        if (!validateCondition(search_condition)) {
            return false;
        }
        console.log(search_condition);
        console.log(postTarget);
        console.log(postTarget);

        // --------------------------------------------------------------
        //   顧客コードまたは売上区分が初期値と異なる場合のチェック
        // --------------------------------------------------------------
        // 初期値の取得
        var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
        var lngDefaultMonetaryUnitCode = $('#lngDefaultMonetaryUnitCode').val();

        // チェックを必要とする条件を満たしているかどうか
        var needConfirm = ((0 < strDefaultCompanyDisplayCode.length)
            && (strDefaultCompanyDisplayCode != search_condition.strCompanyDisplayCode))
            ||
            ((0 < lngDefaultMonetaryUnitCode.length)
                && (lngDefaultMonetaryUnitCode != search_condition.lngMonetaryUnitCode));

        // --------------------------------------------------------------
        //   親画面に子画面の値を引き継いで明細検索を実行
        // --------------------------------------------------------------        
        // 明細検索実行
        // 部分書き換えのためajaxでPOST
        $.ajax({
            type: 'POST',
            url: postTarget,
            data: {
                strMode: "search-detail",
                strSessionID: $('input[name="strSessionID"]').val(),
                condition: search_condition,
            },
            async: false,
        }).done(function (data) {
            console.log("done:search-detail");
            console.log(data);
            // 検索結果をテーブルにセット

            var data = JSON.parse(data);

            if (data.monetaryunitCount != 1 && data.count != 0) {
                // $('input[name="monetaryunitCount"]').val(data.monetaryunitCount);
                alert("複数の顧客が候補にありますので、顧客を指定してください。");
                return false;
            }

            if (data.count == 0) {
                alert("該当する受注データが存在していません。");
                return false;
            }

            // ユーザーに確認
            if (needConfirm) {
                if (confirm("選択された明細を全てクリアしますが、よろしいですか？")) {
                    // 親画面の選択明細を全てクリア
                    window.opener.ClearAllEditDetail();
                } else {
                    //「キャンセル」が押下された場合は何も処理しない
                    return false;
                }
            }
            window.opener.SearchReceiveDetail(data);
            // 検索条件値設定
            window.opener.SetSearchConditionWindowValue(data.strcompanydisplaycode, data.strcompanydisplayname);

            // 本画面を閉じる
            window.close();
        });

    });

    // 閉じるボタン
    $('#CancelBt').on('click', function () {
        window.close();
    });

    // ------------------------------------
    //  functions
    // ------------------------------------
    // 入力された検索条件のバリデーション
    function validateCondition(cnd) {

        // 顧客コード必須チェック
        if (!cnd.strCompanyDisplayCode && !cnd.strCustomerReceiveCode) {
            alert("顧客コード、顧客受注番号のいずれかを入力してください。");
            return false;
        }


        // FROM納期が不正
        if (cnd.From_dtmDeliveryDate) {
            if (!isValidDate(cnd.From_dtmDeliveryDate)) {
                alert("納期（FROM）の入力形式が不正です");
                return false;
            }
        }

        // TO納期が不正
        if (cnd.To_dtmDeliveryDate) {
            if (!isValidDate(cnd.To_dtmDeliveryDate)) {
                alert("納期（TO）の入力形式が不正です");
                return false;
            }
        }

        // FROM納期＞TO納期
        if (cnd.From_dtmDeliveryDate && cnd.To_dtmDeliveryDate) {
            var from = new Date(cnd.From_dtmDeliveryDate);
            var to = new Date(cnd.To_dtmDeliveryDate);

            if (from.getTime() > to.getTime()) {
                alert("納期（TO）が納期（FROM）より過去の日です");
                return false;
            }
        }

        return true;

    };

    // 日付書式チェック
    function isValidDate(text) {
        if (!/^\d{1,4}(\/|-)\d{1,2}\1\d{1,2}$/.test(text)) {
            return false;
        }

        const [year, month, day] = text.split(/\/|-/).map(v => parseInt(v, 10));

        return year >= 1
            && (1 <= month && month <= 12)
            && (1 <= day && day <= daysInMonth(year, month));

        function daysInMonth(year, month) {
            if (month === 2 && isLeapYear(year)) {
                return 29;
            }

            return {
                1: 31, 2: 28, 3: 31, 4: 30,
                5: 31, 6: 30, 7: 31, 8: 31,
                9: 30, 10: 31, 11: 30, 12: 31
            }[month];
        }

        function isLeapYear(year) {
            return ((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0);
        }
    };

    function SetMonetaryUnitCode(postTarge, strCompanyDisplayCode, strSessionID) {
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
                if (data == "81") {
                    // 81：「日本円」を選択（他の項目も選択可能）
                    $("select[name='lngMonetaryUnitCode']").val("1");
                } else {
                    // 81以外：「 」固定
                    $("select[name='lngMonetaryUnitCode']").val("2");
                }

            }).fail(function (error) {
                console.log("fail:get-lngcountrycode");
                console.log(error);
            });
        } else {
            $("select[name='lngMonetaryUnitCode']").val("");
        }
    }

    // ------------------------------------------------------------------
    //   Enterキー押下イベント
    // ------------------------------------------------------------------
    window.document.onkeydown = fncEnterKeyDown;

    function fncEnterKeyDown(e) {
        // Enterキー押下で明細追加
        if (window.event.keyCode == 13) {
            $('#OkBt').trigger('click');
            return false;
        }
        else {
            // document.dispatchEvent(e);
        }
    }



});