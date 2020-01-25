<?php
// ----------------------------------------------------------------------------
/**
 *       仕入管理 登録画面の適用レート取得イベント
 *
 *       処理概要
 *         ・通貨単位コード、通貨レートコードにより適用レート情報を取得する
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "pc/cmn/lib_pc.php";

//値の取得
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();
//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}


$strFile = file_get_contents(LIB_ROOT . "sql/" . $aryData["lngProcessID"] . ".sql");

$strQuery = str_replace('_%strFormValue0%_', $aryData["strFormValue"], $strFile);

$result["pulldown"] = fncGetPulldownQueryExec($strQuery, '', $objDB, 2);

$objDB->close();
echo $s->encodeUnsafe($result);
