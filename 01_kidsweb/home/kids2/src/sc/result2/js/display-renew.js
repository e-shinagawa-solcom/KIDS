(function(){
    $('img.renew.button').on('click', function(){
        url = '/sc/regist2/renew.php';
        lngslipno = 'lngSlipNo=' + $(this).attr('lngslipno');
        lngsalesno = 'lngSalesNo=' + $(this).attr('lngsalesno');
        strcustomercode = 'strCustomerCode=' + $(this).attr('strcustomercode');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngslipno + '&' + lngsalesno + '&' + strcustomercode + '&' + sessionID, 
            'display-renew', 
            'resizable=yes, scrollbars=yes, menubar=no');
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