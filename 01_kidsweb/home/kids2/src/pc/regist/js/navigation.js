(function(sessionId){
		// ¥µ¥¤¥É¥Ğ¡¼É½¼¨ÀÚÂØ
		$('.navigate').on('click', function(){
			$('.navigate-box').toggle();
		});
		// ¶â·¿ÍúÎòÅĞÏ¿²èÌÌ
		$('.navi-mold-history-regist').on('click', function(){
			$(location).attr('href', '/mm/regist/index.php?strSessionID=' + sessionId);
		});
		// ¶â·¿ÍúÎò¸¡º÷²èÌÌ
		$('.navi-mold-history-search').on('click', function(){
			// $(location).attr('href', '/mm/search/index.php?strSessionID=' + sessionId);
		});
		// ¶â·¿Ä¢É¼ÅĞÏ¿²èÌÌ
		$('.navi-mold-report-regist').on('click', function(){
			$(location).attr('href', '/mr/regist/index.php?strSessionID=' + sessionId);
		});
		// ¶â·¿Ä¢É¼¸¡º÷²èÌÌ
		$('.navi-mold-report-search').on('click', function(){
			$(location).attr('href', '/mr/search/index.php?strSessionID=' + sessionId);
		});
		// ¶â·¿Ä¢É¼½ĞÎÏ²èÌÌ
		$('.navi-list').on('click', function(){
			$(location).attr('href', '/list/index.php?strSessionID=' + sessionId);
		});
})($.cookie('strSessionID'));
