
(function(){
    $('img.detail.button').on('click', function(){
        url = '/inv/result/index2.php';
        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('lnginvoiceno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lnginvoiceno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });

    $('img.renew.button').on('click', function(){
        url = '/inv/regist/renew.php';
        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('lnginvoiceno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lnginvoiceno + '&' + sessionID, 'display-detail', 'width=1011, height=650,resizable=yes, scrollbars=yes, menubar=no');
    });

    $('img.delete.button').on('click', function(){
        url = '/inv/result/index3.php';
        lnginvoiceno = 'lngInvoiceNo=' + $(this).attr('lnginvoiceno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lnginvoiceno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
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