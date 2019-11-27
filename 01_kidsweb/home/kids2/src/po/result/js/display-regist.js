//
// display-regist.js
//
(function(){
    $('img.decide.button').on('click',function(){
        url='/po/regist/index.php';
        // lngorderno = 'lngOrderNo=' + $(this).attr('lngorderno') + ',4840';
        lngorderno = 'lngOrderNo=' + $(this).attr('id');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + '&' + sessionID, 'display-regist', 'width=996, height=689, resizable=yes, scrollbars=yes, menubar=no');
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
