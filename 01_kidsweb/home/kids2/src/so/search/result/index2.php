<?php
// ----------------------------------------------------------------------------
/**
 *       受注検索 履歴取得イベント
 *
 *       処理概要
 *         ・受注コード、リビジョン番号により受注履歴情報を取得する
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------
// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "so/cmn/lib_sos.php";

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

$displayColumns = array();
// 表示項目の抽出
foreach ($aryData["displayColumns"] as $key) {
    $displayColumns[$key] = $key;
}

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// 受注履歴情報を取得SQL
$strQuery = fncGetReceivesByStrReceiveCodeSQL($aryData["strReceiveCode"], $aryData["lngReceiveDetailNo"], $aryData["lngRevisionNo"]);

// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

// 指定数以内であれば通常処理
for ($i = 0; $i < $lngResultNum; $i++) {
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();

// キー文字列を小文字に変換
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);

// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
// 詳細カラムを表示
$existsDetail = array_key_exists("btndetail", $displayColumns);
// 確定カラムを表示
$existsDecide = array_key_exists("btndecide", $displayColumns);
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// 確定取消カラムを表示
$existsCancel = array_key_exists("btncancel", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "登録日";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName["strreceivecode"] = "受注ＮＯ.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName["lngsalesclasscode"] = "売上区分";
$aryTableHeaderName["strgoodscode"] = "顧客品番";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["dtmdeliverydate"] = "納期";
$aryTableHeaderName["lngreceivestatuscode"] = "状態";
// $aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["lngrecordno"] = "明細行番号";
$aryTableHeaderName["curproductprice"] = "単価";
$aryTableHeaderName["lngproductunitcode"] = "単位";
$aryTableHeaderName["lngproductquantity"] = "数量";
$aryTableHeaderName["cursubtotalprice"] = "税抜金額";
$aryTableHeaderName["strdetailnote"] = "明細備考";
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
$index = 0;
// 検索結果件数分走査
foreach ($records as $i => $record) {
    
    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strreceivecode"] . "_" . $record["lngreceivedetailno"] . "_" . $record["lngrevisionno"]);

    // 項番
    $index +=1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $trBody->appendChild($tdIndex);

    // 詳細を表示
    if ($existsDetail) {
        // 詳細セル
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");

        // 詳細ボタンの表示
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngreceiveno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 確定項目を表示
    if ($existsDecide) {
        // 確定セル
        $tdDecide = $doc->createElement("td");
        $tdDecide->setAttribute("class", $exclude);
        $tdDecide->setAttribute("style", $bgcolor . "text-align: center;");
        // tr > td
        $trBody->appendChild($tdDecide);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    
    // ヘッダー部データ設定
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, false);

    // 確定取消項目を表示
    if ($existsCancel) {
        // 確定取消セル
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);
        $tdCancel->setAttribute("style", $bgcolor . "text-align: center;");
        // tr > td
        $trBody->appendChild($tdCancel);
    }


    $strHtml .= $doc->saveXML($trBody);

}

// // HTML出力
echo $strHtml;
