<?
/** 
*	ユーザー管理 検索画面
*
*	@package   kuwagata
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
require (SRC_ROOT . "wf/cmn/lib_wf.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_GET;

// 文字列チェック
$aryCheck["strSessionID"]    = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", FALSE, "", $objDB );
}


// ヘルプリンク用機能コードをセット
$aryData["lngFunctionCode"] = DEF_FUNCTION_UC3;

// HTML出力
// $aryData["Pwin"] = "search.php?strSessionID=" . $aryData["strSessionID"];
//$aryData["Pwin"] = "../search_ifrm/index.html";
// echo fncGetReplacedHtml( "uc/search/parts.html", $aryData, $objAuth );
// lngCompanyCode SELECTタグ生成
$aryData["lngCompanyCode"] = fncGetPulldown( "m_Company", "lngcompanyCode", "strCompanyDisplayCode || ' ' || strCompanyDisplayName", "", "WHERE bytCompanyDisplayFlag = TRUE", $objDB );

// lngAuthorityGroupCode SELECTタグ生成
$aryData["lngAuthorityGroupCode"] = fncGetPulldown( "m_AuthorityGroup", "lngAuthorityGroupCode", "strAuthorityGroupName", "", "", $objDB );

// lngAccessIPAddressCode SELECTタグ生成
$aryData["lngAccessIPAddressCode"] = fncGetPulldown( "m_AccessIPAddress", "lngAccessIPAddressCode", "strAccessIPAddress || ' ' || strNote", "", "", $objDB );


$objDB->close();
echo fncGetReplacedHtmlWithBase("search/base_search.html", "uc/search/search.html", $aryData, $objAuth);

return TRUE;
?>
