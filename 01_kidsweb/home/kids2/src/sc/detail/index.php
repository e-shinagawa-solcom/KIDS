<?php

// ----------------------------------------------------------------------------
/**
 *       売上管理  詳細
 *
 *       処理概要
 *         ・指定売上番号データの詳細表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;

require SRC_ROOT . "sc/cmn/lib_scs.php";
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

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
// 権限確認
// 602 売上管理（売上検索）
if (!fncCheckAuthority(DEF_FUNCTION_SC2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 604 売上管理（詳細表示）
if (!fncCheckAuthority(DEF_FUNCTION_SC4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

$lngSalesNo = $aryData["lngSalesNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// 指定売上番号の売上データ取得用SQL文の作成
$strQuery = fncGetSalesHeadNoToInfoSQL($lngSalesNo, $lngRevisionNo);

// 詳細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(603, DEF_ERROR, "該当データの取得に失敗しました", true, "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    }
} else {
    fncOutputError(603, DEF_ERROR, "データが異常です", true, "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// 取得データの調整
$aryNewResult = fncSetSalesHeadTabelData($aryResult);

////////// 明細行の取得 ////////////////////
// 指定売上番号の売上明細データ取得用SQL文の作成
$strQuery = fncGetSalesDetailNoToInfoSQL($lngSalesNo, $lngRevisionNo);
// 明細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(603, DEF_WARNING, "売上番号に対する明細情報が見つかりません。", false, "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// 明細情報の出力
for ($i = 0; $i < count($aryDetailResult); $i++) {

    $aryNewDetailResult[$i] = fncSetSalesDetailTabelData($aryDetailResult[$i], $aryNewResult);

    $aryNewDetailResult[$i]["lngmonetaryunitcode"] = $aryNewResult["strmonetaryunitname"];
    $aryNewDetailResult[$i]["curconversionrate"] = $aryNewResult["curconversionrate"];

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("sc/detail/sc_parts_detail.html");

    // テンプレート生成
    $objTemplate->replace($aryNewDetailResult[$i]);
    $objTemplate->complete();

    // HTML出力
    $aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode("\n", $aryDetailTable);

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("sc/detail/sc_detail.html");

// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();
return true;
