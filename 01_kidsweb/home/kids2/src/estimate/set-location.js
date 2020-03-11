
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){

	// 見積原価検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '/estimate/search/index.php?strSessionID=' + sessionId);
	});
	// アップロード画面
	$('.function-buttons__upload').on('click', function(){
		$(location).attr('href', '/upload2/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
