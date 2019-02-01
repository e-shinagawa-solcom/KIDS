<?php
/** 
*	ログイン表示　ログイン処理
*
*	ログイン画面の表示処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	ログイン画面からのユーザー情報よりログイン処理を行う
*
*	更新履歴
*	2004.02.26	リファラーを判断しTOPページ以外からのアドレスを直指定を禁止する
*
*/

// ログイン処理

// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータの取得
$aryData = $_POST;

if ( isset($aryData["strUserID"]) && isset($aryData["strPassword"]) )
{
	// 文字列チェック
	$aryCheck["strUserID"]      = "null:ascii(0,20)";
	$aryCheck["strPassword"] 	= "null:ascii(0,20)";

	// 変数名となるキーを取得
	$aryKey = array_keys( $aryCheck );
	$flag = TRUE;
	// キーの数だけチェック
	foreach ( $aryKey as $strKey )
	{
		// $aryData[$strKey]  : チェック対象データ
		// $aryCheck[$strKey] : チェック内容(数値、英数字、アスキー等)
		$strResult = fncCheckString( $aryData[$strKey], $aryCheck[$strKey] );
		if ( $strResult ) {
			list ( $lngErrorNo, $strErrorMessage ) = split ( ":", $strResult );
//			fncOutputError ( $lngErrorNo, DEF_ERROR, $strErrorMessage, FALSE, "", $objDB );
			$flag = FALSE;
		}
	}

	if ( $flag == FALSE )
	{
		fncOutputError ( 9052, DEF_ERROR, "ログインに失敗しました。", TRUE, "", $objDB );
		exit;
	}

	// セッション確認
	if ( !$objAuth->isLogin( $aryData["strSessionID"], $objDB ) )
	{
		// 認証処理
		if ( !$objAuth->login( $aryData["strUserID"], $aryData["strPassword"], $objDB ) )
		{
			fncOutputError( 9052, DEF_ERROR, "ログインに失敗しました。", TRUE, "", $objDB );
			exit;
		}
	}

	// HTML出力（システムメニュー画面）
	header ( "Location:  ../menu/menu.php?strSessionID=" . $objAuth->SessionID );

}
else
{
// 2004.02.26 suzukaze update start
	if ( $_GET["value"] == "kids" )
	{
		// HTML出力（ログイン画面）
		require ( TMP_ROOT . 'login/index.html' );
	}
	else
	{
		// HTML出力（TOP）
		require ( SRC_ROOT . "index.html" );
	}
// 2004.02.26 suzukaze update end
}


$objDB->close();


return TRUE;
?>
