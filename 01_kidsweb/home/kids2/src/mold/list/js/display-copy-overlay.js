(function(){
    // クエリ文字列にisCopyが含まれている場合
    if(window.location.search.match(/isCopy/)){
        // COPY オーバーレイを表示
        $('.copy-overlay').toggle();
    }
})();
