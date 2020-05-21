
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){

	// 金型履歴登録画面
	$('.function-buttons__regist').on('click', function(){
		$(location).attr('href', '../regist/index.php?strSessionID=' + sessionId);
	});
	// 金型履歴検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '../search/index.php?strSessionID=' + sessionId);
	});
	// 金型一覧検索画面
	$('.function-buttons__list_search').on('click', function(){
		$(location).attr('href', '../search/list/index.php?strSessionID=' + sessionId);
	});
})($('input[name="strSessionID"]').val());
