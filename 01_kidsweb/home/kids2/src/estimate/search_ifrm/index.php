<?php
/** 
*	見積原価管理 検索条件入力画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
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
//require (SRC_ROOT . "wf/cmn/lib_wf.php");

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
if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", FALSE, "", $objDB );
}


$aryParts["strSessionID"] = &$aryData["strSessionID"];




// クッキーの設定
if( $_COOKIE["EstimateSearch"] )
{
	$aryCookie = fncStringToArray ( $_COOKIE["EstimateSearch"], "&", ":" );
	while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
	{
		$aryParts[$strKeys] = $strValues;
	}
}


// ワークフロー状態
$aryParts["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_estimatestatus", "lngestimatestatuscode", "strestimatestatusname", "lngWorkFlowStatusCode[]", '', $objDB );



// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/search_ifrm/parts.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryParts );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();

return true;

?>
