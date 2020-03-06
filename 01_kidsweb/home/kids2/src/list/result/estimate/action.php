<?
/**
 *    帳票出力 見積原価計算 印刷プレビュー画面
 *
 *    @package   KIDS
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 */
// 見積原価 印刷プレビュー画面
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

ini_set("default_charset", "UTF-8");

// 設定読み込み
include_once 'conf.inc';
require LIB_DEBUGFILE;

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";
require SRC_ROOT . "list/result/estimate/estimate.php";
require_once VENDOR_AUTOLOAD_FILE;

require_once SRC_ROOT . "/estimate/cmn/estimateDB.php";
require_once SRC_ROOT . "/estimate/cmn/estimatePreviewController.php";

require_once SRC_ROOT . "estimate/cmn/estimateSheetController.php";
require_once SRC_ROOT . "estimate/cmn/makeHTML.php";

use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

$objDB = new estimateDB();
$objAuth = new clsAuth();

$objDB->InputEncoding = 'UTF-8';
$objDB->open("", "", "", "");

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
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
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
$aryParts["lngprintcount"] = $firstEstimateDetail->lngprintcount;

// 印刷回数を更新する
fncUpdatePrintCount(DEF_REPORT_ESTIMATE, $aryParts, $objDB);

echo "<script language=javascript>parent.window.close();</script>";

$objDB->close();

return true;
