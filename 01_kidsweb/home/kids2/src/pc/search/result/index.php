<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  検索
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
require LIB_DEBUGFILE;
require SRC_ROOT . "pc/cmn/lib_pcs.php";
require SRC_ROOT . "search/cmn/lib_search.php";
require_once LIB_DEBUGFILE;

// DB接続
$objDB = new clsDB();
$objAuth = new clsAuth();
$objCache = new clsCache();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;

// 権限確認
// 702 仕入管理（仕入検索）
if (!fncCheckAuthority(DEF_FUNCTION_PC2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

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

// 検索項目から一致する最新の仕入データを取得するSQL文の作成関数
$strQuery = fncGetMaxStockSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);
// echo $strQuery . "<br>";
// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// 検索件数がありの場合
if ($lngResultNum > 0) {
    // 指定数以上の場合エラーメッセージを表示する
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $errorFlag = true;
        $lngErrorCode = 9057;
        $aryErrorMessage = DEF_SEARCH_MAX;
    }
} else {
    $errorFlag = true;
    $lngErrorCode = 703;
    $aryErrorMessage = "";
}

if ($errorFlag) {

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, "", $objDB);

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
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/pc/search/pc_search_result.html");

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
$aryAuthority = fncGetAryAuthority('pc', $objAuth);

// 管理者モードチェック
$isadmin = array_key_exists("admin", $optionColumns);

// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName, $aryTableBackBtnName, $aryTableHeaderName_PC, $aryTableDetailHeaderName_PC, $displayColumns);
$thead->appendChild($trHead);
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {
    $index = $index + 1;

    $bgcolor = fncSetBgColor('pc', $record["strstockcode"], true, $objDB);

    $detailData = array();
    $rowspan == 0;

    // 詳細データを取得する
    $detailData = fncGetDetailData('pc', $record["lngstockno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    $trBody->setAttribute("id", $record["strstockcode"]);
    $trBody->setAttribute("before-click-bgcolor", $bgcolor);

    $bgcolor = "background-color: " .$bgcolor . ";";
    
    $maxdetailno = $detailData[$rowspan - 1]["lngstockdetailno"];

    // 先頭ボタン設定
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, $index, 'pc', $maxdetailno);

    // ヘッダー部データ設定
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_PC, $displayColumns, $record, true);

    // 明細部データ設定
    fncSetDetailTable($doc, $trBody, $bgcolor, $aryTableDetailHeaderName_PC, $displayColumns, $record, $detailData, true, true);

    // フッターボタン表示
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $aryAuthority, true, $isadmin, 'pc');
       
    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();