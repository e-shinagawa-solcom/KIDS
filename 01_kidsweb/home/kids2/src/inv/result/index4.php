<?php
// ----------------------------------------------------------------------------
/**
 *       請求書検索 履歴取得イベント
 *
 *       処理概要
 *         ・請求書コード、リビジョン番号により請求履歴情報を取得する
 *
 *       更新履歴
 *
 */

 // 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "inv/cmn/lib_regist.php";

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
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// 請求コードにより仕入履歴取得SQL
$strQuery = fncGetInvoicesByStrInvoiceCodeSQL($aryData["strInvoiceCode"], $aryData["lngRevisionNo"]);
// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

// 指定数以内であれば通常処理
for ($i = 0; $i < $lngResultNum; $i++) {
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
// 修正を表示
$allowedFix = fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth);
// 削除を表示
$allowedDelete = fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth);
// 無効カラムを表示
$allowedInvalid = fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth);

// ヘッダ部
$aryTableHeaderName["lngCustomerCode"] = "顧客";
$aryTableHeaderName["strInvoiceCode"] = "請求書No";
$aryTableHeaderName["dtmInvoiceDate"] = "請求日";
$aryTableHeaderName["curLastMonthBalance"] = "先月請求残額";
$aryTableHeaderName["curThisMonthAmount"] = "当月請求金額";
$aryTableHeaderName["curSubTotal1"] = "消費税額";
$aryTableHeaderName["dtmInsertDate"] = "作成日";
$aryTableHeaderName["lngUserCode"] = "担当者";
$aryTableHeaderName["lngInsertUserCode"] = "入力者";
$aryTableHeaderName["lngPrintCount"] = "印刷回数";
$aryTableHeaderName["strNote"] = "備考";

// 明細部
$aryTableDetailHeaderName["lngInvoiceDetailNo"] = "請求書明細番号";
$aryTableDetailHeaderName["dtmDeliveryDate"] = "納品日";
$aryTableDetailHeaderName["strSlipCode"] = "納品書NO";
$aryTableDetailHeaderName["lngDeliveryPlaceCode"] = "納品先";
$aryTableDetailHeaderName["curSubTotalPrice"] = "税抜金額";
$aryTableDetailHeaderName["lngTaxClassCode"] = "課税区分";
$aryTableDetailHeaderName["curDetailTax"] = "税率";
$aryTableDetailHeaderName["curTaxPrice"] = "消費額";
$aryTableDetailHeaderName["strDetailNote"] = "明細備考";

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {

    // 詳細データを取得する
    $detailData = fncGetDetailData($record["lnginvoiceno"], $record["lngrevisionno"], $objDB);

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
            $detailnos = $detailData[$i]["lnginvoicedetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lnginvoicedetailno"];
        }
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strinvoicecode"] . "_" . $record["lngrevisionno"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // 項番
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $index);
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $tdIndex->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($tdIndex);

    // 詳細セル
    $tdDetail = $doc->createElement("td");
    $tdDetail->setAttribute("class", $exclude);
    $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");
    $tdDetail->setAttribute("rowspan", $rowspan);

    // 詳細ボタンの表示
    if ($allowedDetail && $record["lngrevisionno"] >= 0) {
        // 詳細ボタン
        $imgDetail = $doc->createElement("img");
        $imgDetail->setAttribute("src", "/img/type01/pc/detail_off_bt.gif");
        $imgDetail->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
        $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
        $imgDetail->setAttribute("class", "detail button");
        // td > img
        $tdDetail->appendChild($imgDetail);
    }
    // tr > td
    $trBody->appendChild($tdDetail);

    // 修正セル
    $tdFix = $doc->createElement("td");
    $tdFix->setAttribute("class", $exclude);
    $tdFix->setAttribute("style", $bgcolor . "text-align: center;");
    $tdFix->setAttribute("rowspan", $rowspan);
    // tr > td
    $trBody->appendChild($tdFix);

    // 履歴セル
    $tdHistory = $doc->createElement("td");
    $tdHistory->setAttribute("class", $exclude);
    $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
    $tdHistory->setAttribute("rowspan", $rowspan);

    if ($historyFlag and array_key_exists("admin", $optionColumns)) {
        // 履歴ボタン
        $imgHistory = $doc->createElement("img");
        $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
        $imgHistory->setAttribute("id", $record["strinvoicecode"]);
        $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
        $imgHistory->setAttribute("rownum", $index);
        $imgHistory->setAttribute("maxdetailno", $detailData[$rowspan - 1]["lnginvoicedetailno"]);
        $imgHistory->setAttribute("class", "history button");
        // td > img
        $tdHistory->appendChild($imgHistory);
    }
    // tr > td
    $trBody->appendChild($tdHistory);

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 項目別に表示テキストを設定
        switch ($key) {
            // 顧客
            case "lngCustomerCode":
                if ($record["strcustomercode"] != '') {
                    $textContent = "[" . $record["strcustomercode"] . "]" . " " . $record["strcustomername"];
                } else {
                    $textContent .= "     ";
                }
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 請求書NO.
            case "strInvoiceCode":
                $td = $doc->createElement("td", $record["strinvoicecode"]);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 請求日.
            case "dtmInvoiceDate":
                $td = $doc->createElement("td", str_replace("-", "/", substr($record["dtminvoicedate"], 0, 19)));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 先月請求残額
            case "curLastMonthBalance":
                $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curlastmonthbalance"]));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 当月請求金額.
            case "curThisMonthAmount":
                $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curthismonthamount"]));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 消費税額
            case "curSubTotal1":
                $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotal1"]));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 作成日
            case "dtmInsertDate":
                $td = $doc->createElement("td", str_replace("-", "/", substr($record["dtminsertdate"], 0, 19)));
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // [担当者] 担当者表示名
            case "lngUserCode":
                if ($record["strusercode"] != '') {
                    $textContent = "[" . $record["strusercode"] . "]" . " " . $record["strusername"];
                } else {
                    $textContent .= "     ";
                }
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 入力者
            case "lngInsertUserCode":

                if ($record["strinsertusercode"] != '') {
                    $textContent = "[" . $record["strinsertusercode"] . "]" . " " . $record["strinsertusername"];
                } else {
                    $textContent .= "     ";
                }
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 印刷回数
            case "lngPrintCount":
                if (empty($record["lngprintcount"])) {
                    $textContent = '0';
                } else {
                    $textContent = $record["lngprintcount"];
                }
                $td = $doc->createElement("td", $textContent);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
            // 備考
            case "strNote":
                $td = $doc->createElement("td", $record["strnote"]);
                $td->setAttribute("style", $bgcolor);
                $td->setAttribute("rowspan", $rowspan);
                $trBody->appendChild($td);
                break;
        }
    }

    // 明細データの設定
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[0], $record, false);

    // 削除セル
    $tdDelete = $doc->createElement("td");
    $tdDelete->setAttribute("class", $exclude);
    $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
    $tdDelete->setAttribute("rowspan", $rowspan);
    // tr > td
    $trBody->appendChild($tdDelete);

    // tbody > tr
    $strHtml .= $doc->saveXML($trBody);

    // 明細行のtrの追加
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");

        $trBody->setAttribute("id", $record["strinvoicecode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lnginvoicedetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[$i], $record, false);
        
		// tbody > tr
        $strHtml .= $doc->saveXML($trBody);
        

    }
}
// HTML出力
echo $strHtml;
