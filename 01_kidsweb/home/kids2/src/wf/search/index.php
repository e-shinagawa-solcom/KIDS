<?
/** 
*	ワークフロー 案件検索画面
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

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_GET;

// 文字列チェック
$aryCheck["strSessionID"]    = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", FALSE, "", $objDB );
}

$objDB->close();


// ヘルプリンク用機能コードをセット
$aryData["lngFunctionCode"] = DEF_FUNCTION_WF2;

// HTML出力

echo fncGetReplacedHtml( "wf/search/parts.tmpl", $aryData, $objAuth );



return TRUE;
?>
