//
// display-regist.js
//
(function(){
    $('img.decide.button').on('click',function(){
        url='/po/regist/index.php';
        // lngorderno = 'lngOrderNo=' + $(this).attr('lngorderno') + ',4840';
        lngorderno = 'lngOrderNo=' + $(this).attr('id');
        var estimateNo = '&estimateNo=' + $(this).attr('lngestimateno');
        var revisionNo = '&revisionNo=' + $(this).attr('revisionno');
        sessionID = '&strSessionID=' + getUrlVars(window.location)["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngorderno + estimateNo + revisionNo + sessionID, 'display-regist', 'width=996, height=689, resizable=yes, scrollbars=yes, menubar=no');
    });
})();
