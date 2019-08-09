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

    $('input[name="From_dtmInsertDate"]').on('blur', function(){
        var value = $(this).val();
      if(/^[0-9]{8}$/.test(value)){
        var str = value.trim();
        var y = str.substr(0,4);
        var m = str.substr(4,2);
        var d = str.substr(6,2);
        $(this).val(y + "/" + m + "/" + d);
      } else if(/(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])/.test(value)){
        var str = value.trim();
        var y = str.substr(0,4);
        var m = str.substr(5,2);
        var d = '01';
        $(this).val(y + "/" + m + "/" + d);
      } else if(/(19[0-9]{2}|2[0-9]{3})(0[1-9]|1[0-2])/.test(value)){
        var str = value.trim();
        var y = str.substr(0,4);
        var m = str.substr(4,2);
        var d = '01';
        $(this).val(y + "/" + m + "/" + d);
      }

      $('input[name="To_dtmInsertDate"]').val($(this).val());
    });

    $('input[name="From_dtmInsertDate"]').on('focus', function(){
        var chgVal = $(this).val().replace(/\//g, "");
        $(this).val(chgVal);
            $(this).select();
    });
})();
