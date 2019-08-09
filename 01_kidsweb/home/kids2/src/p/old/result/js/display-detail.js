
(function(){
    $('img.detail.button').on('click', function(){
        url = '/p/result/index2.php';
        lngproductno = 'lngProductNo=' + $(this).attr('strproductcode');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngproductno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
    $('img.fix.button').on('click', function(){
        url = '/p/regist/renew.php';
        strProductCode = 'strProductCode=' + $(this).attr('strproductcode');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + strProductCode + '&' + sessionID, 'display-detail', 'width=961,height=552,resizable=yes, scrollbars=yes, menubar=no');
    });
})();

function getUrlVars(){
    var vars = {}; 
    var param = location.search.substring(1).split('&');
    for(var i = 0; i < param.length; i++) {
        var keySearch = param[i].search(/=/);
        var key = '';
        if(keySearch != -1) key = param[i].slice(0, keySearch);
        var val = param[i].slice(param[i].indexOf('=', 0) + 1);
        if(key != '') vars[key] = decodeURI(val);
    } 
    return vars; 
}