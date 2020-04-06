

// メインメニューボタン
$('.control-block__buttan-main-menu').on({
	'click' : function() {
		// セッションID取得
		var sessionId = $('input[name="strSessionID"]').val();

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
		// セッションID取得
		var sessionId = $('input[name="strSessionID"]').val();

		// 取得できた場合
		if (sessionId) {
			// メインメニューへ遷移
			window.location.href = '/login/logout.php?strSessionID=' + sessionId;
		}
	}
});
