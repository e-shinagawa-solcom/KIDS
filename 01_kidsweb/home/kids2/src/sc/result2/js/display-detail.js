
(function(){
    $('img.detail.button').on('click', function(){
        url = '/sc/result2/index2.php';
        lngslipno = 'lngSlipNo=' + $(this).attr('id');
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngslipno + '&' + lngrevisionno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
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