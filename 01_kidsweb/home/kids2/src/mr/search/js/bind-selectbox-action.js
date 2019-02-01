
(function(){
    // �ⷿ�ꥹ��
    var moldList = $('.mold-selection__list');
    // ������ζⷿ�ꥹ��
    var moldChoosenList = $('.mold-selection__choosen-list');

    // �ɲåܥ���(��)
    $('.list-add').on({
        'click': function(){
            // ���쥯�ȥܥå����֤ΰ�ư
            selectBoxMoveTo(moldList, moldChoosenList);
            // ������ζⷿ�ꥹ�ȤΥ�����
            selectBoxCommand(moldChoosenList, 'sort');
        }
    });

    // ����ܥ���(��)
    $('.list-del').on({
        'click': function(){
            // ���쥯�ȥܥå����֤ΰ�ư
            selectBoxMoveTo(moldChoosenList, moldList);
            // �ⷿ�ꥹ�ȤΥ�����
            selectBoxCommand(moldList, 'sort');
        }
    });

    // UP�ܥ���
    $('.list-up').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'up');
        }
    });

    // DOWN�ܥ���
    $('.list-down').on({
        'click': function(){
            selectBoxCommand(moldChoosenList, 'down');
        }
    });

    // SELECT ALL�ܥ���
    $('.mold-selection tr > td:nth-of-type(even) > img.list-sort').on({
        'click': function(){
            $(this).parent().prev().find('select').find('option').prop('selected', true);
        }
    });

})();
