<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  削除
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
*         ・指定仕入番号データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



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
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
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
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 706 仕入管理（仕入削除）
if ( !fncCheckAuthority( DEF_FUNCTION_PC6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 削除対象の仕入NOの仕入情報取得
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
////////////////////// 削除処理実行 ////////////////////
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

	// m_stockのシーケンスを取得
	$sequence_m_stock = fncGetSequence( 'm_Stock.lngStockNo', $objDB );

	// 最小リビジョン番号の取得
	$strStockCode = $aryStockResult["strstockcode"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Stock WHERE strStockCode = '" . $strStockCode . "'";
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

	$aryQuery[] = "INSERT INTO m_stock (lngStockNo, lngRevisionNo, ";					// 仕入NO、リビジョン番号
	$aryQuery[] = "strStockCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// 仕入コード、入力者コード、無効フラグ、登録日
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_stock . ", ";		// 1:仕入番号
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:リビジョン番号
	$aryQuery[] = "'" . $strStockCode . "', ";	// 3:仕入コード．
	$aryQuery[] = $objAuth->UserCode . ", ";	// 4:入力者コード
	$aryQuery[] = "false, ";					// 5:無効フラグ
	$aryQuery[] = "now()";						// 6:登録日
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 702, DEF_FATAL, "削除処理に伴うマスタ処理失敗", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

// 2004.03.10 suzukaze update start
	// 該当仕入削除による状態変更関数呼び出し
	if ( fncStockDeleteSetStatus( $aryStockResult, $objDB ) != 0 )
	{
		fncOutputError( 9051, DEF_ERROR, "データが異常です", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
// 2004.03.10 suzukaze update end

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryStockResult;
	$aryDeleteData["strAction"] = "/pc/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/finish/remove_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
//////////////////// 削除確認画面表示 //////////////////
////////////////////////////////////////////////////////
// 該当仕入の状態が「締め済」の状態であれば
if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_APPLICATE )
{
	fncOutputError( 712, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 該当仕入の状態が「締め済」の状態であれば
if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
{
	fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

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

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryStockDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 703, DEF_WARNING, "仕入番号に対する明細が存在しません", FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

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