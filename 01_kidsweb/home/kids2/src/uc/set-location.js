
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){
	
	// ユーザー登録画面
	$('.function-buttons__regist').on('click', function () {
		$(location).attr('href', '/uc/regist/edit.php?strSessionID=' + sessionId　+ '&lngFunctionCode=1102');
	});
	// ユーザー情報画面
	$('.function-buttons__info').on('click', function () {
		$(location).attr('href', '/uc/regist/edit.php?strSessionID=' + sessionId　+ '&lngFunctionCode=1101');
	});
	// ユーザー検索画面
	$('.function-buttons__search').on('click', function () {
		$(location).attr('href', '/uc/search/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
