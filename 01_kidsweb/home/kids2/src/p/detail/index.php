<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  詳細
 *
 *       処理概要
 *         ・指定製品番号データの詳細表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;

// require (LIB_ROOT . "clscache.php" );
require (SRC_ROOT . "p/cmn/lib_ps1.php");
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
// 302 商品管理（商品検索）
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 304 商品管理（詳細表示）
if (!fncCheckAuthority(DEF_FUNCTION_P4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

$lngProductNo = $aryData["lngProductNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// 指定商品番号の商品データ取得用SQL文の作成
$strQuery = fncGetProductNoToInfoSQL($lngProductNo, $lngRevisionNo);

// 詳細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(303, DEF_ERROR, "該当データの取得に失敗しました", true, "../p/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    }
} else {
    fncOutputError(303, DEF_ERROR, "データが異常です", true, "../p/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// 取得データの調整
$aryNewResult = fncSetProductTableData($aryResult, $objDB);

// 帳票出力対応
// 表示対象が削除データの場合はプレビューボタンを表示しない
// なお権限を持ってない場合もプレビューボタンを表示しない
if (!$aryResult["bytInvalidFlag"] && fncCheckAuthority(DEF_FUNCTION_LO1, $objAuth)) {
    $aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] 
    . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $aryResult["lnggoodsplancode"] . "&bytCopyFlag=TRUE";

    $aryNewResult["listview"] = 'style="visibility: visible"';
} else {
    $aryNewResult["listview"] = 'style="visibility: hidden"';
}

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("p/detail/p_detail.html");

// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();
return true;
