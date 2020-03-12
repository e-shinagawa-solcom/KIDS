
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){
	
	// メッセージ
	$('.function-buttons__info').on('click', function () {
		$(location).attr('href', '/sysc/inf/index.php?strSessionID=' + sessionId);
	});
	// 管理者メール
	$('.function-buttons__mail').on('click', function () {
		$(location).attr('href', '/sysc/mail/index.php?strSessionID=' + sessionId);
	});
	// セッション
	$('.function-buttons__session').on('click', function () {
		$(location).attr('href', '/sysc/session/index.php?strSessionID=' + sessionId);
	});
	// サーバー
	$('.function-buttons__server').on('click', function () {
		$(location).attr('href', '/sysc/sev/index.php?strSessionID=' + sessionId);
	});
})($('input[name="strSessionID"]').val());
