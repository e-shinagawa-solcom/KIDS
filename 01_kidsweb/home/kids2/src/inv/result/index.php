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
require SRC_ROOT . "search/cmn/lib_search.php";
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
$doc->loadHTML($objTemplate->strTemplate);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$table = $doc->getElementById("result");
$thead = $table->getElementsByTagName("thead")->item(0);
$tbody = $table->getElementsByTagName("tbody")->item(0);



// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('inv', $objAuth);

// 管理者モードチェック
$isadmin = array_key_exists("admin", $optionColumns);

// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName_INV, $aryTableBackBtnName_INV, $aryTableHeaderName_INV, $aryTableDetailHeaderName_INV, null);
$thead->appendChild($trHead);
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($aryResult as $i => $record) {
    $index = $index + 1;

    $bgcolor = fncSetBgColor('inv', $record["strinvoicecode"], true, $objDB);

    $detailData = array();
    $rowspan == 0;

    // 詳細データを取得する
    $detailData = fncGetDetailData('inv', $record["lnginvoiceno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    if ($rowspan == 0) {
        $rowspan = 1;
    }
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    $trBody->setAttribute("id", $record["strinvoicecode"]);

    $maxdetailno = $detailData[$rowspan - 1]["lnginvoicedetailno"];

    // 先頭ボタン設定
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName_INV, null, $record, $aryAuthority, true, $isadmin, $index, 'inv', $maxdetailno);

    // ヘッダー部データ設定
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_INV, null, $record, true);
    
    // 明細部データ設定
    fncSetDetailTable($doc, $trBody, $bgcolor, $aryTableDetailHeaderName_INV, null, $record, $detailData, true, true);
    
    // フッターボタン表示
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName_INV, null, $record, $aryAuthority, true, $isadmin, 'inv');

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
