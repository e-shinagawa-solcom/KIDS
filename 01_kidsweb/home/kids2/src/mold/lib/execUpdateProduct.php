<?php

// 設定読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require_once SRC_ROOT . '/mold/lib/exception/SQLException.class.php';

// DBオープン
$objDB = new clsDB();
$objDB->open("", "", "", "");

//JSONクラスインスタンス化
$s = new Services_JSON();

$json_string = file_get_contents('php://input');
$condition = json_decode($json_string, true);

if (!$condition) {
    echo "無効な値が指定されました。";
    exit;
}

$_REQUEST = array_merge($_REQUEST, $condition);
// セッションが有効な場合
if ((new clsAuth())->isLogin($_REQUEST["strSessionID"], $objDB)) {
    $aryQuery[] = "UPDATE m_product ";
    $aryQuery[] = "SET";
    $aryQuery[] = "  strgoodscode = '" . $_REQUEST["GoodsCode"] . "'";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  strproductcode = '" . $_REQUEST["ProductCode"] . "'";
    $aryQuery[] = "  and strrevisecode = '" . $_REQUEST["ReviseCode"] . "'";
    $aryQuery[] = "  AND (strgoodscode is null OR strgoodscode = '')";
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultID) {
        throw new SQLException(
            "データ更新失敗しました。",
            $strQuery,
            $condition);
    }
    $objDB->freeResult($lngUpdateResultID);

//結果出力
    echo $s->encodeUnsafe($result);
}
// セッションが無効な場合
else {
    echo "無効なセッション";
}
