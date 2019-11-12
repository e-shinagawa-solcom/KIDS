<?php

// ----------------------------------------------------------------------------
/**
 *       請求管理  請求書検索画面
 *
 *
 *       @package    K.I.D.S.
 *       @license    http://www.kuwagata.co.jp/
 *       @copyright  KUWAGATA CO., LTD.
 *       @author     K.I.D.S. Groups <info@kids-groups.com>
 *       @access     public
 *       @version    2.00
 *
 *
 *       処理概要
 *         ・請求書データ検索結果画面表示処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';

require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';

// ライブラリ読み込み
require LIB_FILE;
require LIB_ROOT . "clscache.php";
require SRC_ROOT . "inv/cmn/lib_regist.php";
require LIB_DEBUGFILE;

// DB接続
$objDB = new clsDB();
$objAuth = new clsAuth();
$objCache = new clsCache();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
// フォームデータから各カテゴリの振り分けを行う
$options = UtilSearchForm::extractArrayByOption($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

$optionColumns = array();
// オプション項目の抽出
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}
$isSearch = array_keys($isSearch);
$aryData['SearchColumn'] = $isSearch;
foreach ($from as $key => $item) {
    $aryData[$key . 'From'] = $item;
}
foreach ($to as $key => $item) {
    $aryData[$key . 'To'] = $item;
}
foreach ($searchValue as $key => $item) {
    $aryData[$key] = $item;
}

// 検索条件項目取得
// 検索条件 $arySearchColumnに格納
if (empty($isSearch)) {
    //    fncOutputError( 502, DEF_WARNING, "検索対象項目がチェックされていません",TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    $bytSearchFlag = true;
}

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;

// 権限確認
// 2200 請求管理
if (!fncCheckAuthority(DEF_FUNCTION_INV0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}
// 2202 請求書検索
if (!fncCheckAuthority(DEF_FUNCTION_INV2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 検索項目  $arySearchColumnに格納
$arySearchColumn = $isSearch;

if (!$bytSearchFlag) {
    reset($arySearchColumn);
}
reset($aryData);

// 検索SQLを実行し検索（ヒット）件数を取得する
$strQuery = fncGetSearchInvoiceSQL($arySearchColumn, $aryData, $objDB, $aryData["strSessionID"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    // 検索件数が指定数以上の場合エラーメッセージを表示する
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $strMessage = fncOutputError(9057, DEF_WARNING, DEF_SEARCH_MAX, false, "../inv/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

        // [lngLanguageCode]書き出し
        $aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

        // [strErrorMessage]書き出し
        $aryHtml["strErrorMessage"] = $strMessage;

        // テンプレート読み込み
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate("/result/error/parts.tmpl");

        // テンプレート生成
        $objTemplate->replace($aryHtml);
        $objTemplate->complete();

        // HTML出力
        echo $objTemplate->strTemplate;

        exit;
    }

    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(9215, DEF_WARNING, "", false, "../inv/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

    // [lngLanguageCode]書き出し
    $aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

    // [strErrorMessage]書き出し
    $aryHtml["strErrorMessage"] = $strMessage;

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML出力
    echo $objTemplate->strTemplate;

    exit;
}

$objDB->freeResult($lngResultID);

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/inv/result/search_result.html");

// テンプレート生成
$objTemplate->replace($aryResult);

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
$table = $doc->getElementById("result");
$thead = $table->getElementsByTagName("thead")->item(0);
$tbody = $table->getElementsByTagName("tbody")->item(0);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
// 修正を表示
$allowedFix = fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth);
// 削除を表示
$allowedDelete = fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth);
// 無効カラムを表示
$allowedInvalid = fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth);

// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");

// クリップボード除外対象クラス
$exclude = "exclude-in-clip-board-target";

// 項番カラム
$thIndex = $doc->createElement("th");
$thIndex->setAttribute("class", $exclude);
// コピーボタン
$imgCopy = $doc->createElement("img");
$imgCopy->setAttribute("src", "/img/type01/cmn/seg/copy_off_bt.gif");
$imgCopy->setAttribute("class", "copy button");
// 項番カラム > コピーボタン
$thIndex->appendChild($imgCopy);
// ヘッダに追加
$trHead->appendChild($thIndex);

// 詳細カラム
$thDetail = $doc->createElement("th", toUTF8("詳細"));
$thDetail->setAttribute("class", $exclude);
// ヘッダに追加
$trHead->appendChild($thDetail);

// 修正カラム
$thFix = $doc->createElement("th", toUTF8("修正"));
$thFix->setAttribute("class", $exclude);
// ヘッダに追加
$trHead->appendChild($thFix);

// 履歴カラム
$thHistory = $doc->createElement("th", toUTF8("履歴"));
$thHistory->setAttribute("class", $exclude);
// ヘッダに追加
$trHead->appendChild($thHistory);

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

// TODO 要リファクタリング
// 指定されたテーブル項目のカラムを作成する
foreach ($aryTableHeaderName as $key => $value) {
    $th = $doc->createElement("th", toUTF8($value));
    $trHead->appendChild($th);
}
// 明細ヘッダーを作成する
foreach ($aryTableDetailHeaderName as $key => $value) {
    $th = $doc->createElement("th", toUTF8($value));
    $trHead->appendChild($th);
}
// 削除項目を表示
// 削除カラム
$thDelete = $doc->createElement("th", toUTF8("削除"));
$thDelete->setAttribute("class", $exclude);
// ヘッダに追加
$trHead->appendChild($thDelete);

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($aryResult as $i => $record) {
    unset($aryQuery);
    // 削除フラグ
    $deletedFlag = false;

    $deletedFlag = fncCheckData($record["strinvoicecode"], $objDB);

    // 詳細データを取得する
    $detailData = fncGetDetailData($record["lnginvoiceno"], $record["lngrevisionno"], $objDB);

    $rowspan = count($detailData);
    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FFB2B2;";
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
    $trBody->setAttribute("id", $record["strinvoicecode"]);
    $trBody->setAttribute("detailnos", $detailnos);

    // 項番
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $index);
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

    // 修正ボタンの表示
    if ($allowedFix && $record["lngrevisionno"] >= 0 && !$deletedFlag) {
        // 修正ボタン
        $imgFix = $doc->createElement("img");
        $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
        $imgFix->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
        $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
        $imgFix->setAttribute("class", "renew button");
        // td > img
        $tdFix->appendChild($imgFix);
    }
    // tr > td
    $trBody->appendChild($tdFix);

    // 履歴セル
    $tdHistory = $doc->createElement("td");
    $tdHistory->setAttribute("class", $exclude);
    $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
    $tdHistory->setAttribute("rowspan", $rowspan);

    if ($record["lngrevisionno"] <> 0 and array_key_exists("admin", $optionColumns)) {
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

    // ヘッダー部データの設定
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, true);
    

    // 明細データの設定
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[0], $record, true);

    // tbody > tr
    $tbody->appendChild($trBody);

    // 削除セル
    $tdDelete = $doc->createElement("td");
    $tdDelete->setAttribute("class", $exclude);
    $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
    $tdDelete->setAttribute("rowspan", $rowspan);

    // 削除ボタンの表示
    if (!$deletedFlag) {
        // 削除ボタン
        $imgDelete = $doc->createElement("img");
        $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
        $imgDelete->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
        $imgDelete->setAttribute("revisionno", $record["lngrevisionno"]);
        $imgDelete->setAttribute("class", "delete button");
        // td > img
        $tdDelete->appendChild($imgDelete);
    }
    // tr > td
    $trBody->appendChild($tdDelete);

    // tbody > tr
    $tbody->appendChild($trBody);

    // 明細行のtrの追加
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");

        $trBody->setAttribute("id", $record["strinvoicecode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lnginvoicedetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData[$i], $record, true);

        $tbody->appendChild($trBody);

    }
}

// HTML出力
echo $doc->saveHTML();
