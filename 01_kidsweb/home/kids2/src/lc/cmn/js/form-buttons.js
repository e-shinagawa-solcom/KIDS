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
        var checks = workForm.find('input[type="checkbox"]');
        for(var i = 0;i < checks.length;i++){
        	checks[i].checked = false;
        }
    });

    // 検索ボタン押下時の処理
    btnSearch.on('click', function(){
        if(workForm.valid()){
            var windowName = 'searchResult';
            // 子ウィンドウの表示
            var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
            // フォーム設定
            workForm.get(0).target = windowName;
            workForm.get(0).method = 'post';
            var baseURI = workForm.get(0).baseURI;
            var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search/index.php')-1)+1,baseURI.indexOf('/search/index.php'))
            
            if(screen != 'list'){
                workForm.get(0).action = '/' + screen + '/result/index.php?strSessionID=' + $.cookie('strSessionID');
            }
            
            // サブミット
            workForm.submit();
        }
        else {
            // バリデーションのキック
            workForm.find(':submit').click();
        }
    });
})();
