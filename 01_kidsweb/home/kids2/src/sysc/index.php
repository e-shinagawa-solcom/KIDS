<?
/** 
*	システム管理 メニュー画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// メイン画面
// index.php -> strSessionID -> index.php
//
// 各機能画面へ
// index.php -> strSessionID -> index.php


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
if ( $_GET )
{
	$aryData = $_GET;
}
else
{
	$aryData = $_POST;
}


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

$aryData["strInfVisibility"]     = "visible";
$aryData["strMailVisibility"]    = "visible";
$aryData["strSessionVisibility"] = "visible";
$aryData["strSevVisibility"]     = "visible";
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
{
	$aryData["strInfVisibility"] = "hidden";
}
if ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )
{
	$aryData["strMailVisibility"] = "hidden";
}
if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
{
	$aryData["strSessionVisibility"] = "hidden";
}
if ( !fncCheckAuthority( DEF_FUNCTION_SYS4, $objAuth ) )
{
	$aryData["strSevVisibility"] = "hidden";
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "システム管理失敗", TRUE, "", $objDB );
}


// HTML出力
echo fncGetReplacedHtml( "sysc/parts.tmpl", $aryData, $objAuth );

?>
<!--
<input type="button" value="MESSAGE" onClick="javascript:window.location='/sysc/inf/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
<input type="button" value="SERVER" onClick="javascript:window.location='/sysc/sev/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
<input type="button" value="MAIL" onClick="javascript:window.location='/sysc/mail/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
<input type="button" value="SESSION" onClick="javascript:window.location='/sysc/session/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';">
-->
<?

return TRUE;
?>
