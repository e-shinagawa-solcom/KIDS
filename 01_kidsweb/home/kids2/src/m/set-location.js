
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){
	
	// マスタA
	$('.function-buttons__mastera').on('click', function () {
		$(location).attr('href', '/m/list/index.php?strSessionID=' + sessionId);
	});
	// マスタB
	$('.function-buttons__masterb').on('click', function () {
		$(location).attr('href', '/m/search/index.php?strSessionID=' + sessionId);
	});
})($('input[name="strSessionID"]').val());
