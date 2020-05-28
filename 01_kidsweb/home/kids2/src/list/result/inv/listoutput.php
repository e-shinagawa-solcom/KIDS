<?
/**
 *    帳票出力 請求書 印刷プレビュー画面
 *
 *
 */
// 帳票出力 印刷プレビュー画面
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";

$objDB = new clsDB();
$objAuth = new clsAuth();
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
$aryCheck["lngReportCode"] = "ascii(1,7)";
$aryCheck["strReportKeyCode"] = "null:number(0,9999999)";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_SO0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// データ取得クエリ
$strQuery = fncGetListOutputQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $objDB);

$objMaster = new clsMaster();
$objMaster->setMasterTableData($strQuery, $objDB);
$aryParts = &$objMaster->aryData[0];

unset($aryQuery);

// 詳細取得
$strQuery = fncGetInvDetailQuery($aryData["strReportKeyCode"], $aryParts["lngrevisionno"]);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum < 1) {
    fncOutputError(9051, DEF_FATAL, "帳票詳細データが存在しませんでした。", true, "", $objDB);
}

// フィールド名取得
for ($i = 0; $i < pg_num_fields($lngResultID); $i++) {
    $aryKeys[] = pg_field_name($lngResultID, $i);
}

// 行数だけデータ取得、配列に代入
for ($i = 0; $i < $lngResultNum; $i++) {
    $aryResult = $objDB->fetchArray($lngResultID, $i);
    for ($j = 0; $j < count($aryKeys); $j++) {
        $aryParts[$aryKeys[$j] . $i] = $aryResult[$j];
    }
}
$objDB->freeResult($lngResultID);

$objDB->close();

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/inv.html");
$aryParts["totalprice_unitsign"] = convertPrice($aryParts["lngmonetaryunitcode"], $aryParts["strmonetaryunitsign"], $aryParts["totalprice"], "price");
$aryParts["dtminvoicedate"] = date("Y年n月", strtotime($aryParts["dtminvoicedate"]));
$aryParts["dtminvoicedate_day"] = ltrim($aryParts["dtminvoicedate_day"], '0');
$aryParts["dtminsertdate"] = date("Y.n.j", strtotime($aryParts["dtminsertdate"]));
$aryParts["dtmchargeternstart_month"] = ltrim($aryParts["dtmchargeternstart_month"], '0');
$aryParts["dtmchargeternstart_day"] = ltrim($aryParts["dtmchargeternstart_day"], '0');
$aryParts["dtmchargeternend_month"] = ltrim($aryParts["dtmchargeternend_month"], '0');
$aryParts["dtmchargeternend_day"] = ltrim($aryParts["dtmchargeternend_day"], '0');

$aryParts["curthismonthamount"] = convertPrice($aryParts["lngmonetaryunitcode"], "", $aryParts["curthismonthamount"], "price");
$aryParts["curlastmonthbalance"] = convertPrice($aryParts["lngmonetaryunitcode"], "", $aryParts["curlastmonthbalance"], "price");
$aryParts["curtaxprice1"] = convertPrice($aryParts["lngmonetaryunitcode"], "", $aryParts["curtaxprice1"], "taxprice");
$aryParts["totalprice"] = convertPrice($aryParts["lngmonetaryunitcode"], "", $aryParts["totalprice"], "price");

// 置き換え
$objTemplate->replace($aryParts);
$objTemplate->complete();
$strHtml = $objTemplate->strTemplate;

echo $strHtml;
