<?php

// ----------------------------------------------------------------------------
/**
 *       受注管理  確定完了
 *
 *       処理概要
 *         ・登録した確定情報を確定登録処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

include 'conf.inc';
require LIB_FILE;
require LIB_EXCLUSIVEFILE;
require SRC_ROOT . "so/cmn/lib_so.php";
include 'JSON.php';

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}
// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
// 権限確認
// 402 受注管理（受注検索）
if (!fncCheckAuthority(DEF_FUNCTION_SO2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 406 受注管理（修正）
if (!fncCheckAuthority(DEF_FUNCTION_SO6, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

$lngReceiveNo = $aryData["detailData"][0]["lngReceiveNo"];
$lngRevisionNo = $aryData["detailData"][0]["lngRevisionNo"];
// 指定受注番号の受注データ取得用SQL文の作成
$strQuery = fncGetReceiveHeadNoToInfoSQL($lngReceiveNo, $lngRevisionNo);

// 詳細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(403, DEF_ERROR, "該当データの取得に失敗しました", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    }
} else {
    fncOutputError(403, DEF_ERROR, "データが異常です", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

if ($aryResult["lngreceivestatuscode"] != DEF_RECEIVE_ORDER) {    
    fncOutputError(401, DEF_ERROR, "他のユーザによって更新または削除されています。", true, "", $objDB);
}

$objDB->transactionBegin();
// 確定処理
foreach ($aryData["detailData"] as $data) {        
    // 受注更新
    $aryQuery = array();
    $aryQuery[] = "UPDATE m_receive ";
    $aryQuery[] = "set strcustomerreceivecode = '" . $data["strCustomerReceiveCode"] . "' ";
    $aryQuery[] = "where lngreceiveno = " . $data["lngReceiveNo"] . " ";
    $aryQuery[] = "and lngrevisionno = " . $data["lngRevisionNo"] . " ";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    $objDB->freeResult($lngResultID);

    // 受注明細更新
    unset($aryQuery);
    // 受注明細更新
    $aryQuery[] = "UPDATE t_receivedetail ";
    $aryQuery[] = "set lngproductquantity = " . str_replace(",", "", $data["lngProductQuantity"]) . " ";
    $aryQuery[] = ", lngproductunitcode = '" . $data["lngProductUnitCode"] . "' ";
    $aryQuery[] = ", lngunitquantity = '" . str_replace(",", "", $data["lngUnitQuantity"]) . "' ";
    $aryQuery[] = ", curproductprice = '" . str_replace(",", "", explode(" ", $data['curProductPrice'])[1]) . "' ";
    $aryQuery[] = ", strnote = '" . $data["strDetailNote"] . "' ";
    $aryQuery[] = "where lngreceiveno = " . $data["lngReceiveNo"] . " ";
    $aryQuery[] = "and lngreceivedetailno = " . $data["lngReceiveDetailNo"] . " ";
    $aryQuery[] = "and lngrevisionno = " . $data["lngRevisionNo"] . " ";
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    $objDB->freeResult($lngResultID);
}

$objDB->transactionCommit();
$objDB->close();
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/so/modify/so_finish_modify.html");
// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($objTemplate->strTemplate);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$tableDetail = $doc->getElementById("table_decide_detail");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

// 明細情報の出力
$num = 0;
foreach ($aryData["detailData"] as $data) {
    $num += 1;
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    // No.
    $td = $doc->createElement("td", $num);
    $trBody->appendChild($td);

    // 顧客
    $td = $doc->createElement("td", $data["strCompanyDisplayCode"]);
    $trBody->appendChild($td);

    // 顧客受注番号.
    $td = $doc->createElement("td", $data["strCustomerReceiveCode"]);
    $trBody->appendChild($td);

    // 製品
    $td = $doc->createElement("td", htmlspecialchars($data["strProductCode"]));
    $trBody->appendChild($td);

    // 顧客品番
    $td = $doc->createElement("td", $data["strGoodsCode"]);
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", $data["dtmDeliveryDate"]);
    $trBody->appendChild($td);

    // 売上区分
    $td = $doc->createElement("td", $data["lngSalesClassCode"]);
    $trBody->appendChild($td);
    // 単価
    $td = $doc->createElement("td", $data["curProductPrice"]);
    $trBody->appendChild($td);
    // 数量
    $td = $doc->createElement("td", $data["lngProductQuantity"]);
    $trBody->appendChild($td);
    // 単位
    $td = $doc->createElement("td", $data["strProductUnitName"]);
    $trBody->appendChild($td);
    // 入数
    $td = $doc->createElement("td", $data["lngUnitQuantity"]);
    $trBody->appendChild($td);
    // 小計
    $td = $doc->createElement("td", $data["curSubtotalPrice"]);
    $trBody->appendChild($td);
    // 備考
    $td = $doc->createElement("td", $data["strDetailNote"]);
    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDetail->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
