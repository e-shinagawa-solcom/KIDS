<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  検索
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
require SRC_ROOT . "p/cmn/lib_ps.php";
require SRC_ROOT . "p/cmn/lib_p.php";
require LIB_DEBUGFILE;

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

// 302 商品管理（商品検索）
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
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
// 検索表示項目取得
if (empty($isDisplay)) {
    $strMessage = fncOutputError(9058, DEF_WARNING, "", false, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

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

// 検索項目から一致する最新の仕入データを取得するSQL文の作成関数
$strQuery = fncGetMaxProductSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);
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
    $lngErrorCode = 303;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // エラー画面の戻り先
    $strReturnPath = "../p/search/index.php?strSessionID=" . $aryData["strSessionID"];

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, $strReturnPath, $objDB);

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
$objTemplate->getTemplate("/p/search/p_search_result.html");

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
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_P4, $objAuth);
// 詳細表示　削除データの表示）
$allowedDetailDelete = fncCheckAuthority(DEF_FUNCTION_P5, $objAuth);
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
$thIndex->setAttribute("unselectable", "off");

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
// 履歴項目を表示
if ($existsHistory) {
    // プレビューカラム
    $thHistory = $doc->createElement("th", toUTF8("履歴"));
    $thHistory->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thHistory);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "作成日";
$aryTableHeaderName["lnggoodsplanprogresscode"] = "企画進行状況";
$aryTableHeaderName["dtmupdatedate"] = "改訂日時";
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "担当者";
$aryTableHeaderName["lngdevelopusercode"] = "開発担当者";
$aryTableHeaderName["lngcategorycode"] = "カテゴリ";
$aryTableHeaderName["strgoodscode"] = "顧客品番";
$aryTableHeaderName["strgoodsname"] = "商品名称";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngcustomerusercode"] = "顧客担当者";
$aryTableHeaderName["lngpackingunitcode"] = "荷姿単位";
$aryTableHeaderName["lngproductunitcode"] = "製品単位";
$aryTableHeaderName["lngproductformcode"] = "商品形態";
$aryTableHeaderName["lngboxquantity"] = "内箱（袋）入数";
$aryTableHeaderName["lngcartonquantity"] = "カートン入数";
$aryTableHeaderName["lngproductionquantity"] = "生産予定数";
$aryTableHeaderName["lngfirstdeliveryquantity"] = "初回納品数";
$aryTableHeaderName["lngfactorycode"] = "生産工場";
$aryTableHeaderName["lngassemblyfactorycode"] = "アッセンブリ工場";
$aryTableHeaderName["lngdeliveryplacecode"] = "納品場所";
$aryTableHeaderName["dtmdeliverylimitdate"] = "納期";
$aryTableHeaderName["curproductprice"] = "納価";
$aryTableHeaderName["curretailprice"] = "上代";
$aryTableHeaderName["lngtargetagecode"] = "対象年齢";
$aryTableHeaderName["lngroyalty"] = "ロイヤリティ";
$aryTableHeaderName["lngcertificateclasscode"] = "証紙";
$aryTableHeaderName["lngcopyrightcode"] = "版権元";
$aryTableHeaderName["strcopyrightnote"] = "版権元備考";
$aryTableHeaderName["strcopyrightdisplaystamp"] = "版権表示（刻印）";
$aryTableHeaderName["strcopyrightdisplayprint"] = "版権表示（印刷物）";
$aryTableHeaderName["strproductcomposition"] = "製品構成";
$aryTableHeaderName["strassemblycontents"] = "アッセンブリ内容";
$aryTableHeaderName["strspecificationdetails"] = "仕様詳細";

// TODO 要リファクタリング
// 指定されたテーブル項目のカラムを作成する
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
$index = 0;
// 検索結果件数分走査
foreach ($records as $i => $record) {
    unset($aryQuery);
    // 削除フラグ
    $deletedFlag = false;
    // 履歴有無フラグ
    $historyFlag = false;

    // 同じ仕入NOの最新仕入データのリビジョン番号を取得する
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngproductno, lngrevisionno ";
    $aryQuery[] = "FROM m_product ";
    $aryQuery[] = "WHERE strproductcode='" . $record["strproductcode"] . "' ";
    $aryQuery[] = "order by lngrevisionno desc";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
        for ($j = 0; $j < $lngResultNum; $j++) {
            $maxProductInfo = $objDB->fetchArray($lngResultID, 0);
            // 該当製品のリビジョン番号<0の場合、削除済となる
            if ($maxProductInfo["lngrevisionno"] < 0) {
                $deletedFlag = true;
            }
        }
    }

    $objDB->freeResult($lngResultID);

    // 背景色設定
    if ($record["strgroupdisplaycolor"]) {
        $bgcolor = "background-color: " . $record["strgroupdisplaycolor"] . ";";
    } else {
        $bgcolor = "background-color: #FFFFFF;";
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strproductcode"]);
    // if (!$isMaxproduct) {
    //     $trBody->setAttribute("id", $record["strproductcode"] . "_" . $record["lngrevisionno"]);
    //     $trBody->setAttribute("style", "display: none;");
    // }

    // 項番
    // if ($isMaxproduct) {
    //     $index = $index + 1;
    //     $subnum = 1;
    //     $tdIndex = $doc->createElement("td", $index);
    // } else {
    //     $subindex = $index . "." . ($subnum++);
    //     $tdIndex = $doc->createElement("td", $subindex);
    // }
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
        // if (($allowedDetailDelete and $record["bytinvalidflag"] != "f") or ($allowedDetail and $record["bytinvalidflag"] == "f")) {
        if (($allowedDetailDelete) or ($allowedDetail and $record["lngrevisionno"] >= 0)) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngproductno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");

        if ($historyFlag) {
            // 履歴ボタン
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strproductcode"]);
            $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("rownum", $index);
            $imgHistory->setAttribute("class", "history button");
            // td > img
            $tdHistory->appendChild($imgHistory);
        }
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 作成日
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 企画進行状況
                case "lnggoodsplanprogresscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodsplanprogressname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 改訂日時
                case "dtmupdatedate":
                    $td = $doc->createElement("td", $record["dtmupdatedate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"] . "_" . $record["strrevisecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // リビジョン番号
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 製品名
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品名（英語）
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 入力者
                case "lnginputusercode":
                    if ($record["strinputuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 営業部署
                case "lnginchargegroupcode":
                    if ($record["strinchargegroupdisplaycode"] != "") {
                        $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [担当者表示コード] 担当者表示名
                case "lnginchargeusercode":
                    if ($record["strinchargeuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lngdevelopusercode":
                    if ($record["strdevelopuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strdevelopuserdisplaycode"] . "]" . " " . $record["strdevelopuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // カテゴリ
                case "lngcategorycode":
                    $td = $doc->createElement("td", toUTF8($record["strcategoryname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 商品名称
                case "strgoodsname":
                    $td = $doc->createElement("td", toUTF8($record["strgoodsname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客
                case "lngcustomercompanycode":
                    if ($record["strcustomercompanycode"] != "") {
                        $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客担当者
                case "lngcustomerusercode":
                    if ($record["strcustomerusercode"] != "") {
                        $textContent = "[" . $record["strcustomerusercode"] . "]" . " " . $record["strcustomerusername"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 荷姿単位
                case "lngpackingunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strpackingunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品単位
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 商品形態
                case "lngproductformcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductformname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 内箱（袋）入数
                case "lngboxquantity":
                    $td = $doc->createElement("td", $record["lngboxquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // カートン入数
                case "lngcartonquantity":
                    $td = $doc->createElement("td", $record["lngcartonquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 生産予定数
                case "lngproductionquantity":
                    $td = $doc->createElement("td", $record["lngproductionquantity"] . " " . $record["strproductionunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 初回納品数
                case "lngfirstdeliveryquantity":
                    $td = $doc->createElement("td", $record["lngfirstdeliveryquantity"] . " " . $record["strfirstdeliveryunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 生産工場
                case "lngfactorycode":
                    if ($record["strfactorycode"] != "") {
                        $textContent = "[" . $record["strfactorycode"] . "]" . " " . $record["strfactoryname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // アッセンブリ工場
                case "lngassemblyfactorycode":
                    if ($record["strassemblyfactorycode"] != "") {
                        $textContent = "[" . $record["strassemblyfactorycode"] . "]" . " " . $record["strassemblyfactoryname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納品場所
                case "lngdeliveryplacecode":
                    if ($record["strdeliveryplacecode"] != "") {
                        $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納期
                case "dtmdeliverylimitdate":
                    $td = $doc->createElement("td", $record["dtmdeliverylimitdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納価
                case "curproductprice":
                    if ($record["curproductprice"] != "") {
                        $textContent = "&yen;" . " " . $record["curproductprice"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 上代
                case "curretailprice":
                    if ($record["curretailprice"] != "") {
                        $textContent = "&yen;" . " " . $record["curretailprice"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 対象年齢
                case "lngtargetagecode":
                    $td = $doc->createElement("td", toUTF8($record["strtargetagename"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ロイヤリティ
                case "lngroyalty":
                    $td = $doc->createElement("td", $record["lngroyalty"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 証紙
                case "lngcertificateclasscode":
                    $td = $doc->createElement("td", toUTF8($record["strcertificateclassname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権元
                case "lngcopyrightcode":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権元備考
                case "strcopyrightnote":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権表示（刻印）
                case "strcopyrightdisplaystamp":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightdisplaystamp"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権表示（印刷物）
                case "strcopyrightdisplayprint":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightdisplayprint"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品構成
                case "strproductcomposition":
                    $td = $doc->createElement("td", toUTF8("全" . $record["strproductcomposition"] . "種アッセンブリ"));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // アッセンブリ内容
                case "strassemblycontents":
                    $td = $doc->createElement("td", toUTF8($record["strassemblycontents"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 仕様詳細
                case "strspecificationdetails":
                    $td = $doc->createElement("td", toUTF8($record["strspecificationdetails"]));
                    $td->setAttribute("style", $bgcolor . "white-space: pre; ");
                    // $td->setAttribute("style", "white-space: pre; ");
                    $trBody->appendChild($td);
                    break;

            }
        }
    }

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
