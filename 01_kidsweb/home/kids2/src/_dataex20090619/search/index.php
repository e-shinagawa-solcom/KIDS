<?
/** 
*	データエクスポート 検索画面
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
// index.php -> strSessionID  -> index.php
// index.php -> lngExportData -> index.php


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


// 権限確認のための出力対象の機能コードを取得
$lngFunctionCode = getFunctionCode( $aryData["lngExportData"] );



// 権限確認
if ( !fncCheckAuthority( $lngFunctionCode, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
$aryCheck["lngExportData"] = "null:number(DEF_EXPORT_SALES,DEF_EXPORT_STOCK)";


// L／C設定日(前日)
$strDefaultLCDate = date( "Y/m/d", strtotime( "-1 day" ) );

$aryData["lcdatestart"] = $strDefaultLCDate;
$aryData["lcdateend"]   = $strDefaultLCDate;


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//echo getArrayTable( $aryCheckResult, "TABLE" );
//echo getArrayTable( $aryData, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "dataex/search/" . $aryDirName[$aryData["lngExportData"]] . "/parts.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
