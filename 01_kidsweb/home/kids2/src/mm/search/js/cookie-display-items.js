// ---------------------------------------------------
// /mold/cmn/search/js/cookie-functions.jsに依存
// ---------------------------------------------------
(function(){
    var form = $('form');
    // 検索ボタン押下時に現在のチェックボックスの状態をCOOKIEに保存
    $('img.search.button').on('click', function(){
        saveCookieDispayItems(form);
    });

    // COOKIEからチェックボックスの状態を復元
    restoreCookieDispayItems(form)
})();
