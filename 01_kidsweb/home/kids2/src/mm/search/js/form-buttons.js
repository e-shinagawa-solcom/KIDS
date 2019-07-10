(function(){
    // �ե�����
    var workForm = $('form');
    // ���ꥢ�ܥ���
    var btnClear = $('img.clear');
    // ��Ͽ�ܥ���
    var btnSearch = $('img.search');
    // �ⷿ���쥯�ȥܥå����μ���
    var moldList = $('.mold-selection__list');
    var moldChoosenList = $('.mold-selection__choosen-list');

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
        moldList.find('option').remove();
        moldChoosenList.find('option').remove();
    });

    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function(){
        if(workForm.valid()){
            var windowName = 'searchResult';
            // alert(windowName);
            // �ҥ�����ɥ���ɽ��
            // var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
            // �ե���������
            // workForm.get(0).target = windowName;
            // workForm.get(0).method = 'post';
            // workForm.get(0).action = '/mm/search/result/searchMoldHistory.php?strSessionID=' + $.cookie('strSessionID');

            // alert(workForm.get(0).action);
            // //
            // moldChoosenList.find('option').prop('selected', true);
            // alert("test11");
            // // ���֥ߥå�
            // workForm.get(0).submit();
            // alert("test");

            workForm.attr('action', '/mm/search/result/searchMoldHistory.php?strSessionID=' + $.cookie('strSessionID'));
            workForm.attr('method', 'post');
            workForm.attr('target', windowName);
            moldChoosenList.find('option').prop('selected', true);
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
