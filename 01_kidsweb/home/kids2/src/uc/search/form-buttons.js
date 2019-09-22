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
        workForm.find('select').val('');
    });

    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function () {
        if (workForm.valid()) {
            var windowName = 'searchResult';
            workForm.attr('action', '/uc/result/index.php?strSessionID=' + $.cookie('strSessionID'));
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

    // ��ҥ������ѹ����٥��
    $('select[name="lngCompanyCode"]').on('change', function () {
        // �ꥯ����������
        $.ajax({
            url: '/cmn/getmasterdata.php?lngProcessID=15&strFormValue[0]=' + $(this).val(),
            type: 'post',
        })
            .done(function (response) {

                $('select[name="lngGroupCode"] option').remove();
                $option = $('<option>')
                    .val('0')
                    .text('');
                $('select[name="lngGroupCode"]').append($option);
                var rows = response.split('\n');
                var cols = rows[1].split('\t');
                for (i = 1, len = rows.length; i < len; i++) {
                    var cols = rows[i].split('\t');
                    $option = $('<option>')
                        .val(cols[0])
                        .text(cols[1]);
                    $('select[name="lngGroupCode"]').append($option);
                }
            })
            .fail(function (response) {
                alert("fail");
            })
    });
})();
