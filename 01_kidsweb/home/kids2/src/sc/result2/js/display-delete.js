
(function(){
    $('img.delete.button').on('click', function(){
        url = '/sc/result2/index3.php';
        lngslipno = 'lngSlipNo=' + $(this).attr('lngslipno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // �̥�����ɥ���ɽ��
        open(url + '?' + lngslipno + '&' + sessionID, 'display-detail', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
