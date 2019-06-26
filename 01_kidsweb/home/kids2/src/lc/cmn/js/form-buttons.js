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
        var checks = workForm.find('input[type="checkbox"]');
        for(var i = 0;i < checks.length;i++){
        	checks[i].checked = false;
        }
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
            var baseURI = workForm.get(0).baseURI;
            var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search/index.php')-1)+1,baseURI.indexOf('/search/index.php'))
            
            if(screen != 'list'){
                workForm.get(0).action = '/' + screen + '/result/index.php?strSessionID=' + $.cookie('strSessionID');
            }
            
            // ���֥ߥå�
            workForm.submit();
        }
        else {
            // �Х�ǡ������Υ��å�
            workForm.find(':submit').click();
        }
    });
})();
