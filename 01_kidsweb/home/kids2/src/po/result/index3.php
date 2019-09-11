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
	// トランザクション開始
	$objDB->transactionBegin();

	$aryOrderCode = explode(",", $aryData["strOrderCode"]);
	$aryRevisionNo = explode(",", $aryData["lngRevisionNo"]);

	for($i = 0; $i < count($aryRevisionNo); $i++){
		//$strOrderCode = explode("_", $aryOrderCode[$i])[0];
		$lngRevisionNo = explode("_", $aryRevisionNo[$i])[1];
		// 確定取消対象となった発注明細に紐づく発注書マスタの発注書番号、リビジョン番号を取得する。
		$aryPurchaseOrder = fncGetPurchaseOrder($aryOrderNo[$i], $lngRevisionNo, $objDB);

		// list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $objDB );
		// if ( $lngResultNum == 1 )
		// {
		// 	$aryPurchaseOrder = $objDB->fetchArray( $lngResultID, 0 );
		// }
		// $objDB->freeResult( $lngResultID );

		// 発注マスタを「仮受注」に変更する
		if(!fncGetCancelOrder($aryOrderNo[$i], $lngRevisionNo, $objDB)){
			fncOutputError ( 9051, DEF_ERROR, "データベースの更新に失敗しました。", TRUE, "", $objDB );
			return FALSE;
		}

		if($aryPurchaseOrder){
			// 発注書明細を取得する
			$strSql = fncGetPurchaseOrderDetailSQL($aryPurchaseOrder["lngpurchaseorderno"], $aryPurchaseOrder["lngrevisionno"]);

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $objDB );
			if ( $lngResultNum == 1 )
			{
				$aryPurchaseOrderDetail = $objDB->fetchArray( $lngResultID, 0 );
			}
			else if ( !$lngResultID = $objDB->execute( $strSql ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "データベースの更新に失敗しました。", TRUE, "", $objDB );
				return FALSE;
			}
			$objDB->freeResult( $lngResultID );

			// 発注書明細を登録
			$aryPurchaseOrderDetail["lngpurchaseorderno"] = $aryPurchaseOrder["lngpurchaseorderno"];
			$aryPurchaseOrderDetail["lngrevisionno"] = intval($aryPurchaseOrder["lngrevisionno"]) + 1;
			$aryPurchaseOrderDetail["lngsortkey"] = intval($aryPurchaseOrderDetail["lngsortkey"]) + 1;

			$strSql = fncInsertPurchaseOrderDetailSQL($aryPurchaseOrderDetail);

			if ( !$lngResultID = $objDB->execute( $strSql ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "発注書明細への登録処理に失敗しました。", TRUE, "", $objDB );
				return FALSE;
			}
			$objDB->freeResult( $lngResultID );

			// 発注書マスタを新規登録する

		}





		$objDB->transactionRollback();

	}

}

for($i = 0; $i < count($aryOrderNo); $i++){
	// 削除対象の発注NOの発注書情報取得
	$strPurchaseOrder = fncGetPurchaseHeadNoToInfo ( $aryOrderNo[$i], $objDB );

	// 指定発注番号の発注書明細データ取得用SQL文の作成
	$strPurchaseOrderDatail = fncGetPurchaseDetailNoToInfo ( $aryOrderNo[$i], $objDB );

	$aryHtml[] = fncDeletePurchaseOrderHtml($strPurchaseOrder, $strPurchaseOrderDatail);
	$aryOrderCode[] = $strPurchaseOrder["strordercode"];
	$aryRevisionNo[] = $strPurchaseOrder["lngrevisionno"];

}

$aryResult["strResult"] = implode("\n", $aryHtml);
$aryResult["strOrderCode"] = implode(",", $aryOrderCode);
$aryResult["lngRevisionNo"] = implode(",", $aryRevisionNo);


// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts3.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryResult );
// $objTemplate->replace( $aryOrderResult );
// $objTemplate->replace( $aryDetailResult );
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>