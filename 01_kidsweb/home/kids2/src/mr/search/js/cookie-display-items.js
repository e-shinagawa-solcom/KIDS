// ---------------------------------------------------
// /mold/cmn/search/js/cookie-functions.js�˰�¸
// ---------------------------------------------------
(function(){
    var form = $('form');
    // �����ܥ��󲡲����˸��ߤΥ����å��ܥå����ξ��֤�COOKIE����¸
    $('img.search.button').on('click', function(){
        saveCookieDispayItems(form);
    });

    // COOKIE��������å��ܥå����ξ��֤�����
    restoreCookieDispayItems(form)
})();
