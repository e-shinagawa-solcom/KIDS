

//@-------------------------------------------------------------------------------------------------------------------
/**
* ファイル概要 : ログイン時の各処理用関数群
*
*
*
* 備考 : ログイン時に使用する関数を定義している。
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/ 
//--------------------------------------------------------------------------------------------------------------------




















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ブラウザ・バージョン・OSのチェック関数
*
* 解説 : トップページ読み込み時にブラウザ・バージョン・OSのチェックを行う。
*        Windows Internet Explorer 6 の環境以外はエラーページへ移動させる。
*
* 対象 : K.I.D.S.システムトップページ
*
* 外部関数 : [fncCheckDisplaySizeModule] ディスプレイサイズのチェックモジュール
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncNavigatorCheck()
{
	// デバッグ
//	alert( '[platform] ' +  navigator.platform + '\n[appName] ' + navigator.appName + '\n[appVersion] ' + navigator.appVersion );

	// Windows Internet Explorer 6 以外の環境の場合
	if ( !((navigator.platform.indexOf('Win') > -1) &&
			(navigator.appName.indexOf('Microsoft') > -1) &&
			((navigator.appVersion.indexOf('MSIE 7') > -1)||
			(navigator.appVersion.indexOf('MSIE 8') > -1)||
			(navigator.appVersion.indexOf('MSIE 9') > -1)||
			(navigator.appVersion.indexOf('MSIE 6') > -1))) )
	{
		// エラーページへ移動
		location.href = '/error/env.html';
	}
	else
	{
		// ウィンドウサイズのチェック
		fncCheckDisplaySizeModule();
	}

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ディスプレイサイズのチェックモジュール
*
* 解説 : トップページ読み込み時にディスプレイサイズのチェックを行う。
*        規定値以下の場合はメッセージを表示させる。
*
* 対象 : [fncNavigatorCheck] ブラウザ・バージョン・OSのチェック関数
*
* @param [lngW] : 規定値の横サイズ
* @param [lngH] : 規定値の縦サイズ
*/
//--------------------------------------------------------------------------------------------------------------------
function fncCheckDisplaySizeModule()
{
	var lngW = 1024; // 規定値横サイズ
	var lngH = 768;  // 規定値縦サイズ

	var lngWdisp = screen.width;  // ウィンドウ横サイズ取得
	var lngHdisp = screen.height; // ウィンドウ縦サイズ取得

	if ( ( lngWdisp < lngW ) || ( lngHdisp < lngH ) )
	{
		EnterButton.style.display = 'none';  // [ENTER]ボタンの非表示
		MessageDisp.style.display = 'block'; // エラーメッセージの表示
	}

	return false;
}











/*
function LoginCheck() {

	var strUid = document.all.strUserID.value;
	var strPasswd = document.all.strPassword.value;

	location.href = "/login/login.php?strUserID=" + strUid + "&strPassword=" + strPasswd;

	return false;

}
*/

function LoginCheck() {

	document.frmLogin.action = "/login/login.php";
	document.frmLogin.submit();

	return false;
}





////////////////////////// GO MENU //////////////////////////
/*
function GoMENU() {

	var menuWin = "Menu";

	if(window.name != menuWin)
	{ 
		window.opener = window.open( '/navi/index.html', 'menuWin','fullscreen=yes');

		window.close(); 
	}

	return false;

}
*/

/*
function GoMENU() {

	var menuWin = "Menu";

	if(window.name != menuWin)
	{ 
		window.opener = window.open( '/navi/index.html', 'menuWin','width=1012,height=689,status=yes,scrollbars=yes,directories=no,menubar=no,resizable=yes,location=no,toolbar=no,left=0,top=0');

		window.close(); 
	}

	return false;

}
*/


function GoMENU()
{

	location.href = '/menu/index.html';

	return false;

}








////////////////////////// PASSWORD REMINDER //////////////////////////
function RemindSet() {

	document.frmLogin.action = "/remind/index.html";
	document.frmLogin.submit();

	return false;
}