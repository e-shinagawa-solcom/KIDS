(function () {
    // フォーム
    var workForm = $('form');
    // クリアボタン
    var btnClear = $('img.clear');
    // 登録ボタン
    var btnSearch = $('img.search');
    // 閉じるボタン
    var btnClose = $('img.close');

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
    $('input[name="lngCustomerCode"]').change();
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

                var lngCustomerCode = $('input[name="lngCustomerCode"]').val();
                if (lngCustomerCode.length == 0) {
                    alert("顧客コードを設定してください。")
                }
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
                        var data = response.result;                        
                        if (response.count == 0) {
                            alert("該当する受注明細が存在しません。");
                        }
                        
                        var tblchkbox = window.opener.$("#tbl_detail_chkbox");
                        var tbl = window.opener.$("#tbl_detail");
                        var tmp_id = "";
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            var chkbox_id = row.lngreceiveno + "_" + row.lngreceivedetailno + "_" + row.lngrevisionno;
                            var isInserted = false;
                            tblchkbox.find('tr').each(function (i, e) {
                                tmp_id = $(this).find('td').first().find('input:checkbox').attr('id');
                                if (tmp_id == chkbox_id) {
                                    isInserted = true;
                                }
                            });

                            if (!isInserted) {
                                var chkboxstr = '<tr><td style="width: 30px;">'
                                    + '<input id="' + chkbox_id + '" style="width: 10px;" type="checkbox">'
                                    + '</td></tr>';
                                tblchkbox.append(chkboxstr);

                                var detailstr = '<tr>'
                                    + '<td style="width: 25px;"></td>'
                                    + '<td style="width: 100px;">' + row.strreceivecode + '</td>'
                                    + '<td style="width: 70px;">' + row.lngreceivedetailno + '</td>'
                                    + '<td style="width: 250px;" nowrap>[' + convertNull(row.strproductcode) + '] ' + convertNull(row.strproductname) + '</td>'
                                    + '<td>' + convertNull(row.dtmdeliverydate) + '</td>'
                                    + '<td style="width: 120px;">[' + convertNull(row.lngsalesclasscode) + '] ' + convertNull(row.strsalesclassname) + '</td>'
                                    + '<td style="width: 250px;">[' + convertNull(row.strcustomerdisplaycode) + '] ' + convertNull(row.strcustomerdisplayname) + '</td>'
                                    + '</tr>';

                                tbl.append(detailstr);
                            }
                        }

                        tbl.find('tr').each(function (i, e) {
                            $(this).find('td').first().html(i+1);
                        });
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

 
$(window).on("beforeunload", function(e) {
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
