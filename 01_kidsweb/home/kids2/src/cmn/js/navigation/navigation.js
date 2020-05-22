(function (sessionId) {
	// サイドバー表示切替
	$('.navigate').on('click', function () {
		$('.navigate-box').toggle();
		if ($('.navigate').attr('src') == '/img/type01/navi/off_bt.gif') {
			$('.navigate').attr('src', '/img/type01/navi/on_bt.gif');
		} else {
			$('.navigate').attr('src', '/img/type01/navi/off_bt.gif');
		}
	});

	// 受注検索画面
	$('.navi-so-search').on('click', function () {
		$(location).attr('href', '/so/search/index.php?strSessionID=' + sessionId);
	});
	// 発注検索画面
	$('.navi-po-search').on('click', function () {
		$(location).attr('href', '/po/search/index.php?strSessionID=' + sessionId);
	});
	// 発注書検索画面
	$('.navi-po2-search').on('click', function () {
		$(location).attr('href', '/po/search2/index.php?strSessionID=' + sessionId);
	});
	// 売上検索画面
	$('.navi-sc-search').on('click', function () {
		$(location).attr('href', '/sc/search/index.php?strSessionID=' + sessionId);
	});
	// 納品書検索画面
	$('.navi-sc2-search').on('click', function () {
		$(location).attr('href', '/sc/search2/index.php?strSessionID=' + sessionId);
	});
	// 仕入検索画面
	$('.navi-pc-search').on('click', function () {
		$(location).attr('href', '/pc/search/index.php?strSessionID=' + sessionId);
	});
	// データベース検索画面
	$('.navi-data-search').on('click', function () {
		$(location).attr('href', '/dataex/index.php?strSessionID=' + sessionId);
	});
	// 帳票出力画面
	$('.navi-list-search').on('click', function () {
		$(location).attr('href', '/list/index.php?strSessionID=' + sessionId);
	});
	// 見積原価検索画面
	$('.navi-estimate-search').on('click', function () {
		$(location).attr('href', '/estimate/search/index.php?strSessionID=' + sessionId);
	});
	// 請求書検索画面
	$('.navi-inv-search').on('click', function () {
		$(location).attr('href', '/inv/search/index.php?strSessionID=' + sessionId);
	});
	// 見積原価アプロード
	$('.navi-upload').on('click', function () {
		$(location).attr('href', '/upload2/index.php?strSessionID=' + sessionId);
	});
	// 金型帳票登録画面
	$('.navi-mr-regist').on('click', function () {
		$(location).attr('href', '/mr/regist/index.php?strSessionID=' + sessionId);
	});
	// 金型帳票検索画面
	$('.navi-mr-search').on('click', function () {
		$(location).attr('href', '/mr/search/index.php?strSessionID=' + sessionId);
	});
	// 金型履歴登録画面
	$('.navi-mm-regist').on('click', function () {
		$(location).attr('href', '/mm/regist/index.php?strSessionID=' + sessionId);
	});
	// 金型履歴検索画面
	$('.navi-mm-search').on('click', function () {
		$(location).attr('href', '/mm/search/index.php?strSessionID=' + sessionId);
	});
	// 金型一覧検索画面
	$('.navi-mm-list').on('click', function () {
		$(location).attr('href', '/mm/list/index.php?strSessionID=' + sessionId);
	});
	// 売上納品書登録画面
	$('.navi-sc-regist').on('click', function () {
		$(location).attr('href', '/sc/regist2/index.php?strSessionID=' + sessionId);
	});
	// 仕入登録画面
	$('.navi-pc-regist').on('click', function () {
		$(location).attr('href', '/pc/regist/index.php?strSessionID=' + sessionId);
	});
	// 請求書登録画面
	$('.navi-inv-regist').on('click', function () {
		$(location).attr('href', '/inv/regist/index.php?strSessionID=' + sessionId);
	});
	// 請求書集計画面
	$('.navi-inv-aggregate').on('click', function () {
		$(location).attr('href', '/inv/aggregate/index.php?strSessionID=' + sessionId);
	});
	// ユーザー登録画面
	$('.navi-uc-regist').on('click', function () {
		$(location).attr('href', '/uc/regist/edit.php?strSessionID=' + sessionId　+ '&lngFunctionCode=1102');
	});
	// ユーザー情報画面
	$('.navi-uc-info').on('click', function () {
		$(location).attr('href', '/uc/regist/edit.php?strSessionID=' + sessionId　+ '&lngFunctionCode=1101');
	});
	// ユーザー検索画面
	$('.navi-uc-search').on('click', function () {
		$(location).attr('href', '/uc/search/index.php?strSessionID=' + sessionId);
	});
	// メッセージ
	$('.navi-sysc-info').on('click', function () {
		$(location).attr('href', '/sysc/inf/index.php?strSessionID=' + sessionId);
	});
	// 管理者メール
	$('.navi-sysc-mail').on('click', function () {
		$(location).attr('href', '/sysc/mail/index.php?strSessionID=' + sessionId);
	});
	// セッション
	$('.navi-sysc-session').on('click', function () {
		$(location).attr('href', '/sysc/session/index.php?strSessionID=' + sessionId);
	});
	// サーバー
	$('.navi-sysc-server').on('click', function () {
		$(location).attr('href', '/sysc/sev/index.php?strSessionID=' + sessionId);
	});
	// マスタA
	$('.navi-mastera').on('click', function () {
		$(location).attr('href', '/m/list/index.php?strSessionID=' + sessionId);
	});
	// マスタB
	$('.navi-masterb').on('click', function () {
		$(location).attr('href', '/m/search/index.php?strSessionID=' + sessionId);
	});
	// データエクスポート
	$('.navi-dataex').on('click', function(){
		$(location).attr('href', '/dataex/index.php?strSessionID=' + sessionId);
	});
	// 締め処理
	$('.navi-closed').on('click', function(){
		$(location).attr('href', '/closed/index.php?strSessionID=' + sessionId);
	});
	// master a
	$('.navi-mastera').on('click', function(){
		$(location).attr('href', '/m/list/index.php?strSessionID=' + sessionId);
	});
	// master b
	$('.navi-masterb').on('click', function(){
		$(location).attr('href', '/m/search/index.php?strSessionID=' + sessionId);
	});
})($('input[name="strSessionID"]').val());
