<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  無効化
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
*         ・指定仕入番号データの無効化処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------


/** 
*	仕入　無効化確認画面
*
*	仕入無効化確認画面の表示処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	検索画面より選択された仕入番号の無効化確認画面を表示する
*
*/

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "pc/cmn/lib_pcs.php");
require (SRC_ROOT . "pc/cmn/lib_pcs1.php");
require (SRC_ROOT . "pc/cmn/column.php");

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
if ( !$aryData["lngStockNo"] )
{
	fncOutputError ( 9018, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngStockNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 702 仕入管理（仕入検索）
if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 707 仕入管理（仕入無効化）
if ( !fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 無効化対象の仕入NOの仕入情報取得
$strQuery = fncGetStockHeadNoToInfoSQL ( $aryData["lngStockNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryStockResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 703, DEF_ERROR, "データが異常です", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 無効化確認処理 //////////////////
////////////////////////////////////////////////////////
// 無効化対象の仕入データの無効化によってどうなるかの確認
$lngCase = fncGetInvalidCodeToMaster ( $aryStockResult, $objDB );

////////////////////////////////////////////////////////
////////////////////// 無効化処理実行 //////////////////
////////////////////////////////////////////////////////
if( $aryData["strSubmit"] )
{
	// 該当仕入の状態が「締め済」の状態であれば
	if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
	{
		fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// トランザクション開始
	$objDB->transactionBegin();

	// 更新対象仕入データをロックする
	$strLockQuery = "SELECT lngStockNo FROM m_Stock WHERE lngStockNo = " . $aryData["lngStockNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 無効化確認
	$strQuery = "UPDATE m_Stock SET bytInvalidFlag = TRUE WHERE lngStockNo = " . $aryData["lngStockNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryStockResult;
	$aryDeleteData["strAction"] = "/pc/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/finish/invalid_parts.tmpl" );

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
// 該当仕入の状態が「締め済」の状態であれば
if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
{
	fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 取得データの調整
$aryNewResult = fncSetStockHeadTabelData ( $aryStockResult );

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
$aryHeadColumnNames = fncSetStockTabelName ( $aryTableViewHead, $aryTytle );
// カラム名の設定
$aryDetailColumnNames = fncSetStockTabelName ( $aryTableViewDetail, $aryTytle );

////////// 明細行の取得 ////////////////////

// 指定仕入番号の仕入明細データ取得用SQL文の作成
$strQuery = fncGetStockDetailNoToInfoSQL ( $aryData["lngStockNo"] );

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$aryStockDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryStockDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetStockDetailTabelData ( $aryStockDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/result/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryStockDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "pc/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>