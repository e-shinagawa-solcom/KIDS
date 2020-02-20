<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  削除完了
 *
 *       処理概要
 *         ・指定仕入番号データの削除処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require LIB_EXCLUSIVEFILE;
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
// 706 仕入管理（削除）
if (!fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 仕入番号の取得
$lngStockNo = $aryData["lngStockNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];
// トランザクション開始
$objDB->transactionBegin();

// エラー画面での戻りURL
$strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

// 対象データロック
if(!lockStock($lngStockNo, $objDB)){
    fncOutputError(703, DEF_ERROR, "該当データのロックに失敗しました", true, $strReturnPath, $objDB);
}

// 締めチェック
if(isStockClosed($lngStockNo, $objDB)){
    fncOutputError(711, DEF_WARNING, "", true, $strReturnPath, $objDB);
}

// 更新有無チェック
if(isStockModified($lngStockNo, $lngRevisionNo, $objDB)){
    fncOutputError(711, DEF_WARNING, "", true, $strReturnPath, $objDB);
}

// 削除対象の仕入NOの仕入情報取得
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo, $lngRevisionNo);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryStockResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(703, DEF_ERROR, "該当データの取得に失敗しました", true, $strReturnPath, $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "データが異常です", true, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

$strStockCode = $aryStockResult["strstockcode"];

$aryQuery[] = "INSERT INTO m_stock (lngStockNo, lngRevisionNo, "; // 仕入NO、リビジョン番号
$aryQuery[] = "strStockCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate"; // 仕入コード、入力者コード、無効フラグ、登録日
$aryQuery[] = ") values (";
$aryQuery[] = $lngStockNo . ", "; // 1:仕入番号
$aryQuery[] = -1 . ", "; // 2:リビジョン番号
$aryQuery[] = "'" . $strStockCode . "', "; // 3:仕入コード．
$aryQuery[] = $objAuth->UserCode . ", "; // 4:入力者コード
$aryQuery[] = "false, "; // 5:無効フラグ
$aryQuery[] = "now()"; // 6:登録日
$aryQuery[] = ")";

unset($strQuery);
$strQuery = implode("\n", $aryQuery);

if (!list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB)) {
    fncOutputError(702, DEF_FATAL, "削除処理に伴うマスタ処理失敗", true, $strReturnPath, $objDB);
}
$objDB->freeResult($lngResultID);

// 発注更新
$aryQuery = array();
$aryQuery[] = "UPDATE m_order ";
$aryQuery[] = "set lngorderstatuscode = " . DEF_ORDER_ORDER . " ";
$aryQuery[] = "WHERE (lngorderno, lngrevisionno) in ( ";
$aryQuery[] = "SELECT  ";
$aryQuery[] = "lngorderno,  ";
$aryQuery[] = "lngorderrevisionno ";
$aryQuery[] = "FROM t_stockdetail ";
$aryQuery[] = "WHERE lngstockno = " . $lngStockNo . " ";
$aryQuery[] = "AND lngrevisionno =" . $lngRevisionNo . " ";
$aryQuery[] = ")";
$strQuery = implode("\n", $aryQuery);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

$objDB->freeResult($lngResultID);

// トランザクションコミット
$objDB->transactionCommit();

$objDB->close();

$aryResult["strStockCode"] = $strStockCode;
$aryResult["dtmStockAppDate"] = $aryStockResult["dtmstockappdate"];
$aryResult["strOrderCode"] = $aryStockResult["strordercode"];

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/pc/delete/pc_finish_delete.html");

// テンプレート生成
$objTemplate->replace($aryResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

return true;
