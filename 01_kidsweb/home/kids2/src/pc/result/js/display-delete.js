
// �������ɽ��


(function(){
    $('img.delete.button').on('click', function(){
        // url = '/mm/delete/confirm/confirm-delete.php';
        url = '/pc/result/index3.php';
        lngstockno = 'lngStockNo=' + $(this).attr('lngstockno');
        sessionID = 'strSessionID=' + getUrlVars()["strSessionID"];

        // �̥�����ɥ���ɽ��
        open(url + '?' + lngstockno + '&' + sessionID, 'display-delete', 'resizable=yes, scrollbars=yes, menubar=no');
    });
})();
