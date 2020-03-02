(function(){
    // フォーム
    var workForm = $('form');
    // クリアボタン
    var btnClear = $('img.clear');
    // 登録ボタン
    var btnSearch = $('img.search');

    // フォームサブミット抑止
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // クリアボタン
    btnClear.on('click', function () {
        window.location.reload();
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function(){
        if(workForm.valid()){
            var windowName = 'searchResult';
            window.open("", windowName,"width=1011, height=700, scrollbars=yes, resizable=yes"); 
            workForm.attr('action', '/sc/result2/index.php?strSessionID=' + $('input[name="strSessionID"]').val());
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
