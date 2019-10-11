<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  検索画面
 *
 *       処理概要
 *         ・検索画面表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定の読み込み
include_once "conf.inc";

// ライブラリ読み込み
require LIB_FILE;

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

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限チェック
// 702 仕入管理（仕入検索）
if (!fncCheckAuthority(DEF_FUNCTION_PC2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 703 仕入管理（仕入検索　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
    // 707 仕入管理（無効化）
    if (fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth)) {
        $aryData["btnInvalid_visibility"] = 'style="visibility: visible"';
        $aryData["btnInvalidVisible"] = "disabled";
    } else {
        $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
        $aryData["btnInvalidVisible"] = "disabled";
    }
}
else
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
    $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
    $aryData["btnInvalidVisible"] = "";
}

// 704 仕入管理（詳細表示）
if (fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth)) {
    $aryData["btnDetail_visibility"] = 'style="visibility: visible"';
    $aryData["btnDetailVisible"] = "checked";
} else {
    $aryData["btnDetail_visibility"] = 'style="visibility: hidden"';
    $aryData["btnDetailVisible"] = "";
}
// 705 仕入管理（修正）
if (fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth)) {
    $aryData["btnFix_visibility"] = 'style="visibility: visible"';
    $aryData["btnFixVisible"] = "checked";
} else {
    $aryData["btnFix_visibility"] = 'style="visibility: hidden"';
    $aryData["btnFixVisible"] = "";
}
// 706 仕入管理（削除）
if (fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth)) {
    $aryData["btnDelete_visibility"] = 'style="visibility: visible"';
    $aryData["btnDeleteVisible"] = "checked";
} else {
    $aryData["btnDelete_visibility"] = 'style="visibility: hidden"';
    $aryData["btnDeleteVisible"] = "";
}
// 仕入状態
$aryData["lngStockStatusCode"] = fncGetCheckBoxObject("m_stockstatus", "lngstockstatuscode", "strstockstatusname", "lngStockStatusCode[]", 'where lngStockStatusCode not in (1)', $objDB);
// 支払条件
$aryData["lngPayConditionCode"] = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", 0, '', $objDB);
// 運搬方法
$aryData["lngDeliveryMethodCode"] = fncGetPulldown("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", 0, '', $objDB);
// 仕入科目
$aryData["lngStockSubjectCode"] = fncGetPulldown("m_stocksubject", "lngstocksubjectcode", "lngstocksubjectcode,	strstocksubjectname", 1, '', $objDB);
// 仕入部品
$aryData["lngStockItemCode"] = fncGetPulldown("m_stockitem", "lngstocksubjectcode || '-' || lngstockitemcode", "lngstockitemcode, 	strstockitemname", 0, '', $objDB);

// 仕入部品復元用
$TmpAry = explode("\n", $aryData["lngStockItemCode"]);

foreach ($TmpAry as $key => $value) {
    if ($value) {
        $ValuePosS = 15;
        $ValuePosE = mb_strpos($value, ">", $ValuePosS) - 1;
        $DispPosS = $ValuePosE + 2;
        $DispPosE = mb_strpos($value, "OPTION", $DispPosS) - 2;
        if (array_key_exists('lngStockItemCodeValue', $aryData)) {
            $aryData["lngStockItemCodeValue"] = $aryData["lngStockItemCodeValue"] . ",," . substr($value, $ValuePosS, $ValuePosE - $ValuePosS);
            $aryData["lngStockItemCodeDisp"] = $aryData["lngStockItemCodeDisp"] . ",," . mb_ereg_replace("</OPTION>", "", substr($value, $DispPosS));
        } else {
            $aryData["lngStockItemCodeValue"] = substr($value, $ValuePosS, $ValuePosE - $ValuePosS);
            $aryData["lngStockItemCodeDisp"] = mb_ereg_replace("</OPTION>", "", substr($value, $DispPosS));
        }
    }
}

$aryData["lngStockItemCodeValue"] = "<input type=\"hidden\" name=\"lngStockItemCodeValue\" value=\"" . $aryData["lngStockItemCodeValue"] . "\"</option>";
$aryData["lngStockItemCodeDisp"] = mb_convert_encoding("<input type=\"hidden\" name=\"lngStockItemCodeDisp\" value=\"" . $aryData["lngStockItemCodeDisp"] . "\"</option>", "EUC-JP", "auto");

//　プルダウンリストの取得に失敗した場合エラー表示
if (!$aryData["lngStockStatusCode"] or !$aryData["lngPayConditionCode"] or !$aryData["lngStockSubjectCode"] or !$aryData["lngStockItemCode"]) {
    fncOutputError(9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", true, "", $objDB);
}

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "pc/search/pc_search.html", $aryData, $objAuth);

$objDB->close();

return true;
