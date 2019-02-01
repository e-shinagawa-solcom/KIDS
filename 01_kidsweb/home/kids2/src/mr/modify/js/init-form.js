(function(){
    // change���٥�Ȥ�ȯ�����������ǥꥹ��
    var list_onchange = [
          $('.regist-tab-header input[name="ProductCode"]')
        , $('input[name="CustomerCode"]')
        , $('input[name="KuwagataGroupCode"]')
        , $('input[name="KuwagataUserCode"]')
        , $('input[name="DestinationFactory"]')
    ];

    // ����������оݤ�SELECT���ǥꥹ��
    var list_select = [
          $('select[name="ReportCategory"]')
        , $('select[name="RequestCategory"]')
        , $('select[name="TransferMethod"]')
        , $('select[name="InstructionCategory"]')
        , $('select[name="FinalKeep"]')
    ];

    // �ⷿ���쥯�ȥܥå����μ���
    var moldList = $('.mold-selection__list');
    var moldChoosenList = $('.mold-selection__choosen-list');

    // ������˻��Ѥ���ⷿ����ꥹ��
    var list_initMoldRecord = $('.init-mold-info__record');

    // �ɲåܥ���
    var btnAdd = $('.mold-selection__backimage-add-del .list-add');
    // �ⷿ�����ơ��֥�
    var tableMoldDescription = $('.table-description');

    // onchange���٥�ȥ��å�
    $.each(list_onchange, function(){
        this.change();
    });

    // SELECT���Ǥν�������� & onchange���٥�ȥ��å�
    $.each(list_select, function(){
        var init_value = this.attr('init-value');
        this.find('option[value="' + init_value + '"]').prop('selected', true);
        this.change();
    });

    // �ⷿ�ꥹ���ɹ���λ���ˡ�����Ѥߤζⷿ�ꥹ�ȡפ���������
    moldList.on('load-completed', function(){
        var options = moldList.find('option');

        // �ⷿNO��ʬ����
        list_initMoldRecord.each(function(i, row){
            var cur = $(row).attr('moldno');
            // OPTION���ǿ�ʬ����
            options.each(function(j, option) {
                var target = $(option).val();
                // �ⷿ�ꥹ�Ȥ˴ޤޤ�Ƥ�����
                if (cur === target) {
                    // ������֤ˤ���
                    $(this).prop('selected', true);
                    // �롼�פ���ȴ����
                    return false;
                }
            });
        });

        // �ɲåܥ���Υ���å�(�ⷿ�����ơ��֥�κ���)
        btnAdd.click();
    });

    // �ⷿ�����κ�����λ���˶ⷿ���������ꤹ��
    tableMoldDescription.on('create-completed', function(){
        var trs = $(this).find('tbody > tr');

        // �ⷿ�����ơ��֥�Υ쥳���ɷ��ʬ����
        trs.each(function(i, table_row){
            var cur_table_moldno = $(table_row).attr('moldno');
            // ������оݤȤʤ�ⷿ�����η��ʬ����
            list_initMoldRecord.each(function(j, init_row){
                var cur_init_moldno = $(init_row).attr('moldno')
                // �ⷿ�����ơ��֥���˽�����оݤζⷿ��¸�ߤ�����
                if (cur_table_moldno === cur_init_moldno){
                    // �ⷿ����������
                    $(table_row).find('input[name^="MoldDescription"]').val($(init_row).attr('desc'));
                    // �롼�פ���ȴ����
                    return false;
                };
            });
        });
    });
})();
