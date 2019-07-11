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
            workForm.attr('action', '/mr/search/result/searchMoldReport.php?strSessionID=' + $.cookie('strSessionID'));
            workForm.attr('method', 'post');
            workForm.attr('target', windowName);
            moldChoosenList.find('option').prop('selected', true);
            workForm.submit();
        }
        else {
            // �Х�ǡ������Υ��å�
            workForm.find(':submit').click();
        }
    });
})();
