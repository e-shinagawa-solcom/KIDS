<?
/**
 *    見積原価管理 TOP画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 *
 */
// index.php -> strSessionID    -> index.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

if ($_GET["strSessionID"]) {
    $aryData["strSessionID"] = $_GET["strSessionID"];
} else {
    $aryData["strSessionID"] = $_POST["strSessionID"];
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_UC0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

$aryData["visibility1"] = "visible";
$aryData["visibility2"] = "hidden";
$aryData["upload_visibility"] = 'visible';

if (fncCheckAuthority(DEF_FUNCTION_E2, $objAuth)) {
    $aryData["Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Search_visibility"] = 'style="visibility: hidden"';
}

// アップロード
if (!fncCheckAuthority(DEF_FUNCTION_UP0, $objAuth)) {
    $aryData["Upload_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Upload_visibility"] = 'style="visibility: hidden"';
}

// ユーザーコード取得
$lngUserCode = $objAuth->UserCode;

$objDB->close();

// ヘルプリンク用機能コードをセット
$aryData["lngFunctionCode"] = DEF_FUNCTION_E0;

$aryData["lngFunctionCode1"] = DEF_FUNCTION_E1;

// HTML出力
echo fncGetReplacedHtmlWithBase("base_mold.html", "estimate/parts.tmpl", $aryData, $objAuth);
