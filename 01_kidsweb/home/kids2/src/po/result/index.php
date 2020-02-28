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
require SRC_ROOT . "po/cmn/column.php";
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
$errorFlag = false;

// クエリの組立に使用するフォームデータを抽出
$optionColumns = array();
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// オプション項目の抽出
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}
// 表示項目の抽出
foreach ($isDisplay as $key => $flag) {
    if ($flag == "on") {
        $displayColumns[$key] = $key;
    }
}

// 検索項目の抽出
foreach ($isSearch as $key => $flag) {
    if ($flag == "on") {
        $searchColumns[$key] = $key;
    }
}

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// 権限確認
// 502 発注管理（発注検索）
if (!fncCheckAuthority(DEF_FUNCTION_PO2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 検索条件に一致する発注コードを取得するSQL文の作成
$strQuery = fncGetSearchPurchaseSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);

// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    // 検索件数が指定数以上の場合エラーメッセージを表示する
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $strMessage = fncOutputError(9057, DEF_WARNING, DEF_SEARCH_MAX, false, "../po/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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

    for ($i = 0; $i < $lngResultNum; $i++) {
        $records[] = $objDB->fetchArray($lngResultID, $i);
	}
	
} else {
    $strMessage = fncOutputError(503, DEF_WARNING, "", false, "../po/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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
$objTemplate->getTemplate("/po/result/po_search_result.html");

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
// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('po', $objAuth);

// 管理者モードチェック
$isadmin = array_key_exists("admin", $optionColumns);

// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName, $aryTableBackBtnName, $aryTableHeaderName_PO, null, $displayColumns);
$thead->appendChild($trHead);
// return;
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {
    $index = $index + 1;

    $bgcolor = fncSetBgColor('po', $record["strordercode"], true, $objDB);

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    $trBody->setAttribute("id", $record["strordercode"] . "_" . $record["lngorderdetailno"]);
    $trBody->setAttribute("before-click-bgcolor", $bgcolor);

    $bgcolor = "background-color: " .$bgcolor . ";";

    // 先頭ボタン設定
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, $index, 'po', null);

    // ヘッダー部データ設定
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_PO, $displayColumns, $record, true);

    // フッターボタン表示
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, 'po');

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
