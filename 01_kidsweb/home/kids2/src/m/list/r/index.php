<?
/** 
*	マスタ管理 通貨レートマスタ マスターテーブル結果一覧画面
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
//
// 登録画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
//
// 修正画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngmonetaryratecode   -> edit.php
// index.php -> lngmonetaryunitcode   -> edit.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;

// 文字列チェック
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], "", "", $aryData, $objDB );





///////////////////////////////////////////////////////////////////
// テーブル生成
///////////////////////////////////////////////////////////////////
// 結果行表示
$count = 0;
foreach ( $objMaster->aryData as $record )
{
	// 最初のカラムをキーとする
	$aryData["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";

	// カラム生成
	foreach ( $record as $colmun )
	{
		$aryData["strResultHtml"] .= "		<td nowrap>$colmun</td>\n";
	}

	// GETで渡す文字列生成
	$getUrl = "strSessionID=" .$aryData["strSessionID"]. "&lngmonetaryratecode=" . $record["lngmonetaryratecode"] . "&lngmonetaryunitcode=" . $record["lngmonetaryunitcode"];


	// 修正ボタン生成
	$aryData["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/r/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , 1 , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

	$aryData["strResultHtml"] .= "	</tr>\n";

}



$objDB->close();



// 登録ボタンのGET文字列生成
$aryData["strInsertForm"] = "/m/regist/r/edit.php?strSessionID=" .$aryData["strSessionID"]. "&lngActionCode=" . DEF_ACTION_INSERT;

$aryData["lngLanguageCode"] =1;

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/list/r/parts.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
