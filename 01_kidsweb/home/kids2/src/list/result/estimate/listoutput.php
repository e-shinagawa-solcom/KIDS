<?
/** 
*	帳票出力 見積原価計算 印刷プレビュー画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*/
// 見積原価 印刷プレビュー画面
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

// 設定読み込み
include_once('conf.inc');
require( LIB_DEBUGFILE );

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "list/result/estimate/estimate.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}


// 文字列チェック
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["lngReportCode"]      = "ascii(1,7)";
$aryCheck["strReportKeyCode"]   = "null:number(0,9999999)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) || !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 見積原価コピーファイルパス取得クエリ生成
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_ESTIMATE, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strReportPathName = $objResult->strreportpathname;
	unset ( $objResult );
}

$copyDisabled = "visible";

// コピーファイルパスが存在しない または
// 帳票コードが無い または コピーフラグが偽(コピー選択ではない) かつ
// コピー解除権限がある場合、
// コピーマークの非表示
if ( !$strReportPathName || ( !( $aryData["lngReportCode"] || $aryData["bytCopyFlag"] ) && fncCheckAuthority( DEF_FUNCTION_LO4, $objAuth ) ) )
{
	$copyDisabled = "hidden";
}


///////////////////////////////////////////////////////////////////////////
// 帳票コードが真の場合、ファイルデータを取得
///////////////////////////////////////////////////////////////////////////
if ( $aryData["lngReportCode"] )
{
	if ( !$lngResultNum )
	{
		fncOutputError ( 9056, DEF_FATAL, "帳票コピーがありません。", TRUE, "", $objDB );
	}

	if ( !$aryHtml[] =  file_get_contents ( SRC_ROOT . "list/result/cash/" . $strReportPathName . ".tmpl" ) )
	{
		fncOutputError ( 9059, DEF_FATAL, "帳票データファイルが開けませんでした。", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
}

///////////////////////////////////////////////////////////////////////////
// テンプレートと置き換えデータ取得
///////////////////////////////////////////////////////////////////////////
else
{
	// 見積原価マスタデータ取得
	$aryEstimateData = fncGetEstimate( $aryData["strReportKeyCode"], $objDB );
	// コメント（バッファ）取得
	$strBuffRemark	= $aryEstimateData["strRemark"];


	// 見積原価のデフォルト値に対する入力値の取得
	//受注価額を出すために実績納価/curReceiveProductPriceを引数に追加
	$aryDefaultValue = fncGetEstimateDefaultValue( $aryData["strReportKeyCode"], $aryEstimateData["lngReceiveProductQuantity"], 
		$aryEstimateData["lngProductionQuantity"], $aryEstimateData["curProductPrice"], $aryRate, $objDB, $aryEstimateData["curReceiveProductPrice"]);

	list ( $aryDetail, $aryOrderDetail ) = fncGetEstimateDetail( $aryData["strReportKeyCode"], $aryEstimateData["strProductCode"], $aryRate, $aryDefaultValue, $objDB );

	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, $aryOrderDetail, $aryDefaultValue, "list/result/e_detail.tmpl", "list/result/e_subject.tmpl", $objDB );

	unset ( $aryHiddenString );
	unset ( $aryRate );

	// 配列のマージ
	$aryEstimateData = array_merge( $aryEstimateData, $aryCalculated );

	// 標準割合取得
	$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

	// 社内USドルレート取得
	$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

	// 計算結果を取得
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	// カンマ処理
	$aryEstimateData = fncGetCommaNumber( $aryEstimateData );


	// コメント
	$aryEstimateData["strRemarkDisp"]	= nl2br($strBuffRemark);


	// ベーステンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/e_base.tmpl" );

	// ベーステンプレート生成
	$objTemplate->replace( $aryEstimateData );
	$objTemplate->replace( $aryDetail );
	$objTemplate->complete();

	// HTML出力
	$aryHtml[] = $objTemplate->strTemplate;
}

echo join( "\n", $aryHtml );

$objDB->close();


return TRUE;
?>
