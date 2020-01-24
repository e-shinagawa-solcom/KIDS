
(function(){
    $('img.delete.button').on('click', function(){
        url = '/sc/result2/index3.php';
        lngslipno = 'lngSlipNo=' + $(this).attr('id');
        lngrevisionno = 'lngRevisionNo=' + $(this).attr('revisionno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // 別ウィンドウで表示
        open(url + '?' + lngslipno + '&' + lngrevisionno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
