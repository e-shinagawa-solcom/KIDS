
$(function() {
	// ���å����ID����
	var sessionId = $.cookie('strSessionID');
	// �ᥤ���˥塼�ܥ���
	$('.control-block__buttan-main-menu').on({
		'click' : function() {
			// �����Ǥ������
			if (sessionId) {
				// �ᥤ���˥塼������
				window.location.href = '/menu/menu.php?strSessionID=' + sessionId;
			}
		}
	});

	// �������ȥܥ���
	$('.control-block__button-logout').on({
		'click' : function() {
			// �����Ǥ������
			if (sessionId) {
				// �ᥤ���˥塼������
				window.location.href = '/login/logout.php?strSessionID=' + sessionId;
			}
		}
	});
});

