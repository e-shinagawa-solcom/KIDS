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
    btnClear.on('click', function(){
        // テキスト入力箇所をリセット
        workForm.find('input[type="text"], textarea').val('');
        workForm.find('select').val('');
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function(){
        if(workForm.valid()){
            var windowName = 'searchResult';
            window.open("", windowName,"width=1011, height=670, scrollbars=yes, resizable=yes"); 
            workForm.attr('action', '/inv/result/index.php?strSessionID=' + $.cookie('strSessionID'));
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
