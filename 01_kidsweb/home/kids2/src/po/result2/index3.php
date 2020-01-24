<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  発注書削除
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・指定発注書データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "po/cmn/lib_por.php");
require (SRC_ROOT . "po/cmn/column.php");
require_once (LIB_DEBUGFILE);
require (LIB_EXCLUSIVEFILE);

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}
if ( !isset($aryData["lngPurchaseOrderNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngPurchaseOrderNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;


// 権限確認
// 510 発注管理（発注書検索）
if ( !fncCheckAuthority( DEF_FUNCTION_PO10, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 513 発注管理（発注書削除）
if ( !fncCheckAuthority( DEF_FUNCTION_PO13, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

//$aryPurchaseOrderNo = explode(",", $aryData["lngPurchaseOrderNo"]);

$lngpurchaseorderno = $aryData["lngPurchaseOrderNo"];
$lngrevisionno = $aryData["lngRevisionNo"];


if($_POST){
//echo "delete key:" . $lngpurchaseorderno . "-" . $lngrevisionno . "<br>";
	
	// 発注書に基づく発注書マスタの中に「納品済」が存在した場合はエラー
	//if(
	$objDB->transactionBegin();

	// 発注書マスタロック
    if( !lockOrder($lngpurchaseorderno, $objDB) )
    {
        fncOutputError(9051, DEF_ERROR, "発注書のロック取得に失敗しました。", true, "", $objDB);
        return false;
    }
    
    // 発注書更新有無チェック
    if( isPurchaseOrderModified($lngpurchaseorderno, $lngrevisionno, $objDB) )
    {
        fncOutputError(9051, DEF_ERROR, "他ユーザーが発注書を更新または削除しています。", true, "", $objDB);
        return false;
    }
	
    // 発注データロック&ステータスチェック
    if( !fncCanDeletePO($lngpurchaseorderno, $lngrevisionno, $objDB))
    {
        fncOutputError(9051, DEF_ERROR, "納品済または締め済の明細があるため削除できません。", true, "", $objDB);
    }
    
	$aryCancelOrderDetail = fncGetDeletePurchaseOrderDetailByPo($lngpurchaseorderno, $lngrevisionno, $objDB);
	$aryPurchaseOrder = fncGetPurchaseOrder2($lngpurchaseorderno, $lngrevisionno, $objDB);
	$aryOrder = $aryPurchaseOrder;
    // 削除対象となった発注書に基づく発注書マスタのステータスを「仮受注」に変更
	if(!fncCancelOrderByPo($lngpurchaseorderno, $lngrevisionno, $objDB)){ return false; }
    
	// 削除対象の発注書マスタにリビジョン-1のデータを追加
	//$aryOrder = fncGetPurchaseOrder2($lngpurchaseorderno, $alngrevisionno, $objDB);
	//$orgRevision = $aryOrder["lngrevisionno"];
	//$aryOrder["lngpurchaseorderno"] = $lngpurchaseorderno;
	$aryOrder["lngrevisionno"] = -1;
	$aryOrder["lngcustomercode"] = null;
	$aryOrder["strcustomername"] = null;
	$aryOrder["strcustomercompanyaddreess"] = null;
	$aryOrder["strcustomercompanytel"] = null;
	$aryOrder["strcustomercompanyfax"] = null;
	$aryOrder["strproductname"] = null;
	$aryOrder["strproductenglishname"] = null;
	$aryOrder["dtmexpirationdate"] = null;
	$aryOrder["lngmonetaryunitcode"] = null;
	$aryOrder["strmonetaryunitname"] = null;
	$aryOrder["strmonetaryunitsign"] = null;
	$aryOrder["lngmonetaryratecode"] = null;
	$aryOrder["strmonetaryratename"] = null;
	$aryOrder["lngpayconditioncode"] = null;
	$aryOrder["strpayconditionname"] = null;
	$aryOrder["lnggroupcode"] = null;
	$aryOrder["strgroupname"] = null;
	$aryOrder["txtsignaturefilename"] = null;
	$aryOrder["lngusercode"] = null;
	$aryOrder["strusername"] = null;
	$aryOrder["lngdeliveryplacecode"] = null;
	$aryOrder["strdeliveryplacename"] = null;
	$aryOrder["curtotalprice"] = null;
	$aryOrder["lnginsertusercode"] = $lngInputUserCode;
	$aryOrder["strinsertusername"] = null;
	$aryOrder["strnote"] = null;
	$aryOrder["lngprintcount"] = null;

	if(!fncInsertPurchaseOrder($aryOrder, $objDB, $objAuth)) { return false; }

	//$objDB->transactionRollback();
	$objDB->transactionCommit();

	$aryHtml[] = "<p class=\"caption\">該当の発注書を削除しました</p>";
	$aryHtml[] = fncDeletePurchaseOrderHtml($aryPurchaseOrder, $aryCancelOrderDetail, $aryData["strSessionID"]);

	if($aryHtml){
		$aryResult["aryPurchaseOrder"] = implode("\n", $aryHtml);
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate("po/finish/remove_parts.tmpl");
		$objTemplate->replace($aryResult);
		$objTemplate->complete();
		echo $objTemplate->strTemplate;
	} else {
		fncOutputError ( 9051, DEF_ERROR, "更新対象の発注書マスタがありません。", TRUE, "", $objDB );
		return FALSE;
	}
	
	$objDB->close();
	return true;
}

//echo "start key:" . $lngpurchaseorderno . "-" . $lngrevisionno . "<br>";

// 発注書削除確認画面
// 発注書マスタを取得
$aryResult = fncGetPurchaseOrderEdit($lngpurchaseorderno, $lngrevisionno, $objDB);

// 取得データの調整
$aryNewResult = fncSetPurchaseHeadTabelData($aryResult[0]);

////////// 明細行の取得 ////////////////////
// 発注書明細を取得

$strQuery = fncGetPurchaseOrderDetailSQL($lngpurchaseorderno, $lngrevisionno);
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryDetailResult[$i] = $objDB->fetchArray( $lngResultID, $i );
	}
}
$objDB->freeResult( $lngResultID );
if(!is_array($aryDetailResult)){
    $aryDetailResult[] = $aryDetailResult;
}
for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/result2/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strResult"] = implode ("\n", $aryDetailTable );


$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strMode"] = "detail";
$aryNewResult["strSessionID"] = $aryData["strSessionID"];
// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result2/parts3.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;// テンプレート読み込み


?>