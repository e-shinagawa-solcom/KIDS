(function () {
    // �ե�����
    var workForm = $('form');
    // ���ꥢ�ܥ���
    var btnClear = $('img.clear');
    // ��Ͽ�ܥ���
    var btnSearch = $('img.search');

    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function (e) {
        e.preventDefault();
        return false;
    });

        
    // ���ꥢ�ܥ���
    btnClear.on('click', function () {
        window.location.reload();
    });
    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function () {

        if (workForm.valid()) {
            var windowName = 'searchResult';
            window.open("", windowName, "width=1011px, height=700px, scrollbars=yes, resizable=yes");
            workForm.attr('action', '/so/search/result/index.php?strSessionID=' + $.cookie('strSessionID'));
            workForm.attr('method', 'post');
            workForm.attr('target', windowName);
            workForm.submit();
            // �Х�ǡ������Υ��å�
            // workForm.find(':submit').click();
        }
        else {
            // �Х�ǡ������Υ��å�
            workForm.find(':submit').click();
        }
    });
})();
