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
// 404 受注管理（確定）
if (!fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

//詳細画面の表示
$lngReceiveNo = $aryData["lngReceiveNo"];
$lngRevisionNo = $aryData["revisionNo"];
$lngestimateno = $aryData["estimateNo"];

$lngReceiveNoList = explode(",", $lngReceiveNo);

if (!is_null($aryData["mode"])) {
    // 排他ロックの解放
    $objDB->transactionBegin();
    $result = unlockExclusive($objAuth, $objDB);
    $objDB->transactionCommit();
    return true;
}

// 排他ロックの取得
$objDB->transactionBegin();
if (isEstimateModified($lngestimateno, $lngRevisionNo, $objDB)) {
    fncOutputError(401, DEF_ERROR, "他のユーザによって更新または削除されています。", true, "", $objDB);
}

// 受注データロック
if (!lockReceiveFix($lngestimateno, DEF_FUNCTION_SO4, $objDB, $objAuth)) {
    fncOutputError(401, DEF_ERROR, "該当データがロックされています。", true, "", $objDB);
}

foreach ($lngReceiveNoList as $eachLngReceiveNo) {
    if (!lockReceive($eachLngReceiveNo, $objDB)) {
        fncOutputError(401, DEF_ERROR, "該当データのロックに失敗しました", true, "", $objDB);
    }
    // 受注データ更新有無チェック
    if (isReceiveModified($eachLngReceiveNo, DEF_RECEIVE_APPLICATE, $objDB)) {
        fncOutputError(404, DEF_ERROR, "", true, "", $objDB);
    }
}

$objDB->transactionCommit();

//詳細画面の表示
// $lngReceiveNo = $aryData["lngReceiveNo"];
// $lngRevisionNo = $aryData["lngRevisionNo"];
// 指定受注番号の受注データ取得用SQL文の作成
$strQuery = fncGetReceiveHeadInfoSQL($aryData["lngReceiveNo"], $lngestimateno);

// 詳細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = pg_fetch_all($lngResultID);
    }
} else {
    fncOutputError(403, DEF_ERROR, "該当データの取得に失敗しました", true, "", $objDB);
}

$objDB->freeResult($lngResultID);
// 取得データの調整
$aryNewResult = array();
$aryNewResult["strProductCode"] = $aryResult[0]["strproductcode"] . "_" . $aryResult[0]["strrevisecode"];
$aryNewResult["strProductName"] = $aryResult[0]["strproductname"];
$aryNewResult["strGoodsCode"] = $aryResult[0]["strgoodscode"];
$aryNewResult["lngProductNo"] = $aryResult[0]["lngproductno"];
$aryNewResult["lngProductRevisionNo"] = $aryResult[0]["lngproductrevisionno"];
$aryNewResult["strReviseCode"] = $aryResult[0]["strrevisecode"];
$aryNewResult["lngInChargeGroupCode"] = $aryResult[0]["strinchargegroupdisplaycode"];
$aryNewResult["strInChargeGroupName"] = $aryResult[0]["strinchargegroupdisplayname"];
$aryNewResult["lngInChargeUserCode"] = $aryResult[0]["strinchargeuserdisplaycode"];
$aryNewResult["strInChargeUserName"] = $aryResult[0]["strinchargeuserdisplayname"];
$aryNewResult["lngDevelopUserCode"] = $aryResult[0]["strdevelopuserdisplaycode"];
$aryNewResult["strDevelopUserName"] = $aryResult[0]["strdevelopuserdisplayname"];
$aryNewResult["strSessionID"] = $aryData["strSessionID"];

for ($i = 0; $i < $lngResultNum; $i++) {
    if ($i == 0) {
        $lngReceiveNo = $aryResult[$i]["lngreceiveno"];
    } else {
        $lngReceiveNo .= "," . $aryResult[$i]["lngreceiveno"];
    }
}

////////// 明細行の取得 ////////////////////
// 指定受注番号の受注明細データ取得用SQL文の作成
$strQuery = fncGetReceiveDetailNoToInfoSQL($lngReceiveNo, "");
// echo $strQuery;
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

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/so/decide/so_decide.html");
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
$tableChkBox = $doc->getElementById("tableA_chkbox");
$tbodyChkBox = $tableChkBox->getElementsByTagName("tbody")->item(0);

$tableDetail = $doc->getElementById("tableA");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

$lngReceiveNos = explode(",", $aryData["lngReceiveNo"]);

if ($lngReceiveNos) {
    // 表示項目の抽出
    foreach ($lngReceiveNos as $key) {
        $lngReceiveNos[$key] = $key;
    }
}

// 検索結果テーブルの取得
$tabledecideno = $doc->getElementById("tableB_no");
$tbodyDecideNO = $tabledecideno->getElementsByTagName("tbody")->item(0);

$tabledecidebody = $doc->getElementById("tableB");
$tbodyDecideBody = $tabledecidebody->getElementsByTagName("tbody")->item(0);

// 明細情報の出力
$detailNum = 0;
$decideNum = 0;
foreach ($aryDetailResult as $detailResult) {
    $isdecideObj = false;
    if (array_key_exists($detailResult["lngreceiveno"], $lngReceiveNos)) {
        $isdecideObj = true;
    }

    if (!$isdecideObj) {
        $detailNum += 1;
        // tbody > tr要素作成
        $trChkBox = $doc->createElement("tr");
        // 選択チェックボックス
        $chkBox = $doc->createElement("input");
        $chkBox->setAttribute("type", "checkbox");
        $chkBox->setAttribute("name", "edit");
        $id = $detailResult["lngreceiveno"] . "_" . $detailResult["lngreceivedetailno"] . "_" . $detailResult["lngrevisionno"];
        $chkBox->setAttribute("id", $id);
        $chkBox->setAttribute("style", "width: 10px;");
        $tdChkBox = $doc->createElement("td");
        $tdChkBox->setAttribute("style", "width: 20px;text-align:center;");
        $tdChkBox->appendChild($chkBox);
        $trChkBox->appendChild($tdChkBox);
        // tbody > tr
        $tbodyChkBox->appendChild($trChkBox);

    } else {
        $decideNum += 1;
        // tbody > tr要素作成
        $trBody = $doc->createElement("tr");
        // No.
        $td = $doc->createElement("td", $decideNum);
        $td->setAttribute("style", "width: 25px;");
        $trBody->appendChild($td);
        // tbody > tr
        $tbodyDecideNO->appendChild($trBody);
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    // No.
    $td = $doc->createElement("td", $detailNum);
    $td->setAttribute("style", "width: 25px;");
    if ($isdecideObj) {
        $td->setAttribute("style", "display:none");
    }
    $trBody->appendChild($td);

    // 顧客受注番号
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strcustomerreceivecode");
    $td->setAttribute("style", "text-align:center;");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("style", "ime-mode:disabled;");
    $text->setAttribute("class", "form-control form-control-sm txt-kids");
    $text->setAttribute("value", $detailResult["strcustomerreceivecode"]);
    $td->appendChild($text);

    $trBody->appendChild($td);

    // 顧客
    if ($aryResult[0]["strcustomerdisplaycode"] != "") {
        $textContent = "[" . $detailResult["strcustomerdisplaycode"] . "]" . " " . $detailResult["strcustomerdisplayname"];
    } else {
        $textContent = "";
    }
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strcompanydisplaycode");
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", $detailResult["dtmdeliverydate"]);
    $td->setAttribute("id", "dtmdeliverydate");
    $trBody->appendChild($td);

    // 売上分類
    $textContent = "[" . $detailResult["lngsalesdivisioncode"] . "]" . " " . $detailResult["strsalesdivisionname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngsalesdivisioncode");
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
    $trBody->appendChild($td);

    // 入数
    $lngunitquantity = 1;
    $detailResult["lngcartonquantity"] = $detailResult["lngcartonquantity"] == null ? 0 : $detailResult["lngcartonquantity"];
    $detailResult["lngproductquantity"] = $detailResult["lngproductquantity_est"] == null ? 0 : $detailResult["lngproductquantity_est"];
    $lngproductquantity = $detailResult["lngproductquantity"] / $lngunitquantity;
    if ($detailResult["lngproductunitcode"] == 2) {
        $lngunitquantity = $detailResult["lngcartonquantity"];
        $lngproductquantity = $detailResult["lngproductquantity"] / $lngunitquantity;
    }

    // 数量
    $textContent = number_format($lngproductquantity);
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngproductquantity_re");
    $trBody->appendChild($td);

    // 単位
    $td = $doc->createElement("td");
    $td->setAttribute("id", "lngproductunitcode");
    $select = $doc->createElement("select");
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
    if ($detailResult["lngproductunitcode"] == 2) {
        $td = $doc->createElement("td");
        $text = $doc->createElement("input");
        $text->setAttribute("type", "text");
        $text->setAttribute("name", "unitQuantity");
        $text->setAttribute("class", "form-control form-control-sm txt-kids");
        $text->setAttribute("style", "width:90px;");
        $text->setAttribute("value", $lngunitquantity);
        $td->appendChild($text);
    } else {
        $td = $doc->createElement("td", $lngunitquantity);
    }
    $td->setAttribute("id", "lngunitquantity");
    $td->setAttribute("style", "width:100px;");
    $trBody->appendChild($td);

    // 小計
    $textContent = convertPrice($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["cursubtotalprice"], "price");
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "cursubtotalprice");
    $trBody->appendChild($td);

    // 備考
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strdetailnote");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("class", "form-control form-control-sm txt-kids");
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

    // 受注コード
    $textContent = $aryResult[0]["strreceivecode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strreceivecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // リビジョン番号
    $textContent = $detailResult["lngrevisionno"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngrevisionno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // カートン入数
    $textContent = $detailResult["lngcartonquantity"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngcartonquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 数量
    $textContent = $detailResult["lngproductquantity"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngproductquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 製品リビジョン番号
    $textContent = $detailResult["strrevisecode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strrevisecode");
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

    if (!$isdecideObj) {
        // tbody > tr
        $tbodyDetail->appendChild($trBody);
    } else {
        // tbody > tr
        $tbodyDecideBody->appendChild($trBody);

    }
}

$objDB->close();

// HTML出力
echo $doc->saveHTML();
