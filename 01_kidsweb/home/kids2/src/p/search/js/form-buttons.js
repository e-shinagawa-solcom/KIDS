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
        // �ƥ��������ϲս��ꥻ�å�
        workForm.find('input[type="text"], textarea').val('');
        var checks = workForm.find('input[type="checkbox"]');
        for(var i = 0;i < checks.length;i++){
        	checks[i].checked = false;
		}
		// ���쥯�ȥܥå���������ܤ�����������
		workForm.find('select').each(function(index){
            $(this).val($(this).find('option').first().val());
        });
    });

    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            var windowName = 'searchResult';
            window.open("", windowName,"width=1011px, height=700px, scrollbars=yes, resizable=yes"); 
            workForm.attr('action', '/p/search/result/index.php?strSessionID=' + $.cookie('strSessionID'));
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
    
    // ���祳�����ѹ����٥��
    $('select[name="lngInChargeGroupCodeSelect"]').on('change', function () {
        // �ꥯ����������
        $.ajax({
            url: '/p/search/getCategoryCode.php',
            type: 'post',
            data: {
                'strSessionID': $.cookie('strSessionID'),
                'groupCode': $(this).val()
            }
        })
            .done(function (response) {
                var data = JSON.parse(response);
                $('select[name="lngCategoryCode"] option').remove();
                $('select[name="lngCategoryCode"]').append(data.lngCategoryCode);
            })
            .fail(function (response) {
                alert("fail");
            })
    });
})();
