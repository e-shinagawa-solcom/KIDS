(function(sessionId){
	// サイドバー表示切替
	$('.navigate').on('click', function(){
        // // COOKIEから言語コードを取得
        var langCode = $.cookie('lngLanguageCode');
        // 言語コードが "0:英語" の場合
        if (langCode == 0) {
            // タイトル画像設定
            $("#navi-lc-info").text("L/C INFOMATION");
            $("#navi-lc-set-chg").text("L/C SETTING CHANGE");
        } else {
            // タイトル画像設定
            $("#navi-lc-info").text("L/C 情報");
            $("#navi-lc-set-chg").text("L/C 設定変更");
		}

		$('.navigate-box').toggle();
	});

	// 金型履歴登録画面
	$('.navi-mold-history-regist').on('click', function(){
		$(location).attr('href', '/mm/regist/index.php?strSessionID=' + sessionId);
	});
})($.cookie('strSessionID'));
