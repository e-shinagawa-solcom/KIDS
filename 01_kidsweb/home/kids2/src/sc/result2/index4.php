<?php
// ----------------------------------------------------------------------------
/**
 *       納品書検索 履歴取得イベント
 *
 *       処理概要
 *         ・納品書コード、リビジョン番号により納品書履歴情報を取得する
 *
 *       更新履歴
 *
 */

// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "sc/cmn/lib_scd.php";

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
$strQuery = fncGetSlipsByStrSlipCodeSQL($aryData["strSlipCode"], $aryData["lngRevisionNo"]);
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
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SC4, $objAuth);
// 修正を表示
$allowedFix = fncCheckAuthority(DEF_FUNCTION_SC5, $objAuth);
// 削除を表示
$allowedDelete = fncCheckAuthority(DEF_FUNCTION_SC6, $objAuth);

// ヘッダ部
$aryTableHeaderName["lngCustomerCode"] = "顧客";
$aryTableHeaderName["lngTaxClassCode"] = "課税区分";
$aryTableHeaderName["strSlipCode"] = "納品書NO";
$aryTableHeaderName["dtmDeliveryDate"] = "納品日";
$aryTableHeaderName["lngDeliveryPlaceCode"] = "納品先";
$aryTableHeaderName["lngInsertUserCode"] = "起票者";
$aryTableHeaderName["strNote"] = "備考";
$aryTableHeaderName["curTotalPrice"] = "合計金額";

// 明細部
$aryTableDetailHeaderName["lngRecordNo"] = "明細行NO";
$aryTableDetailHeaderName["strCustomerSalesCode"] = "注文書NO";
$aryTableDetailHeaderName["strGoodsCode"] = "顧客品番";
$aryTableDetailHeaderName["strProductName"] = "品名";
$aryTableDetailHeaderName["strSalesClassName"] = "売上区分";
$aryTableDetailHeaderName["curProductPrice"] = "単価";
$aryTableDetailHeaderName["lngQuantity"] = "入数";
$aryTableDetailHeaderName["lngProductQuantity"] = "数量";
$aryTableDetailHeaderName["strProductUnitName"] = "単位";
$aryTableDetailHeaderName["curSubTotalPrice"] = "税抜金額";
$aryTableDetailHeaderName["strDetailNote"] = "明細備考";

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {

    // 詳細データを取得する
    $detailData = fncGetDetailData($record["lngslipno"], $record["lngrevisionno"], $objDB);

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
            $detailnos = $detailData[$i]["lngslipdetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lngslipdetailno"];
        }
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strslipcode"] . "_" . $record["lngrevisionno"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // 項番
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"] . "." . $index);
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
        $imgDetail->setAttribute("lngslipno", $record["lngslipno"]);
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
    // tr > td
    $trBody->appendChild($tdHistory);

    // ヘッダー部データの設定
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, false);

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

        $trBody->setAttribute("id", $record["strslipcode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngslipdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[$i], $record, false);

        // tbody > tr
        $strHtml .= $doc->saveXML($trBody);

    }
}
// HTML出力
echo $strHtml;
