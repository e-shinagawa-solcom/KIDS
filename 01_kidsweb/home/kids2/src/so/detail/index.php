<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  詳細
*
*       処理概要
*         ・指定受注番号データの詳細表示処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "so/cmn/lib_so.php");
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

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
// 権限確認
// 402 受注管理（受注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 404 受注管理（詳細表示）
if ( !fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
//詳細画面の表示
$lngReceiveNo = $aryData["lngReceiveNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];
// 指定受注番号の受注データ取得用SQL文の作成
$strQuery = fncGetReceiveHeadNoToInfoSQL( $lngReceiveNo, $lngRevisionNo);
// 詳細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	else
	{
		fncOutputError( 403, DEF_ERROR, "該当データの取得に失敗しました", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
}
else
{
	fncOutputError( 403, DEF_ERROR, "データが異常です", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );
// 取得データの調整
$aryNewResult = fncSetReceiveHeadTabelData ( $aryResult );
////////// 明細行の取得 ////////////////////
// 指定受注番号の受注明細データ取得用SQL文の作成
// $aryData["strreceivecode2"] = $aryResult["strreceivecode2"];
// $aryData["lngrevisionno"] = $aryResult["lngrevisionno"];
$strQuery = fncGetReceiveDetailNoToInfoSQL ($lngReceiveNo, $lngRevisionNo);

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		// var_dump($aryDetailResult);
		// echo "tst";
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "受注番号に対する明細情報が見つかりません。", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

// 明細情報の出力
for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetReceiveDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/detail/so_parts_detail.html" );

	// テンプレート生成
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/detail/so_detail.html" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>