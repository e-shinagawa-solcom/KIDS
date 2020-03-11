
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){

	// 仕入登録画面
	$('.function-buttons__regist').on('click', function(){
		$(location).attr('href', '/pc/regist/index.php?strSessionID=' + sessionId);
	});
	// 仕入検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '/pc/search/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
