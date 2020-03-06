<?php

// ----------------------------------------------------------------------------
/**
 *       発注管理  検索
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
 *         ・検索結果画面表示処理
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
require SRC_ROOT . "po/cmn/lib_pos.php";
require SRC_ROOT . "search/cmn/lib_search.php";
require SRC_ROOT . "po/cmn/column2.php";
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
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;
// クエリの組立に使用するフォームデータを抽出
$optionColumns = array();
$displayColumns = array();

// オプション項目の抽出
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}

// 表示項目の抽出
foreach ($isDisplay as $key => $flag) {
    if ($flag == "on") {
        if ($key == "strProductCode") {
            $displayColumns[$key] = $key;
            $displayColumns["strproductname"] = "strproductname";
            $displayColumns["strproductenglishname"] = "strproductenglishname";
        } else {
            $displayColumns[$key] = $key;
        }
        
    }
}

$isDisplay = array_keys($isDisplay);
$isSearch = array_keys($isSearch);
$aryData['ViewColumn'] = $isDisplay;
$aryData['SearchColumn'] = $isSearch;

// 管理者モードチェック
$isadmin = array_key_exists("admin", $optionColumns);

foreach ($from as $key => $item) {
//echo $key.'From' . "=" . $item . "<br>";
    $aryData[$key . 'From'] = $item;
}
foreach ($to as $key => $item) {
//echo $key.'To' . "=" . $item . "<br>";
    $aryData[$key . 'To'] = $item;
}
foreach ($searchValue as $key => $item) {
    $aryData[$key] = $item;
}

// 検索表示項目取得
if (empty($isDisplay)) {
    $strMessage = fncOutputError(9058, DEF_WARNING, "", false, "", $objDB);

    // [lngLanguageCode]書き出し
    $aryHtml["lngLanguageCode"] = 1;

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

// 検索条件項目取得
// 検索条件 $arySearchColumnに格納
if (empty($isSearch)) {
    //    fncOutputError( 502, DEF_WARNING, "検索対象項目がチェックされていません",TRUE, "../so/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    $bytSearchFlag = true;
}

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
// 510 発注管理（発注書検索）
if (!fncCheckAuthority(DEF_FUNCTION_PO10, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 511 発注管理（発注書検索　管理モード）
if (fncCheckAuthority(DEF_FUNCTION_PO11, $objAuth) and isset($aryData["Admin"])) {
    $aryUserAuthority["Admin"] = 1; // 511 管理モードでの検索
}
// 512 発注管理（発注書修正）
if (fncCheckAuthority(DEF_FUNCTION_PO12, $objAuth)) {
    $aryUserAuthority["Edit"] = 1; // 512 修正
}

// 表示項目  $aryViewColumnに格納
// $aryViewColumn=$isDisplay;
$aryViewColumn = fncResortSearchColumn2($isDisplay);
// 検索項目  $arySearchColumnに格納
$arySearchColumn = $isSearch;

reset($aryViewColumn);
if (!$bytSearchFlag) {
    reset($arySearchColumn);
}
reset($aryData);

// 検索条件に一致する発注コードを取得するSQL文の作成
$strQuery = fncGetSearchPurcheseOrderSQL($aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, $isadmin);

// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    // 検索件数が指定数以上の場合エラーメッセージを表示する
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $strMessage = fncOutputError(9057, DEF_WARNING, DEF_SEARCH_MAX, false, "", $objDB);

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
        $records[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(503, DEF_WARNING, "", false, "", $objDB);

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
$objTemplate->getTemplate("/po/result2/po_search_result.html");

$aryResult["displayColumns"] = implode(",", $displayColumns);
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

// キー文字列を小文字に変換
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);
//var_dump($displayColumns);
// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('purchaseorder', $objAuth);
// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName, $aryTableBackBtnName, $aryTableHeaderName_PURORDER, $aryTableDetailHeaderName_PURORDER, $displayColumns);
$thead->appendChild($trHead);
// return;
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {
    $index = $index + 1;
    $bgcolor = fncSetBgColor('purchaseorder', $record["strordercode"], true, $objDB);

    $detailData = fncGetDetailData('purchaseorder', $record["lngpurchaseorderno"], $record["lngrevisionno"], $objDB);
fncDebug("kids2.log", sprintf("detail count=%d", count($detailData)), __FILE__, __LINE__, "a");
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    $trBody->setAttribute("id", $record["strordercode"]);
    $trBody->setAttribute("before-click-bgcolor", $bgcolor);

    $bgcolor = "background-color: " .$bgcolor . ";";

    // 先頭ボタン設定
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, $index, 'purchaseorder', null);

    // ヘッダー部データ設定
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_PURORDER, $displayColumns, $record, true);

    // 明細部データ設定
    fncSetDetailTable($doc, $trBody, $bgcolor, $aryTableDetailHeaderName_PURORDER, $displayColumns, $record, $detailData, true, true);

    // フッターボタン表示
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, 'purchaseorder');

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
