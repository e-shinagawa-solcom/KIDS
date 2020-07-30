<?php

// ----------------------------------------------------------------------------
/**
 *       受注管理 修正
 *
 *       処理概要
 *         ・指定受注番号データの修正処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require LIB_DEBUGFILE;
require LIB_EXCLUSIVEFILE;
require SRC_ROOT . "so/cmn/lib_so.php";
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
// 406 受注管理（修正）
if (!fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

//詳細画面の表示
$lngReceiveNo = $aryData["lngReceiveNo"];
$lngRevisionNo = $aryData["revisionNo"];
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

$objDB->freeResult($lngResultID);
////////// 明細行の取得 ////////////////////
// 指定受注番号の受注明細データ取得用SQL文の作成
$strQuery = fncGetReceiveDetailNoToInfoSQL($lngReceiveNo, $lngRevisionNo);

// 明細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    fncOutputError(403, DEF_WARNING, "受注番号に対する明細情報が見つかりません。", true, "", $objDB);
}

$objDB->freeResult($lngResultID);

$strQuery = "SELECT lngproductunitcode, strproductunitname FROM m_productunit";
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// 検索件数がありの場合
if ($lngResultNum) {
    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryProductUnit[] = $objDB->fetchArray($lngResultID, $i);
    }
}

$objDB->freeResult($lngResultID);

// 取得データの調整
$aryNewResult = array();
$aryNewResult["strProductCode"] = $aryResult["strproductcode"] . "_" . $aryResult["strrevisecode"];
$aryNewResult["strProductName"] = $aryResult["strproductname"];
$aryNewResult["strGoodsCode"] = $aryResult["strgoodscode"];
$aryNewResult["lngProductNo"] = $aryResult["lngproductno"];
$aryNewResult["lngProductRevisionNo"] = $aryResult["lngproductrevisionno"];
$aryNewResult["strReviseCode"] = $aryResult["strrevisecode"];
$aryNewResult["lngInChargeGroupCode"] = $aryResult["strinchargegroupdisplaycode"];
$aryNewResult["strInChargeGroupName"] = $aryResult["strinchargegroupdisplayname"];
$aryNewResult["lngInChargeUserCode"] = $aryResult["strinchargeuserdisplaycode"];
$aryNewResult["strInChargeUserName"] = $aryResult["strinchargeuserdisplayname"];
$aryNewResult["lngDevelopUserCode"] = $aryResult["strdevelopuserdisplaycode"];
$aryNewResult["strDevelopUserName"] = $aryResult["strdevelopuserdisplayname"];
$aryNewResult["strSessionID"] = $aryData["strSessionID"];
$aryNewResult["mode"] = "modify";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/so/modify/so_modify.html");
$objTemplate->replace($aryNewResult);
$strTemplate = $objTemplate->strTemplate;
// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($strTemplate);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$tabledecideno = $doc->getElementById("tableB_no");
$tbodyDecideNO = $tabledecideno->getElementsByTagName("tbody")->item(0);

$tabledecidebody = $doc->getElementById("tableB");
$tbodyDecideBody = $tabledecidebody->getElementsByTagName("tbody")->item(0);

// 明細情報の出力
$detailNum = 0;
$decideNum = 0;
foreach ($aryDetailResult as $detailResult) {
    $detailNum += 1;
    $decideNum += 1;
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    // No.
    $td = $doc->createElement("td", $decideNum);
    $td->setAttribute("style", "width: 25px;");
    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDecideNO->appendChild($trBody);

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    // No.
    $td = $doc->createElement("td", $detailNum);
    $td->setAttribute("style", "width: 25px;");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 顧客受注番号
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strcustomerreceivecode");
    $td->setAttribute("style", "text-align:center;");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("style", "ime-mode:disabled;width:90px;margin: 0 0 0 1px;");
    $text->setAttribute("class", "form-control form-control-sm");
    $text->setAttribute("value", $aryResult["strcustomerreceivecode"]);
    $td->appendChild($text);

    $trBody->appendChild($td);

    // 顧客
    if ($aryResult["strcustomerdisplaycode"] != "") {
        $textContent = "[" . $detailResult["strcustomerdisplaycode"] . "]" . " " . $detailResult["strcustomerdisplayname"];
    } else {
        $textContent = "";
    }
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strcompanydisplaycode");
    $td->setAttribute("style", "white-space: nowrap;");
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", $detailResult["dtmdeliverydate"]);
    $td->setAttribute("id", "dtmdeliverydate");
    $trBody->appendChild($td);

    // 売上区分
    $textContent = "[" . $detailResult["lngsalesclasscode"] . "]" . " " . $detailResult["strsalesclassname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngsalesclasscode");
    $trBody->appendChild($td);

    // 単価
    $textContent = convertPrice($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["curproductprice"], "unitprice");
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "curproductprice");
    $td->setAttribute("style", "text-align:right;");
    $trBody->appendChild($td);

    // 数量
    $textContent = number_format($detailResult["lngproductquantity"]);
    $td = $doc->createElement("td", $detailResult["lngproductquantity"]);
    $td->setAttribute("id", "lngproductquantity_re");
    $td->setAttribute("style", "text-align:right;");
    $trBody->appendChild($td);

    // 単位
    $td = $doc->createElement("td");
    $td->setAttribute("id", "lngproductunitcode");
    $select = $doc->createElement("select");
    $select->setAttribute("style", "width:50px;margin: 0 0 0 1px;");
    foreach ($aryProductUnit as $productunit) {
        $option = $doc->createElement("option", $productunit["strproductunitname"]);
        $option->setAttribute("value", $productunit["lngproductunitcode"]);
        if ($productunit["lngproductunitcode"] == $detailResult["lngproductunitcode"]) {
            $option->setAttribute("selected", "true");
        }
        $select->appendChild($option);
    }
    $td->appendChild($select);
    $trBody->appendChild($td);

    // 入数
    $lngunitquantity = number_format($detailResult["lngunitquantity"]);
    if ($detailResult["lngproductunitcode"] == 2) {
        $length = strlen(trim($detailResult["lngunitquantity"]));
        $td = $doc->createElement("td");
        $text = $doc->createElement("input");
        $text->setAttribute("type", "text");
        $text->setAttribute("name", "unitQuantity");
        $text->setAttribute("class", "form-control form-control-sm");
        $text->setAttribute("style", "width:" . $length. "em;");
        $text->setAttribute("value", $lngunitquantity);
        $td->appendChild($text);
    } else {
        $td = $doc->createElement("td", $lngunitquantity);
    }
    $td->setAttribute("id", "lngunitquantity");
    $td->setAttribute("style", "width:100px;text-align:right;");
    $trBody->appendChild($td);

    // 小計
    $textContent = convertPrice($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["cursubtotalprice"], "price");
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "cursubtotalprice");
    $td->setAttribute("style", "text-align:right;");
    $trBody->appendChild($td);

    // 備考
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strdetailnote");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("class", "form-control form-control-sm");
    $text->setAttribute("style", "width:240px;");
    $text->setAttribute("value", toUTF8($detailResult["strdetailnote"]));
    $td->appendChild($text);
    $trBody->appendChild($td);

    // 製品コード
    $textContent = $detailResult["strproductcode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strproductcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 再販コード
    $textContent = $detailResult["strrevisecode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strrevisecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 製品名称
    $textContent = "[" . $detailResult["strproductcode"] . "]" . " " . $detailResult["strproductname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strproductname");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 受注番号
    $textContent = $detailResult["lngreceiveno"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngreceiveno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 明細行番号
    $td = $doc->createElement("td", $detailResult["lngreceivedetailno"]);
    $td->setAttribute("id", "lngreceivedetailno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // リビジョン番号
    $textContent = $detailResult["lngrevisionno"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngrevisionno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 受注コード
    $textContent = $aryResult["strreceivecode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strreceivecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 売上分類
    $textContent = "[" . $detailResult["lngsalesdivisioncode"] . "]" . " " . $detailResult["strsalesdivisionname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngsalesdivisioncode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // カートン入数
    $textContent = $detailResult["lngcartonquantity"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngcartonquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 数量
    $textContent = $detailResult["lngproductquantity_est"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngproductquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 通貨単位コード
    $textContent = $detailResult["lngmonetaryunitcode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngmonetaryunitcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 通貨単位記号
    $textContent = $detailResult["strmonetaryunitsign"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strmonetaryunitsign");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // tbody > tr
    $tbodyDecideBody->appendChild($trBody);
}

$objDB->close();

// HTML出力
echo $doc->saveHTML();
