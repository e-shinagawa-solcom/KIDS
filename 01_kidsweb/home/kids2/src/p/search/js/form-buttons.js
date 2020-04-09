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
        window.location.reload();
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            var windowName = 'searchResult';
            window.open("", windowName,"width=1011, height=670, scrollbars=yes, resizable=yes"); 
            workForm.attr('action', '/p/search/result/index.php?strSessionID=' + $.cookie('strSessionID'));
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
    
    // 部門コード変更イベント
    $('select[name="lngInChargeGroupCodeSelect"]').on('change', function () {
        // リクエスト送信
        $.ajax({
            url: '/p/search/getCategoryCode.php',
            type: 'post',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'groupCode': $(this).val()
            }
        })
            .done(function (response) {
                var data = JSON.parse(response);
                $('select[name="lngCategoryCode"] option').remove();
                $('select[name="lngCategoryCode"]').append(data.lngCategoryCode);
            })
            .fail(function (response) {
                alert("fail");
            })
    });
})();
