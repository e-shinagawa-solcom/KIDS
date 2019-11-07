<?php
// ----------------------------------------------------------------------------
/**
 *       売上検索 履歴取得イベント
 *
 *       処理概要
 *         ・売上コード、リビジョン番号により売上履歴情報を取得する
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------
// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "po/cmn/lib_pos.php";

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

$displayColumns = $aryData["displayColumns"];

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);


// 検索条件に一致する発注コードを取得するSQL文の作成
$strQuery = fncGetPurchseOrderByOrderCodeSQL($_REQUEST["strOrderCode"], $_REQUEST["lngRevisionNo"]);

// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

$objDB->freeResult($lngResultID);

// テーブル構成で検索結果を取得、ＨＴＭＬ形式で出力する
$strHtml = fncSetPurchaseOrderHtml($displayColumns, $aryResult, null, false, $_REQUEST["rownum"]);

echo $strHtml;

