
(function(){
    // �ⷿ�ꥹ��
    var moldList = $('.mold-selection__list');
    // ������ζⷿ�ꥹ��
    var moldChoosenList = $('.mold-selection__choosen-list');
    // �ݴɸ�����
    var sourceFactory = $('input[name="SourceFactory"]');
    // �ⷿ�����ơ��֥�
    var tableMoldDescription = $('.table-description');

    // �ɲåܥ���(��)
    $('.list-add').on({
        'click': function(){
            // ���쥯�ȥܥå����֤ΰ�ư
            selectBoxMoveTo(moldList, moldChoosenList);
            // ���ߤ��ݴɸ�(�ǿ��ΰ�ư��)�����ߤ��Ƥ��ʤ��������å�
            checkUniqueSourceFactory(moldChoosenList.find('option'));
            // �ݴɸ�������ܤؤ��������
            propSourceFactory(moldChoosenList);
            // ������ζⷿ�ꥹ�ȤΥ�����
            selectBoxCommand(moldChoosenList, 'sort');
            // �ⷿ����������κ���
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // ����ܥ���(��)
    $('.list-del').on({
        'click': function(){
            // ���쥯�ȥܥå����֤ΰ�ư
            selectBoxMoveTo(moldChoosenList, moldList);
            // �ݴɸ�������ܤؤ��������
            propSourceFactory(moldChoosenList);
            // �ⷿ�ꥹ�ȤΥ�����
            selectBoxCommand(moldList, 'sort');
            // �ⷿ����������κ���
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // UP�ܥ���
    $('.list-up').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'up');
            // �ݴɸ�������ܤؤ��������
            propSourceFactory(moldChoosenList);
            // �ⷿ����������κ���
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // DOWN�ܥ���
    $('.list-down').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'down');
            // �ݴɸ�������ܤؤ��������
            propSourceFactory(moldChoosenList);
            // �ⷿ����������κ���
            createFormMoldDescription(moldChoosenList.find('option'));
        }
    });

    // SELECT ALL�ܥ���
    $('.mold-selection tr > td:nth-of-type(even) > img.list-sort').on({
        'click': function(){
            $(this).parent().prev().find('select').find('option').prop('selected', true);
        }
    });

    // �ݴɸ�������ܤؤ��������
    var propSourceFactory = function(selectBox){
        sourceFactory.val(selectBox.find('option').first().attr('displaycode'));
        sourceFactory.trigger('change');
    };

    // �ⷿ����������κ���
    var createFormMoldDescription = function(options){
        // �ǡ������ꥻ�å�
        tableMoldDescription.find('tbody').empty();
        // OPTION���ǿ�ʬ����
        options.each(function(index){
            var nameMoldNo = 'MoldNo' + (index + 1);
            var nameMoldDescription = 'MoldDescription' + (index + 1);

            var row = $('<tr>').attr('moldno', $(this).val());
            var colNo = $('<td>').append(index + 1);
            var colMoldNo = $('<td>').append(
                                $('<input>')
                                    .attr('name', nameMoldNo)
                                    .attr('readonly', "")
                                    .val($(this).val())
                            );
            var colDescription = $('<td>').append($('<input>').attr('name', nameMoldDescription));

            row.append(colNo);
            row.append(colMoldNo);
            row.append(colDescription);

            tableMoldDescription.append(row);
        });
    };

    // ���ߤ��ݴɸ�(�ǿ��ΰ�ư��)�����ߤ��Ƥ��ʤ��������å�
    var checkUniqueSourceFactory = function(options){
        // OPTION���Ǥ�¸�ߤ��ʤ����
        if (options.length <= 0) {return;}

        // ��������ɽ����ҥ����ɥꥹ��
        var excludeDisplayCompanyCode = new Array();

        // ɽ����ҥ����ɥꥹ�Ȥ����
        options.each(function(index){
            excludeDisplayCompanyCode.push($(this).attr('displaycode'));
        });

        // �ͤ���ʣ���Ƥ������Ǥ���
        excludeDisplayCompanyCode = $.unique(excludeDisplayCompanyCode);

        // ��ˡ�����ɽ����ҥ����ɤ�1�Ĥξ��
        if (excludeDisplayCompanyCode.length === 1){return;}

        // ��Ƭ��ɽ����ҥ�����(ͥ��)�����
        var ignoreCode = excludeDisplayCompanyCode[0];
        options.each(function(i, option){
            // �����оݤ�OPTION��SELECTED���ڤ��ؤ���
            $.each(excludeDisplayCompanyCode, function(j, value){
                // ��Ƭ����(�����оݳ�)�ξ��
                if ($(option).attr('displaycode') === ignoreCode){
                    $(option).prop('selected', false);
                }
                // ����ʳ�(�����о�)
                else if ($(option).attr('displaycode') === value){
                    $(option).prop('selected', true);
                }
            });
        });
        // ��å���������
        alert('�ݴɸ����줬�ۤʤ�ⷿ��Ʊ������Ͽ���뤳�ȤϤǤ��ޤ���');
        // �����оݤ�OPTION���Ǥ��᤹
        $('.list-del').trigger('click');
    };
})();
