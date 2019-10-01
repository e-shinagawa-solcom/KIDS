(function () {
    // フォーム
    var workForm = $('form');
    // クリアボタン
    var btnClear = $('img.clear');
    // 登録ボタン
    var btnSearch = $('img.search');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

    // クリアボタン
    btnClear.on('click', function () {
        // テキスト入力箇所をリセット
        workForm.find('input[type="text"], textarea').val('');
        var checks = workForm.find('input[type="checkbox"]');
        for (var i = 0; i < checks.length; i++) {
            checks[i].checked = false;
        }
        // セレクトボックスを一番目の選択肢に設定
        workForm.find('select').each(function (index) {
            if ($(this).attr('name') == "lngSalesClassCode") {
                $(this).val('1');
            } else {
                $(this).val($(this).find('option').first().val());
            }
        });
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            var windowName = 'searchResult';
            window.open("", windowName, "width=1011px, height=700px, scrollbars=yes, resizable=yes");
            workForm.attr('action', '/so/search/result/index.php?strSessionID=' + $.cookie('strSessionID'));
            workForm.attr('method', 'post');
            workForm.attr('target', windowName);
            workForm.submit();
            // バリデーションのキック
            // workForm.find(':submit').click();
        }
        else {
            // バリデーションのキック
            workForm.find(':submit').click();
        }
    });
})();
