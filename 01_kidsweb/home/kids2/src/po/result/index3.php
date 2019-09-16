<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  削除
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
*         ・指定発注番号データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "po/cmn/lib_pos.php");
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "po/cmn/column.php");

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
if ( !isset($aryData["lngOrderNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngOrderNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;


// 権限確認
// 502 発注管理（発注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 506 発注管理（発注削除）
if ( !fncCheckAuthority( DEF_FUNCTION_PO6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

$aryOrderNo = explode(",", $aryData["lngOrderNo"]);

if($_POST){
	for($i = 0; $i < count($aryOrderNo); $i++){
		$lngorderno = intval(explode("_", $aryOrderNo[$i])[0]);
		$lngrevisionno = intval(explode("_", $aryOrderNo[$i])[1]);
	
		// 確定取消対象となった発注明細に基づく発注書明細を取得
		$aryPurchaseOrderDetail = fncGetDeletePurchaseOrderDetail($lngorderno, $lngrevisionno, $objDB);
	
		$objDB->transactionBegin();
		// 確定取り消しとなった発注明細に基づく発注マスタの発注ステータスを「仮発注」へ戻す
		if(!fncCancelOrder($lngorderno, $lngrevisionno, $objDB)){ return false; }
	
		// 確定取り消しとなった発注明細を取得
		$aryOrderDetail = fncGetDeleteOrderDetail($lngorderno, $lngrevisionno, $objDB);
	
		// 取得した発注書番号、リビジョン番号に該当し、かつ確定取消となった発注明細に該当しない発注書明細を
		// 表示用ソートキーの昇順に取得した結果を基に、以下の仕様で新規の発注書明細を新規に登録する
		$aryInsertPurchaseOrderDetail = [];
		$aryDetailNo = array_column($aryOrderDetail, "lngorderdetailno");
		for($j = 0; $j < count($aryPurchaseOrderDetail); $j++){
			if(!in_array($aryOrderDetail[$j]["lngorderdetailno"], $aryDetailNo, true)){
				$aryInsertPurchaseOrderDetail[$j]["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
				$aryInsertPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"];
				$aryInsertPurchaseOrderDetail[$j]["lngrevisionno"] = intval($aryPurchaseOrderDetail[$j]["lngrevisionno"]) + 1;
				$aryInsertPurchaseOrderDetail[$j]["lngorderno"] = $aryPurchaseOrderDetail[$j]["lngorderno"];
				$aryInsertPurchaseOrderDetail[$j]["lngorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngorderdetailno"];
				$aryInsertPurchaseOrderDetail[$j]["lngorderrevisionno"] = $aryPurchaseOrderDetail[$j]["lngorderrevisionno"];
				$aryInsertPurchaseOrderDetail[$j]["lngstocksubjectcode"] = $aryPurchaseOrderDetail[$j]["lngstocksubjectcode"];
				$aryInsertPurchaseOrderDetail[$j]["lngstockitemcode"] = $aryPurchaseOrderDetail[$j]["lngstockitemcode"];
				$aryInsertPurchaseOrderDetail[$j]["strstockitemname"] = $aryPurchaseOrderDetail[$j]["strstockitemname"];
				$aryInsertPurchaseOrderDetail[$j]["lngdeliverymethodcode"] = $aryPurchaseOrderDetail[$j]["lngdeliverymethodcode"];
				$aryInsertPurchaseOrderDetail[$j]["strdeliverymethodname"] = $aryPurchaseOrderDetail[$j]["strdeliverymethodname"];
				$aryInsertPurchaseOrderDetail[$j]["curproductprice"] = $aryPurchaseOrderDetail[$j]["curproductprice"];
				$aryInsertPurchaseOrderDetail[$j]["lngproductquantity"] = $aryPurchaseOrderDetail[$j]["lngproductquantity"];
				$aryInsertPurchaseOrderDetail[$j]["lngproductunitcode"] = $aryPurchaseOrderDetail[$j]["lngproductunitcode"];
				$aryInsertPurchaseOrderDetail[$j]["strproductunitname"] = $aryPurchaseOrderDetail[$j]["strproductunitname"];
				$aryInsertPurchaseOrderDetail[$j]["cursubtotalprice"] = $aryPurchaseOrderDetail[$j]["cursubtotalprice"];
				$aryInsertPurchaseOrderDetail[$j]["dtmdeliverydate"] = $aryPurchaseOrderDetail[$j]["dtmdeliverydate"];
				$aryInsertPurchaseOrderDetail[$j]["strnote"] = $aryPurchaseOrderDetail[$j]["strnote"];
				$aryInsertPurchaseOrderDetail[$j]["lngsortkey"] = $j + 1;
			}
		}
	
		if($aryInsertPurchaseOrderDetail){
			// 発注書明細を新規に登録する
			foreach($aryInsertPurchaseOrderDetail as $row){
				if(!fncInsertPurchaseOrderDetail($row, $objDB)) { return false; }
			}
	
			// 発注書マスタを更新する
			$aryOrder = fncGetPurchaseOrder2($aryPurchaseOrderDetail[0]["lngpurchaseorderno"], $aryPurchaseOrderDetail[0]["lngrevisionno"], $objDB);
			$aryOrder["lngrevisionno"] = intval($aryPurchaseOrderDetail[0]["lngrevisionno"]) + 1;
			$aryOrder["lngprintcount"] = 0;
			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
		}
	
		// $objDB->transactionRollback();
		$objDB->transactionCommit();

		if($aryInsertPurchaseOrderDetail){
			$aryHtml[] = "<p class=\"caption\">発注確定取消に伴い、以下の発注書を修正しました。</p>";
			$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryInsertPurchaseOrderDetail);
		}
	}

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

for($i = 0; $i < count($aryOrderNo); $i++){
	$lngorderno = intval(explode("_", $aryOrderNo[$i])[0]);
	$lngrevisionno = intval(explode("_", $aryOrderNo[$i])[1]);

	$aryOrder[] = fncGetOrder($lngorderno, $lngrevisionno, $objDB);
}

$aryResult["strResult"] = fncCancelOrderHtml($aryOrder);
$aryResult["lngOrderNo"] = $aryData["lngOrderNo"];
$aryResult["strSessionID"] = $aryData["strSessionID"];
//$aryResult["strResult"] = implode("\n", $aryHtml);
//$aryResult["strOrderCode"] = implode(",", $aryOrderCode);
//$aryResult["lngRevisionNo"] = implode(",", $aryRevisionNo);


// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts3.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryResult );
// $objTemplate->replace( $aryOrderResult );
// $objTemplate->replace( $aryDetailResult );
//$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>