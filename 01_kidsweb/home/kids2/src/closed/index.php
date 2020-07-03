<?php
/** 
*	締め処理　選択表示画面
*
*	締め処理の選択表示処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	締め処理を行う期間、処理内容を選択させる画面の表示処理
*
*/

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
// require (SRC_ROOT . "closed/cmn/lib_cld.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 1400 締め処理
if ( !fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// HTML出力
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// ヘルプ対応
// $aryData["lngFunctionCode"] = DEF_FUNCTION_CLD0;

echo fncGetReplacedHtml( "closed/parts.tmpl", $aryData, $objAuth );

$objDB->close();

return true;

?>