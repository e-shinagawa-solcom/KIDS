(function(sessionId){
	// サイドバー表示切替
	$('.navigate').on('click', function(){
		$('.navigate-box').toggle();
	});

	// 受注検索画面
	$('.navi-sc-search').on('click', function(){
		$(location).attr('href', '/so/search/index.php?strSessionID=' + sessionId);
	});
	// 売上検索画面
	$('.navi-so-search').on('click', function(){
		$(location).attr('href', '/sc/search/index.php?strSessionID=' + sessionId);
	});
	// 売上納品書検索画面
	$('.navi-sc-hogesearch').on('click', function(){
		$(location).attr('href', '/sc/search2/index.php?strSessionID=' + sessionId);
	});
	// 帳票検索画面
	$('.navi-list-search').on('click', function(){
		$(location).attr('href', '/list/index.php?strSessionID=' + sessionId);
	});
	// 請求書検索
	$('.navi-inv-search').on('click', function(){
		$(location).attr('href', '/inv/search/index.php?strSessionID=' + sessionId);
	});
	// データベース検索画面
	$('.navi-data-search').on('click', function(){
		$(location).attr('href', '/dataex/index.php?strSessionID=' + sessionId);
	});
})($('input[name="strSessionID"]').val());
