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
// システム起動設定完了画面
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
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
$aryCheck["lngActionCode"] = "null:number(" . DEF_ACTION_RESTART . "," . DEF_ACTION_STOP . ")";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "システム管理失敗", TRUE, "", $objDB );
}


// 再起動
if ( $aryData["lngActionCode"] == DEF_ACTION_RESTART )
{
	$strShell = DEF_PATH_RESTART;
	$strMessage = "再起動中";
}

// ストップ
elseif ( $aryData["lngActionCode"] == DEF_ACTION_STOP )
{
	$strShell = DEF_PATH_STOP;
	$strMessage = "停止中";
}


// HTML出力
/*
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
*/

echo $strMessage;
echo "<a href=/login.php>BACK</a>";


// シェル実行
if ( !$strShell || !$strResult = exec ( $strShell ) )
{
	fncOutputError ( 9052, DEF_WARNING, "シェルの実行に失敗しました。", TRUE, "", $objDB );
}
return TRUE;
?>
