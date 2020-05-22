(function () {
    switch (location.pathname) {
        case "/mm/regist/index.php":
            $('.navi-mold-history-regist')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        case "/mm/search/index.php":
            $('.navi-mold-history-search')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        case "/mm/list/index.php":
            $('.navi-mold-list-search')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        case "/mr/regist/index.php":
            $('.navi-mold-report-regist')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        case "/mr/search/index.php":
            $('.navi-mold-report-search')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        default:
            break;
    }
})();
