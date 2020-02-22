<?php
// ----------------------------------------------------------------------------
/**
 *       各検索 履歴取得イベント
 *
 *       処理概要
 *         ・コード、リビジョン番号により履歴情報を取得する
 *
 *       更新履歴
 *
 */

// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "search/cmn/lib_search.php";
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

// パラメータ取得
$type = $aryData["type"];
$strCode = $aryData["strCode"];
$lngRevisionNo = $aryData["lngRevisionNo"];
$lngDetailNo = $aryData["lngDetailNo"];
$displayColumns = array();
if ($aryData["displayColumns"]) {
    // 表示項目の抽出
    foreach ($aryData["displayColumns"] as $key) {
        $displayColumns[$key] = $key;
    }
    // キー文字列を小文字に変換
    $displayColumns = array_change_key_case($displayColumns, CASE_LOWER);
}
// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);
// コード、履歴データにより履歴取得SQL
$records = fncGetHistoryDataByPKSQL($type, $strCode, $lngRevisionNo, $lngDetailNo, $objDB);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();

// -------------------------------------------------------
// 各種ボタン権限チェック
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('so', $objAuth);

// ヘッダーの設定
if ($type == 'purchaseorder') { // 発注書
    $aryTableHeaderName = $aryTableHeaderName_PURORDER;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_PURORDER;
} else if ($type == 'po') { // 発注
    $aryTableHeaderName = $aryTableHeaderName_PO;
} else if ($type == 'so') { // 受注
    $aryTableHeaderName = $aryTableHeaderName_SO;
} else if ($type == 'sc') { // 売上    
    $aryTableHeaderName = $aryTableHeaderName_SC;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_SC;
} else if ($type == 'slip') { //納品書
    $aryTableHeadBtnName = $aryTableHeadBtnName_SLIP;
    $aryTableHeaderName = $aryTableHeaderName_SLIP;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_SLIP;
    $aryTableBackBtnName = $aryTableBackBtnName_SLIP;
    $displayColumns = null;
} else if ($type == 'pc') { // 仕入   
    $aryTableHeaderName = $aryTableHeaderName_PC;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_PC;
} else if ($type == 'inv') {
    $aryTableHeadBtnName = $aryTableHeadBtnName_INV;
    $aryTableHeaderName = $aryTableHeaderName_INV;
    $aryTableDetailHeaderName = $aryTableDetailHeaderName_INV;
    $aryTableBackBtnName = $aryTableBackBtnName_INV;
    $displayColumns = null;
} else if ($type == 'estimate') {
}

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
$index = 0;
// 検索結果件数分走査
foreach ($records as $i => $record) {
    if ($type == 'slip') {
        $strcode = $record["lngpkno"];
    } else {
        $strcode = $record["strcode"];
    }
    $lngrevisionno = $record["lngrevisionno"];
    $lngpkno = $record["lngpkno"];
    // 背景色設定
    $bgcolor = fncSetBgColor($type, $strcode, false, $objDB);

    $detailData = array();
    $rowspan == 0;

    // 請求書・仕入・売上・納品書の場合詳細データを取得する
    if ($type == 'inv' || $type == 'pc' || $type == 'sc' || $type == 'slip'|| $type == 'purchaseorder') {
        $detailData = fncGetDetailData($type, $lngpkno, $lngrevisionno, $objDB);
        $rowspan = count($detailData);
    }

    if ($rowspan == 0) {
        $rowspan = 1;
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    if ($type == 'so' || $type == 'po') {
        $trBody->setAttribute("id", $strcode . "_" . $record["lngdetailno"] . "_" . $lngrevisionno);
    } else {
        $trBody->setAttribute("id", $strcode . "_" . $lngrevisionno);
    }
    $trBody->setAttribute("class", 'detail');

    $aryTableHeaderName;
    // 項番
    // $index = $index + 1;
    // $subindex = $aryData["rownum"] . "." . $index;
    $subindex = $aryData["rownum"] . "." . $lngrevisionno;

    // 先頭ボタンの設定
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $aryAuthority, false, false, $subindex, 'sc', null);

    // ヘッダーデータの設定
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, false);

    // 明細部データ設定
    if (count($detailData) > 0) {
        fncSetDetailTable($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $record, $detailData, false, false);
    }

    // 末尾ボタンの設定
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $aryAuthority, false, false, $type);

    // tbody > tr
    $strHtml .= $doc->saveXML($trBody);
}

// HTML出力
echo $strHtml;