

//@-------------------------------------------------------------------------------------------------------------------
/**
* ファイル概要 : K.I.D.S.システム共通使用関数群
*
*
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/
//--------------------------------------------------------------------------------------------------------------------




///// FOCUS COLOR /////
var focuscolor = '#c7d0cb';














//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : トリム関数
*
* 対象 : すべて
*/
//--------------------------------------------------------------------------------------------------------------------
function trim(str) {
	var strRet, strFinal;

	strTemp = str;

	//LTRIM
	strRet = LTrim(strTemp);
	//RTRIM
	strFinal = RTrim(strRet);

	//結果表示
	//document.frmSample.txtWordLen.value = strTemp.length;
	//document.frmSample.txtResult.value = strFinal;
	//document.frmSample.txtResultLen.value = strFinal.length;

	return strFinal;
}
function RTrim(strTemp) {
	var nLoop = 0;
	var strReturn = strTemp;
	while (nLoop < strTemp.length) {
		if ((strReturn.substring(strReturn.length - 1, strReturn.length) == " ") || (strReturn.substring(strReturn.length - 1, strReturn.length) == "　")) {
			strReturn = strTemp.substring(0, strTemp.length - (nLoop + 1));
		}
		else {
			break;
		}
		nLoop++;
	}
	return strReturn;
}
function LTrim(strTemp) {
	var nLoop = 0;
	var strReturn = strTemp;
	while (nLoop < strTemp.length) {
		if ((strReturn.substring(0, 1) == " ") || (strReturn.substring(0, 1) == "　")) {
			strReturn = strTemp.substring(nLoop + 1, strTemp.length);
		}
		else {
			break;
		}
		nLoop++;
	}
	return strReturn;
}





//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 日付・時間取得処理関数
*
* 対象 : すべて
*/
//--------------------------------------------------------------------------------------------------------------------
function fncGetDate(obj) {
	aryWeek = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	objDate = new Date();

	var yy1 = objDate.getYear();
	var yy2 = (yy1 < 2000) ? yy1 + 1900 : yy1;
	var mm = objDate.getMonth() + 1;
	var dd = objDate.getDate();
	var num = objDate.getDay();

	if (mm < 10) { mm = '0' + mm; }
	if (dd < 10) { dd = '0' + dd; }

	var h = objDate.getHours();
	var m = objDate.getMinutes();
	var s = objDate.getSeconds();

	if (h < 10) { h = '0' + h; }
	if (m < 10) { m = '0' + m; }
	if (s < 10) { s = '0' + s; }

	var date = yy2 + '/' + mm + '/' + dd;
	var week = aryWeek[num];
	var time = h + ':' + m + ':' + s;

	var dwt = date + '&nbsp;' + week + '&nbsp;' + time;

	strDateVars.innerHTML = dwt;

	setTimeout('fncGetDate()', 1000);
}













//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 日本語英語切替用グローバル変数
*
* 対象 : すべて
*
* @param [lngLanguageCode] : [数値型] . COOKIE用
* @param [lngClickCode]    : [数値型] . クリック用
*/
//--------------------------------------------------------------------------------------------------------------------
var lngLanguageCode = 1; //クッキー用
var lngClickCode = 0; //クリック用
















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ローディング画面処理関数
*
* 解説 : データのロード完了時に、ローディング画面を非表示にする関数。
*
* 対象 : すべて
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function Loading() {

	Preload.style.display = 'none';
	Preload.style.width = 0;
	Preload.style.height = 0;

	if (typeof (OverLayer) != 'undefined') {
		OverLayer.style.display = 'none';
	}

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : オブジェクトのリサイズ処理関数
*
* 解説 : ウィンドウ枠内表示可能領域サイズから
*        オブジェクトのwidth,height値に代入。
*
* 対象 : プリロード用iframe
*
* @param [objId]     : [オブジェクト型] . オブジェクトID
* @param [lngWidth]  : [数値型]         . WIDTH微調整値
* @param [lngHeight] : [数値型]         . HEIGHT微調整値
*
* @event [onload],[onresize] : body
* @event [onload]            : iframe
*/
//--------------------------------------------------------------------------------------------------------------------
function fncObjectResize(objId, lngWidth, lngHeight) {

	// ウィンドウ枠内表示可能領域の取得
	var winH = document.body.offsetHeight;
	var winW = document.body.offsetWidth;

	// リサイズ - 微調整値
	objId.style.width = winW - lngWidth;
	objId.style.height = winH - lngHeight;

	return false;
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : サブウィンドウの重なり順変更関数
*
* 解説 : データのロード完了時に、ローディング画面を非表示にする関数。
*
* 対象 : サブウィンドウ
*
* @param [Znum] : [数値型] . z-index初期値
* @param [obj]  : [オブジェクト型] . 対象オブジェクト名
*
* @event [onclick] : body , 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
var Znum = 8;

function Zchange(obj) {
	obj.style.zIndex = Znum++;
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : オンラインヘルプ機能コードセット関数
*
* 解説 : 各管理画面上での機能コードを取得して
*        それぞれのヘルプページのファイルネームとする。
*
* 対象 : ヘルプボタンオブジェクト
*
* 外部関数 : [fncOpenHelp] オンラインヘルプウィンドウオープンモジュール
*
* @param [g_lngFncCode] : [数値型] . 機能コード値格納用グローバル変数
* @param [lngFncCode]   : [数値型] . 機能コード値
*
* @event [onclick]      : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
var g_lngFncCode; // 機能コード格納用グローバル変数

function fncSetFncCode(lngFncCode) {
	// 機能コードがある場合
	if (lngFncCode) {
		// 機能コードの代入
		g_lngFncCode = lngFncCode;
	}

	// 機能コードがない場合
	else {
		// 機能コードの代入(オンラインヘルプトップページ)
		g_lngFncCode = 1;
	}

	// ヘルプウィンドウオープン
	fncOpenHelp();

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : オンラインヘルプウィンドウオープンモジュール
*
* 解説 : ヘルプウィンドウを開く。
*
* 対象 : [fncSetFncCode] オンラインヘルプ機能コードセット関数
*/
//--------------------------------------------------------------------------------------------------------------------
function fncOpenHelp() {
	helpW = window.open('/help/index.html', 'helpWin', 'top=10,left=10,width=600,height=500');
}




















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ホスト名取得関数
*
* 解説 : ホスト名を取得して、ヘッダに表示させる。
*
* 対象 : すべて
*
* @param [obj]         : 対象オブジェクト
* @param [strProtocol] : プロトコルヘッダー取得用ローカル変数
* @param [strHostname] : ホスト名取得用ローカル変数
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncReferer(obj) {
	var strProtocol = location.protocol;
	var strHostname = location.hostname;

	obj.innerHTML = strProtocol + '//' + strHostname + '/';

	return false;
}























//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 明細行エラー表示関数
*
* 解説 : 明細データ読み込み時に明細行にエラーがあった場合に表示する。
*
* 対象 : [受注・発注・売上・仕入]
*
* @param [g_DetailErrorFlag] : [数値型]   . エラーメッセージ表示フラグ
* @param [strErrorMessage]   : [文字列型] . エラーメッセージ文字列
*/
//--------------------------------------------------------------------------------------------------------------------
var g_DetailErrorFlag = 0;

function fncDetailErrorOpen(strErrorMessage) {

	if (strErrorMessage != '') {
		window.ErrorMessageFrame.style.visibility = 'visible';
		window.errorWin.ErrMeg.innerText = strErrorMessage;
		g_DetailErrorFlag = 1;
	}

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 明細行エラー非表示関数
*
* 解説 : エラーメッセージ表示フラグ[g_DetailErrorFlag]が[1]の時
*        エラーメッセージを非表示にする。
*
* 対象 : [受注・発注・売上・仕入]
*
* @param [g_DetailErrorFlag] : [数値型] . エラーメッセージ表示フラグ
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDetailErrorClose() {
	if (g_DetailErrorFlag == 1) {
		window.ErrorMessageFrame.style.visibility = 'hidden';
		window.errorWin.ErrMeg.innerText = '';
		g_DetailErrorFlag = 0;
	}

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 特殊文字ウィンドウ表示関数
*
* 解説 : 特殊文字ウィンドウを表示する。
*
* 対象 : 特殊文字ボタン
*
* @param [specialCnt] : [数値型] . 特殊文字ウィンドウ表示フラグ
*
* @event [onclick]      : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
var specialCnt = 0;

function fncSpecialChar() {
	if (specialCnt == 0) {
		SpecialCharFrame.style.visibility = 'visible';
		SpecialBt.innerHTML = specialButton3;
		specialCnt = 1;
	}
	else if (specialCnt == 1) {
		SpecialCharFrame.style.visibility = 'hidden';
		SpecialBt.innerHTML = specialButton1;
		specialCnt = 0;
	}

	return false;
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : tabindex制御処理
*
* 解説 : tabindexを指定のオブジェクトに移動させる。
*
* 対象 : 入力フィールド
*
* @param [objName] : [オブジェクト型] . 指定先オブジェクト名
*
* @event [onblur] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDefaultTabindex(objName) {
	objName.focus();

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : マウスダウン時にオブジェクトのアルファ値を変更する
*
* 対象 : ボタンオブジェクト
*
* @param [objName] : [オブジェクト型] . オブジェクト名
*
* @event [onmousedown] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAlphaOn(obj) {
	obj.style.filter = 'alpha(opacity=50)';
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : マウスアップ時にオブジェクトのアルファ値を変更する
*
* 対象 : ボタンオブジェクト
*
* @param [objName] : [オブジェクト型] . オブジェクト名
*
* @event [onmouseup] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAlphaOff(obj) {
	obj.style.filter = 'alpha(opacity=100)';
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索画面上で「FROM - TO」の箇所でのコピー処理
*
* 対象 : 入力フィールド
*
* @param [objFrom] : [オブジェクト型] . コピー元オブジェクト名
* @param [objTo]   : [オブジェクト型] . コピー先オブジェクト名
*
* @event [onblur] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncCopyValue(objFrom, objTo) {
	if (typeof (m_strErrorObjectName) != 'undefined') {
		if (m_strErrorObjectName == '') {
			objTo.value = objFrom.value;
		}
	}

	return false;
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ステータスバーの表示を変化させない
*
* 対象 : すべて
*
* @event [window.document.onmouseover]
*/
//--------------------------------------------------------------------------------------------------------------------
defaultStatus = 'K.I.D.S.';

window.document.onmouseover = onMouseOver;

function onMouseOver(e) {
	window.status = 'K.I.D.S.';
	return true
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : キーイベント処理全般
*
* 対象 : すべて
*
* @event [window.document.onkeydown]
*/
//--------------------------------------------------------------------------------------------------------------------
window.document.onkeydown = onKeyDown;


g_aryStaffKeyCode = new Array(); // スタッフウィンドウキーコード用配列定義

g_aryRupinKeyCode = new Array(); // サウンドキーコード用配列定義

g_aryUraKIDSKeyCode = new Array(); // 裏KIDSキーコード用配列定義


var g_strStaffKey = '38,38,40,40,37,39,37,39,66,65'; // スタッフキーコード定義

var g_strRupinKey = '82,85,80,73,78,51';             // サウンドキーコード定義

var g_strUraKIDSKey = '85,82,65,75,73,68,83';    // 裏KIDSキーコード定義


function onKeyDown(e) {

	//------------------------------------------
	// 概要 : [BACKSPACE]キー押下時処理
	//------------------------------------------
	if (window.event.keyCode == 8) {
		//要素が編集不可能領域の場合
		if (window.event.srcElement.contentEditable == 'false') {
			return false;
		}
		//要素が編集可能領域の場合
		else if (window.event.srcElement.contentEditable == 'true') {
			//画像だった場合
			if (document.selection.type == "Control") return false;

			return true;
		}

		// 要素が [text][textarea][password][file] の場合
		if (window.event.srcElement.type == 'text' ||
			window.event.srcElement.type == 'tel' ||
			window.event.srcElement.type == 'file' ||
			window.event.srcElement.type == 'password' ||
			window.event.srcElement.type == 'textarea') {
			return true;
		}
		else {
			return false;
		}

		/*
		// 要素が未定義の場合
		if (typeof(window.event.srcElement.name) == "undefined")
		{
			return false ;
		}

		// 要素の属性が [disabled] の場合
		if (window.event.srcElement.disabled == true)
		{
			return false ;
		}
		*/
	}



	//------------------------------------------
	// 概要 : [FUNCTION]キー押下時処理
	//------------------------------------------
	//[F5]キー押下防止(更新禁止)
	if (window.event.keyCode == 116) {
		event.keyCode = 0;
		return false;
	}

	//[F3]キー押下防止(検索画面表示禁止)
	if (window.event.keyCode == 114) {
		event.keyCode = 0;
		return false;
	}

	//[F11]キー押下防止(全画面表示禁止)
	if (window.event.keyCode == 122) {
		event.keyCode = 0;
		return false;
	}



	//------------------------------------------
	// 概要 : [ctrl]キーと併せての押下時処理
	//------------------------------------------
	// [ctrl] + [r]キー押下防止(更新禁止)
	//if( window.event.ctrlKey == true && window.event.keyCode == 82 ) return false ;

	// [ctrl] + [e]キー押下防止(検索画面表示禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 69) return false;

	// [ctrl] + [w]キー押下防止(ウィンドウクローズ禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 87) return false;

	// [ctrl] + [i]キー押下防止(お気に入り画面表示禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 73) return false;

	// [ctrl] + [o]キー押下防止(ファイルオープン画面禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 79) return false;

	// [ctrl] + [l]キー押下防止(ファイルオープン画面禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 76) return false;

	// [ctrl] + [n]キー押下防止(新規ウィンドウオープン禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 78) return false;

	// [ctrl] + [b]キー押下防止(お気に入りの整理画面表示禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 66) return false;

	// [ctrl] + [p]キー押下防止(プリント画面禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 80) return false;

	// [ctrl] + [h]キー押下防止(履歴画面禁止)
	if (window.event.ctrlKey == true && window.event.keyCode == 72) return false;

	// [alt] + [←]キー押下防止(戻る禁止)
	if (window.event.altKey == true && window.event.keyCode == 37) return false;

	// [alt] + [→]キー押下防止(進む禁止)
	if (window.event.altKey == true && window.event.keyCode == 39) return false;



	//------------------------------------------
	// 概要 : [ENTER]キー押下処理(ログイン画面専用)
	//------------------------------------------
	if (typeof (LoginObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			// ログインボタンのアルファ値変更
			fncAlphaOn(document.all.loginbutton);

			// ログイン処理
			LoginCheck();
		}
	}





	// キーコードの取得
	var lngKeyCode = window.event.keyCode;



	//------------------------------------------
	// 概要 : 裏KIDS処理
	//------------------------------------------
	if (g_aryUraKIDSKeyCode.length <= 7) {
		// キーコード用配列に格納
		g_aryUraKIDSKeyCode.push(lngKeyCode);

		// デバッグ
		//alert( lngKeyCode );
	}

	// [s]キー押下時の場合
	if (window.event.keyCode == 83) {
		// 下記の配列順序の場合
		if (g_aryUraKIDSKeyCode == g_strUraKIDSKey) {
			// 裏KIDS処理
			alert('ウラキッヅ');

			// キーコード用配列の初期化
			g_aryUraKIDSKeyCode = new Array();
		}
		// 上記の配列順序以外の場合
		else {
			// キーコード用配列の初期化
			g_aryUraKIDSKeyCode = new Array();
		}
	}


	/*
		//------------------------------------------
		// 概要 : スタッフウィンドウオープン処理
		//------------------------------------------
		if( g_aryStaffKeyCode.length <= 10 )
		{
			// キーコード用配列に格納
			g_aryStaffKeyCode.push( lngKeyCode );
		}
	
		// [a]キー押下時の場合
		if( window.event.keyCode == 65 )
		{
			// 下記の配列順序の場合
			if( g_aryStaffKeyCode == g_strStaffKey )
			{
				// スタッフページオープン
				fncStaffMatrix();
	
				// キーコード用配列の初期化
				g_aryStaffKeyCode = new Array();
			}
			// 上記の配列順序以外の場合
			else
			{
				// キーコード用配列の初期化
				g_aryStaffKeyCode = new Array();
			}
		}
	*/


	/*
		//------------------------------------------
		// 概要 : サウンド再生処理
		//------------------------------------------
		if( g_aryRupinKeyCode.length <= 6 )
		{
			// キーコード用配列に格納
			g_aryRupinKeyCode.push( lngKeyCode );
		}
	
		// [3]キー押下時の場合
		if( window.event.keyCode == 51 )
		{
			// 下記の配列順序の場合
			if( g_aryRupinKeyCode == g_strRupinKey )
			{
				if( typeof(BeepSound) != 'undefined' )
				{
					// サウンド再生
					BeepSound.src = '/error/lupin.wav';
				}
	
				// キーコード用配列の初期化
				g_aryRupinKeyCode = new Array();
			}
			// 上記の配列順序以外の場合
			else
			{
				// キーコード用配列の初期化
				g_aryRupinKeyCode = new Array();
			}
		}
	*/


	//------------------------------------------
	// 概要 : 商品検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (PSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('ProductSearch', window.PS);
		}
	}
	//------------------------------------------
	// 概要 : 見積原価検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (ESSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('EstimateSearch', window.PS);
		}
	}
	//------------------------------------------
	// 概要 : 発注検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (POSearchObject) != 'undefined') {
		if (m_lngErrorFlag == 0) {
			if (window.event.keyCode == 13 || window.event.keyCode == 14) {
				GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
				setCookie('PurchaseSearch', window.PS);
			}
		}
	}
	//------------------------------------------
	// 概要 : 仕入検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (PCSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('PurchaseControlSearch', window.PS);
		}
	}
	//------------------------------------------
	// 概要 : 受注検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (SOSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('ReceiveSearch', window.PS);
		}
	}
	//------------------------------------------
	// 概要 : 売上検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (SCSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('SalesSearch', window.PS);
		}
	}
	//------------------------------------------
	// 概要 : ワークフロー検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (WFSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('WorkflowSearch', window.PS);
		}
	}
	//------------------------------------------
	// 概要 : 商品化企画書検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (PListSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
	//------------------------------------------
	// 概要 : 発注書検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (POListSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
	//------------------------------------------
	// 概要 : 見積原価書検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (ESListSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
	//------------------------------------------
	// 概要 : ユーザー検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (UCSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.PS, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
			setCookie('UserSearch', window.PS);
		}
	}




	//------------------------------------------
	// 概要 : 会社マスタ検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (COMasterSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.form1, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
	//------------------------------------------
	// 概要 : グループマスタ検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (GMasterSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.form1, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
	//------------------------------------------
	// 概要 : 通貨レートマスタ検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (RMasterSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.form1, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
	//------------------------------------------
	// 概要 : ワークフロー順序マスタ検索[ENTER]キー押下時処理
	//------------------------------------------
	if (typeof (WFMasterSearchObject) != 'undefined') {
		if (window.event.keyCode == 13 || window.event.keyCode == 14) {
			GoResult(window.form1, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES');
		}
	}
}














//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ドラッグアンドドロップの禁止
*
* 対象 : すべて
*
* @event [window.document.ondragstart]
*/
//--------------------------------------------------------------------------------------------------------------------
window.document.ondragstart = onDragStart;

function onDragStart(e) {
	//編集可能領域のみドラックアンドドロップができる
	if (document.selection.type == "Control") {
		return true;
	}
	//上記以外は、ドラックアンドドロップ禁止
	else {
		return false;
	}
}













//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索画面チェックボックス値のクッキー保存処理関数
*
* 解説 : 検索画面チェックボックス値をクッキーに保存する。
*
* 対象 : チェックボックス
*
* @param [strCookieName] : [文字列型]       . クッキー名
* @param [obj]           : [オブジェクト型] . 対象フォーム名
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function setCookie(strCookieName, obj) {
	aryCookie = new Array();
	var j = 0;
	var expdate = new Date();
	expdate.setTime(expdate.getTime() + (7 * 24 * 60 * 60 * 1000));

	for (i = 0; i < obj.elements.length; i++) {
		//if ( obj.elements[i].type == 'checkbox' )
		if (obj.elements[i].name == 'ViewColumn[]') {
			if (obj.elements[i].checked == true) {
				aryCookie[j] = obj.elements[i].value + ':checked';
				j++;
			}
			else if (obj.elements[i].checked == false) {
				aryCookie[j] = obj.elements[i].value + ':';
				j++;
			}
		}
	}
	strCookie = aryCookie.join("&");
	document.cookie = strCookieName + '=' + strCookie + ';expires=' + expdate.toGMTString() + ';path=/;';

}



















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索画面チェックボックス値一斉クリア処理関数
*
* 解説 : 検索画面チェックボックス値を一斉にクリアする。
*
* 対象 : チェックボックス
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function CheckResetCnt() {

	if (typeof (window.Pwin.CheckAll1) != 'undefined') {
		window.Pwin.CheckAll1.innerHTML = offBt;
		window.Pwin.checkcount1 = 0;
	}

	if (typeof (window.Pwin.CheckAll2) != 'undefined') {
		window.Pwin.CheckAll2.innerHTML = offBt;
		window.Pwin.checkcount2 = 0;
	}
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 登録確認処理用リダイレクト関数
*
* 解説 : 登録確認時にダイアログを表示させるためのリダイレクト用関数。
*        [strCheckName]の値が[1]の場合に[fncGetRegistrationDataModule]を呼び出す。
*
* 対象 : 登録用テンプレート
*
* 外部関数 : [fncGetRegistrationDataModule] 登録確認処理用ダイアログ表示モジュール
*
* @param [objFrmA]         : [オブジェクト型] . フォームＡのオブジェクト名
* @param [objFrmB]         : [オブジェクト型] . フォームＢのオブジェクト名
* @param [strIfrmParent]   : [文字列型]       . ダイアログ上で呼び出す親HTMLファイルのURL
* @param [strIfrmChild]    : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルのURL(Iframe)
* @param [strIfrmStyleId]  : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll]       : [文字列型]       . Iframeでスクロールの許可・不許可
* @param [lngLanguageCode] : [数値型]         . ランゲージコード値
* @param [strCheckName]    : [数値型]         . [fncGetRegistrationDataModule]の実行判断値
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncRegistrationConfirm(objFrmA, objFrmB, strIfrmParent, strIfrmChild, strIfrmStyleId, strScroll, lngLanguageCode, strCheckName, strFunction) {

	// 登録データが正常な場合
	if (strCheckName == 1) {
		// ダイアログ呼出モジュールの実行
		fncGetRegistrationDataModule(objFrmA, objFrmB, strIfrmParent, strIfrmChild, strIfrmStyleId, strScroll, lngLanguageCode, strFunction);
	}

	return false;

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 登録確認処理用ダイアログ表示モジュール
*
* 解説 : 登録確認時にダイアログを表示させるためのモジュール。
*        配列[aryFrm]にフォーム要素の値を代入後ダイアログを表示する。
*
* @param [objFrmA]         : [オブジェクト型] . フォームＡのオブジェクト名
* @param [objFrmB]         : [オブジェクト型] . フォームＢのオブジェクト名
* @param [strIfrmParent]   : [文字列型]       . ダイアログ上で呼び出す親HTMLファイルのURL
* @param [strIfrmChild]    : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルのURL(Iframe)
* @param [strIfrmStyleId]  : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll]       : [文字列型]       . Iframeでスクロールの許可・不許可
* @param [lngLanguageCode] : [数値型]         . ランゲージコード値
*/
//--------------------------------------------------------------------------------------------------------------------
function fncGetRegistrationDataModule(objFrmA, objFrmB, strIfrmParent, strIfrmChild, strIfrmStyleId, strScroll, lngLanguageCode, strFunction) {

	// 各配列の生成
	aryFrm = new Array();
	aryFrm[0] = new Array(); // Iframeのパラメーター格納用
	aryFrm[1] = new Array(); // フォームＡ「NAME」値格納用
	aryFrm[2] = new Array(); // フォームＡ「VALUE」値格納用
	aryFrm[3] = new Array(); // フォームＢ「NAME」値格納用
	aryFrm[4] = new Array(); // フォームＢ「VALUE」値格納用


	// 固定値の代入
	aryFrm[0][0] = strIfrmChild;
	aryFrm[0][1] = strIfrmStyleId;
	aryFrm[0][2] = strScroll;
	aryFrm[0][3] = lngLanguageCode;


	var j = 0;
	var k = 0;


	// フォームAのデータを取得する
	if (objFrmA != '') {
		for (i = 0; i < objFrmA.elements.length; i++) {

			aryFrm[1][j] = objFrmA.elements[i].name;
			aryFrm[2][j] = fncCheckReplaceString(objFrmA.elements[i].value);
			j++;

		}
	}

	// フォームBのデータを取得する
	if (objFrmB != '') {
		for (i = 0; i < objFrmB.elements.length; i++) {

			aryFrm[3][k] = objFrmB.elements[i].name;
			aryFrm[4][k] = fncCheckReplaceString(objFrmB.elements[i].value);
			k++;

		}
	}


	switch (strFunction) {
		case "ES":
			// ダイアログに出力
			retVal = window.showModalDialog(strIfrmParent, aryFrm, "dialogHeight:700px; dialogWidth:1011px; center:yes; status:no; edge:raised; help:no; scroll:no;");
			break;

		default:
			// ダイアログに出力
			retVal = window.showModalDialog(strIfrmParent, aryFrm, "dialogHeight:580px; dialogWidth:970px; center:yes; status:no; edge:raised; help:no; scroll:no;");
			break;
	}



	// Return Value取得
	if (retVal) {
		var i;
		var strRef = "";

		for (i = 0; i < retVal.length; i++) {
			strRef += retVal[i];
		}

		// 移動
		location.href = strRef;

		// 移動
		//		location.href = retVal[0] + retVal[1];
	}


	return false;

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 登録確認処理用取得済みデータ展開用関数
*
* 解説 : [fncGetRegistrationDataModule]より取得された配列データをダイアログ上で再展開し、サブミットをする。
*
* 対象 : ダイアログリダイレクト用テンプレート
*
* @param [objFrm]    : [オブジェクト型] . リダイレクトフォーム名
* @param [strAction] : [文字列型]       . サブミット先URL
* @param [objLayA]   : [オブジェクト型] . 取得済みデータ展開用オブジェクト名Ａ
* @param [objLayB]   : [オブジェクト型] . 取得済みデータ展開用オブジェクト名Ｂ
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSetArgsforRegistration(objFrm, strAction, objLayA, objLayB) {

	var aryArgs = window.parent.g_aryArgs;
	var aryInner1 = new Array();
	var aryInner2 = new Array();


	if (window.parent.g_DialogLoadFlag) return;


	if (objLayA) {
		for (i = 0; i < aryArgs[1].length; i++) {
			aryInner1[i] = '<input type="hidden" name="' + aryArgs[1][i] + '" value="' + aryArgs[2][i] + '">';
		}

		objLayA.innerHTML = aryInner1.join('\n');

	}


	if (objLayB) {
		for (i = 0; i < aryArgs[3].length; i++) {
			aryInner2[i] = '<input type="hidden" name="' + aryArgs[3][i] + '" value="' + aryArgs[4][i] + '">';
		}

		objLayB.innerHTML = aryInner2.join('\n');

	}


	objFrm.action = strAction;

	objFrm.submit();

	// ローディング画面の非表示
	fncShowHidePreload(1);

	window.parent.g_DialogLoadFlag = true;

	return false;
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 発注状態処理関数
*
* 解説 : 発注状態を複数選択可能にする。
*
* 対象 : 検索用テンプレート
*
* @param [obj]    : [オブジェクト型] . 対象リストボックスオブジェクト
* @param [objHdn] : [オブジェクト型] . バッファ用オブジェクト
*
* @event [onchange] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAryOrderStatusCode(obj, objBuffer) {
	aryOrderStatus = new Array();

	for (i = 0; i < obj.options.length; i++) {
		if (obj.options[i].selected) {
			aryOrderStatus.push(obj.options[i].value);
		}
	}

	objBuffer.value = aryOrderStatus;

	// デバッグ
	//alert( objBuffer.value );
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索結果表示用関数
*
* 解説 : 検索結果をダイアログを表示させるための関数。
*        配列[args]にフォーム要素の値を代入後ダイアログを表示する。
*
* 対象 : 検索用テンプレート
*
* @param [obj1]      : [オブジェクト型] . フォームのオブジェクト名
* @param [obj2]      : [文字列型]       . ダイアログ上で呼び出す親HTMLファイルのURL
* @param [strUrl]    : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルのURL(Iframe)
* @param [strID]     : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll] : [文字列型]       . Iframeでスクロールの許可・不許可
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function GoResult(obj1, obj2, strUrl, strID, strScroll) {
	var j = 0;
	var k = 0;

	args = new Array();
	args[0] = new Array();
	args[1] = new Array();
	args[2] = new Array();
	args[3] = new Array();
	args[4] = new Array();

	args[0][0] = strUrl;
	args[0][1] = strID;
	args[0][2] = strScroll;


	///// other name /////
	for (i = 0; i < obj1.elements.length; i++) {

		if (typeof (obj1.elements[i]) == 'undefined') {
			continue;
		}

		if (obj1.elements[i].type == 'checkbox') {
			if (obj1.elements[i].checked == true) {
				args[1][j] = obj1.elements[i].name;
				args[2][j] = obj1.elements[i].value;
				j++;
			}
			continue;
		}

		// 検索ページ[状態]項目以外
		if (obj1.elements[i].name != 'OrderStatusObject') {
			args[3][k] = obj1.elements[i].name;
			args[4][k] = obj1.elements[i].value;
			k++;
		}

	}



	// alert(args[4].join('\n'));

	//alert(args[2][1]);return;

	//retVal = window.showModalDialog( obj2 , args , "dialogHeight:530px;dialogWidth:1011px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
	retVal = window.open(obj2, args, "width=1011, height=650, resizable=no, scrollbars=no, menubar=no");

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索結果表示用取得済みデータ展開関数
*
* 解説 : [GoResult]より取得された配列データをダイアログ上で再展開し、サブミットをする。
*
* 対象 : ダイアログリダイレクト用テンプレート
*
* @param [objFrm] : [オブジェクト型] . リダイレクトフォーム名
* @param [obj2]   : [オブジェクト型] . 取得済みデータ展開用オブジェクト名Ａ
* @param [obj3]   : [オブジェクト型] . 取得済みデータ展開用オブジェクト名Ｂ
* @param [strUrl] : [文字列型] . サブミット先URL
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSetArgs(ObjFrm, obj2, obj3, strUrl) {

	var aryArgs = window.parent.g_aryArgs;
	var aryInner1 = new Array();
	var aryInner2 = new Array();


	if (window.parent.g_DialogLoadFlag) return;


	for (i = 0; i < aryArgs[1].length; i++) {
		aryInner1[i] = '<input type="hidden" name="' + aryArgs[1][i] + '" value="' + aryArgs[2][i] + '">';
	}

	obj2.innerHTML = aryInner1.join('\n');


	for (i = 0; i < aryArgs[3].length; i++) {
		aryInner2[i] = '<input type="hidden" name="' + aryArgs[3][i] + '" value="' + aryArgs[4][i] + '">';
	}

	obj3.innerHTML = aryInner2.join('\n');


	//alert( obj2.innerHTML );
	//alert( obj3.innerHTML );


	ObjFrm.action = strUrl;
	//ObjFrm.target = '_blank';
	//alert(ObjFrm.name);

	//alert('['+ ObjFrm.action +']');

	ObjFrm.submit();

	//var doc = document.body.createTextRange();
	//alert(doc.htmlText);

	window.parent.g_DialogLoadFlag = true;

	return false;
}












//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ワークフロー詳細画面ダイアログ表示用関数
*
* 対象 : 検索結果用テンプレート
*
* @param [strUrl]          : [文字列型]       . サブミット先URL
* @param [ObjFrm]          : [オブジェクト型] . フォームのオブジェクト名
* @param [strID]           : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll]       : [文字列型]       . Iframeでスクロールの許可・不許可
* @param [lngLanguageCode] : [数値型]         . ランゲージコード値
* @param [strMode]         : [文字列型]       . 処理モードの文字列
* @param [width]           : [数値型]         . ウィンドウの横幅
* @param [height]          : [数値型]         . ウィンドウの縦幅
* @param [xops]            : [数値型]         . ウィンドウのX座標
* @param [ypos]            : [数値型]         . ウィンドウのY座標
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowWfDialogCommon(strUrl, ObjFrm, strID, strScroll, lngLanguageCode, strMode, width, height, xpos, ypos) {
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl;          // 実行先URL
	args[0][1] = strID;           // IFrameのスタイル用ID
	args[0][2] = strScroll;       // IFrameスクロールの許可・不許可
	args[0][3] = lngLanguageCode; // $lngLanguageCode
	args[0][4] = strMode;         // 'detail' または 'delete'

	// dialogWidth:696px; dialogHeight:679px;

	var status = 'dialogWidth:' + width + 'px; dialogHeight:' + height + 'px;';
	status += 'dialogLeft:' + xpos + 'px;dialogTop:' + ypos + 'px;';
	status += 'center:yes;status:no;edge:raised;help:no;scroll:no;';

	window.showModelessDialog('/result/common.html', args, status);
}










//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索結果画面上からのダイアログ表示用共通関数
*
* 解説 : 検索結果画面上からダイアログを表示させるための関数。
*        配列[args]にフォーム要素の値を代入後ダイアログを表示する。
*        [詳細][削除][無効化]画面表示に使用。
*
* 対象 : 検索結果用テンプレート
*
* @param [strUrl]          : [文字列型]       . サブミット先URL
* @param [ObjFrm]          : [オブジェクト型] . フォームのオブジェクト名
* @param [strID]           : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll]       : [文字列型]       . Iframeでスクロールの許可・不許可
* @param [lngLanguageCode] : [数値型]         . ランゲージコード値
* @param [strMode]         : [文字列型]       . 処理モードの文字列
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowDialogCommon(strUrl, ObjFrm, strID, strScroll, lngLanguageCode, strMode) {
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl; // 実行先URL
	args[0][1] = strID; // IFrameのスタイル用ID
	args[0][2] = strScroll; // IFrameスクロールの許可・不許可
	args[0][3] = lngLanguageCode; // $lngLanguageCode
	args[0][4] = strMode; // 'detail' または 'delete'


	// retval = window.showModalDialog( '/result/common.html' , args , "dialogHeight:679px;dialogWidth:696px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
	retval = window.open('/result/common.html', args, "width=696, height=670, resizable=no, scrollbars=no, menubar=no");

	if (retval) {
		// ローディング画面の表示
		fncShowHidePreload(0);

		ObjFrm.submit();
	}
	//onunload="window.returnValue=true;" ←戻り値設定
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 検索結果画面上からの修正ダイアログ表示用関数
*
* 解説 : 検索結果画面上から修正ダイアログを表示させるための関数。
*        配列[args]にフォーム要素の値を代入後ダイアログを表示する。
*        [修正]画面表示に使用。
*
* 対象 : 検索結果用テンプレート
*
* @param [strUrl]          : [文字列型]       . サブミット先URL
* @param [ObjFrm]          : [オブジェクト型] . フォームのオブジェクト名
* @param [strID]           : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll]       : [文字列型]       . Iframeでスクロールの許可・不許可
* @param [lngLanguageCode] : [数値型]         . ランゲージコード値
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowDialogRenew(strUrl, ObjFrm, strID, strScroll, lngLanguageCode) {
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl;
	args[0][1] = strID;
	args[0][2] = strScroll;
	args[0][3] = lngLanguageCode;

	//retval = window.showModalDialog( '/result/renew.html' , args , "dialogHeight:580px;dialogWidth:970px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
	retval = window.open('/result/renew.html', args, "width=970px, height=580px, resizable=no, scrollbars=no, menubar=no");

	if (retval) {
		// ローディング画面の表示
		fncShowHidePreload(0);

		ObjFrm.submit();
	}

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : マスタ管理での検索結果画面上からのダイアログ表示用共通関数
*
* 解説 : マスタ管理での検索結果画面上からダイアログを表示させるための関数。
*        配列[args]にフォーム要素の値を代入後ダイアログを表示する。
*        [追加][修正][削除]画面表示に使用。
*
* 対象 : 検索結果用テンプレート
*
* @param [strUrl]          : [文字列型]       . サブミット先URL
* @param [ObjFrm]          : [オブジェクト型] . フォームのオブジェクト名
* @param [strID]           : [文字列型]       . ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
* @param [strScroll]       : [文字列型]       . Iframeでスクロールの許可・不許可
* @param [lngLanguageCode] : [数値型]         . ランゲージコード値
* @param [strMode]         : [文字列型]       . 処理モードの文字列
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowDialogCommonMaster(strUrl, ObjFrm, strID, strScroll, lngLanguageCode, strMode) {
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl; // 実行先URL
	args[0][1] = strID; // IFrameのスタイル用ID
	args[0][2] = strScroll; // IFrameスクロールの許可・不許可
	args[0][3] = lngLanguageCode; // $lngLanguageCode
	args[0][4] = strMode; // 'fix' , 'add' , 'delete'

	// retval = window.showModalDialog( '/result/remove_master.html' , args , "dialogHeight:510px;dialogWidth:600px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
	retval = window.open('/result/remove_master.html', args, "height:510px;width:600px;center:yes;status:no;edge:raised;help:no;scroll:no;");


	if (retval) {
		// ローディング画面の表示
		//fncShowHidePreload( 0 );

		ObjFrm.submit();
	}
	//onunload="window.returnValue=true;" ←戻り値設定
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : コピー用テキストレンジ取得モジュール
*
* 解説 : 指定のタグ(ID)内のテキストレンジを取得する。
*        戻り値を[fncDoCopy]で呼び出す。

* @param [strID]   : [オブジェクト型] . 範囲用オブジェクト名
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDoCopyModule(strID) {
	// 指定のタグ(ID)内のテキストレンジを取得する
	// var doc1 = document.body.createTextRange();
	// doc1.moveToElementText(strID);

	// return doc1.htmlText;

	if (document.body.createTextRange) {
		// IEの場合
		var doc1 = document.body.createTextRange();
		doc1.moveToElementText(strID);
		return doc1.htmlText;
	} else if (window.getSelection) {
		var selection = window.getSelection();
		var range = document.createRange();
		range.selectNodeContents(strID);
		selection.removeAllRanges();
		selection.addRange(range);
		var str = range.cloneContents();
		selection.removeAllRanges();

		var container = document.createElement("div");
		container.appendChild(str);
		return container.innerHTML;
	}
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : クリップボードへコピーする関数
*
* 解説 : 指定タグ(id)内の文字列をクリップボードへコピーする関数。
*        [fncDoCopyModule]の戻り値を取得してクリップボードにコピーする。
*
* 対象 : 検索結果用テンプレート
*
* 外部関数 : [fncDoCopyModule] コピー用テキストレンジ取得モジュール
*
* @param [objBuff1] : [オブジェクト型] . コピー値格納バッファ用オブジェクト名
* @param [strID1]   : [オブジェクト型] . 範囲Ａオブジェクト名
* @param [strID2]   : [オブジェクト型] . 範囲Ｂオブジェクト名
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDoCopy(objBuff1, strID1, strID2) {
	// バッファ値を結合
	objBuff1.value = fncDoCopyModule(strID1) + fncDoCopyModule(strID2);
	if (objBuff1.createTextRange) {
		// IEの場合
		var docA = objBuff1.createTextRange();
		// バッファのテキストレンジからクリップボードにコピー
		docA.moveStart('character', 0);
		docA.moveEnd('character');
		docA.execCommand("copy");
		alert('クリップボードにコピーしました。');
	} else if (window.getSelection) {
		// div要素を作成
		var node = document.createElement("div");
		node.innerHTML = objBuff1.value;
		// div要素を追加
		document.body.appendChild(node);
		// div要素をコピー
		var selection = getSelection();
		selection.removeAllRanges();
		var range = document.createRange();
		range.selectNodeContents(node);
		selection.addRange(range);
		document.execCommand('copy');
		selection.removeAllRanges();
		// div要素を削除
		document.body.removeChild(node);
		alert('クリップボードにコピーしました。');
	}
	return true;
}

// function fncDoCopy(objname) {
// 	// クリップボードに値を反映
// 	if (window.getSelection) {
// 		var selection = getSelection();
// 		selection.removeAllRanges();
// 		var range = document.createRange();
// 		range.selectNodeContents(document.getElementById(objname));
// 		selection.addRange(range);
// 		document.execCommand('copy');
// 		selection.removeAllRanges();
// 		alert('クリップボードにコピーしました。');
// 	} else {
// 		alert("クリップボードへのコピーに失敗しました。");
// 	}
// 	return true;
// }

/*
	サンプルソース
	<input type="button" value="copy" onclick="fncDoCopy( 'COPYAREA1', copyhidden1 , 'COPYAREA2', copyhidden2 );">
	<!-- バッファ用オブジェクト1 --><input type="text" value="" name="copyhidden1" style="visibility:hidden">
	<!-- バッファ用オブジェクト2 --><input type="text" value="" name="copyhidden2" style="visibility:hidden">
	<SPAN ID=COPYAREA1></span>
	<SPAN ID=COPYAREA2></span>
*/


















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 登録時のエラーメッセージ表示関数
*
* 解説 : 登録時にエラーがあった場合にその箇所にエラーメッセージを表示する関数。
*
* 対象 : 登録用テンプレート
*
* @param [obj1] : [オブジェクト型] . エラーメッセージ表示用オブジェクト名
* @param [obj2] : [文字列型] . エラーメッセージ文字列
*
* @event [oumouseover] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function ShowComment(obj1, obj2) {
	var nowX = window.event.clientX;
	var nowY = window.event.clientY;

	//alert(event.clientX);
	//alert(event.clientY);
	obj1.style.left = nowX + 10;
	obj1.style.top = nowY;

	if (obj2 != '') {
		obj1.innerHTML = 'ERROR: ' + obj2;
		obj1.style.visibility = 'visible';
	}

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 登録時のエラーメッセージ表示関数
*
* 解説 : 登録時にエラーメッセージが表示されている場合にそれを非表示にする関数。
*
* 対象 : 登録用テンプレート
*
* @param [obj1] : [オブジェクト型] . エラーメッセージ表示用オブジェクト名
*
* @event [oumouseout] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function HideComment(obj1) {
	obj1.style.visibility = 'hidden';

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : NEW : 左メニュー表示・非表示関数
*
* 解説 : 左メニューを表示・非表示にする関数。
*
* 対象 : すべて
*
* @param [obj1] : [オブジェクト型] . 左メニュー用オブジェクト名(iframe)
* @param [obj2] : [オブジェクト型] . 左メニュー用ボタンオブジェクト名
*
* @event [oumouseover] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
var Navicnt = 0;

function fncNaviVisible(obj1, obj2) {
	// 非表示
	if (Navicnt) {
		obj1.style.visibility = 'hidden';
		obj2.innerHTML = naviButton1;

		Navicnt = 0;
	}
	// 表示
	else {
		obj1.style.visibility = 'visible';
		obj2.innerHTML = naviButton3;

		Navicnt = 1;
	}

	return false;
}



//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 左メニュー表示関数
*
* 解説 : 左メニューを表示する関数。
*
* 対象 : すべて
*
* @param [obj1] : [オブジェクト型] . 左メニュー用オブジェクト名(iframe)
* @param [obj2] : [オブジェクト型] . 左メニュー用ボタンオブジェクト名
*
* @event [oumouseover] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function NavigationON(obj1, obj2) {
	if (Navicnt == 0) {
		obj1.style.visibility = 'visible';
		obj2.innerHTML = naviButton3;
		Navicnt = 1;
	}
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 左メニュー表示非関数
*
* 解説 : 左メニューを非表示にする関数。
*
* 対象 : すべて
*
* @param [obj1] : [オブジェクト型] . 左メニュー用オブジェクト名(iframe)
* @param [obj2] : [オブジェクト型] . 左メニュー用ボタンオブジェクト名
*
* @event [oumouseover] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function NavigationOFF(obj1, obj2) {
	return;

	if (Navicnt == 1) {
		obj1.style.visibility = 'hidden';
		obj2.innerHTML = naviButton1;
		Navicnt = 0;
	}
	return false;
}




















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : サブミット関数
*
* 解説 : フォームのデータをサブミットする。
*
* 対象 : 入力フィールド
*
* @param [objFrm] : [オブジェクト型] . フォームのオブジェクト名
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSubmitQuery(objFrm) {
	objFrm.submit();
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : フォームクリア関数
*
* 解説 : フォームのデータをクリアする。
*
* 対象 : 入力フィールド
*
* @param [objFrm] : [オブジェクト型] . フォームのオブジェクト名
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncResetFrm(objFrm) {
	objFrm.reset();
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : iframe内のフォームクリア関数
*
* 解説 : iframe内のフォームのデータをクリアする。
*
* 対象 : 入力フィールド
*
* @param [obj1] : [オブジェクト型] . iframeのオブジェクト名
* @param [obj2] : [オブジェクト型] . iframeのフォームオブジェクト名
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncClearIfrm(obj1, obj2) {

	strFrm = 'window.' + obj1.name + '.' + obj2.name + '.reset();';
	//alert(strFrm);

	// window.execScriptはEdge非対応のためevalに換装
	//window.execScript(strFrm, "JavaScript");
	eval(strFrm);

	//ErrMeg.style.visibility = 'hidden' ;
	//ERmark.style.visibility = 'hidden' ;

	return false;
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : オンロード時のオートフォーカス処理関数
*
* 解説 : オンロード時に自動的に任意のオブジェクトをフォーカスさせる。
*
* 対象 : 入力フィールド
*
* @param [obj] : [オブジェクト型] . 対象オブジェクト
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAutoFocus(obj) {
	if (obj) {
		obj.focus();
	}
	else {
		return false;
	}
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : フォーカス時の色変更&入力済み文字列全選択関数
*
* 解説 : オンフォーカス時に入力オブジェクトの背景色を変更させ、
*        入力済みの文字列があった場合はそれを全選択状態にさせる。
*
* 対象 : 入力フィールド
*
* @param [obj] : [オブジェクト型] . 対象オブジェクト
*
* @event [onfocus] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function chColorOn(obj) {
	obj.style.backgroundColor = focuscolor;
	obj.select();
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : フォーカス時の色変更関数
*
* 解説 : オンフォーカス時に入力オブジェクトの背景色を変更させる。
*
* 対象 : 入力フィールド
*
* @param [obj] : [オブジェクト型] . 対象オブジェクト
*
* @event [onblur] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function chColorOff(obj) {
	obj.style.backgroundColor = '#ffffff';
	return false;
}




















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : スタッフページウィンドウ表示関数
*
* 解説 : スタッフページウィンドウを表示させる。
*
* 対象 : すべて
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncStaffMatrix() {
	retVal = window.showModalDialog('/staff/index.html', '1', "dialogHeight:530px; dialogWidth:392px; dialogLeft:5px; dialogTop:5px; edge:raised; center:no; help:no; resizable:no; status:no; unadorned:yes;");

	return false;
}



























//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ボタンオブジェクトのイベント処理関数
*
* 解説 : ボタンオブジェクトでの各種イベントを処理する関数。
*
* 対象 : ボタンオブジェクト
*
* @param [obj]     : [オブジェクト型] . 対象オブジェクト
* @param [strMode] : [オブジェクト型] . 処理モード文字列
*
* @event [onmouseover],[onmouseout],[onfocus],[onblur] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
///// LIST ADD-DEL BT /////
function AddOff(obj) {
	obj.src = addbt1;
}

function AddOn(obj) {
	obj.src = addbt2;
}

function DelOff(obj) {
	obj.src = delbt1;
}

function DelOn(obj) {
	obj.src = delbt2;
}




///// LIST UP-DOWN BT /////
function UpOff(obj) {
	obj.src = upbt1;
}

function UpOn(obj) {
	obj.src = upbt2;
}

function DownOff(obj) {
	obj.src = downbt1;
}

function DownOn(obj) {
	obj.src = downbt2;
}





////////// MSW BUTTON ROLLOVER //////////

function MswOff(obj) {
	obj.src = mswbt1;
}
function MswOn(obj) {
	obj.src = mswbt2;
}





///// WF LIST BUTTON /////
function WFlistJOff(obj) {
	obj.src = listJ1;
}

function WFlistJOn(obj) {
	obj.src = listJ2;
}

function WFlistEOff(obj) {
	obj.src = listE1;
}

function WFlistEOn(obj) {
	obj.src = listE2;
}





///// MAIN MENU BUTTON /////
function MainmenuOff(obj) {
	obj.src = mainmenubt1;
}

function MainmenuOn(obj) {
	obj.src = mainmenubt2;
}





///// ONLINE HELP BUTTON /////
function HelpOff(obj) {
	obj.src = helpbt1;
}

function HelpOn(obj) {
	obj.src = helpbt2;
}





///// NAVIGATION BUTTON /////
function NaviOff(obj) {
	obj.src = navibt1;
}

function NaviOn(obj) {
	obj.src = navibt2;
}









































//--------------------------------------------------
// 概要 : [RELOAD]ボタン
//--------------------------------------------------
function fncReloadButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = reloadbt1;
			break;

		case 'on':
			obj.src = reloadbt2;
			break;

		default:
			break;
	}
}



//--------------------------------------------------
// 概要 : [ENTER]ボタン
//--------------------------------------------------
function fncEnterButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = enterbt1;
			break;

		case 'on':
			obj.src = enterbt2;
			break;

		default:
			break;
	}
}






function LoginOff(obj) {
	obj.src = login1;
}

function LoginOn(obj) {
	obj.src = login2;
}







////////// [SEARCH]SEARCH BT //////////
function schSchJOff(obj) {
	obj.src = schSchJ1;
}

function schSchJOn(obj) {
	obj.src = schSchJ2;
}

function schSchEOff(obj) {
	obj.src = schSchE1;
}

function schSchEOn(obj) {
	obj.src = schSchE2;
}


function schClrJOff(obj) {
	obj.src = schClrJ1;
}

function schClrJOn(obj) {
	obj.src = schClrJ2;
}

function schClrEOff(obj) {
	obj.src = schClrE1;
}

function schClrEOn(obj) {
	obj.src = schClrE2;
}





////////// SELECTW BUTTON //////////

function SelectJOff(obj) {
	obj.src = sltbtJ1;
}

function SelectJOn(obj) {
	obj.src = sltbtJ2;
}

function SelectEOff(obj) {
	obj.src = sltbtE1;
}

function SelectEOn(obj) {
	obj.src = sltbtE2;
}







////////// OFF ON BT //////////
function OffBt(obj) {
	obj.src = off;
}

function OnBt(obj) {
	obj.src = offon;
}









// ボタンイメージ変更共通関数
function fncChangeBtImg(obj, img) {
	obj.src = img;
}




////////// ADD ROW BUTTON //////////

function AddRJOff(obj) {
	obj.src = addrowJ1;
}

function AddRJOn(obj) {
	obj.src = addrowJ2;
}

function AddREOff(obj) {
	obj.src = addrowE1;
}

function AddREOn(obj) {
	obj.src = addrowE2;
}



////////// DEL ROW BUTTON //////////

function DelRJOff(obj) {
	obj.src = delrowJ1;
}

function DelRJOn(obj) {
	obj.src = delrowJ2;
}

function DelREOff(obj) {
	obj.src = delrowE1;
}

function DelREOn(obj) {
	obj.src = delrowE2;
}



////////// COMMIT BUTTON //////////

function CmtJOff(obj) {
	obj.src = commitJ1;
}

function CmtJOn(obj) {
	obj.src = commitJ2;
}

function CmtEOff(obj) {
	obj.src = commitE1;
}

function CmtEOn(obj) {
	obj.src = commitE2;
}



////////// GRAY REGIST BUTTON //////////

function GrayRegistJOff(obj) {
	obj.src = grayregistJ1;
}

function GrayRegistJOn(obj) {
	obj.src = grayregistJ2;
}

function GrayRegistEOff(obj) {
	obj.src = grayregistE1;
}

function GrayRegistEOn(obj) {
	obj.src = grayregistE2;
}





////////// BLUE REGIST BUTTON //////////

function BlueRegistJOff(obj) {
	obj.src = blueregistJ1;
}

function BlueRegistJOn(obj) {
	obj.src = blueregistJ2;
}

function BlueRegistEOff(obj) {
	obj.src = blueregistE1;
}

function BlueRegistEOn(obj) {
	obj.src = blueregistE2;
}


////////// BLUE BACK BUTTON //////////

function BlueBackJOff(obj) {
	obj.src = bluebackJ1;
}

function BlueBackJOn(obj) {
	obj.src = bluebackJ2;
}

function BlueBackEOff(obj) {
	obj.src = bluebackE1;
}

function BlueBackEOn(obj) {
	obj.src = bluebackE2;
}



////////// BLOWN BACK BUTTON //////////

function GrayBackJOff(obj) {
	obj.src = graybackJ1;
}

function GrayBackJOn(obj) {
	obj.src = graybackJ2;
}

function GrayBackEOff(obj) {
	obj.src = graybackE1;
}

function GrayBackEOn(obj) {
	obj.src = graybackE2;
}



////////// BLOWN BACK BUTTON //////////

function BlownBackJOff(obj) {
	obj.src = blownbackJ1;
}

function BlownBackJOn(obj) {
	obj.src = blownbackJ2;
}

function BlownBackEOff(obj) {
	obj.src = blownbackE1;
}

function BlownBackEOn(obj) {
	obj.src = blownbackE2;
}




////////// REGIST BUTTON //////////

function RegJOff(obj) {
	obj.src = registJ1;
}

function RegJOn(obj) {
	obj.src = registJ2;
}

function RegEOff(obj) {
	obj.src = registE1;
}

function RegEOn(obj) {
	obj.src = registE2;
}









////////// MASTER REGIST BUTTON //////////

function MasterRegJOff(obj) {
	obj.src = mregistJ1;
}

function MasterRegJOn(obj) {
	obj.src = mregistJ2;
}

function MasterRegEOff(obj) {
	obj.src = mregistE1;
}

function MasterRegEOn(obj) {
	obj.src = mregistE2;
}







////////// MASTER NAVI BUTTON //////////

function MAJOff(obj) {
	obj.src = mAJ1;
}

function MAJOn(obj) {
	obj.src = mAJ2;
}

function MAEOff(obj) {
	obj.src = mAE1;
}

function MAEOn(obj) {
	obj.src = mAE2;
}


function MBJOff(obj) {
	obj.src = mBJ1;
}

function MBJOn(obj) {
	obj.src = mBJ2;
}

function MBEOff(obj) {
	obj.src = mBE1;
}

function MBEOn(obj) {
	obj.src = mBE2;
}



////////// CLEAR BUTTON //////////

function ClearJOff(obj) {
	obj.src = clearJ1;
}

function ClearJOn(obj) {
	obj.src = clearJ2;
}

function ClearEOff(obj) {
	obj.src = clearE1;
}

function ClearEOn(obj) {
	obj.src = clearE2;
}





function fncGrayClearButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = grayclearJ1;
			break;

		case 'onJ':
			obj.src = grayclearJ2;
			break;

		case 'offE':
			obj.src = grayclearE1;
			break;

		case 'onE':
			obj.src = grayclearE2;
			break;
	}
}




////////// SEARCH BUTTON //////////

function SearchJOff(obj) {
	obj.src = searchJ1;
}

function SearchJOn(obj) {
	obj.src = searchJ2;
}

function SearchEOff(obj) {
	obj.src = searchE1;
}

function SearchEOn(obj) {
	obj.src = searchE2;
}







////////// NAVI USER INFO BUTTON //////////

function UInfoJOff(obj) {
	obj.src = infoJ1;
}

function UInfoJOn(obj) {
	obj.src = infoJ2;
}

function UInfoEOff(obj) {
	obj.src = infoE1;
}

function UInfoEOn(obj) {
	obj.src = infoE2;
}









////////// NAVI REGIST BUTTON //////////

function RegiJOff(obj) {
	obj.src = regiJ1;
}

function RegiJOn(obj) {
	obj.src = regiJ2;
}

function RegiEOff(obj) {
	obj.src = regiE1;
}

function RegiEOn(obj) {
	obj.src = regiE2;
}





//-----------------------------------------------------------------------------
function fncChangeBtnImg(objID, strPath) {
	objID.src = strPath;
}
//-----------------------------------------------------------------------------






//////////納品書　BUTTON///////////
function hghJOff(obj) {
	obj.src = hghJ1;
}
function hghJOn(obj) {
	obj.src = hghJ2;
}

////////// NAVI SEARCH BUTTON //////////

function SchJOff(obj) {
	obj.src = schJ1;
}

function SchJOn(obj) {
	obj.src = schJ2;
}

function DocSchJOff(obj) {
	obj.src = docschJ1;
}

function DocSchJOn(obj) {
	obj.src = docschJ2;
}
function SchEOff(obj) {
	obj.src = schE1;
}

function SchEOn(obj) {
	obj.src = schE2;
}


////////// NAVI SEARCH BUTTON //////////

function SearchOff(obj) {
	obj.src = search1;
}

function SearchOn(obj) {
	obj.src = search2;
}

function RegistOff(obj) {
	obj.src = regist1;
}

function RegitstOn(obj) {
	obj.src = regist2;
}


function TotalOff(obj) {
	obj.src = total1;
}

function TotalOn(obj) {
	obj.src = total2;
}




//-----------------------------------------------------------------------------
// 概要 : メインメニューボタンのロールオーバー処理・マウスダウン処理関数
// 解説 : 各ボタン毎に「日本語」「英語」版を定義
//-----------------------------------------------------------------------------

//---------------------------------------------------
// 適用：「商品管理」ボタン
//---------------------------------------------------
function fncPButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = pJ1;
			break;

		case 'onJ':
			obj.src = pJ2;
			break;

		case 'offE':
			obj.src = pE1;
			break;

		case 'onE':
			obj.src = pE2;
			break;

		case 'downJ':
			obj.src = pJ3;
			break;

		case 'downE':
			obj.src = pE3;
			break;

		default:
			break;
	}
}



//---------------------------------------------------
// 適用：「受注管理」ボタン
//---------------------------------------------------
function fncSOButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = soJ1;
			break;

		case 'onJ':
			obj.src = soJ2;
			break;

		case 'offE':
			obj.src = soE1;
			break;

		case 'onE':
			obj.src = soE2;
			break;

		case 'downJ':
			obj.src = soJ3;
			break;

		case 'downE':
			obj.src = soE3;
			break;

		default:
			break;
	}
}



//---------------------------------------------------
// 適用：「発注管理」ボタン
//---------------------------------------------------
function fncPOButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = poJ1;
			break;

		case 'onJ':
			obj.src = poJ2;
			break;

		case 'offE':
			obj.src = poE1;
			break;

		case 'onE':
			obj.src = poE2;
			break;

		case 'downJ':
			obj.src = poJ3;
			break;

		case 'downE':
			obj.src = poE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「仕入管理」ボタン
//---------------------------------------------------
function fncPCButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = pcJ1;
			break;

		case 'onJ':
			obj.src = pcJ2;
			break;

		case 'offE':
			obj.src = pcE1;
			break;

		case 'onE':
			obj.src = pcE2;
			break;

		case 'downJ':
			obj.src = pcJ3;
			break;

		case 'downE':
			obj.src = pcE3;
			break;

		default:
			break;
	}
}




//---------------------------------------------------
// 適用：「売上管理」ボタン
//---------------------------------------------------
function fncSCButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = scJ1;
			break;

		case 'onJ':
			obj.src = scJ2;
			break;

		case 'offE':
			obj.src = scE1;
			break;

		case 'onE':
			obj.src = scE2;
			break;

		case 'downJ':
			obj.src = scJ3;
			break;

		case 'downE':
			obj.src = scE3;
			break;

		default:
			break;
	}
}




//---------------------------------------------------
// 適用：「ワークフロー」ボタン
//---------------------------------------------------
function fncWFButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = wfJ1;
			break;

		case 'onJ':
			obj.src = wfJ2;
			break;

		case 'offE':
			obj.src = wfE1;
			break;

		case 'onE':
			obj.src = wfE2;
			break;

		case 'downJ':
			obj.src = wfJ3;
			break;

		case 'downE':
			obj.src = wfE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「請求管理」ボタン
//---------------------------------------------------
function fncINVButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = invJ1;
			break;

		case 'onJ':
			obj.src = invJ2;
			break;

		case 'offE':
			obj.src = invE1;
			break;

		case 'onE':
			obj.src = invE2;
			break;

		case 'downJ':
			obj.src = invJ3;
			break;

		case 'downE':
			obj.src = invE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「ユーザー管理」ボタン
//---------------------------------------------------
function fncUCButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = ucJ1;
			break;

		case 'onJ':
			obj.src = ucJ2;
			break;

		case 'offE':
			obj.src = ucE1;
			break;

		case 'onE':
			obj.src = ucE2;
			break;

		case 'downJ':
			obj.src = ucJ3;
			break;

		case 'downE':
			obj.src = ucE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「帳票出力」ボタン
//---------------------------------------------------
function fncListoutButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = blownlistoutJ1;
			break;

		case 'onJ':
			obj.src = blownlistoutJ2;
			break;

		case 'offE':
			obj.src = blownlistoutE1;
			break;

		case 'onE':
			obj.src = blownlistoutE2;
			break;

		default:
			break;
	}
}






//---------------------------------------------------
// 適用：「DATA OPEN」ボタン
//---------------------------------------------------
function fncDataOpenButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = dataopen1;
			break;

		case 'on':
			obj.src = dataopen2;
			break;

		default:
			break;
	}
}




////////// NAVI DATA EX BUTTON //////////

function DataExJOff(obj) {
	obj.src = dataexJ1;
}

function DataExJOn(obj) {
	obj.src = dataexJ2;
}

function DataExEOff(obj) {
	obj.src = dataexE1;
}

function DataExEOn(obj) {
	obj.src = dataexE2;
}



////////// NAVI LIST OUTPUT BUTTON //////////

function ListOutJOff(obj) {
	obj.src = listoutJ1;
}

function ListOutJOn(obj) {
	obj.src = listoutJ2;
}

function ListOutEOff(obj) {
	obj.src = listoutE1;
}

function ListOutEOn(obj) {
	obj.src = listoutE2;
}



//---------------------------------------------------
// 適用：「帳票出力」ボタン
//---------------------------------------------------
function fncLISTButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = listoutJ1;
			break;

		case 'onJ':
			obj.src = listoutJ2;
			break;

		case 'offE':
			obj.src = listoutE1;
			break;

		case 'onE':
			obj.src = listoutE2;
			break;

		default:
			break;
	}
}


//---------------------------------------------------
// 適用：「データエクスポート」ボタン
//---------------------------------------------------
function fncDATAEXButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = dataexJ1;
			break;

		case 'onJ':
			obj.src = dataexJ2;
			break;

		case 'offE':
			obj.src = dataexE1;
			break;

		case 'onE':
			obj.src = dataexE2;
			break;

		default:
			break;
	}
}


//---------------------------------------------------
// 適用：「マスタ管理」ボタン
//---------------------------------------------------
function fncMButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = mstJ1;
			break;

		case 'onJ':
			obj.src = mstJ2;
			break;

		case 'offE':
			obj.src = mstE1;
			break;

		case 'onE':
			obj.src = mstE2;
			break;

		case 'downJ':
			obj.src = mstJ3;
			break;

		case 'downE':
			obj.src = mstE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「システム管理」ボタン
//---------------------------------------------------
function fncSYSButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = sys1;
			break;

		case 'on':
			obj.src = sys2;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「締め日」ボタン
//---------------------------------------------------
function fncDataClosedButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = dataclosed1;
			break;

		case 'on':
			obj.src = dataclosed2;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// 適用：「締め処理ナビ」ボタン
//---------------------------------------------------
function fncClosedNaviButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = closednaviJ1;
			break;

		case 'onJ':
			obj.src = closednaviJ2;
			break;

		case 'offE':
			obj.src = closednaviE1;
			break;

		case 'onE':
			obj.src = closednaviE2;
			break;

		default:
			break;
	}
}




//---------------------------------------------------
// 適用：「帳票一覧」ボタン
//---------------------------------------------------
function fncListAllButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = listallJ1;
			break;

		case 'on':
			obj.src = listallJ2;
			break;

		default:
			break;
	}
}



//---------------------------------------------------
// 適用：「データ一覧」ボタン
//---------------------------------------------------
function fncDataAllButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = dataallJ1;
			break;

		case 'on':
			obj.src = dataallJ2;
			break;

		default:
			break;
	}
}

//---------------------------------------------------
//適用：「金型管理」ボタン
//---------------------------------------------------
function fncMMButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = mmJ1;
			break;

		case 'onJ':
			obj.src = mmJ2;
			break;

		case 'offE':
			obj.src = mmE1;
			break;

		case 'onE':
			obj.src = mmE2;
			break;

		case 'downJ':
			obj.src = mmJ3;
			break;

		case 'downE':
			obj.src = mmE3;
			break;

		default:
			break;
	}
}

//---------------------------------------------------
//適用：「金型帳票管理」ボタン
//---------------------------------------------------
function fncMRButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = mrJ1;
			break;

		case 'onJ':
			obj.src = mrJ2;
			break;

		case 'offE':
			obj.src = mrE1;
			break;

		case 'onE':
			obj.src = mrE2;
			break;

		case 'downJ':
			obj.src = mrJ3;
			break;

		case 'downE':
			obj.src = mrE3;
			break;

		default:
			break;
	}
}

//---------------------------------------------------
//適用：「L/C管理」ボタン
//---------------------------------------------------
function fncLCButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = lcJ1;
			break;

		case 'onJ':
			obj.src = lcJ2;
			break;

		case 'offE':
			obj.src = lcE1;
			break;

		case 'onE':
			obj.src = lcE2;
			break;

		case 'downJ':
			obj.src = lcJ3;
			break;

		case 'downE':
			obj.src = lcE3;
			break;

		default:
			break;
	}
}

////////// MAIN MENU NAVI BUTTON //////////

function MainJOff(obj) {
	obj.src = mainJ1;
}

function MainJOn(obj) {
	obj.src = mainJ2;
}

function MainEOff(obj) {
	obj.src = mainE1;
}

function MainEOn(obj) {
	obj.src = mainE2;
}



////////// LOGOUT BUTTON //////////

function LogoutOff(obj) {
	obj.src = logout1;
}

function LogoutOn(obj) {
	obj.src = logout2;
}



////////// ENGLISH JAPANESE BUTTON //////////

function EtoJ1EOff(obj) {
	obj.src = etojE1;
}

function EtoJ1EOn(obj) {
	obj.src = etojE2;
}

function EtoJ1JOff(obj) {
	obj.src = etojJ1;
}

function EtoJ1JOn(obj) {
	obj.src = etojJ2;
}



////////// TAB BUTTON //////////

function TabAOff(obj) {
	obj.src = tabA1;
}

function TabAOn(obj) {
	obj.src = tabA2;
}

function TabBOff(obj) {
	obj.src = tabB1;
}

function TabBOn(obj) {
	obj.src = tabB2;
}



////////// [PRODUCTS] TAB BUTTON //////////

function PTabAOff(obj) {
	obj.src = ptabA1;
}

function PTabAOn(obj) {
	obj.src = ptabA2;
}

function PTabBOff(obj) {
	obj.src = ptabB1;
}

function PTabBOn(obj) {
	obj.src = ptabB2;
}



////////// PACKING UNIT BUTTON //////////

function PunitOff(obj) {
	obj.src = punit1;
}

function PunitOn(obj) {
	obj.src = punit2;
}




function fncDarkGrayOpenButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = darkgrayopen1;
			break;

		case 'on':
			obj.src = darkgrayopen2;
			break;

		case 'down':
			obj.src = darkgrayopen3;
			break;

		default:
			break;
	}
}

////////// DATE BUTTON //////////

function DateOff(obj) {
	obj.src = datebt1;
}

function DateOn(obj) {
	obj.src = datebt2;
}






////////// GOODS PLAN BUTTON //////////
function GPOff(obj) {
	obj.src = showwin1;
}

function GPOn(obj) {
	obj.src = showwin2;
}





////////// DETAIL BUTTON //////////
function DetailOff(obj) {
	obj.src = detail1;
}

function DetailOn(obj) {
	obj.src = detail2;
}

////////// RENEW BUTTON //////////
function RenewOff(obj) {
	obj.src = renew1;
}

function RenewOn(obj) {
	obj.src = renew2;
}


////////// REMOVE BUTTON //////////
function RemoveOff(obj) {
	obj.src = remove1;
}

function RemoveOn(obj) {
	obj.src = remove2;
}

////////// COPY BUTTON //////////
function CopyOff(obj) {
	obj.src = copy1;
}

function CopyOn(obj) {
	obj.src = copy2;
}

////////// CLOSE BUTTON //////////
function CloseJOff(obj) {
	obj.src = close1J;
}

function CloseJOn(obj) {
	obj.src = close2J;
}

function CloseEOff(obj) {
	obj.src = close1E;
}

function CloseEOn(obj) {
	obj.src = close2E;
}


////////// BLOWN CLOSE BUTTON //////////
function BlownCloseJOff(obj) {
	obj.src = blownclose1J;
}

function BlownCloseJOn(obj) {
	obj.src = blownclose2J;
}

function BlownCloseEOff(obj) {
	obj.src = blownclose1E;
}

function BlownCloseEOn(obj) {
	obj.src = blownclose2E;
}


////////// DARK CLOSE BUTTON //////////
function DarkCloseJOff(obj) {
	obj.src = darkclose1J;
}

function DarkCloseJOn(obj) {
	obj.src = darkclose2J;
}

function DarkCloseEOff(obj) {
	obj.src = darkclose1E;
}

function DarkCloseEOn(obj) {
	obj.src = darkclose2E;
}

function DarkCloseEOff_Gold(obj) {
	obj.src = darkclose1E_Gold;
}

function DarkCloseEOn_Gold(obj) {
	obj.src = darkclose2E_Gold;
}



////////// DELETE BUTTON //////////
function DeleteJOff(obj) {
	obj.src = delete1J;
}

function DeleteJOn(obj) {
	obj.src = delete2J;
}

function DeleteEOff(obj) {
	obj.src = delete1E;
}

function DeleteEOn(obj) {
	obj.src = delete2E;
}







////////// DISPOSAL BUTTON //////////
function DispoOff(obj) {
	obj.src = dispo1;
}

function DispoOn(obj) {
	obj.src = dispo2;
}







////////// PREV-NEXT BUTTON //////////
function PrevOff(obj) {
	obj.src = prev1;
}

function PrevOn(obj) {
	obj.src = prev2;
}

function NextOff(obj) {
	obj.src = next1;
}

function NextOn(obj) {
	obj.src = next2;
}










//---------------------------------------------------
// 適用：「INVALID BIG」ボタン
//---------------------------------------------------
function fncInvalidBigButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = invalidbigbtJ1;
			break;

		case 'onJ':
			obj.src = invalidbigbtJ2;
			break;

		case 'offE':
			obj.src = invalidbigbtE1;
			break;

		case 'onE':
			obj.src = invalidbigbtE2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「INVALID SMALL」ボタン
//---------------------------------------------------
function fncInvalidSmallButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = '/img/type01/cmn/querybt/invalid_small_off_bt.gif'; /* invalidsmallbt1 */
			break;

		case 'on':
			obj.src = '/img/type01/cmn/querybt/invalid_small_off_on_bt.gif'; /* invalidsmallbt2 */
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「特殊文字」ボタン
//---------------------------------------------------
function fncSpecialButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = specialbt1;
			break;

		case 'on':
			obj.src = specialbt2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// 適用：「DARK LOGOUT」ボタン
//---------------------------------------------------
function fncDarkLogoutButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = darklogout1;
			break;

		case 'on':
			obj.src = darklogout2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「DARK BACK」ボタン
//---------------------------------------------------
function fncDarkBackButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = darkback1;
			break;

		case 'on':
			obj.src = darkback2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「→」ボタン
//---------------------------------------------------
function fncDarkRAllowButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = ralowbt1;
			break;

		case 'on':
			obj.src = ralowbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「SBMIT」ボタン
//---------------------------------------------------
function fncSubmitButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = submit1;
			break;

		case 'on':
			obj.src = submit2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// 適用：「INSERT」ボタン
//---------------------------------------------------
function fncInsertButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = insertbtJ1;
			break;

		case 'onJ':
			obj.src = insertbtJ2;
			break;

		case 'offE':
			obj.src = insertbtE1;
			break;

		case 'onE':
			obj.src = insertbtE2;
			break;

		default:
			break;
	}

	return false;
}





//---------------------------------------------------
// 適用：「COPY PREVIEW」ボタン
//---------------------------------------------------
function fncCopyPreviewButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = copybigbt1;
			break;

		case 'on':
			obj.src = copybigbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「PREVIEW」ボタン
//---------------------------------------------------
function fncPreviewButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = previewbt1;
			break;

		case 'on':
			obj.src = previewbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「BLOWN PREVIEW」ボタン
//---------------------------------------------------
function fncBlownPreviewButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = blownpreviewbt1;
			break;

		case 'on':
			obj.src = blownpreviewbt2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// 適用：「BLOWN REGIST」ボタン
//---------------------------------------------------
function fncBlownRegistButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = blownregistbtJ1;
			break;

		case 'onJ':
			obj.src = blownregistbtJ2;
			break;

		case 'offE':
			obj.src = blownregistbtE1;
			break;

		case 'onE':
			obj.src = blownregistbtE2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// 適用：「LOG」ボタン
//---------------------------------------------------
function fncLogButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = logbt1;
			break;

		case 'on':
			obj.src = logbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「BACK SMALL」ボタン
//---------------------------------------------------
function fncBackSmallButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = backsmallbt1;
			break;

		case 'on':
			obj.src = backsmallbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「RELOAD SMALL」ボタン
//---------------------------------------------------
function fncReloadSmallButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = reloadsmallbt1;
			break;

		case 'on':
			obj.src = reloadsmallbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「NEXT SMALL」ボタン
//---------------------------------------------------
function fncNextSmallButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = blownnextbt1;
			break;

		case 'on':
			obj.src = blownnextbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「PREV SMALL」ボタン
//---------------------------------------------------
function fncPrevSmallButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = blownprevbt1;
			break;

		case 'on':
			obj.src = blownprevbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「BLOWN EXPORT」ボタン
//---------------------------------------------------
function fncBlownExportButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = blownexportbt1;
			break;

		case 'on':
			obj.src = blownexportbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「GRAY PREVIEW」ボタン
//---------------------------------------------------
function fncGrayPreviewButton(strMode, obj) {
	switch (strMode) {
		case 'off':
			obj.src = graypreviewbt1;
			break;

		case 'on':
			obj.src = graypreviewbt2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// 適用：「RECOGNIZE」ボタン
//---------------------------------------------------
function fncRecognizeButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = recognwfJ1;
			break;

		case 'onJ':
			obj.src = recognwfJ2;
			break;

		case 'offE':
			obj.src = recognwfE1;
			break;

		case 'onE':
			obj.src = recognwfE2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「DENY」ボタン
//---------------------------------------------------
function fncDenyButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = denywfJ1;
			break;

		case 'onJ':
			obj.src = denywfJ2;
			break;

		case 'offE':
			obj.src = denywfE1;
			break;

		case 'onE':
			obj.src = denywfE2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「CANCEL」ボタン
//---------------------------------------------------
function fncCancelButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = cancelwfJ1;
			break;

		case 'onJ':
			obj.src = cancelwfJ2;
			break;

		case 'offE':
			obj.src = cancelwfE1;
			break;

		case 'onE':
			obj.src = cancelwfE2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// 適用：「PROCESS」ボタン
//---------------------------------------------------
function fncProcessButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = processwfJ1;
			break;

		case 'onJ':
			obj.src = processwfJ2;
			break;

		case 'offE':
			obj.src = processwfE1;
			break;

		case 'onE':
			obj.src = processwfE2;
			break;

		default:
			break;
	}

	return false;
}







//---------------------------------------------------
// 適用：「MESSAGE」ボタン
//---------------------------------------------------
function fncMessageButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = messageJ1;
			break;

		case 'onJ':
			obj.src = messageJ2;
			break;

		case 'offE':
			obj.src = messageE1;
			break;

		case 'onE':
			obj.src = messageE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// 適用：「SERVER」ボタン
//---------------------------------------------------
function fncServerButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = serverJ1;
			break;

		case 'onJ':
			obj.src = serverJ2;
			break;

		case 'offE':
			obj.src = serverE1;
			break;

		case 'onE':
			obj.src = serverE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// 適用：「EMAIL」ボタン
//---------------------------------------------------
function fncEmailButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = emailJ1;
			break;

		case 'onJ':
			obj.src = emailJ2;
			break;

		case 'offE':
			obj.src = emailE1;
			break;

		case 'onE':
			obj.src = emailE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// 適用：「SESSION」ボタン
//---------------------------------------------------
function fncSessionButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = sessionJ1;
			break;

		case 'onJ':
			obj.src = sessionJ2;
			break;

		case 'offE':
			obj.src = sessionE1;
			break;

		case 'onE':
			obj.src = sessionE2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// 適用：「APACHE RESTART」ボタン
//---------------------------------------------------
function fncRestartButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = restartbtJ1;
			break;

		case 'onJ':
			obj.src = restartbtJ2;
			break;

		case 'offE':
			obj.src = restartbtE1;
			break;

		case 'onE':
			obj.src = restartbtE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// 適用：「APACHE STOP」ボタン
//---------------------------------------------------
function fncStopButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = stopbtJ1;
			break;

		case 'onJ':
			obj.src = stopbtJ2;
			break;

		case 'offE':
			obj.src = stopbtE1;
			break;

		case 'onE':
			obj.src = stopbtE2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「BLOWN PROCESS」ボタン
//---------------------------------------------------
function fncBlownProcessButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = blownprocessbtJ1;
			break;

		case 'onJ':
			obj.src = blownprocessbtJ2;
			break;

		case 'offE':
			obj.src = blownprocessbtE1;
			break;

		case 'onE':
			obj.src = blownprocessbtE2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// 適用：「BLOWN REVIVAL」ボタン
//---------------------------------------------------
function fncBlownRevivalButton(strMode, obj) {
	switch (strMode) {
		case 'offJ':
			obj.src = blownrevivalbtJ1;
			break;

		case 'onJ':
			obj.src = blownrevivalbtJ2;
			break;

		case 'offE':
			obj.src = blownrevivalbtE1;
			break;

		case 'onE':
			obj.src = blownrevivalbtE2;
			break;

		default:
			break;
	}

	return false;
}
