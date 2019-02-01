<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  無効化
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
*         ・指定発注番号データの無効化処理
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
if ( !$aryData["lngOrderNo"] )
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

// 権限確認
// 502 発注管理（発注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 507 発注管理（発注無効化）
if ( !fncCheckAuthority( DEF_FUNCTION_PO7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 無効化対象の発注NOの発注情報取得
$strQuery = fncGetPurchaseHeadNoToInfoSQL ( $aryData["lngOrderNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryOrderResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 503, DEF_ERROR, "データが異常です", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 無効化確認処理 //////////////////
////////////////////////////////////////////////////////
// 無効化対象の発注データの無効化によってどうなるかの確認
$lngCase = fncGetInvalidCodeToMaster ( $aryOrderResult, $objDB );

// 仕入データの確認
$strOrderCode = $aryOrderResult["strrealordercode"];
$aryCode = fncGetDeleteNoToMaster ( $aryOrderResult["lngorderno"], 1, $objDB );
if ( $aryCode )
{
	$lngStockCount = count($aryCode);
}
else
{
	$lngStockCount = 0;
}

////////////////////////////////////////////////////////
////////////////////// 無効化処理実行 //////////////////
////////////////////////////////////////////////////////
// 該当発注Ｎｏを指定した仕入が存在しなければ無効化実行
if ( $aryData["strSubmit"] == "submit" and $lngStockCount == 0 )
{
	// 該当発注の状態が「申請中」「締め済」の状態であれば
	if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
	{
		fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// トランザクション開始
	$objDB->transactionBegin();

	// 更新対象発注データをロックする
	$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $aryData["lngOrderNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 無効化確認
	$strQuery = "UPDATE m_Order SET bytInvalidFlag = TRUE WHERE lngOrderNo = " . $aryData["lngOrderNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// コミット処理
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryOrderResult;
	$aryDeleteData["strAction"] = "/po/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/finish/invalid_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
////////////////////// 無効化できない //////////////////
////////////////////////////////////////////////////////
if ( $lngStockCount )
{
	// 置換用文字列の設定
	for( $i = 0; $i < $lngStockCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "仕入管理";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// テンプレート生成
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML出力
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] = implode ("\n", $aryDetail );
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "error/use/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
//////////////////// 無効化確認画面表示 //////////////////
////////////////////////////////////////////////////////
// 該当発注の状態が「申請中」「締め済」の状態であれば
if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
{
	fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 取得データの調整
$aryNewResult = fncSetPurchaseHeadTabelData ( $aryOrderResult );

// 言語の設定
if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
{
	$aryTytle = $aryTableTytleEng;
}
else
{
	$aryTytle = $aryTableTytle;
}

// カラム名の設定
$aryHeadColumnNames = fncSetPurchaseTabelName ( $aryTableViewHead, $aryTytle );
// カラム名の設定
$aryDetailColumnNames = fncSetPurchaseTabelName ( $aryTableViewDetail, $aryTytle );

////////// 明細行の取得 ////////////////////

// 指定発注番号の発注明細データ取得用SQL文の作成
$strQuery = fncGetPurchaseDetailNoToInfoSQL ( $aryData["lngOrderNo"] );

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 503, DEF_WARNING, "発注番号に対する明細が存在しません", FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryOrderDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData ( $aryOrderDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/result/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryOrderDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>