(function(){
    // �ⷿ���ơ�������������֤���������
    $status = $('select[init-value]')
    // �ⷿ���ơ�������value�Ȱ���
    $status.find('option[value="' + $status.attr("init-value") + '"]').prop('selected', true);
    $status.change();
})();
