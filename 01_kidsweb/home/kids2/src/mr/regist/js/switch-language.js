

// English �� ���ܸ��ڤ��ؤ��ܥ��󲡲���
$('.control-block__button-language').on('click', function(){

	// COOKIE������쥳���ɤ����
	var langCode = $.cookie('lngLanguageCode');

	// �ڤ��ؤ��о����ǤΥ���å���
	var title = $('.base-header__title-image');

	// ���쥳���ɤ� "0:�Ѹ�" �ξ��
	if (langCode == 0) {
		// �����ȥ��������
		title.attr('src', '/img/type01/mr/title_ja.gif');

		// ���쥳���ɤ�1(���ܸ�)������
		$.removeCookie('lngLanguageCode', {path: '/'});
		$.cookie('lngLanguageCode', 1, {path: '/'});
		// �����ڤ��ؤ��ܥ�������ܸ���ѹ�
		$(this).attr('src', '/img/type01/cmn/etoj/en_off_bt.gif');
	}
	// ����ʳ��ξ��(����̵�Ѥ����ܸ찷��)
	else {
		// �����ȥ��������
		title.attr('src', '/img/type01/mr/title_en.gif');

		// ���쥳���ɤ�0(�Ѹ�)������
		$.removeCookie('lngLanguageCode', {path: '/'});
		$.cookie('lngLanguageCode', 0, {path: '/'});
		// �����ڤ��ؤ��ܥ����Ѹ���ѹ�
		$(this).attr('src', '/img/type01/cmn/etoj/ja_off_bt.gif');
	}
});

// �ɤ߹��߻����ڤ��ؤ��¹�
// ���Τޤ޼¹Ԥ���ȱ��������å����Ƥ��ޤ��Τ�
// langCode��դ����ꤷ�Ƥ���kick����
(function(langCode){
	
	// ���쥳����ȿž
	langCode = (langCode == 1) ? 0 : 1;
	// COOKIE������
	$.removeCookie('lngLanguageCode', {path: '/'});
	$.cookie('lngLanguageCode', langCode, {path: '/'});

	// �����ڤ��ؤ��ܥ��� click���٥��ȯ��
	$('.control-block__button-language').trigger('click');

})($.cookie('lngLanguageCode'));
