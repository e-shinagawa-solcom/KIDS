
(function(){
    $('img.remove.button').on('click', function(){
        url = '/po/result/index3.php';
        strordercode = $(this).parent().parent().find($('td.td-strordercode')).text();
        lngrevisionno = parseInt(strordercode.split('_')[1], 10);
        lngorderno = 'lngOrderNo=' + $(this).attr('lngorderno') + '_' + lngrevisionno;
        // lngorderno = 'lngOrderNo=' + $(this).attr('lngorderno');
        // lngorderno = 'lngOrderNo=1018_' + lngrevisionno + ',437_2';
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
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