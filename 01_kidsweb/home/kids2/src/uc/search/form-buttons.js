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
        workForm.find('select').val('');
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            var windowName = 'searchResult';
            workForm.attr('action', '/uc/result/index.php?strSessionID=' + $.cookie('strSessionID'));
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

    // 会社コード変更イベント
    $('select[name="lngCompanyCode"]').on('change', function () {
        // リクエスト送信
        $.ajax({
            url: '/cmn/getmasterdata.php?lngProcessID=15&strFormValue[0]=' + $(this).val(),
            type: 'post',
        })
            .done(function (response) {

                $('select[name="lngGroupCode"] option').remove();
                $option = $('<option>')
                    .val('0')
                    .text('');
                $('select[name="lngGroupCode"]').append($option);
                var rows = response.split('\n');
                var cols = rows[1].split('\t');
                for (i = 1, len = rows.length; i < len; i++) {
                    var cols = rows[i].split('\t');
                    $option = $('<option>')
                        .val(cols[0])
                        .text(cols[1]);
                    $('select[name="lngGroupCode"]').append($option);
                }
            })
            .fail(function (response) {
                alert("fail");
            })
    });
})();
