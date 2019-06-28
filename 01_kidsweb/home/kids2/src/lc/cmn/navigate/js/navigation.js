(function(sessionId){
	// サイドバー表示切替
	$('.navigate').on('click', function(){
		$('.navigate-box').toggle();
	});

	// L/C情報画面
	$('.navi-lc-info').on('click', function(){
		$(location).attr('href', '/lc/info/index.php?strSessionID=' + sessionId);
	});
	// L/C設定変更画面
	$('.navi-lc-set').on('click', function(){
		$(location).attr('href', '/lc/set/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
