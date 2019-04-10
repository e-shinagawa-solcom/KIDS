<?
/** 
*	データエクスポート メニュー画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// メニュー表示
// index.php -> strSessionID -> index.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "dataex/cmn/lib_dataex.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]   = "null:numenglish(32,32)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//echo getArrayTable( $aryCheckResult, "TABLE" );
//echo getArrayTable( $aryData, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );


// lngExportData の設定
if ( fncCheckAuthority( DEF_FUNCTION_DE1, $objAuth ) )
{
	//echo "OK!";
	$aryData["strLCURL"]            = DEF_EXPORT_LC;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE2, $objAuth ) )
{
	$aryData["strSaleURL"]          = DEF_EXPORT_SALES;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE3, $objAuth ) )
{
	$aryData["strStockURL"]         = DEF_EXPORT_STOCK;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE4, $objAuth ) )
{
	$aryData["strPurchaseOrderURL"] = DEF_EXPORT_PURCHASE;
}

if ( fncCheckAuthority( DEF_FUNCTION_DE5, $objAuth ) )
{
	$aryData["strEstimateURL"] = DEF_EXPORT_ESTIMATE;
}

if ( fncCheckAuthority( DEF_FUNCTION_DE6, $objAuth ) )
{
	$aryData["strStat01URL"] = DEF_EXPORT_STAT01;
}
if ( fncCheckAuthority( DEF_FUNCTION_DE7, $objAuth ) )
{
	$aryData["strStat02URL"] = DEF_EXPORT_STAT02;
}



//echo fncCheckAuthority( DEF_FUNCTION_DE1, $objAuth );
//echo $objAuth->FunctionCode[1002];
//echo getArrayTable( $objAuth->FunctionCode, "TABLE" );


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "dataex/select.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
