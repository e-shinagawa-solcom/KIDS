<?php
/** 
*	ログアウト　処理
*
*	ログアウト画面の表示、ログアウト処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	メインメニューよりログアウトボタンが押された際に実行
*	実行OKであればログアウト処理を行う
*
*/

// ログアウト処理

// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

// ログアウト実行処理なのか、ログアウト確認処理なのか判断
if ( !$aryData["bytLogoutFlag"] )
{
	// ログアウト確認処理の場合
	if ( isset( $aryData["strSessionID"] ) )
	{
		// 文字列チェック
		$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
		$aryResult = fncAllCheck( $aryData, $aryCheck );
		fncPutStringCheckError( $aryResult, $objDB );

		// セッション確認
		$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

		// LanguageCode取得
		$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

		// HTML出力
		$fp = fopen ( TMP_ROOT . "login/logout.html", "r" );

		while ( $strTemplLine = fgets ( $fp, 1000 ) )
		{
			$strTempl .= $strTemplLine;
		}

		$strTempl = preg_replace ( "/_%strSessionID%_/i", $aryData["strSessionID"], $strTempl );
		$strTempl = preg_replace ( "/_%lngLanguageCode%_/i", $aryData["lngLanguageCode"], $strTempl );
		$strTempl = preg_replace ( "/_%bytLogoutFlag%_/i", TRUE, $strTempl );

		// 置換されなかった置き換え文字列を削除
		$strTempl = preg_replace ( "/_%.+?%_/", "", $strTempl );

		echo $strTempl;
	}
	else
	{
		fncOutputError ( 9052, DEF_ERROR, "セッションが異常です。", TRUE, "", $objDB );
	}
}
else
{
	// ログアウト実行処理の場合
	if ( isset( $aryData["strSessionID"] ) )
	{
		// 文字列チェック
		$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
		$aryResult = fncAllCheck( $aryData, $aryCheck );
		fncPutStringCheckError( $aryResult, $objDB );

		// セッション確認
		$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

		// ログアウト実行処理
		fncLogout( $aryData["strSessionID"], $objDB );

		// HTML出力（ログイン画面）
		require ( TMP_ROOT . 'login/index.html' );
	}
	else
	{
		fncOutputError ( 9052, DEF_ERROR, "セッションが異常です。", TRUE, "", $objDB );
	}
}

$objDB->close();


return TRUE;
?>
