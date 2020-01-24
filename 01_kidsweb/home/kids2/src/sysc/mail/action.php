<?
/** 
*	システム管理 メール設定画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// システムメール設定完了画面
// index.php -> strSessionID        -> action.php
// index.php -> strAdminMailAddress -> action.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
//require (SRC_ROOT . "sysc/cmn/lib_sys.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_POST;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["strAdminMailAddress"] = "null:email(1,100)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );


// メール文字列エラーチェック
if ( $aryCheckResult["strAdminMailAddress_Error"] )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<form action=mail.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
	exit;
}


// トランザクション開始
$objDB->transactionBegin();

// 更新処理実行
$strQuery = "UPDATE m_AdminFunction SET strValue = '" . $aryData["strAdminMailAddress"] . "' WHERE strClass = 'adminmailaddress'";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// 変更案内メール送信
list ( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_SYS3, $aryData, $objDB );
if ( !$aryData["strAdminMailAddress"] || !mail ( $aryData["strAdminMailAddress"], $strSubject, $strBody, "From: " . $aryData["strAdminMailAddress"] . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" ) )
{
	// 送信失敗によるロールバック
	list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );

	fncOutputError ( 9053, DEF_WARNING, "メール送信失敗。", TRUE, "", $objDB );
}

// トランザクションコミット
$objDB->transactionCommit();

//echo "<a href=../>BACK</a>";

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/mail/finish.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
