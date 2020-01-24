<?
/** 
*	ワークフロー 案件一覧機能画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

if ( $_GET["strSessionID"] )
{
	$aryData["strSessionID"]    = $_GET["strSessionID"];
}
else
{
	$aryData["strSessionID"]    = $_POST["strSessionID"];
}

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_WF0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
if ( fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) )
{
	$aryData["strListURL"]   = "list/index.php?strSessionID=" . $aryData["strSessionID"];
}
if ( fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
{
	$aryData["strSearchURL"] = "search/index.php?strSessionID=" . $aryData["strSessionID"];
}

$objDB->close();


// ヘルプリンク用機能コードをセット
$aryData["lngFunctionCode"] = DEF_FUNCTION_WF0;

// HTML出力
echo fncGetReplacedHtml( "wf/parts.tmpl", $aryData, $objAuth );
echo $_COOKIE["lngLanguageCode"];
?>
