$(function () {
    // �ڤ��ؤ��о����ǤΥ���å���
    var title = $('.base-header__title-image');
    var label = $('.label-select-function');
    var regist = $('.function-buttons__regist');
    var search = $('.function-buttons__search');
    var total = $('.function-buttons__total');

    // �����ȥ��������
    title.attr('src', '/img/type01/inv/title_ja.gif');
    // ��٥� ��ǽ����
    label.text('��ǽ����');

    // �ܥ����������(�������Ͽ����)
    regist.attr('src', '/img/type01/inv/regist_off_ja_bt.gif');
    // �ܥ����������(����񸡺�����)
    search.attr('src', '/img/type01/inv/search_off_ja_bt.gif');
    // �ܥ����������(���ὸ�ײ���)
    total.attr('src', '/img/type01/inv/total_off_ja_bt.gif');
});