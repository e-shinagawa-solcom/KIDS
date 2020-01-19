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
require_once (LIB_DEBUGFILE);
require_once (LIB_EXCLUSIVEFILE);
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
    $objDB->transactionBegin();
	for($i = 0; $i < count($aryOrderNo); $i++){  // 実際は1件のみ。
		$lngorderno = intval(explode("_", $aryOrderNo[$i])[0]);
		$lngrevisionno = fncGetLatestRevisionNo($lngorderno, $objDB);
	    
	    // 確定取消対象となった発注明細に基づく発注書明細全件を取得
		$aryPurchaseOrderDetail = fncGetDeletePurchaseOrderDetail($lngorderno, $lngrevisionno, $objDB);
		if( is_null($aryPurchaseOrderDetail) )
		{
			fncOutputError ( 501, DEF_ERROR, "取消対象の発注は既に取消されています。", TRUE, "", $objDB );
		}
	
		// 確定取り消しとなった発注明細を取得
		$aryOrderDetail = fncGetDeleteOrderDetail($lngorderno, $lngrevisionno, $objDB);

        // 発注書明細から発注書Noを取得
        $lngpurchaseorderno = $aryPurchaseOrderDetail[0]["lngpurchaseorderno"];
        
        // 発注書ロック
        if(!lockOrder($lngpurchaseorderno, $objDB)){
			fncOutputError ( 501, DEF_ERROR, "取消対象の発注の発注書が修正中です。", TRUE, "", $objDB );
        }
        
        // 対象発注のステータスチェック
		if(!isOrderModified($lngorderno, DEF_ORDER_ORDER, $objDB)){
			fncOutputError ( 505, DEF_ERROR, "", TRUE, "", $objDB );
		}
		// 確定取り消しとなった発注明細に基づく発注マスタの発注ステータスを「仮発注」へ戻す
		if(!fncCancelOrder($lngorderno, $lngrevisionno, $objDB)){ return false; }

		// 取得した発注書番号、リビジョン番号に該当し、かつ確定取消となった発注明細に該当しない発注書明細を
		// 表示用ソートキーの昇順に取得した結果を基に、以下の仕様で新規の発注書明細を新規に登録する
		$aryInsertPurchaseOrderDetail = [];
		$aryDetailNo = array_column($aryOrderDetail, "lngorderdetailno");
		if( is_array($aryPurchaseOrderDetail) )
		{
		    $count = 0;
			for($j = 0; $j < count($aryPurchaseOrderDetail); $j++){
				if($aryPurchaseOrderDetail[$j]["lngorderdetailno"] != $aryDetailNo[0]){
					$aryInsertPurchaseOrderDetail[$count]["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
					$aryInsertPurchaseOrderDetail[$count]["lngpurchaseorderdetailno"] = $count + 1;
					$aryInsertPurchaseOrderDetail[$count]["lngrevisionno"] = intval($aryPurchaseOrderDetail[$j]["lngrevisionno"]) + 1;
					$aryInsertPurchaseOrderDetail[$count]["lngorderno"] = $aryPurchaseOrderDetail[$j]["lngorderno"];
					$aryInsertPurchaseOrderDetail[$count]["lngorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngorderdetailno"];
					$aryInsertPurchaseOrderDetail[$count]["lngorderrevisionno"] = $aryPurchaseOrderDetail[$j]["lngorderrevisionno"];
					$aryInsertPurchaseOrderDetail[$count]["lngstocksubjectcode"] = $aryPurchaseOrderDetail[$j]["lngstocksubjectcode"];
					$aryInsertPurchaseOrderDetail[$count]["lngstockitemcode"] = $aryPurchaseOrderDetail[$j]["lngstockitemcode"];
					$aryInsertPurchaseOrderDetail[$count]["strstockitemname"] = $aryPurchaseOrderDetail[$j]["strstockitemname"];
					$aryInsertPurchaseOrderDetail[$count]["lngdeliverymethodcode"] = $aryPurchaseOrderDetail[$j]["lngdeliverymethodcode"];
					$aryInsertPurchaseOrderDetail[$count]["strdeliverymethodname"] = $aryPurchaseOrderDetail[$j]["strdeliverymethodname"];
					$aryInsertPurchaseOrderDetail[$count]["curproductprice"] = $aryPurchaseOrderDetail[$j]["curproductprice"];
					$aryInsertPurchaseOrderDetail[$count]["lngproductquantity"] = $aryPurchaseOrderDetail[$j]["lngproductquantity"];
					$aryInsertPurchaseOrderDetail[$count]["lngproductunitcode"] = $aryPurchaseOrderDetail[$j]["lngproductunitcode"];
					$aryInsertPurchaseOrderDetail[$count]["strproductunitname"] = $aryPurchaseOrderDetail[$j]["strproductunitname"];
					$aryInsertPurchaseOrderDetail[$count]["cursubtotalprice"] = $aryPurchaseOrderDetail[$j]["cursubtotalprice"];
					$aryInsertPurchaseOrderDetail[$count]["dtmdeliverydate"] = $aryPurchaseOrderDetail[$j]["dtmdeliverydate"];
					$aryInsertPurchaseOrderDetail[$count]["strnote"] = $aryPurchaseOrderDetail[$j]["strnote"];
					$aryInsertPurchaseOrderDetail[$count]["lngsortkey"] = $count + 1;
					$aryInsertPurchaseOrderDetail[$count]["lngprintcount"] = 0;
					$count++;
				}
				else{
					$aryCancelOrderDetail["lngpurchaseorderno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderno"];
					$aryCancelOrderDetail["lngpurchaseorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngpurchaseorderdetailno"];
					$aryCancelOrderDetail["lngrevisionno"] = intval($aryPurchaseOrderDetail[$j]["lngrevisionno"]);
					$aryCancelOrderDetail["lngorderno"] = $aryPurchaseOrderDetail[$j]["lngorderno"];
					$aryCancelOrderDetail["lngorderdetailno"] = $aryPurchaseOrderDetail[$j]["lngorderdetailno"];
					$aryCancelOrderDetail["lngorderrevisionno"] = $aryPurchaseOrderDetail[$j]["lngorderrevisionno"];
					$aryCancelOrderDetail["lngstocksubjectcode"] = $aryPurchaseOrderDetail[$j]["lngstocksubjectcode"];
					$aryCancelOrderDetail["lngstockitemcode"] = $aryPurchaseOrderDetail[$j]["lngstockitemcode"];
					$aryCancelOrderDetail["strstockitemname"] = $aryPurchaseOrderDetail[$j]["strstockitemname"];
					$aryCancelOrderDetail["lngdeliverymethodcode"] = $aryPurchaseOrderDetail[$j]["lngdeliverymethodcode"];
					$aryCancelOrderDetail["strdeliverymethodname"] = $aryPurchaseOrderDetail[$j]["strdeliverymethodname"];
					$aryCancelOrderDetail["curproductprice"] = $aryPurchaseOrderDetail[$j]["curproductprice"];
					$aryCancelOrderDetail["lngproductquantity"] = $aryPurchaseOrderDetail[$j]["lngproductquantity"];
					$aryCancelOrderDetail["lngproductunitcode"] = $aryPurchaseOrderDetail[$j]["lngproductunitcode"];
					$aryCancelOrderDetail["strproductunitname"] = $aryPurchaseOrderDetail[$j]["strproductunitname"];
					$aryCancelOrderDetail["cursubtotalprice"] = $aryPurchaseOrderDetail[$j]["cursubtotalprice"];
					$aryCancelOrderDetail["dtmdeliverydate"] = $aryPurchaseOrderDetail[$j]["dtmdeliverydate"];
					$aryCancelOrderDetail["strnote"] = $aryPurchaseOrderDetail[$j]["strnote"];
					$aryCancelOrderDetail["lngsortkey"] = $aryPurchaseOrderDetail[$j]["lngsortkey"];
				}
			}
		}
		else
		{
			$aryCancelOrderDetail["lngpurchaseorderno"] = $aryPurchaseOrderDetail["lngpurchaseorderno"];
			$aryCancelOrderDetail["lngpurchaseorderdetailno"] = $aryPurchaseOrderDetail["lngpurchaseorderdetailno"];
			$aryCancelOrderDetail["lngrevisionno"] = intval($aryPurchaseOrderDetail["lngrevisionno"]);
			$aryCancelOrderDetail["lngorderno"] = $aryPurchaseOrderDetail["lngorderno"];
			$aryCancelOrderDetail["lngorderdetailno"] = $aryPurchaseOrderDetail["lngorderdetailno"];
			$aryCancelOrderDetail["lngorderrevisionno"] = $aryPurchaseOrderDetail["lngorderrevisionno"];
			$aryCancelOrderDetail["lngstocksubjectcode"] = $aryPurchaseOrderDetail["lngstocksubjectcode"];
			$aryCancelOrderDetail["lngstockitemcode"] = $aryPurchaseOrderDetail["lngstockitemcode"];
			$aryCancelOrderDetail["strstockitemname"] = $aryPurchaseOrderDetail["strstockitemname"];
			$aryCancelOrderDetail["lngdeliverymethodcode"] = $aryPurchaseOrderDetail["lngdeliverymethodcode"];
			$aryCancelOrderDetail["strdeliverymethodname"] = $aryPurchaseOrderDetail["strdeliverymethodname"];
			$aryCancelOrderDetail["curproductprice"] = $aryPurchaseOrderDetail["curproductprice"];
			$aryCancelOrderDetail["lngproductquantity"] = $aryPurchaseOrderDetail["lngproductquantity"];
			$aryCancelOrderDetail["lngproductunitcode"] = $aryPurchaseOrderDetail["lngproductunitcode"];
			$aryCancelOrderDetail["strproductunitname"] = $aryPurchaseOrderDetail["strproductunitname"];
			$aryCancelOrderDetail["cursubtotalprice"] = $aryPurchaseOrderDetail["cursubtotalprice"];
			$aryCancelOrderDetail["dtmdeliverydate"] = $aryPurchaseOrderDetail["dtmdeliverydate"];
			$aryCancelOrderDetail["strnote"] = $aryPurchaseOrderDetail["strnote"];
			$aryCancelOrderDetail["lngsortkey"] = $aryPurchaseOrderDetail[$j]["lngsortkey"];
		}
		if(count($aryInsertPurchaseOrderDetail) > 0){
			// 残明細がある場合は、発注書明細を新規に登録する
			foreach($aryInsertPurchaseOrderDetail as $row){
				if(!fncInsertPurchaseOrderDetail($row, $objDB)) { return false; }
			}
	
			// 発注書マスタを更新する
			$aryOrder_org = fncGetPurchaseOrder2($aryPurchaseOrderDetail[0]["lngpurchaseorderno"], $aryPurchaseOrderDetail[0]["lngrevisionno"], $objDB);
			$aryOrder = $aryOrder_org;
			$aryOrder["lngrevisionno"] = intval($aryOrder["lngrevisionno"]) + 1;
			$aryOrder["lngprintcount"] = 0;
			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
		}
		else
		{
			// 残明細がない場合は発注書マスタも削除する。
			$aryOrder_org = fncGetPurchaseOrder2($aryCancelOrderDetail["lngpurchaseorderno"], $aryCancelOrderDetail["lngrevisionno"], $objDB);
			$aryOrder = $aryOrder_org;
			$orgRevision = $aryOrder["lngrevisionno"];
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

			if(!fncInsertPurchaseOrder($aryOrder, $objDB)) { return false; }
			// 確定取消完了画面表示のためにリビジョン番号を復元
			$aryOrder["lngrevisionno"] = $orgRevision;
			
		}
	

		if(count($aryInsertPurchaseOrderDetail) > 0){
			$aryHtml[] = "<p class=\"caption\">以下の発注の確定取消に伴い、該当の発注書を修正しました。</p>";
			$aryHtml[] = fncCancelPurchaseOrderHtml($aryOrder, $aryCancelOrderDetail, $aryData["strSessionID"]);
		}
		else
		{
			// 残明細がない場合
			$aryHtml[] = "<p class=\"caption\">以下の発注の確定取消に伴い、該当の発注書を削除しました</p>";
			$aryHtml[] = fncDeletePurchaseOrderHtml($aryOrder_org, $aryPurchaseOrderDetail, $aryData["strSessionID"]);
			
		}
	}
	// $objDB->transactionRollback();
	$objDB->transactionCommit();

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
	$lngrevisionno = fncGetLatestRevisionNo($lngorderno, $objDB);

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