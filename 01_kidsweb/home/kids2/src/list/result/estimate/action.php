<?
/** 
*	帳票出力 見積原価計算 印刷完了画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*/
// 印刷プレビュー画面( * は指定帳票のファイル名 )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

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
$aryCheck["strReportKeyCode"]   = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 指定キーコードの帳票データを取得
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_ESTIMATE, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum === 1 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strListOutputPath = $objResult->strreportpathname;
	unset ( $objResult );
	$objDB->freeResult( $lngResultID );
	//echo "コピーファイル有り。";
}

// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ( $lngResultNum === 0 )
{
	// 見積原価マスタデータ取得
	$aryEstimateData = fncGetEstimate( $aryData["strReportKeyCode"], $objDB );


	// コメント（バッファ）取得
	$strBuffRemark	= $aryEstimateData["strRemark"];

//fncDebug( 'es_list.txt', $aryEstimateData, __FILE__, __LINE__);


	// 見積原価のデフォルト値に対する入力値の取得
	// 2005/06/10 ABE Yuuki
	//受注価額を出すために実績納価/curReceiveProductPriceを引数に追加
	$aryDefaultValue = fncGetEstimateDefaultValue( $aryData["strReportKeyCode"], $aryEstimateData["lngReceiveProductQuantity"], 
		$aryEstimateData["lngProductionQuantity"], $aryEstimateData["curProductPrice"], $aryRate, $objDB , $aryEstimateData["curReceiveProductPrice"]);
	//old
	//$aryDefaultValue = fncGetEstimateDefaultValue( $aryData["strReportKeyCode"], $aryEstimateData["lngReceiveProductQuantity"], 
	//	$aryEstimateData["lngProductionQuantity"], $aryEstimateData["curProductPrice"], $aryRate, $objDB );

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

	$strBodyHtml = $objTemplate->strTemplate;

	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ↓
	$strHtml = $strBodyHtml;
	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ↑

	$objDB->transactionBegin();

	// シーケンス発行
	$lngSequence = fncGetSequence( "t_Report.lngReportCode", $objDB );

	// 帳票テーブルにINSERT
	$strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_ESTIMATE . ", " . $aryData["strReportKeyCode"] . ", '', '$lngSequence' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 帳票ファイルオープン
	if ( !$fp = fopen ( SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w" ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "帳票ファイルのオープンに失敗しました。", TRUE, "", $objDB );
	}

	// 帳票ファイルへの書き込み
	if ( !fwrite ( $fp, $strHtml ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "帳票ファイルの書き込みに失敗しました。", TRUE, "", $objDB );
	}

	$objDB->transactionCommit();
	//echo "コピーファイル作成";
}
//echo "<script language=javascript>window.form1.submit();window.returnValue=true;window.close();</script>";
echo "<script language=javascript>parent.window.close();</script>";


$objDB->close();



return TRUE;
?>
