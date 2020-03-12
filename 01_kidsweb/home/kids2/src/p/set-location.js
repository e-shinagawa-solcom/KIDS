
// ---------------------------------
// -- クリック時の画面遷移先を設定
// ---------------------------------
(function(sessionId){
	// 仕入検索画面
	$('.function-buttons__search').on('click', function(){
		$(location).attr('href', '/p/search/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
