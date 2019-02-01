<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  無効化
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
*         ・指定売上番号データの無効化処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "sc/cmn/lib_scs.php");
require (SRC_ROOT . "sc/cmn/lib_scs1.php");
require (SRC_ROOT . "sc/cmn/column.php");

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
if ( !$aryData["lngSalesNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngSalesNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 602 売上管理（売上検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 607 売上管理（売上無効化）
if ( !fncCheckAuthority( DEF_FUNCTION_SC7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 無効化対象の売上NOの売上情報取得
$strQuery = fncGetSalesHeadNoToInfoSQL ( $aryData["lngSalesNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$arySalesResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 603, DEF_ERROR, "データが異常です", TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 無効化確認処理 //////////////////
////////////////////////////////////////////////////////
// 無効化対象の売上データの無効化によってどうなるかの確認
$lngCase = fncGetInvalidCodeToMaster ( $arySalesResult, $objDB );

////////////////////////////////////////////////////////
////////////////////// 無効化処理実行 //////////////////
////////////////////////////////////////////////////////
if( $aryData["strSubmit"] )
{
	// 該当売上の状態が「締め済」の状態であれば
	if ( $arySalesResult["lngsalesstatuscode"] == DEF_SALES_CLOSED )
	{
		fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// トランザクション開始
	$objDB->transactionBegin();

	// 更新対象売上データをロックする
	$strLockQuery = "SELECT lngSalesNo FROM m_Sales WHERE lngSalesNo = " . $aryData["lngSalesNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 無効化確認
	$strQuery = "UPDATE m_Sales SET bytInvalidFlag = TRUE WHERE lngSalesNo = " . $aryData["lngSalesNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $arySalesResult;
	$aryDeleteData["strAction"] = "/sc/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish/invalid_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
//////////////////// 無効化確認画面表示 //////////////////
////////////////////////////////////////////////////////
// 該当売上の状態が「締め済」の状態であれば
if ( $arySalesResult["lngsalesstatuscode"] == DEF_SALES_CLOSED )
{
	fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 取得データの調整
$aryNewResult = fncSetSalesHeadTabelData ( $arySalesResult );

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
$aryHeadColumnNames = fncSetSalesTabelName ( $aryTableViewHead, $aryTytle );
// カラム名の設定
$aryDetailColumnNames = fncSetSalesTabelName ( $aryTableViewDetail, $aryTytle );

////////// 明細行の取得 ////////////////////

// 指定売上番号の売上明細データ取得用SQL文の作成
$strQuery = fncGetSalesDetailNoToInfoSQL ( $aryData["lngSalesNo"] );

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$arySalesDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 603, DEF_WARNING, "売上番号に対する明細が存在しません", FALSE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($arySalesDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetSalesDetailTabelData ( $arySalesDetailResult[$i], $aryNewResult );

	//-------------------------------------------------------------------------
	// *v2* 部門・担当者の取得
	//-------------------------------------------------------------------------
	$aryQuery   = array();
	$aryQuery[] = "SELECT DISTINCT";
	$aryQuery[] = "	mg.strgroupdisplaycode";
	$aryQuery[] = "	,mg.strgroupdisplayname";
	$aryQuery[] = "	,mu.struserdisplaycode";
	$aryQuery[] = "	,mu.struserdisplayname";
	$aryQuery[] = "FROM";
	$aryQuery[] = "	m_group mg";
	$aryQuery[] = "	,m_user mu";
	$aryQuery[] = "WHERE";
	$aryQuery[] = "	mg.lnggroupcode =";
	$aryQuery[] = "	(";
	$aryQuery[] = "		SELECT mp1.lnginchargegroupcode";
	$aryQuery[] = "		FROM m_product mp1";
	$aryQuery[] = "		WHERE mp1.strproductcode = '" . $arySalesDetailResult[$i]["strproductcode"] . "'";
	$aryQuery[] = "	)";
	$aryQuery[] = "	AND mu.lngusercode =";
	$aryQuery[] = "	(";
	$aryQuery[] = "		SELECT mp2.lnginchargeusercode";
	$aryQuery[] = "		FROM m_product mp2";
	$aryQuery[] = "		WHERE mp2.strproductcode = '" . $arySalesDetailResult[$i]["strproductcode"] . "'";
	$aryQuery[] = "	)";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );


	// クエリー実行
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// 部門コード・名称
		$aryNewDetailResult[$i]["strInChargeGroup"] = "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupdisplayname;
		// 担当者コード・名称
		$aryNewDetailResult[$i]["strInChargeUser"]  = "[" . $objResult->struserdisplaycode . "] " . $objResult->struserdisplayname;
	}
	else
	{
		fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	//-------------------------------------------------------------------------


	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/result/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($arySalesDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>