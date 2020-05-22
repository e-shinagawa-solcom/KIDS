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
            window.open("", windowName, "width=1011, height=670, scrollbars=yes, resizable=yes");
            workForm.attr('action', '/mm/list/result/index.php?strSessionID=' + $('input[name="strSessionID"]').val());
            workForm.attr('method', 'post');
            workForm.attr('target', windowName);
            moldChoosenList.find('option').prop('selected', true);
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
