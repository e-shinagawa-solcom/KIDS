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



//-------------------------------------------------------------------------
// ■「製品」にログインユーザーが属しているかチェック
//-------------------------------------------------------------------------
$strFncFlag = "PO";
$blnCheck = fncCheckInChargeProduct( $aryData["lngOrderNo"], $lngInputUserCode, $strFncFlag, $objDB );

// ユーザーが対象製品に属していない場合
if( !$blnCheck )
{
	fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
}



// 削除対象の発注NOの発注情報取得
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
////////////////////// 削除確認処理 ////////////////////
////////////////////////////////////////////////////////
// 仕入データの確認
$strOrderCode = $aryOrderResult["strrealordercode"];
$aryCode = fncGetDeleteCodeToMaster ( $strOrderCode, 1, $objDB );
if ( $aryCode )
{
	$lngStockCount = count($aryCode);
}
else
{
	$lngStockCount = 0;
}

////////////////////////////////////////////////////////
////////////////////// 削除処理実行 ////////////////////
////////////////////////////////////////////////////////
// 該当発注Ｎｏを指定している仕入データが存在しなければ
if ( $aryData["strSubmit"] == "submit" and $lngStockCount == 0 )
{
	// 該当発注の状態が「申請中」「締め済」の状態であれば
	if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
	{
		fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// トランザクション開始
	$objDB->transactionBegin();

	// m_orderのシーケンスを取得
	$sequence_m_order = fncGetSequence( 'm_Order.lngOrderNo', $objDB );

	// 最小リビジョン番号の取得
	$strOrderCode = $aryOrderResult["strrealordercode"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Order WHERE strOrderCode = '" . $strOrderCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	$lngMinRevisionNo--;

	// 最大リバイズコードの取得
	$strReviseGetQuery = "SELECT MAX(strReviseCode) as maxrevise FROM m_Order WHERE strOrderCode = '" . $strOrderCode . "' AND bytInvalidFlag = FALSE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strReviseGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$strMaxReviseCode = $objResult->maxrevise;
	}
	else
	{
		$strMaxReviseCode = "00";
	}
	$objDB->freeResult( $lngResultID );

	$aryQuery[] = "INSERT INTO m_order (lngOrderNo, lngRevisionNo, strReviseCode, ";	// 発注NO、リビジョン番号、リバイズ番号
	$aryQuery[] = "strOrderCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// 発注コード、入力者コード、無効フラグ、登録日
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_order . ", ";		// 1:発注番号
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:リビジョン番号
	$aryQuery[] = "'" . $strMaxReviseCode . "', ";	// 3:リバイスコード
	$aryQuery[] = "'" . $strOrderCode . "', ";	// 4:発注コード．
	$aryQuery[] = $objAuth->UserCode . ", ";	// 5:入力者コード
	$aryQuery[] = "false, ";					// 6:無効フラグ
	$aryQuery[] = "now()";						// 7:登録日
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 502, DEF_FATAL, "削除処理に伴うマスタ処理失敗", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryOrderResult;
	$aryDeleteData["strAction"] = "/po/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/finish/remove_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
////////////////////// 削除できない ////////////////////
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
//////////////////// 削除確認画面表示 //////////////////
////////////////////////////////////////////////////////
// 該当発注の状態が「申請中」「締め済」の状態であれば
if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
{
	fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 取得データの調整
// レートタイプ
if ( $aryOrderResult["lngmonetaryratecode"] and $aryOrderResult["lngmonetaryratecode"] != DEF_MONETARY_YEN )
{
	$aryOrderResult["strmonetaryratename"] = fncGetMasterValue(m_monetaryrateclass, lngMonetaryRateCode, strMonetaryRateName, $aryOrderResult["lngmonetaryratecode"], '', $objDB);
}

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

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

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