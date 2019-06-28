(function(){
    switch(location.pathname){
        case "/lc/info/index.php":
            $('.navi-lc-info')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        case "/lc/set/index.php":
            $('.navi-lc-set')
                .off()
                .css('opacity', 0.5)
                .css('cursor', 'default');
            break;
        default:
            break;
    }
})();
