(function(){
    // 帳票をページ単位で取得
    var pages = $('div#mold-report-page');

    // 前ページボタン
    var btnPrev = $('img.button.prev-page');
    // 次ページボタン
    var btnNext = $('img.button.next-page');

    // 現在のページラベル
    var labelCurrentPage = $('span.current-page');
    // 最後のページラベル
    var labelLastPage = $('span.last-page');

    // 1ページ目
    var firstPage = 1;

    // 最大ページ数の取得/設定
    var lastPage = pages.length;
    labelLastPage.get(0).innerHTML = lastPage;

    // 現在ページ(初期)数の設定
    var currentPage = firstPage;
    labelCurrentPage.get(0).innerHTML = currentPage;

    // 初期ページの表示
    $('div#mold-report-page[page=' + currentPage + ']').addClass('show-page');

    // 前のページへ切り替える
    btnPrev.on('click', function(){
        // 全体で1ページしかない場合
        if (lastPage == 1){ return; }

        // 現在のページが最初の1ページ目の場合
        if (currentPage == 1) {
            // 最後のページを表示
            changePage(currentPage, lastPage);
        } else {
            // 前のページを表示
            changePage(currentPage, --currentPage);
        }
    });
    // 次のページへ切り替える
    btnNext.on('click', function(){
        // 全体で1ページしかない場合
        if (lastPage == 1){ return; }

        // 現在のページが最後の1ページ目の場合
        if (currentPage == lastPage) {
            // 最初のページを表示
            changePage(lastPage, firstPage);
        } else {
            // 次のページを表示
            changePage(currentPage, ++currentPage);
        }
    });

    var changePage = function(hidePage, showPage){
        $('div#mold-report-page[page="' + hidePage + '"]').removeClass('show-page');
        $('div#mold-report-page[page="' + showPage + '"]').addClass('show-page');
        labelCurrentPage.get(0).innerHTML = showPage
        currentPage = showPage;
    }
})();
