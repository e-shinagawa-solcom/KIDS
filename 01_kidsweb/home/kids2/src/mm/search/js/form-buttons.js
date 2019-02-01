(function(){
    // フォーム
    var workForm = $('form');
    // クリアボタン
    var btnClear = $('img.clear');
    // 登録ボタン
    var btnSearch = $('img.search');
    // 金型セレクトボックスの取得
    var moldList = $('.mold-selection__list');
    var moldChoosenList = $('.mold-selection__choosen-list');

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
        moldList.find('option').remove();
        moldChoosenList.find('option').remove();
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
            workForm.get(0).action = '/mm/search/result/searchMoldHistory.php?strSessionID=' + $.cookie('strSessionID');
            //
            moldChoosenList.find('option').prop('selected', true);
            // サブミット
            workForm.submit();
        }
        else {
            // バリデーションのキック
            workForm.find(':submit').click();
        }
    });
})();
