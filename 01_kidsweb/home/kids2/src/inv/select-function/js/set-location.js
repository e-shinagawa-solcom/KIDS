
// ---------------------------------
// -- ����å����β��������������
// ---------------------------------
(function(sessionId){

	// �������Ͽ����
	$('.function-buttons__regist').on('click', function(){
		$(location).attr('href', '../regist/index.php?strSessionID=' + sessionId);
	});
	// ����񸡺�����
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '../search/index.php?strSessionID=' + sessionId);
	});
	// ���ὸ�ײ���
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '../total/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
