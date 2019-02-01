(function(sessionId){
	// サイドバー表示切替
	$('.navigate').on('click', function(){
		$('.navigate-box').toggle();
	});

	// 金型履歴登録画面
	$('.navi-mold-history-regist').on('click', function(){
		$(location).attr('href', '/mm/regist/index.php?strSessionID=' + sessionId);
	});
	// 金型履歴検索画面
	$('.navi-mold-history-search').on('click', function(){
		$(location).attr('href', '/mm/search/index.php?strSessionID=' + sessionId);
	});
	// 金型帳票登録画面
	$('.navi-mold-report-regist').on('click', function(){
		$(location).attr('href', '/mr/regist/index.php?strSessionID=' + sessionId);
	});
	// 金型帳票検索画面
	$('.navi-mold-report-search').on('click', function(){
		$(location).attr('href', '/mr/search/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
