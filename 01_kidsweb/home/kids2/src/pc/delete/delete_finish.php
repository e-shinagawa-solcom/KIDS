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
// エラー画面での戻りURL
$strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];
// 削除対象の仕入NOの仕入情報取得
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo, $lngRevisionNo);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryStockResult = $objDB->fetchArray($lngResultID, 0);
        // 該当仕入の状態が「締め済」の状態であれば
        if ($aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED) {
            fncOutputError(711, DEF_WARNING, "", true, $strReturnPath, $objDB);
        }
    } else {
        fncOutputError(703, DEF_ERROR, "該当データの取得に失敗しました", true, $strReturnPath, $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "データが異常です", true, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

// トランザクション開始
$objDB->transactionBegin();

// 最小リビジョン番号の取得
$strStockCode = $aryStockResult["strstockcode"];
$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Stock WHERE strStockCode = '" . $strStockCode . "'";

list($lngResultID, $lngResultNum) = fncQuery($strRevisionGetQuery, $objDB);
if ($lngResultNum) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $lngMinRevisionNo = $objResult->minrevision;
    if ($lngMinRevisionNo > 0) {
        $lngMinRevisionNo = 0;
    }
} else {
    $lngMinRevisionNo = 0;
}
$objDB->freeResult($lngResultID);
$lngMinRevisionNo--;

$aryQuery[] = "INSERT INTO m_stock (lngStockNo, lngRevisionNo, "; // 仕入NO、リビジョン番号
$aryQuery[] = "strStockCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate"; // 仕入コード、入力者コード、無効フラグ、登録日
$aryQuery[] = ") values (";
$aryQuery[] = $lngStockNo . ", "; // 1:仕入番号
$aryQuery[] = $lngMinRevisionNo . ", "; // 2:リビジョン番号
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

// 該当仕入削除による状態変更関数呼び出し
if (fncStockDeleteSetStatus($aryStockResult, $objDB) != 0) {
    fncOutputError(9051, DEF_ERROR, "データが異常です", true, $strReturnPath, $objDB);
}

// トランザクションコミット
$objDB->transactionCommit();

$objDB->close();



$aryResult["strStockCode"] = $strStockCode;
$aryResult["dtmStockAppDate"] = $aryStockResult["dtmstockappdate"];
$aryResult["strOrderCode"] = $aryStockResult["strordercode"];

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/pc/delete/pc_finish_delete.html" );

// テンプレート生成
$objTemplate->replace($aryResult);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

return true;