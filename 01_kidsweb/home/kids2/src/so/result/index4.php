<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  無効化
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
*         ・指定受注番号データの無効化処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "so/cmn/lib_sos.php");
require (SRC_ROOT . "so/cmn/lib_sos1.php");
require (SRC_ROOT . "so/cmn/column.php");

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
if ( !$aryData["lngReceiveNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngReceiveNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 402 受注管理（受注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 407 受注管理（受注無効化）
if ( !fncCheckAuthority( DEF_FUNCTION_SO7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 無効化対象の受注NOの受注情報取得
$strQuery = fncGetReceiveHeadNoToInfoSQL ( $aryData["lngReceiveNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 403, DEF_ERROR, "データが異常です", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 無効化確認処理 //////////////////
////////////////////////////////////////////////////////
// 無効化対象の受注データの無効化によってどうなるかの確認
$lngCase = fncGetInvalidCodeToMaster ( $aryReceiveResult, $objDB );

// 売上データの確認
$strReceiveCode = $aryReceiveResult["strreceivecode2"];
$aryCode = fncGetDeleteNoToMaster ( $aryReceiveResult["lngreceiveno"], 1, $objDB );
if ( $aryCode )
{
	$lngSalesCount = count($aryCode);
}
else
{
	$lngSalesCount = 0;
}

////////////////////////////////////////////////////////
////////////////////// 無効化処理実行 //////////////////
////////////////////////////////////////////////////////
// 該当受注Ｎｏを指定している売上データが存在しなければ
if ( $aryData["strSubmit"] == "submit" and $lngSalesCount == 0 )
{
	// 該当受注の状態が「締め済」の状態であれば
	if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// トランザクション開始
	$objDB->transactionBegin();

	// 更新対象受注データをロックする
	$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 無効化確認
	$strQuery = "UPDATE m_Receive SET bytInvalidFlag = TRUE WHERE lngReceiveNo = " . $aryData["lngReceiveNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryReceiveResult;
	$aryDeleteData["strAction"] = "/so/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/finish/invalid_parts.tmpl" );

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
if ( $lngSalesCount )
{
	// 置換用文字列の設定
	for( $i = 0; $i < $lngSalesCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "売上管理";
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
// 該当受注の状態が「締め済」の状態であれば
if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
{
	fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 取得データの調整
$aryNewResult = fncSetReceiveHeadTabelData ( $aryReceiveResult );

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
$aryHeadColumnNames = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
// カラム名の設定
$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail, $aryTytle );

////////// 明細行の取得 ////////////////////

// 指定受注番号の受注明細データ取得用SQL文の作成
$strQuery = fncGetReceiveDetailNoToInfoSQL ( $aryData["lngReceiveNo"] );

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryReceiveDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "受注番号に対する明細が存在しません", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryReceiveDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetReceiveDetailTabelData ( $aryReceiveDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/result/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryReceiveDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>