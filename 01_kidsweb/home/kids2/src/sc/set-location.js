
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){

	// 売上（納品書）登録画面
	$('.function-buttons__regist').on('click', function(){
		$(location).attr('href', '/sc/regist2/index.php?strSessionID=' + sessionId);
	});
	// 売上検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '/sc/search/index.php?strSessionID=' + sessionId);
	});
	// 納品書画面
	$('.function-buttons__search2').on('click', function(){
		$(location).attr('href', '/sc/search2/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
