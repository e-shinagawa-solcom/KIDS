(function(sessionId){
		// �����ɥС�ɽ������
		$('.navigate').on('click', function(){
			$('.navigate-box').toggle();
		});
		// �ⷿ������Ͽ����
		$('.navi-mold-history-regist').on('click', function(){
			$(location).attr('href', '/mm/regist/index.php?strSessionID=' + sessionId);
		});
		// �ⷿ���򸡺�����
		$('.navi-mold-history-search').on('click', function(){
			// $(location).attr('href', '/mm/search/index.php?strSessionID=' + sessionId);
		});
		// �ⷿĢɼ��Ͽ����
		$('.navi-mold-report-regist').on('click', function(){
			$(location).attr('href', '/mr/regist/index.php?strSessionID=' + sessionId);
		});
		// �ⷿĢɼ��������
		$('.navi-mold-report-search').on('click', function(){
			$(location).attr('href', '/mr/search/index.php?strSessionID=' + sessionId);
		});
		// �ⷿĢɼ���ϲ���
		$('.navi-list').on('click', function(){
			$(location).attr('href', '/list/index.php?strSessionID=' + sessionId);
		});
})($.cookie('strSessionID'));
