<?php

/**
*
*	@charset	: UTF-8
*/

ini_set("default_charset", "UTF-8");

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_FILE );										// ライブラリ読み込み

require ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
require ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");
require_once ( SRC_ROOT . "/estimate/cmn/estimatePreviewController.php");

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

$objDB			= new estimateDB();
$objAuth		= new clsAuth();
$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成

$charset = 'UTF-8';

$objReader      = new XlsxReader();

$mode = workSheetConst::MODE_ESTIMATE_DOWNLOAD; // 処理モード：ダウンロード

//-------------------------------------------------------------------------
// DBオープン
//-------------------------------------------------------------------------
$objDB->InputEncoding = 'UTF-8';
$objDB->open( "", "", "", "" );

//-------------------------------------------------------------------------
// パラメータ取得
//-------------------------------------------------------------------------
$aryData	= array();
$aryData	= $_GET;

//-------------------------------------------------------------------------
// 入力文字列値・セッション・権限チェック
//-------------------------------------------------------------------------

// 文字列チェック
$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
$aryResult	= fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ユーザーコード取得
$lngUserCode = $objAuth->UserCode;



// 権限確認
if( !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 権限グループコードの取得
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

// GETパラメータよりパラメータを取得
$estimateNo = $aryData['estimateNo']; // 製品コード

// リビジョン番号の取得
if (isset($_POST['revisionNo'])) {
	$revisionNo = $_POST['revisionNo'];
	$estimate = $objDB->getEstimateDetail($estimateNo, $revisionNo);
} else {
	// リビジョン番号がPOSTされなかった場合は最新のデータを取得する
	$estimate = $objDB->getEstimateDetail($estimateNo);
}

$firstEstimateDetail = current($estimate);

if (!isset($revisionNo)) {
	$revisionNo = $firstEstimateDetail->lngrevisionno;
}

// 最大のリビジョン番号の取得
if (isset($_POST['maxRevisionNo'])) {
	$maxRevisionNo = $_POST['maxRevisionNo'];
} else {
	$result = $objDB->getEstimateDetail($estimateNo);
	if ($result) {
		$firstRecord = current($result);
		$maxRevisionNo = $firstRecord->lngrevisionno;
	}
}

$productCode = $firstEstimateDetail->strproductcode;
$reviseCode = $firstEstimateDetail->strrevisecode;
$productRevisionNo = $firstEstimateDetail->lngproductrevisionno;

// 製品マスタの情報取得
$product = $objDB->getProduct($productCode, $reviseCode, $productRevisionNo);

$objPreview = new estimatePreviewController();
$objPreview->dataInitialize($product, $estimate, $objDB);

$productData = $objPreview->getProduct();

$estimateData = $objPreview->getEstimate();


$tempFilePath = EXCEL_TMP_ROOT. 'workSheetTmp.xlsx';

$spreadSheet = $objReader->load($tempFilePath);

// 必要な定数を取得する
$nameList = workSheetConst::getAllNameListForDownload();
$rowCheckNameList = workSheetConst::DETAIL_HEADER_CELL_NAME_LIST;
$targetAreaList = workSheetConst::TARGET_AREA_DISPLAY_NAME_LIST;

// phpSpreadSheetオブジェクトからシートの情報を取得
$allSheetInfo = estimateSheetController::getSheetInfo($spreadSheet, $nameList, $rowCheckNameList);

$sheetInfo = estimateSheetController::getFirstElement($allSheetInfo);

if ($sheetInfo['displayInvalid']) {
	// テンプレート用のシートが無効になっていない場合、エラーを出力する
	if ( !$sheetDataList ) {
		$strMessage = 'テンプレート異常';

		// [strErrorMessage]書き出し
		$aryHtml["strErrorMessage"] = $strMessage;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		exit;
	}
}

$objSheet = null;

// シートが表示無効でない場合はワークシート処理オブジェクトのインスタンス生成
$objSheet = new estimateSheetController();

// オブジェクトにデータをセットする
$objSheet->dataInitialize($sheetInfo, $objDB);

// ワークシートオブジェクトに必要な値をセット
$objSheet->setDBEstimateData($productData, $estimateData, $mode);
fncDebug("dl.log", "set excel data", __FILE__, __LINE__, "a");
// phpSpreadSheetオブジェクトをxlsxに書き込むオブジェクトにセットする
$writer = new XlsxWriter($spreadSheet);
fncDebug("dl.log", "create excel writer", __FILE__, __LINE__, "a");


$time = new DateTime();
$time->setTimezone(new DateTimeZone('JST')); // 日本標準時をセット

$fileName = FILE_DOWNLOAD_TMPDIR. $time->format('YmdHisu');

$DlFileName = 'estimate_'. $productCode. '_'. $reviseCode. '_'. $time->format('Ymd');

$excelDLFileName = $DlFileName. '.xlsx';

$excelFileName = $fileName. '.xlsx';

$writer->save($excelFileName);
fncDebug("dl.log" . "write excel file", __FILE__, __LINE__, "a");

$zipFileName = $time->format('YmdHisu'). '.zip';

$zip = new ZipArchive;

$zip->open($zipFileName, ZipArchive::CREATE|ZipArchive::OVERWRITE);
$zip->addFile($excelFileName);
$zip->renameName($excelFileName, mb_convert_encoding($excelDLFileName, 'SJIS', 'UTF-8')); // ZipArchive内でSJISを扱うので文字コードを変換する
$zip->close();

unlink($excelFileName);

$zipDLFileName = $DlFileName. '.zip';

// zip出力
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.$zipDLFileName.'"');
header('Content-Length: ' . filesize($zipFileName));
echo file_get_contents($zipFileName);

unlink($zipFileName);

?>
