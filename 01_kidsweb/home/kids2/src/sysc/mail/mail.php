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
// システム管理メニュー画面
// index.php -> strSessionID -> index.php
//
// システムメール設定完了画面へ
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
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
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


// 現在の管理者メールアドレス取得
$strQuery = "SELECT strValue AS adminmailaddress FROM m_AdminFunction WHERE strClass = 'adminmailaddress'";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
}
else
{
	$objResult->adminmailaddress = "";
}

$aryData["strAdminMailAddress"] = $objResult->adminmailaddress;

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/mail/mail.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
<!--
<form action="action.php" method="POST">
<input type="hidden" name="strSessionID" value="<? echo $aryData["strSessionID"]; ?>">
ADMIN MAIL ADDRESS:<input type="text" name="strAdminMailAddress" value="<? echo $objResult->adminmailaddress; ?>">
<input type="submit">
</form>
-->
<?

return TRUE;
?>
