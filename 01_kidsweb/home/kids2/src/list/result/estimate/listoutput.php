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


ini_set("default_charset", "UTF-8");

// 設定読み込み
include_once('conf.inc');
require( LIB_DEBUGFILE );

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "list/result/estimate/estimate.php");
require_once ( VENDOR_AUTOLOAD_FILE );

require_once (SRC_ROOT . "/estimate/cmn/estimateDB.php");
require_once (SRC_ROOT . "/estimate/cmn/estimatePreviewController.php");

require_once ( SRC_ROOT . "estimate/cmn/estimateSheetController.php" );
require_once ( SRC_ROOT . "estimate/cmn/makeHTML.php" );

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

$objDB   = new estimateDB();
$objAuth = new clsAuth();

$objDB->InputEncoding = 'UTF-8';
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
	// GETパラメータよりパラメータを取得
	$estimateNo = $aryData["strReportKeyCode"]; // 見積原価番号

	// リビジョン番号の取得
	if (isset($aryData['revisionNo'])) {
		$revisionNo = $aryData['revisionNo'];
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


	$tempFilePath = EXCEL_TMP_ROOT. 'workSheetPrintTmp.xlsx';

	$objReader = new XlsxReader();

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

			$objDB->close();

			exit;
		}
	}

	$objSheet = null;

	// シートが表示無効でない場合はワークシート処理オブジェクトのインスタンス生成
	$objSheet = new estimateSheetController();

	// オブジェクトにデータをセットする
	$objSheet->dataInitialize($sheetInfo, $objDB);

	// phpSpreadSheetで生成したシートオブジェクトをグローバル参照用にセットする
	$sheet = $sheetInfo['sheet'];
	$cellAddressList = $sheetInfo['cellAddress'];

	// テンプレートの整形を行う
	$objSheet->templateAdjust($estimateData);

	// ワークシートオブジェクトに必要な値をセット
	$objSheet->setDBEstimateData($productData, $estimateData);

	$objDB->close();

	$objWriter = new HtmlWriter($spreadSheet);
}

// 置換文字列生成
$time = new DateTime();
$replace = '_%'. md5($time->format('YmdHisu')). '%_';

$output = $objWriter->generateHTMLHeader();
$output .= $objWriter->generateStyles();

$customCSS = "<style>";
$customCSS .= "table {table-layout: fixed; width: 950px; white-space:nowrap;}";
$customCSS .= "td {overflow: hidden;}";
$customCSS .= "</style>";

$output .= $customCSS;

// 文字化け対策：エクセルの¥マークを置換文字列に置換(UTF-8 → EUC-JPの変換時に上手く変換できないため)
$sheetData = str_replace('¥', $replace, $objWriter->generateSheetData());

$output .= $sheetData;

$output .= $objWriter->generateHTMLFooter();

$output = mb_convert_encoding($output, 'EUC-JP', 'UTF-8');

// 置換文字列をhtmlの円マーク出力文字に変換してhtmlを出力
echo str_replace($replace, '&yen;', $output);

return TRUE;
?>
