<?php
// ----------------------------------------------------------------------------
/**
 *       仕入管理 登録画面の発注No取得ボタン
 *
 *       処理概要
 *         ・発注NOにより発注情報取得する
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "pc/cmn/lib_pc.php";
include 'JSON.php';


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

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 発注情報を取得
$aryOrderDetail = fncGetPoInfoSQL($aryData["strOrderCode"], $objDB);

// 消費税情報を取得
$taxObj = fncGetTaxInfo($aryData["dtmStockAppDate"], $objDB);

$aryTaxclass = fncGetTaxClassAry($objDB);

// レートタイプ
$result["taxclass"] = $aryTaxclass;
$result["tax"] = $taxObj;
$result["orderdetail"] = $aryOrderDetail;

$objDB->close();

//結果出力
echo $s->encodeUnsafe($result);
