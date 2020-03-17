


//---------------------------------------------------------------------
// 概要 : メンバ定義
//
// @member Object [g_objMenu]		: メニューオブジェクト
// @member Array  [g_arySubMenu]	: メニュー表示文字列配列
// @member Array  [g_aryMenuPos]	: メニュー表示座標配列
// @member Number [g_lngLang]		: 言語フラグ
//---------------------------------------------------------------------
var g_objMenu;
var g_arySubMenu;
var g_aryMenuPos;
var g_lngLang;




//---------------------------------------------------------------------
// 概要 : メニュー・コンストラクタ
//
// @param Object [objMenu]	: メニューオブジェクト
//---------------------------------------------------------------------
function clsSubMenu(objMenu) {
	// メニューオブジェクト取得
	this.g_objMenu = objMenu;

	// メニュー表示座標配列生成
	this.g_aryMenuPos = new Array();

	with (this) {
		g_aryMenuPos['es'] = new Array();
		g_aryMenuPos['p'] = new Array();
		g_aryMenuPos['so'] = new Array();
		g_aryMenuPos['po'] = new Array();
		g_aryMenuPos['sc'] = new Array();
		g_aryMenuPos['pc'] = new Array();
		g_aryMenuPos['wf'] = new Array();
		g_aryMenuPos['inv'] = new Array();
		g_aryMenuPos['list'] = new Array();
		g_aryMenuPos['mm'] = new Array();
		g_aryMenuPos['mr'] = new Array();
		g_aryMenuPos['lc'] = new Array();

		g_aryMenuPos['es'][0] = 24;
		g_aryMenuPos['es'][1] = 354;

		g_aryMenuPos['p'][0] = 184;
		g_aryMenuPos['p'][1] = 354;

		g_aryMenuPos['so'][0] = 344;
		g_aryMenuPos['so'][1] = 354;

		g_aryMenuPos['po'][0] = 504;
		g_aryMenuPos['po'][1] = 354;

		g_aryMenuPos['sc'][0] = 664;
		g_aryMenuPos['sc'][1] = 354;

		g_aryMenuPos['pc'][0] = 824;
		g_aryMenuPos['pc'][1] = 354;

		g_aryMenuPos['wf'][0] = 24;
		g_aryMenuPos['wf'][1] = 424;

		g_aryMenuPos['inv'][0] = 24;
		g_aryMenuPos['inv'][1] = 424;

		g_aryMenuPos['list'][0] = 184;
		g_aryMenuPos['list'][1] = 424;

		g_aryMenuPos['mm'][0] = 504;
		g_aryMenuPos['mm'][1] = 424;

		g_aryMenuPos['mr'][0] = 664;
		g_aryMenuPos['mr'][1] = 424;

		g_aryMenuPos['lc'][0] = 824;
		g_aryMenuPos['lc'][1] = 424;
	}
}
//---------------------------------------------------------------------
// 概要 : メニュー表示文字列生成
//---------------------------------------------------------------------
function fncInitArySubMenu() {
	// 言語フラグ取得
	this.g_lngLang = lngLanguageCode;

	// メニュー表示文字列配列生成
	this.g_arySubMenu = new Array();


	with (this) {
		g_arySubMenu['es'] = new Array();
		g_arySubMenu['p'] = new Array();
		g_arySubMenu['so'] = new Array();
		g_arySubMenu['po'] = new Array();
		g_arySubMenu['sc'] = new Array();
		g_arySubMenu['pc'] = new Array();
		g_arySubMenu['wf'] = new Array();
		g_arySubMenu['inv'] = new Array();
		g_arySubMenu['list'] = new Array();
		g_arySubMenu['mm'] = new Array();
		g_arySubMenu['mr'] = new Array();
		g_arySubMenu['lc'] = new Array();

		switch (g_lngLang) {
			case 0:
				g_arySubMenu['p'][0] = 'REGISTRATION';
				g_arySubMenu['p'][1] = 'SEARCH';

				//						g_arySubMenu['es'][0]	= 'REGISTRATION';
				g_arySubMenu['es'][0] = 'SEARCH';
				g_arySubMenu['es'][1] = 'UPLOAD';

				g_arySubMenu['so'][0] = 'REGISTRATION';
				g_arySubMenu['so'][1] = 'SEARCH';

				g_arySubMenu['po'][0] = 'REGISTRATION';
				g_arySubMenu['po'][1] = 'SEARCH';

				g_arySubMenu['sc'][0] = 'REGISTRATION';
				g_arySubMenu['sc'][1] = 'SEARCH';

				g_arySubMenu['pc'][0] = 'REGISTRATION';
				g_arySubMenu['pc'][1] = 'SEARCH';

				g_arySubMenu['wf'][0] = 'LIST';
				g_arySubMenu['wf'][1] = 'SEARCH';

				g_arySubMenu['list'][0] = 'PRODUCT PLAN';
				g_arySubMenu['list'][1] = 'PO';
				g_arySubMenu['list'][2] = 'ESTIMATE COST';

				g_arySubMenu['mm'][0] = 'REGISTRATION';
				g_arySubMenu['mm'][1] = 'SEARCH';

				g_arySubMenu['mr'][0] = 'REGISTRATION';
				g_arySubMenu['mr'][1] = 'SEARCH';
				break;

			case 1:
				g_arySubMenu['p'][0] = '商品登録';
				g_arySubMenu['p'][1] = '商品検索';

				//						g_arySubMenu['es'][0]	= '見積原価登録';
				g_arySubMenu['es'][0] = '見積原価検索';
				g_arySubMenu['es'][1] = 'ダウンロード';
				g_arySubMenu['es'][2] = 'アップロード';

				g_arySubMenu['so'][0] = '受注登録';
				g_arySubMenu['so'][1] = '受注検索';

				g_arySubMenu['po'][0] = '発注書検索';
				g_arySubMenu['po'][1] = '発注検索';

				g_arySubMenu['sc'][0] = '売上(納品書)登録';
				g_arySubMenu['sc'][1] = '納品書検索';
				g_arySubMenu['sc'][2] = '売上検索';

				g_arySubMenu['pc'][0] = '仕入登録';
				g_arySubMenu['pc'][1] = '仕入検索';

				g_arySubMenu['wf'][0] = '案件一覧';
				g_arySubMenu['wf'][1] = '案件検索';

				g_arySubMenu['inv'][0] = '請求書登録';
				g_arySubMenu['inv'][1] = '請求書検索';
				g_arySubMenu['inv'][2] = '請求集計';

				g_arySubMenu['list'][0] = '商品化企画書';
				g_arySubMenu['list'][1] = '発注書';
				g_arySubMenu['list'][2] = '見積原価書';
				g_arySubMenu['list'][3] = '納品伝票';
				g_arySubMenu['list'][4] = '請求書';

				g_arySubMenu['mm'][0] = '金型履歴登録';
				g_arySubMenu['mm'][1] = '金型履歴検索';

				g_arySubMenu['mr'][0] = '金型帳票登録';
				g_arySubMenu['mr'][1] = '金型帳票検索';

				g_arySubMenu['lc'][0] = 'L/C 情報';
				g_arySubMenu['lc'][1] = 'L/C 設定変更';
				g_arySubMenu['lc'][2] = 'L/C 編集';
				g_arySubMenu['lc'][3] = 'L/C帳票出力';
				break;

			default:
				break;
		}
	}

	return false;
}

//---------------------------------------------------------------------
// 概要 : メニュー表示
//
// @param  String [strMode]	: メニュー種類文字列
//---------------------------------------------------------------------
function fncShowSubMenu(strMode) {
	// メニュー座標微調整値
	//var lngBuffXpos = 8;
	//var lngBuffYpos = 10;

	// メニュー表示文字列生成
	this.fncInitArySubMenu();

	// HTML生成
	this.g_objMenu.innerHTML = this.fncGetSubMenuHTML(strMode, this.g_arySubMenu[strMode]);

	// メニュー座標調整
	this.g_objMenu.style.left = this.g_aryMenuPos[strMode][0] + 'px';
	this.g_objMenu.style.top = this.g_aryMenuPos[strMode][1] + 'px';

	//this.g_objMenu.style.left = lngBuffXpos + window.event.clientX + 'px';
	//this.g_objMenu.style.top  = lngBuffYpos + window.event.clientY + 'px';
	//alert( this.g_objMenu.style.top );

	// メニュー表示
	this.g_objMenu.style.display = 'block';

	return false;
}


//---------------------------------------------------------------------
// 概要 : メニュー非表示
//---------------------------------------------------------------------
function fncHideSubMenu() {
	// サブメニューが存在しない場合、処理終了
	if (typeof (this.g_objMenu) == 'undefined') return;

	// メニュー非表示
	this.g_objMenu.style.display = 'none';

	return false;
}
//---------------------------------------------------------------------
// 概要 : HTML生成
//
// @param  String [strMode]		: メニュー種類文字列
// @param  Array  [arySubMenu]	: メニュー表示文字列配列
//
// @return String [strHTML]		: メニューHTML
//---------------------------------------------------------------------
function fncGetSubMenuHTML(strMode, arySubMenu) {
	var i, j;
	var aryHTML = new Array();
	var strHTML = '';


	strHTML += '<div></div>';

	for (i = 0; i < arySubMenu.length; i++) {
		// メニューステータスチェック
		if (!this.fncGetSubMenuStatus(strMode, i)) continue;

		// Ref取得・HTML生成
		aryHTML[i] = '<button onmouseover="fncChangeBtnBGCol( this, \'#bcbcbc\' ); return false;" onmouseout="fncChangeBtnBGCol( this, \'#dedede\' ); return false;" onclick="fncSubMenuLocation( \'' + this.fncGetSubMenuRef(strMode, i) + '\' ); return false;">' + arySubMenu[i] + '</button><br>';
	}

	// HTML文字列の結合
	strHTML += aryHTML.join("");

	delete aryHTML;

	return strHTML;
}
//---------------------------------------------------------------------
// 概要 : メニューステータス取得
//
// @param  String [strMode]		: メニュー種類文字列
// @param  Number [i]			: メニュー表示文字列配列番号
//
// @return Number [lngStatus]	: メニューステータス
//---------------------------------------------------------------------
function fncGetSubMenuStatus(strMode, i) {
	var strStatus = '';
	var lngStatus = 0;

	// メニューステータス取得
	strStatus = eval('document.all.lngSubFlag_' + strMode + '_' + i).value;

	lngStatus = Number(strStatus);

	return lngStatus;
}
//---------------------------------------------------------------------
// 概要 : メニューRef取得
//
// @param  String [strMode]	: メニュー種類文字列
// @param  Number [i]		: メニュー表示文字列配列番号
//
// @return String [strRef]	: メニューRef
//---------------------------------------------------------------------
function fncGetSubMenuRef(strMode, i) {
	var strRef = '';

	// メニューRef取得
	strRef = eval('document.all.lngSubRef_' + strMode + '_' + i).value;

	return strRef;
}
//---------------------------------------------------------------------
// 概要 : メニューロケーション実行
//
// @param  String [strURL]	: メニューRef文字列
//---------------------------------------------------------------------
function fncSubMenuLocation(strURL) {
	if (strURL.indexOf('/lc/info') >= 0) {
		window.open(strURL, 'LC INFO', 'width='+ screen.availWidth + ', height=' + (screen.availHeight -50) + ', resizable=yes, scrollbars=yes, menubar=no');        
	} else if (strURL.indexOf('/lc/set') >= 0) {
		window.open(strURL, 'LC SETTING', 'width=1000, height=650, resizable=yes, scrollbars=yes, menubar=no');        
	} else {
		// メニューロケーション実行
		window.location.href = strURL;
	}

	return false;
}
//---------------------------------------------------------------------
// 概要 : ボタン背景色変更
//
// @param  Object [objBtn]		: ボタンオブジェクト
// @param  String [strColor]	: 変更色
//---------------------------------------------------------------------
function fncChangeBtnBGCol(objBtn, strColor) {
	// ボタン背景色変更
	objBtn.style.backgroundColor = strColor;

	return false;
}
