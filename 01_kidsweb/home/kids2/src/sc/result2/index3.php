<?php

// ----------------------------------------------------------------------------
/**
*       売上管理  削除
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
*         ・指定売上番号データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "sc/cmn/lib_scd1.php");
require (SRC_ROOT . "sc/cmn/column_scd.php");
require (LIB_DEBUGFILE);

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
if ( !$aryData["lngSlipNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngSlipNo"]	  = "null:number(0,10)";

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 602 売上管理（売上検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 606 売上管理（売上削除）
if ( !fncCheckAuthority( DEF_FUNCTION_SC6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 削除対象の納品伝票番号の納品情報取得
$strQuery = fncGetSlipHeadNoToInfoSQL ( $aryData["lngSlipNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryHeadResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 603, DEF_ERROR, "データが異常です", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 削除処理実行 ////////////////////
////////////////////////////////////////////////////////
if( $aryData["strSubmit"] )
{
	// TODO:１．顧客の国が日本で、かつ納品書ヘッダに紐づく請求書明細が存在する（=紐づく売上ヘッダの請求書番号がnull以外）場合、削除エラー画面(U-06-06-2)で以下のメッセージを表示して処理を終了する。
	//「請求書発行済みのため、削除できません」

	// TODO:２．納品書明細に紐づく受注ステータスが「締済み」である場合、削除エラー画面(U-06-06-2)で以下のメッセージを表示して処理を終了する。
	//「締済みのため、削除できません」

	/* //参考
	// 該当売上の状態が「締め済」の状態であれば
	if ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_CLOSED )
	{
		fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
	*/

	// トランザクション開始
	$objDB->transactionBegin();

	// 売上データの削除
	$lngSalesNo = $aryHeadResult["lngsalesno"];
	if (!fncDeleteSales($lngSalesNo, $objDB, $objAuth))
	{
		fncOutputError ( 602, DEF_FATAL, "削除処理に伴う売上マスタ処理失敗", TRUE, "", $objDB );
	}

	// 納品書データの削除
	$strSlipCode = $aryHeadResult["strslipcode"];
	if (!fncDeleteSlip($strSlipCode, $objDB, $objAuth))	
	{
		fncOutputError ( 602, DEF_FATAL, "削除処理に伴う納品書マスタ処理失敗", TRUE, "", $objDB );
	}

	// 納品伝票明細に紐づく受注明細のステータス更新
	$lngSlipNo = $aryHeadResult["lngslipno"];
	if (!fncUpdateReceiveStatus($lngSlipNo, $objDB))
	{
		fncOutputError ( 602, DEF_FATAL, "削除処理に伴う受注明細テーブル処理失敗", TRUE, "", $objDB );
	}

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryHeadResult;
	$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	//日本語のみになったので不要
	//$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

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
// 該当売上の状態が「申請中」の状態であれば
if ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_APPLICATE )
{
	fncOutputError( 608, DEF_WARNING, "", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 該当売上の状態が「締め済」の状態であれば
if ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_CLOSED )
{
	fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 取得データを表示用に整形
$aryNewResult = fncSetSlipHeadTableData ( $aryHeadResult );
// ヘッダ部のカラム名の設定（キーの頭に"CN"を付与する）
$aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );
// 詳細部のカラム名の設定（キーの頭に"CN"を付与する）
$aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

////////// 明細行の取得 ////////////////////

// 指定売上番号の売上明細データ取得用SQL文の作成
$strQuery = fncGetSlipDetailNoToInfoSQL ( $aryData["lngSlipNo"] );

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
	$strMessage = fncOutputError( 603, DEF_WARNING, "納品伝票番号に対する明細が存在しません", FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($arySalesDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetSlipDetailTableData ( $arySalesDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/result2/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames_CN );
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

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/result2/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames_CN );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>