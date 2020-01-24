<?
/** 
*	ユーザー管理 検索条件入力画面
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
// index.php -> lngFunctionCode -> index.php
//

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "wf/cmn/lib_wf.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData   = $_GET;

$aryParts = fncStringToArray ( $_COOKIE["UserSearch"], "&", ":" );

//$aryParts = array_merge ( $_GET, $_COOKIE );

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", FALSE, "", $objDB );
}


// HIDDENタグ生成
$aryParts["strHiddenForm"] = "
<input type=\"hidden\" name=\"strSessionID\" value=\"" . $aryData["strSessionID"] . "\">
<input type=\"hidden\" name=\"lngFunctionCode\" value=\"" . DEF_FUNCTION_UC3 . "\">
";

if ( $bytCancellFlag )
{
	$aryParts["workflowStatusCodeMenu"] .= "<option value=\"" . DEF_STATUS_CANCELL . "\">申請取消</option>\n";
}

// lngCompanyCode SELECTタグ生成
$aryParts["lngCompanyCode"] = fncGetPulldown( "m_Company", "lngcompanyCode", "strCompanyDisplayCode || ' ' || strCompanyDisplayName", "", "WHERE bytCompanyDisplayFlag = TRUE", $objDB );

// lngAuthorityGroupCode SELECTタグ生成
$aryParts["lngAuthorityGroupCode"] = fncGetPulldown( "m_AuthorityGroup", "lngAuthorityGroupCode", "strAuthorityGroupName", "", "", $objDB );

// lngAccessIPAddressCode SELECTタグ生成
$aryParts["lngAccessIPAddressCode"] = fncGetPulldown( "m_AccessIPAddress", "lngAccessIPAddressCode", "strAccessIPAddress || ' ' || strNote", "", "", $objDB );

$aryParts["strSessionID"]    = &$aryData["strSessionID"];
$aryParts["lngFunctionCode"] = DEF_FUNCTION_UC3;

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "uc/search/search.html" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
