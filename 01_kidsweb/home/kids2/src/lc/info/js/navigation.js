(function(sessionId){
	// サイドバー表示切替
	$('.navigate').on('click', function(){
		$('.navigate-box').toggle();
	});

	// 帳票出力画面
	$('#reportOutputBtn').on('click', function(){
		$(location).attr('href', '/lc/report/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
