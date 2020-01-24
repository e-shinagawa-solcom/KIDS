<?
/** 
*	システム管理 管理者設定画面
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
// システム管理者設定完了画面へ
// index.php -> strSessionID              -> action.php
// index.php -> strSystemInformationTitle -> action.php
// index.php -> strSystemInformationBody  -> action.php
//
// システム管理者ログ閲覧画面へ
// index.php -> strSessionID              -> log.php
//
// システム管理者ログインセッション閲覧画面へ
// index.php -> strSessionID              -> session.php


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
if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
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


// 現在のお知らせ記事取得
$strQuery = "SELECT strSystemInformationTitle, strSystemInformationBody FROM m_SystemInformation ORDER BY dtmInsertDate DESC LIMIT 1";
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryData["strSystemInformationTitle"] = $objResult->strsysteminformationtitle;
	$aryData["strSystemInformationBody"]  = preg_replace ( "/<br>/i", "\n", $objResult->strsysteminformationbody );
}


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sysc/inf/inf.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
<?

return TRUE;
?>
