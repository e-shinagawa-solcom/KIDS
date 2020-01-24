<?
/** 
*	システム管理 システム起動設定画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// システム管理メニュー画面
// index.php -> strSessionID -> index.php
//
// システム起動設定完了画面へ
// index.php -> strSessionID  -> action.php
// index.php -> lngActionCode -> action.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "システム管理失敗", TRUE, "", $objDB );
}


$aryData["DEF_ACTION_RESTART"] = DEF_ACTION_RESTART;
$aryData["DEF_ACTION_STOP"]    = DEF_ACTION_STOP;

// HTML出力
/*
echo fncGetReplacedHtml( "sysc/sev/parts.tmpl", $aryData, $objAuth );
*/
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/sev/sev.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
<!--
<input type="button" value="RESTART" onClick="javascript:window.location='action.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngActionCode=<? echo DEF_ACTION_RESTART; ?>';">
<input type="button" value="STOP" onClick="javascript:window.location='action.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngActionCode=<? echo DEF_ACTION_STOP; ?>';">
-->
<?

return TRUE;
?>
