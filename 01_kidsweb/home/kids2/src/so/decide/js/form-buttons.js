(function () {
    // フォーム
    var workForm = $('form');
    // クリアボタン
    var btnClear = $('#clear');
    // 登録ボタン
    var btnSearch = $('#search');
    // 閉じるボタン
    var btnClose = $('#close');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // クリアボタン
    btnClear.on('click', function () {
        // テキスト入力箇所をリセット
        workForm.find('input[type="text"]').each(function (i, e) {
            $(this).val('');
        });
    });

    // 閉じるボタン
    btnClose.on('click', function () {
        //ウィンドウを閉じる
        window.close();
        // 親ウィンドウのロックを解除する
        if (window.opener.$('#lockId').length) {
            window.opener.$('#lockId').remove();
        }
    });

    var parantExistanceFlag = true;

    // 検索ボタン押下時の処理
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            //ウィンドウオブジェクトが存在していない時警告してフラグをfalseに
            if (!window.opener || !Object.keys(window.opener).length) {
                window.alert('親画面が存在しません');
                parantExistanceFlag = false;
            }

            //親画面に値を挿入
            if (parantExistanceFlag) {
                var strProductCode = $('input[name="strProductCode"]').val();
                var strProductName = $('input[name="strProductName"]').val();
                if (strProductCode.length == 0) {
                    alert("製品コードを設定してください。");
                    return;
                }

                // 親製品コード
                var parentStrProductCode = window.opener.$('input[name="strProductCode"]').val();
                var lngSalesDivisionCode = $('select[name="lngSalesDivisionCode"] option:selected').val();
                if (lngSalesDivisionCode === undefined) {
                    alert("売上分類コードを設定してください。");
                    return;
                }

                var decideId = "";
                window.opener.$('#table_decide_body tr').each(function (i, e) {
                    decideId += $(this).find('#lngreceiveno').text();
                });

                $('input[name="lngReceiveNo"]').val(decideId);

                var formData = workForm.serializeArray();
                // リクエスト送信
                $.ajax({
                    url: '/so/decide/search_result.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    async: false,
                    data: formData
                })
                    .done(function (response) {
                        console.log("取得データ：" + response);
                        var response = JSON.parse(response);
                        if (response.count == 0) {
                            alert("該当する受注明細が存在しません。");
                            exit;
                        }

                        let changeCode = false;

                        if (parentStrProductCode != strProductCode) {
                            var msg = '選択された明細を全てクリアしますが、よろしいですか？';
                            var $tableA_rows = window.opener.$('#tableA tbody tr');
                            var $tableA_rows_length = $tableA_rows.length;
                            var warn = ($tableA_rows_length > 0) ? true : false;
                            if (warn && window.confirm(msg) === false) {
                                exit;
                            }
                            window.opener.$('input[name="strProductCode"]').val(response.strProductCode);
                            window.opener.$('input[name="strProductName"]').val(response.strProductName);
                            window.opener.$('input[name="lngProductNo"]').val(response.lngProductNo);
                            window.opener.$('input[name="lngProductRevisionNo"]').val(response.lngProductRevisionNo);
                            window.opener.$('input[name="strReviseCode"]').val(response.strReviseCode);
                            window.opener.$('input[name="strGoodsCode"]').val(response.strGoodsCode);
                            window.opener.$('input[name="lngInChargeGroupCode"]').val(response.lngInChargeGroupCode);
                            window.opener.$('input[name="strInChargeGroupName"]').val(response.strInChargeGroupName);
                            window.opener.$('input[name="lngInChargeUserCode"]').val(response.lngInChargeUserCode);
                            window.opener.$('input[name="strInChargeUserName"]').val(response.strInChargeUserName);
                            window.opener.$('input[name="lngDevelopUserCode"]').val(response.lngDevelopUserCode);
                            window.opener.$('input[name="strDevelopUserName"]').val(response.strDevelopUserName);

                            if (response.strGoodsCode != "") {
                                window.opener.$('input[name="strGoodsCode"]').attr('readonly', true);
                            } else {
                                window.opener.$('input[name="strGoodsCode"]').attr('readonly', false);
                            }

                            window.opener.$('#tableB tbody tr').remove();
                            window.opener.$('#tableB_no tbody tr').remove();
                        }

                        window.opener.$('input[name="allSel"]').prop('checked', false);
                        window.opener.$('#tableA tbody tr').remove();
                        window.opener.$('#tableA_chkbox tbody tr').remove();

                        var data = response.result;
                        var tblchkbox = window.opener.$("#tableA_chkbox");
                        window.opener.$('#tableA_chkbox tbody').append(response.tblA_chkbox_result);
                        window.opener.$('#tableA tbody').append(response.tblA_detail_result);
                        window.opener.$('input[name="strProductName"]').val(response.strProductName);
                        window.opener.$('input[name="strProductCode"]').val(response.strProductCode);
                        
                        window.opener.resetTableADisplayStyle();

                        // テーブルAの幅をリセットする
                        resetTableWidth(window.opener.$("#tableA_chkbox_head"), window.opener.$("#tableA_chkbox"), window.opener.$("#tableA_head"), window.opener.$("#tableA"));
                        // テーブルBの幅をリセットする
                        resetTableWidth(window.opener.$("#tableB_no_head"), window.opener.$("#tableB_no"), window.opener.$("#tableB_head"), window.opener.$("#tableB"));
                        // チェックボックスクリックイベントの設定
                        setCheckBoxClickEvent(window.opener.$('input[name="edit"]'), window.opener.$("#tableA"), window.opener.$("#tableA_chkbox"), window.opener.$("#allChecked"));

                        // テーブルAの行クリックイベントの設定
                        selectRow('hasChkbox', window.opener.$("#tableA_chkbox"), window.opener.$("#tableA"), window.opener.$("#allChecked"));

                        // 対象チェックボックスチェック状態の設定
                        scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
                    })
                    .fail(function (response) {
                        console.log("処理結果：" + JSON.stringify(response));
                        alert("fail");
                        // Ajaxリクエストが失敗
                    });
            }
            //ウィンドウを閉じる
            window.close();

            // 親ウィンドウのロックを解除する
            if (window.opener.$('#lockId').length) {
                window.opener.$('#lockId').remove();
            }
        }
        else {
            // バリデーションのキック
            workForm.find(':submit').click();
        }
    });
})();

$(window).on("beforeunload", function (e) {
    // 親ウィンドウのロックを解除する
    if (window.opener.$('#lockId').length) {
        window.opener.$('#lockId').remove();
    }
});
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
