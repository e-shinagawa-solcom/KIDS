<?
/** 
*	帳票出力 帳票選択画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 帳票選択画面
// index.php -> strSessionID    -> index.php

// 検索画面へ( * は指定帳票のファイル名 )
// index.php -> strSessionID    -> *.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryParts["strGoodsPlanURL"]     = "#";
$aryParts["strPurchaseOrderURL"] = "#";

if ( fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) )
{
	// 商品化企画書帳票出力可能
	$aryParts["strGoodsPlanURL"] = "/list/search/p/search.php?strSessionID=" . $aryData["strSessionID"];
}
if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
{
	// 発注書（P.O）帳票出力可能
	$aryParts["strPurchaseOrderURL"] = "/list/search/po/search.php?strSessionID=" . $aryData["strSessionID"];
}
if ( fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	// 見積原価計算帳票出力可能
	$aryParts["strEstimateURL"] = "/list/search/estimate/search.php?strSessionID=" . $aryData["strSessionID"];
}



$objDB->close();


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/list/list/select.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
