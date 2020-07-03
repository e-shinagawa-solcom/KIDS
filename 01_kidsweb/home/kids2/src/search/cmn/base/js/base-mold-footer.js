
$(function() {
	// セッションID取得
	var sessionId = $.cookie('strSessionID');
	// メインメニューボタン
	$('.control-block__buttan-main-menu').on({
		'click' : function() {
			// 取得できた場合
			if (sessionId) {
				// メインメニューへ遷移
				window.location.href = '/menu/menu.php?strSessionID=' + sessionId;
			}
		}
	});

	// ログアウトボタン
	$('.control-block__button-logout').on({
		'click' : function() {
			// 取得できた場合
			if (sessionId) {
				// メインメニューへ遷移
				window.location.href = '/login/logout.php?strSessionID=' + sessionId;
			}
		}
	});	

	// ログアウトボタン
	$('.control-block__button-help ').on({
		'click' : function() {
			var lngFunctionCode = $(this).attr('lngFunctionCode');
			console.log(lngFunctionCode);
			fncSetFncCode(lngFunctionCode);
			return false;
			window.open('/help/index.html', 'helpWin', 'top=10,left=10,width=600,height=500');
		}
	});
});

