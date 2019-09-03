<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  詳細
 *
 *       処理概要
 *         ・指定受注番号データの詳細表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "pc/cmn/lib_pc.php";
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
// 700 仕入管理
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 704 仕入管理（詳細表示）
if (!fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 仕入番号の取得
$lngStockNo = $aryData["lngStockNo"];
// エラー画面での戻りURL
$strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];
// 指定仕入番号の仕入データ取得用SQL文の作成
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo);
// echo $strQuery;
// return;
// 詳細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(703, DEF_ERROR, "該当データの取得に失敗しました", true, $strReturnPath, $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "データが異常です", true, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);
// 取得データの調整
$aryNewResult = fncSetStockHeadTabelData($aryResult);

// 指定仕入番号の仕入明細データ取得用SQL文の作成
$strQuery = fncGetStockDetailNoToInfoSQL($lngStockNo);
// 明細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(703, DEF_WARNING, "仕入番号に対する明細情報が見つかりません。", false, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

// 明細情報の出力
for ($i = 0; $i < count($aryDetailResult); $i++) {
    $aryNewDetailResult[$i] = fncSetStockDetailTabelData($aryDetailResult[$i], $aryNewResult);

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("pc/detail/pc_parts_detail.html");

    // テンプレート生成
    $objTemplate->replace($aryNewDetailResult[$i]);
    $objTemplate->complete();

    // HTML出力
    $aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode("\n", $aryDetailTable);

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("pc/detail/pc_detail.html");

// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();
return true;
