
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){

	// 請求書登録画面
	$('.function-buttons__regist').on('click', function(){
		$(location).attr('href', '../regist/index.php?strSessionID=' + sessionId);
	});
	// 請求書検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '../search/index.php?strSessionID=' + sessionId);
	});
	// 請求集計画面
	$('.function-buttons__total').on('click', function(){
		$(location).attr('href', '../aggregate/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
