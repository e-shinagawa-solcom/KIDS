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
            // �ҥ�����ɥ���ɽ��
            var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
            // �ե���������
            workForm.get(0).target = windowName;
            workForm.get(0).method = 'post';
            workForm.get(0).action = '/mm/search/result/searchMoldHistory.php?strSessionID=' + $.cookie('strSessionID');
            //
            moldChoosenList.find('option').prop('selected', true);
            // ���֥ߥå�
            workForm.submit();
        }
        else {
            // �Х�ǡ������Υ��å�
            workForm.find(':submit').click();
        }
    });
})();
