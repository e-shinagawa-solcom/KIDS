<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  メニュー画面
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
 *         ・メニュー画面を表示
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;

$objDB = new clsDB();
$objAuth = new clsAuth();

$objDB->open("", "", "", "");

$aryData["strSessionID"] = $_POST["strSessionID"];

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($_POST["strSessionID"], $objAuth, $objDB);

// 300 商品管理
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

if (fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    $aryData["Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Search_visibility"] = 'style="visibility: hidden"';
}
// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_P0;

$objDB->close();

echo fncGetReplacedHtmlWithBase("base_mold.html", "p/parts.tmpl", $aryData, $objAuth);

return true;
