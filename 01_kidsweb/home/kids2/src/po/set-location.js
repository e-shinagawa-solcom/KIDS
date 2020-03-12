
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){

	// 発注検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '/po/search/index.php?strSessionID=' + sessionId);
	});
	// 発注書検索画面
	$('.function-buttons__search2').on('click', function(){
		$(location).attr('href', '/po/search2/index.php?strSessionID=' + sessionId);
	});
})($('input[name="strSessionID"]').val());
