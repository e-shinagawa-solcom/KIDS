(function(sessionId){
	// サイドバー表示切替
	$('.navigate').on('click', function(){
		$('.navigate-box').toggle();
	});

	// 発注検索画面
	$('.navi-so-search').on('click', function(){
		$(location).attr('href', '/so/search/index.php?strSessionID=' + sessionId);
	});
	// 発注検索画面
	$('.navi-po-search').on('click', function(){
		$(location).attr('href', '/po/search/index.php?strSessionID=' + sessionId);
	});
	// 売上検索画面
	$('.navi-sc-search').on('click', function(){
		$(location).attr('href', '/sc/search/index.php?strSessionID=' + sessionId);
	});
	// 仕入検索画面
	$('.navi-pc-search').on('click', function(){
		$(location).attr('href', '/pc/search/index.php?strSessionID=' + sessionId);
	});
	// 帳票検索画面
	$('.navi-list-search').on('click', function(){
		$(location).attr('href', '/list/index.php?strSessionID=' + sessionId);
	});
	// データベース検索画面
	$('.navi-data-search').on('click', function(){
		$(location).attr('href', '/dataex/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
