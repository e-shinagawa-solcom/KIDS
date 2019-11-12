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
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
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
// 詳細カラムを表示
$existsDetail = array_key_exists("btndetail", $displayColumns);
// 確定カラムを表示
$existsDecide = array_key_exists("btndecide", $displayColumns);
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// 確定取消カラムを表示
$existsCancel = array_key_exists("btncancel", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
// 確定ボタンを表示
$allowedDecide = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
// 確定取消カラムを表示
$allowedCancel = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);

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

// 詳細を表示
if ($existsDetail) {
    // 詳細カラム
    $thDetail = $doc->createElement("th", toUTF8("詳細"));
    $thDetail->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thDetail);
}

// 確定項目を表示
if ($existsDecide) {
    // 確定カラム
    $thDecide = $doc->createElement("th", toUTF8("確定"));
    $thDecide->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thDecide);
}

// 履歴項目を表示
if ($existsHistory) {
    // 履歴カラム
    $thHistory = $doc->createElement("th", toUTF8("履歴"));
    $thHistory->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thHistory);
}
$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "登録日";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["strordercode"] = "発注ＮＯ.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName["lngcustomercode"] = "仕入先";
$aryTableHeaderName["lngstocksubjectcode"] = "仕入科目";
$aryTableHeaderName["lngstockitemcode"] = "仕入部品";
$aryTableHeaderName["dtmdeliverydate"] = "納期";
$aryTableHeaderName["lngorderstatuscode"] = "状態";
$aryTableHeaderName["strgoodscode"] = "顧客品番";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngreceivestatuscode"] = "状態";
$aryTableHeaderName["lngrecordno"] = "明細行番号";
$aryTableHeaderName["curproductprice"] = "単価";
$aryTableHeaderName["lngproductquantity"] = "数量";
$aryTableHeaderName["cursubtotalprice"] = "税抜金額";
$aryTableHeaderName["strdetailnote"] = "明細備考";
// TODO 要リファクタリング
// 指定されたテーブル項目のカラムを作成する
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// 削除項目を表示
if ($existsCancel) {
    // 削除カラム
    $thCancel = $doc->createElement("th", toUTF8("確定取消"));
    $thCancel->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thCancel);
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {
    unset($aryQuery);
    // 確定対象フラグ
    $decideObjFlag = false;
    // 履歴有無フラグ
    $historyFlag = false;

    // 同じ受注NO,同じ明細番号の最新受注データのリビジョン番号を取得する
    $aryQuery[] = "SELECT";
    $aryQuery[] = " o.lngorderno, o.lngrevisionno ";
    $aryQuery[] = "FROM m_order o inner join t_orderdetail od ";
    $aryQuery[] = "on o.lngorderno = od.lngorderno ";
    $aryQuery[] = "AND o.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "WHERE o.strordercode='" . $record["strordercode"] . "' ";
    $aryQuery[] = "and od.lngorderdetailno=" . $record["lngorderdetailno"] . " ";
    $aryQuery[] = "and o.lngrevisionno >= 0";
    $aryQuery[] = "and o.bytInvalidFlag = FALSE ";
    $aryQuery[] = "order by o.lngorderno desc, o.lngrevisionno desc";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
    }

    $objDB->freeResult($lngResultID);

    $decideObjFlag = fncCheckData($record["strordercode"], $objDB);

    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FFB2B2;";
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strordercode"] . "_" . $record["lngorderdetailno"]);

    // 項番
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $index);
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $trBody->appendChild($tdIndex);

    // 詳細を表示
    if ($existsDetail) {
        // 詳細セル
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");

        // 詳細ボタンの表示
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngorderno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 確定項目を表示
    if ($existsDecide) {
        // 確定セル
        $tdDecide = $doc->createElement("td");
        $tdDecide->setAttribute("class", $exclude);
        $tdDecide->setAttribute("style", $bgcolor . "text-align: center;");

        // 確定ボタンの表示
        if ($allowedDecide and $record["lngrevisionno"] >= 0 and $record["lngorderstatuscode"] == DEF_ORDER_APPLICATE and $decideObjFlag) {
            // 確定ボタン
            $imgDecide = $doc->createElement("img");
            $imgDecide->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgDecide->setAttribute("id", $record["lngorderno"]);
            $imgDecide->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDecide->setAttribute("class", "decide button");
            // td > img
            $tdDecide->appendChild($imgDecide);
        }
        // tr > td
        $trBody->appendChild($tdDecide);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        if ($historyFlag and array_key_exists("admin", $optionColumns)) {
            // 履歴ボタン
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strordercode"] . "_" . $record["lngorderdetailno"]);
            $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("rownum", $index);
            $imgHistory->setAttribute("class", "history button");
            // td > img
            $tdHistory->appendChild($imgHistory);
        }
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    // ヘッダー部データ設定
    fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, true);

    // 確定取消項目を表示
    if ($existsCancel) {
        // 確定取消セル
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);
        $tdCancel->setAttribute("style", $bgcolor . "text-align: center;");

        // 確定取消ボタンの表示
        if ($allowedCancel and $record["lngrevisionno"] >= 0 and $record["lngorderstatuscode"] == DEF_ORDER_ORDER and $decideObjFlag) {
            // 確定取消ボタン
            $imgCancel = $doc->createElement("img");
            $imgCancel->setAttribute("src", "/img/type01/so/cancel_off_bt.gif");
            $imgCancel->setAttribute("id", $record["lngorderno"]);
            $imgCancel->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgCancel->setAttribute("class", "cancel button");
            // td > img
            $tdCancel->appendChild($imgCancel);
        }
        // tr > td
        $trBody->appendChild($tdCancel);
    }

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
