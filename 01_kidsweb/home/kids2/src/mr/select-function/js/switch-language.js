

// English �� ���ܸ��ڤ��ؤ��ܥ��󲡲���
$('.control-block__button-language').on('click', function(){

	// COOKIE������쥳���ɤ����
	var langCode = $.cookie('lngLanguageCode');

	// �ڤ��ؤ��о����ǤΥ���å���
	var title = $('.base-header__title-image');
	var label = $('.label-select-function');
	var regist = $('.function-buttons__regist');
	var search = $('.function-buttons__search');

	// ���쥳���ɤ� "0:�Ѹ�" �ξ��
	if (langCode == 0) {
		// �����ȥ��������
		title.attr('src', '/img/type01/mr/title_en.gif');
		// ��٥� ��ǽ����
		label.text('SELECT FUNCTIONS');

		// �ܥ����������(��Ͽ->�Ѹ�:���ѥܥ���)
		regist.attr('src', '/img/type01/cmn/navi/regist_off_en_bt.gif');
		// �ܥ����������(����->�Ѹ�:���ѥܥ���)
		search.attr('src', '/img/type01/cmn/navi/search_off_en_bt.gif');

		// ���쥳���ɤ�1(���ܸ�)������
		$.cookie('lngLanguageCode', 1);
		// �����ڤ��ؤ��ܥ�������ܸ���ѹ�
		$(this).attr('src', '/img/type01/cmn/etoj/ja_off_bt.gif');
	}
	// ����ʳ��ξ��(����̵�Ѥ����ܸ찷��)
	else {
		// �����ȥ��������
		title.attr('src', '/img/type01/mr/title_ja.gif');
		// ��٥� ��ǽ����
		label.text('��ǽ����');

		// �ܥ����������(�ⷿ������Ͽ����)
		regist.attr('src', '/img/type01/mr/regist_off_ja_bt.gif');
		// �ܥ����������(�ⷿ������Ͽ����)
		search.attr('src', '/img/type01/mr/search_off_ja_bt.gif');

		// ���쥳���ɤ�0(�Ѹ�)������
		$.cookie('lngLanguageCode', 0);
		// �����ڤ��ؤ��ܥ����Ѹ���ѹ�
		$(this).attr('src', '/img/type01/cmn/etoj/en_off_bt.gif');
	}
});

// �ɤ߹��߻����ڤ��ؤ��¹�
// ���Τޤ޼¹Ԥ���ȱ��������å����Ƥ��ޤ��Τ�
// langCode��դ����ꤷ�Ƥ���kick����
(function(langCode){

	// ���쥳����ȿž
	langCode = (langCode === 1) ? 0 : 1;
	// COOKIE������
	$.cookie('lngLanguageCode', langCode);

	// �����ڤ��ؤ��ܥ��� click���٥��ȯ��
	$('.control-block__button-language').trigger('click');

})($.cookie('lngLanguageCode'));
