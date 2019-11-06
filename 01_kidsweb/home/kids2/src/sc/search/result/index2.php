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
require SRC_ROOT . "sc/cmn/lib_sc.php";

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
// 売上履歴情報取得SQL
$strQuery = fncGetSalesByStrSalesCodeSQL($aryData["strSalesCode"], $aryData["lngRevisionNo"]);

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
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SC11, $objAuth);

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "登録日";
$aryTableHeaderName["dtmappropriationdate"] = "請求日";
$aryTableHeaderName["strsalescode"] = "売上NO.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName["strslipcode"] = "納品書NO.";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngsalesstatuscode"] = "状態";
$aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["curtotalprice"] = "合計金額";
$aryTableDetailHeaderName["lngrecordno"] = "明細行番号";
$aryTableDetailHeaderName["strproductcode"] = "製品コード";
$aryTableDetailHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableDetailHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableDetailHeaderName["strproductname"] = "製品名";
$aryTableDetailHeaderName["lngsalesclasscode"] = "売上区分";
$aryTableDetailHeaderName["strgoodscode"] = "顧客品番";
$aryTableDetailHeaderName["curproductprice"] = "単価";
$aryTableDetailHeaderName["lngproductunitcode"] = "単位";
$aryTableDetailHeaderName["lngproductquantity"] = "数量";
$aryTableDetailHeaderName["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName["lngtaxclasscode"] = "税区分";
$aryTableDetailHeaderName["curtax"] = "税率";
$aryTableDetailHeaderName["curtaxprice"] = "税額";
$aryTableDetailHeaderName["strdetailnote"] = "明細備考";

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {
    unset($aryQuery);
    // 明細番号
    $detailnos = "";

    // 詳細データを取得する
    $detailData = fncGetDetailData($record["lngsalesno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // 明細番号取得
    for ($i = $rowspan; $i > 0; $i--) {
        if ($detailnos == "") {
            $detailnos = $detailData[$i]["lngsalesdetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lngsalesdetailno"];
        }
    }
    if ($rowspan == 0) {
        $rowspan = 1;
        $detailnos = "";
    }
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strsalescode"] . "_" . $record["lngrevisionno"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // 項番
    $index +=1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $tdIndex->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($tdIndex);
    // 詳細を表示
    if ($existsDetail) {
        // 詳細セル
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor. "text-align: center;");
        $tdDetail->setAttribute("rowspan", $rowspan);

        // 詳細ボタンの表示
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngsalesno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor. "text-align: center;");
        $tdHistory->setAttribute("rowspan", $rowspan);
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 登録日
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 請求日
                case "dtmappropriationdate":
                    $td = $doc->createElement("td", $record["dtmappropriationdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 売上NO.
                case "strsalescode":
                    $td = $doc->createElement("td", $record["strsalescode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // リビジョン番号
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 納品書NO.
                case "strslipcode":
                    $td = $doc->createElement("td", $record["strslipcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [入力者表示コード] 入力者表示名
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [顧客表示コード] 入力者表示名
                case "lngcustomercompanycode":
                    if ($record["strcustomercompanycode"] != '') {
                        $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 状態
                case "lngsalesstatuscode":
                    $td = $doc->createElement("td", $record["strsalesstatusname"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 備考
                case "strnote":
                    $td = $doc->createElement("td", $record["strnote"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 合計金額
                case "curtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }

    // 明細データの設定
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[0], $record["lngmonetaryunitcode"], $record["strmonetaryunitsign"]);

    $strHtml .= $doc->saveXML($trBody);
    // 明細行のtrの追加
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");

        $trBody->setAttribute("id", $record["strsalescode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngsalesdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i], $record["lngmonetaryunitcode"], $record["strmonetaryunitsign"]);

        $strHtml .= $doc->saveXML($trBody);
    }



}

// // HTML出力
echo $strHtml;
