

// English ⇔ 日本語切り替えボタン押下時
$('.control-block__button-language').on('click', function(){

	// COOKIEから言語コードを取得
	var langCode = $.cookie('lngLanguageCode');

	// 切り替え対象要素のキャッシュ
	var title = $('.base-header__title-image');

	// 言語コードが "0:英語" の場合
	if (langCode == 0) {
		// タイトル画像設定
		title.attr('src', '/img/type01/mr/title_en.gif');

		// 言語コードを1(日本語)に設定
		$.cookie('lngLanguageCode', 1);
		// 言語切り替えボタンを日本語に変更
		$(this).attr('src', '/img/type01/cmn/etoj/ja_off_bt.gif');
	}
	// それ以外の場合(問答無用で日本語扱い)
	else {
		// タイトル画像設定
		title.attr('src', '/img/type01/mr/title_ja.gif');

		// 言語コードを0(英語)に設定
		$.cookie('lngLanguageCode', 0);
		// 言語切り替えボタンを英語に変更
		$(this).attr('src', '/img/type01/cmn/etoj/en_off_bt.gif');
	}
});

// 読み込み時に切り替え実行
// そのまま実行すると英日スイッチしてしまうので
// langCodeを逆に設定してからkickする
(function(langCode){

	// 言語コード反転
	langCode = (langCode === 1) ? 0 : 1;
	// COOKIEに設定
	$.cookie('lngLanguageCode', langCode);

	// 言語切り替えボタン clickイベント発行
	$('.control-block__button-language').trigger('click');

})($.cookie('lngLanguageCode'));
