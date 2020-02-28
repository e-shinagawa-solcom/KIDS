<?
/**
 *    帳票出力 請求書 印刷完了画面
 *
 */
// 印刷プレビュー画面( * は指定帳票のファイル名 )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "/list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";
require LIB_DEBUGFILE;

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
$aryCheck["strReportKeyCode"] = "null:number(0,99999999)";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// データ取得クエリ
$strQuery = fncGetListOutputQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $objDB);

$objMaster = new clsMaster();
$objMaster->setMasterTableData($strQuery, $objDB);

$aryParts = &$objMaster->aryData[0];

unset($aryQuery);

// 印刷回数を更新する
fncUpdatePrintCount(DEF_REPORT_INV, $aryParts, $objDB);

echo "<script language=javascript>parent.window.close();</script>";

$objDB->close();

return true;
