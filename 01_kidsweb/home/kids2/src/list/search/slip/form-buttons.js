(function(){
    // �ե�����
    var workForm = $('form');
    // ���ꥢ�ܥ���
    var btnClear = $('img.clear');
    // ��Ͽ�ܥ���
    var btnSearch = $('img.search');

    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // ���ꥢ�ܥ���
    btnClear.on('click', function(){
        // �ƥ��������ϲս��ꥻ�å�
        workForm.find('input[type="text"], textarea').val('');
        workForm.find('select').val('');
    });

    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function(){
        if(workForm.valid()){
            var windowName = 'searchResult';
            workForm.attr('action', '/list/result/slip/index.php?strSessionID=' + $.cookie('strSessionID'));
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