<?php

// ----------------------------------------------------------------------------
/**
 *       請求管理  請求書登録画面
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
 *         ・登録時の入力画面を表示
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "m/cmn/lib_m.php";
require SRC_ROOT . "inv/cmn/lib_regist.php";

// オブジェクト生成
$objDB = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// パラメータ取得
if ($_POST) {
    $aryData = $_POST;
} elseif ($_GET) {
    $aryData = $_GET;
}

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// cookieにSET
if (!empty($aryData["strSessionID"])) {
    setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
}

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// 2200 請求管理
if (!fncCheckAuthority(DEF_FUNCTION_INV0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 2202 請求書検索
if (!fncCheckAuthority(DEF_FUNCTION_INV2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 2203 請求管理（請求検索　管理モード）
if (fncCheckAuthority(DEF_FUNCTION_INV3, $objAuth)) {
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["AdminSet_visibility"] = 'style="visibility: hidden"';
}

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("inv/base_inv.html", "inv/search/search.tmpl", $aryData, $objAuth);

$objDB->close();

return true;
