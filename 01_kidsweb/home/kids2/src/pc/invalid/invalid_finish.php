<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  無効完了
 *
 *       処理概要
 *         ・指定仕入番号データの無効処理
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
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 707 仕入管理（仕入無効化）
if ( !fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 仕入番号の取得
$lngStockNo = $aryData["lngStockNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// 削除対象の仕入NOの仕入情報取得
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo, $lngRevisionNo);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryStockResult = $objDB->fetchArray($lngResultID, 0);
        // 該当仕入の状態が「締め済」の状態であれば
        if ($aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED) {
            fncOutputError(711, DEF_WARNING, "", true, "", $objDB);
        }
    } else {
        fncOutputError(703, DEF_ERROR, "該当データの取得に失敗しました", true, "", $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "データが異常です", true, "", $objDB);
}

$objDB->freeResult($lngResultID);

// トランザクション開始
$objDB->transactionBegin();

// 更新対象仕入データをロックする
$strLockQuery = "SELECT lngStockNo FROM m_Stock WHERE lngStockNo = " . $aryData["lngStockNo"] . " AND lngRevisionNo = " . $aryData["lngRevisionNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
echo $strLockQuery;
list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
if ( !$lngResultNum )
{
    fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
}
$objDB->freeResult( $lngResultID );

// 無効化確認
$strQuery = "UPDATE m_Stock SET bytInvalidFlag = TRUE WHERE lngStockNo = " . $aryData["lngStockNo"]. " AND lngRevisionNo = " . $aryData["lngRevisionNo"] . " AND bytInvalidFlag = FALSE";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
$objDB->freeResult( $lngResultID );

// トランザクションコミット
$objDB->transactionCommit();

$objDB->close();

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/pc/invalid/pc_finish_invalid.html" );

// テンプレート生成
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

return true;
