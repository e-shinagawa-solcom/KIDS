<?php

// ----------------------------------------------------------------------------
/**
 *       発注書管理  詳細
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
 *         ・指定発注書番号データの詳細表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "po/cmn/lib_pos1.php";
require SRC_ROOT . "list/cmn/lib_lo.php";
require SRC_ROOT . "po/cmn/lib_por.php";
require SRC_ROOT . "po/cmn/column.php";

require LIB_DEBUGFILE;

// DB接続
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
if ($_GET) {
    $aryData = $_GET;
} else if ($_POST) {
    $aryData = $_POST;
}

if (!isset($aryData["lngPurchaseOrderNo"])) {
    fncOutputError(9061, DEF_ERROR, "データ異常です。", true, "", $objDB);
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngPurchaseOrderNo"] = "null:number(0,10)";
$aryCheck["lngRevisionNo"] = "null:number(0,10)";

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;

// 権限確認
// 502 発注管理（発注検索）
if (!fncCheckAuthority(DEF_FUNCTION_PO10, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 504 発注管理（詳細表示）
if (!fncCheckAuthority(DEF_FUNCTION_PO4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

//詳細画面の表示
$lngPurchaseOrderNo = $aryData["lngPurchaseOrderNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// 指定発注書番号の発注データ取得用SQL文の作成
$aryResult = fncGetPurchaseOrderEdit($lngPurchaseOrderNo, $lngRevisionNo, $objDB);
if (!$aryResult || count($aryResult) == 0) {
        fncOutputError(503, DEF_ERROR, "該当データの取得に失敗しました。", true, "", $objDB);
}
// 取得データの調整
$aryNewResult = fncSetPurchaseHeadTabelData($aryResult[0]);

////////// 明細行の取得 ////////////////////
// 発注書明細を取得
$strQuery = fncGetPurchaseOrderDetailSQL($lngPurchaseOrderNo, $lngRevisionNo);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
$aryDetailResult = array();
if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[$i] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    // fncOutputError(503, DEF_ERROR, "発注番号に対する明細情報が見つかりません。", true, "", $objDB);
}
$objDB->freeResult($lngResultID);

for ($i = 0; $i < count($aryDetailResult); $i++) {
    $aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData($aryDetailResult[$i], $aryNewResult);

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("po/result2/parts_detail.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryNewDetailResult[$i]);
    $objTemplate->complete();

    // HTML出力
    $aryDetailTable[] = $objTemplate->strTemplate;
}

if ($aryDetailTable != null) {
    $aryNewResult["strDetailTable"] = implode("\n", $aryDetailTable);
}

// 帳票出力対応
// 表示対象が削除データ、申請中データの場合はプレビューボタンを表示しない
// また帳票出力権限を持ってない場合もプレビューボタンは表示しない
if ($aryResult["lngrevisionno"] >= 0 and fncCheckAuthority(DEF_FUNCTION_LO2, $objAuth)) {
    $aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&bytCopyFlag=true&strReportKeyCode=" . $aryData["lngPurchaseOrderNo"] . "";

    $aryNewResult["listview"] = 'visible';
} else {
    $aryNewResult["listview"] = 'hidden';
}

$aryNewResult["strAction"] = "index2.php";
$aryNewResult["strMode"] = "detail";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("po/result2/parts2.tmpl");

// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();
return true;
