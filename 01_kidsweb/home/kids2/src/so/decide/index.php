<?php

// ----------------------------------------------------------------------------
/**
 *       受注管理 確定
 *
 *       処理概要
 *         ・指定受注番号データの確定処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "so/cmn/lib_sos1.php";
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
// 402 受注管理（受注検索）
if (!fncCheckAuthority(DEF_FUNCTION_SO2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 404 受注管理（確定）
if (!fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
//詳細画面の表示
$lngReceiveNo = $aryData["lngReceiveNo"];
// 指定受注番号の受注データ取得用SQL文の作成
$strQuery = fncGetReceiveHeadNoToInfoSQL($lngReceiveNo, DEF_RECEIVE_PREORDER);

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

$objDB->freeResult($lngResultID);
// 取得データの調整
$aryNewResult = array();
$aryNewResult["strcustomerdisplaycode"] = $aryResult["strcustomerdisplaycode"];
$aryNewResult["strcustomerdisplayname"] = $aryResult["strcustomerdisplayname"];
$aryNewResult["strreceivecode"] = $aryResult["strreceivecode2"];
$aryNewResult["strnote"] = $aryResult["strnote"];

// $aryData["strreceivecode2"] = $aryResult["strreceivecode2"];
// $aryData["lngrevisionno"] = $aryResult["lngrevisionno"];

////////// 明細行の取得 ////////////////////
// 指定受注番号の受注明細データ取得用SQL文の作成
$strQuery = fncGetReceiveDetailNoToInfoSQL($lngReceiveNo);
// $strQuery = fncGetReceiveDetailNoToInfoSQL($aryResult["strreceivecode2"], $aryResult["lngrevisionno"]);
// 明細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    fncOutputError(403, DEF_WARNING, "受注番号に対する明細情報が見つかりません。", true, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/so/decide/so_decide.html");
$objTemplate->replace($aryNewResult);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$tableChkBox = $doc->getElementById("tbl_detail_chkbox");
$tbodyChkBox = $tableChkBox->getElementsByTagName("tbody")->item(0);

$tableDetail = $doc->getElementById("tbl_detail");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

// 明細情報の出力
$num = 0;
foreach ($aryDetailResult as $detailResult) {
    $num += 1;
    // tbody > tr要素作成
    $trChkBox = $doc->createElement("tr");
    // 選択チェックボックス
    $chkBox = $doc->createElement("input");
    $chkBox->setAttribute("type", "checkbox");
    $id = $detailResult["lngreceiveno"] . "_" . $detailResult["lngreceivedetailno"] . "_" . $detailResult["lngrevisionno"];
    $chkBox->setAttribute("id", $id);
    $chkBox->setAttribute("style", "width: 10px;");
    $tdChkBox = $doc->createElement("td");
    $tdChkBox->setAttribute("style", "width: 30px;");
    $tdChkBox->appendChild($chkBox);
    $trChkBox->appendChild($tdChkBox);
    // tbody > tr
    $tbodyChkBox->appendChild($trChkBox);

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    // No.
    $td = $doc->createElement("td", $num);
    $td->setAttribute("style", "width: 25px;");
    $trBody->appendChild($td);

    // 受注番号
    $td = $doc->createElement("td", $aryResult["strreceivecode"]);
    $td->setAttribute("style", "width: 100px;");
    $trBody->appendChild($td);

    // 明細行番号
    $td = $doc->createElement("td", $detailResult["lngreceivedetailno"]);
    $td->setAttribute("style", "width: 70px;");
    $trBody->appendChild($td);

    // 製品名
    $textContent = "[" . $detailResult["strproductcode"] . "]" . " " . $detailResult["strproductname"];
    $td = $doc->createElement("td", substr(toUTF8($textContent), 0, 58));
    $td->setAttribute("style", "width: 250px;");
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", toUTF8($detailResult["dtmdeliverydate"]));
    $trBody->appendChild($td);
    // 売上区分
    $textContent = "[" . $detailResult["lngsalesclasscode"] . "]" . " " . $detailResult["strsalesclassname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("style", "width: 120px;");
    $trBody->appendChild($td);
    // 顧客
    $textContent = "[" . $aryResult["strcustomerdisplaycode"] . "]" . " " . $aryResult["strcustomerdisplayname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("style", "width: 250px;");
    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDetail->appendChild($trBody);

}

$objDB->close();

// HTML出力
echo $doc->saveHTML();

function toUTF8($str)
{
    return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}
