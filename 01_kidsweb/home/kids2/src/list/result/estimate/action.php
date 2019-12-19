<?
/**
 *    帳票出力 見積原価計算 印刷完了画面
 *
 *    @package   KIDS
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 */
// 印刷プレビュー画面( * は指定帳票のファイル名 )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// 設定読み込み
include_once 'conf.inc';
require LIB_DEBUGFILE;


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
if ($_POST) {
    $aryData = $_POST;
} elseif ($_GET) {
    $aryData = $_GET;
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["strReportKeyCode"] = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 指定キーコードの帳票データを取得
$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_ESTIMATE, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum === 1) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strListOutputPath = $objResult->strreportpathname;
    unset($objResult);
    $objDB->freeResult($lngResultID);
    //echo "コピーファイル有り。";
}
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
$aryParts["lngestimateno"] = $firstEstimateDetail->lngestimateno;
$aryParts["lngrevisionno"] = $maxRevisionNo;
if ($lngResultNum === 1) {	
    // 印刷回数を更新する
	fncUpdatePrintCount(DEF_REPORT_ESTIMATE, $aryParts, $objDB);
// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
}
// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ($lngResultNum === 0) {
    $productCode = $firstEstimateDetail->strproductcode;
    $reviseCode = $firstEstimateDetail->strrevisecode;
    $productRevisionNo = $firstEstimateDetail->lngproductrevisionno;

    // 製品マスタの情報取得
    $product = $objDB->getProduct($productCode, $reviseCode, $productRevisionNo);

    $objPreview = new estimatePreviewController();
    $objPreview->dataInitialize($product, $estimate, $objDB);

    $productData = $objPreview->getProduct();

    $estimateData = $objPreview->getEstimate();

    $tempFilePath = EXCEL_TMP_ROOT . 'workSheetPrintTmp.xlsx';

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
        if (!$sheetDataList) {
            $strMessage = 'テンプレート異常';

            // [strErrorMessage]書き出し
            $aryHtml["strErrorMessage"] = $strMessage;

            // テンプレート読み込み
            $objTemplate = new clsTemplate();
            $objTemplate->getTemplate("/result/error/parts.tmpl");

            // テンプレート生成
            $objTemplate->replace($aryHtml);
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

    // ワークシートオブジェクトに必要な値をセット
    $objSheet->setDBEstimateData($productData, $estimateData);

    $objWriter = new HtmlWriter($spreadSheet);

// 置換文字列生成
    $time = new DateTime();
    $replace = '_%' . md5($time->format('YmdHisu')) . '%_';

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

    $strHtml = $output;

    $objDB->transactionBegin();

    // シーケンス発行
    $lngSequence = fncGetSequence("t_Report.lngReportCode", $objDB);

    // 帳票テーブルにINSERT
    $strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_ESTIMATE . ", " . $aryData["strReportKeyCode"] . ", '', '$lngSequence' )";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

	$objDB->freeResult($lngResultID);
	
    // 印刷回数を更新する
    fncUpdatePrintCount(DEF_REPORT_ESTIMATE, $aryParts, $objDB);

    // 帳票ファイルオープン
    if (!$fp = fopen(SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w")) {
        list($lngResultID, $lngResultNum) = fncQuery("ROLLBACK", $objDB);
        fncOutputError(9059, DEF_FATAL, "帳票ファイルのオープンに失敗しました。", true, "", $objDB);
    }

    // 帳票ファイルへの書き込み
    if (!fwrite($fp, $strHtml)) {
        list($lngResultID, $lngResultNum) = fncQuery("ROLLBACK", $objDB);
        fncOutputError(9059, DEF_FATAL, "帳票ファイルの書き込みに失敗しました。", true, "", $objDB);
    }

    $objDB->transactionCommit();
    //echo "コピーファイル作成";
}
//echo "<script language=javascript>window.form1.submit();window.returnValue=true;window.close();</script>";
echo "<script language=javascript>parent.window.close();</script>";

$objDB->close();

return true;
