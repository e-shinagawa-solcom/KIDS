$(function () {
    // �ڤ��ؤ��о����ǤΥ���å���
    var title = $('.base-header__title-image');
    var label = $('.label-select-function');
    var regist = $('.function-buttons__regist');
    var search = $('.function-buttons__search');

    // �����ȥ��������
    title.attr('src', '/img/type01/mm/title_ja.gif');
    // ��٥� ��ǽ����
    label.text('��ǽ����');

    // �ܥ����������(�ⷿ������Ͽ����)
    regist.attr('src', '/img/type01/mm/regist_off_ja_bt.gif');
    // �ܥ����������(�ⷿ������Ͽ����)
    search.attr('src', '/img/type01/mm/search_off_ja_bt.gif');
});