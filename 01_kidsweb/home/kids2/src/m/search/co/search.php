<?
/** 
*	マスタ管理 会社マスタ 検索画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> search.php
//

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;


// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// HIDDEN 生成
$aryParts["strSessionID"] =& $aryData["strSessionID"];

// 会社属性プルダウンメニュー 生成
$aryParts["lngAttributeCode"]  = "<option value=\"\"></option>\n";
$aryParts["lngAttributeCode"] .= fncGetPulldown( "m_Attribute", "lngAttributeCode", "strAttributeName", "", "", $objDB );


$objDB->close();

// HTML出力

$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/m/search/co/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
