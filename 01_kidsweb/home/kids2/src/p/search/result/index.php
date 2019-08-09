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

// クエリの組立て
$aryQuery = array();
$aryQuery[] = "SELECT distinct";
$aryQuery[] = "  p.lngProductNo as lngProductNo";
$aryQuery[] = "  , p.lngInChargeGroupCode as lngGroupCode";
$aryQuery[] = "  , p.bytInvalidFlag as bytInvalidFlag";
$aryQuery[] = "  , to_char(p.dtmInsertDate, 'YYYY/MM/DD') as dtmInsertDate";
$aryQuery[] = "  , p.strProductCode as strProductCode";
$aryQuery[] = "  , p.strProductName as strProductName";
$aryQuery[] = "  , p.strproductenglishname as strproductenglishname";
$aryQuery[] = "  , p.lngInputUserCode as lngInputUserCode";
$aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
$aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
$aryQuery[] = "  , p.lngInChargeGroupCode as lngInChargeGroupCode";
$aryQuery[] = "  , inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
$aryQuery[] = "  , inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
$aryQuery[] = "  , p.lngInChargeUserCode as lngInChargeUserCode";
$aryQuery[] = "  , inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
$aryQuery[] = "  , inchg_u.strUserDisplayName as strInChargeUserDisplayName ";
$aryQuery[] = "  , p.lngdevelopusercode as lngdevelopusercode";
$aryQuery[] = "  , devp_u.strUserDisplayCode as strDevelopUserDisplayCode";
$aryQuery[] = "  , devp_u.strUserDisplayName as strDevelopUserDisplayName ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_Product p ";
$aryQuery[] = "  LEFT JOIN m_User input_u ";
$aryQuery[] = "    ON p.lngInputUserCode = input_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_Group inchg_g ";
$aryQuery[] = "    ON p.lngInChargeGroupCode = inchg_g.lngGroupCode ";
$aryQuery[] = "  LEFT JOIN m_User inchg_u ";
$aryQuery[] = "    ON p.lngInChargeUserCode = inchg_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_User devp_u ";
$aryQuery[] = "    ON p.lngDevelopUsercode = devp_u.lngUserCode ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  p.lngProductNo >= 0 ";
// 登録日
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = " AND p.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}
// 製品コード
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $searchValue)) {
    $strProductCodeArray = explode(",", $searchValue["strProductCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strProductCodeArray as $strProductCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        $aryQuery[] = "p.strProductCode = '" . $strProductCode . "'";
    }
    $aryQuery[] = ")";
}
// 製品名称
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}
// 製品名称(英語)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
    $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
}
// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode ~ * '" . $searchValue["lngInputUserCode"] . "'";
}
// 営業部署
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = "inchg_g.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
}

// 担当者
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = "inchg_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
}

// 開発担当者
if (array_key_exists("lngDevelopUsercode", $searchColumns) &&
    array_key_exists("lngDevelopUsercode", $searchValue)) {
    $aryQuery[] = "devp_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngDevelopUsercode"]) . "'";
}
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  p.lngProductNo ASC";

// クエリを平易な文字列に変換
$strQuery = implode("\n", $aryQuery);

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
    $lngErrorCode = 9057;
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
// 修正カラムを表示
$existsModify = array_key_exists("btnmodify", $displayColumns);
// 再販を表示
$existsResale = array_key_exists("btnresale", $displayColumns);
// 履歴カラムを表示
$existsRecord = array_key_exists("btnrecord", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
// 修正ボタンを表示
$allowedModify = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
// 再販カラムを表示
$allowedResale = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);

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

// 修正項目を表示
if ($existsModify) {
    // 確定カラム
    $thModify = $doc->createElement("th", toUTF8("修正"));
    $thModify->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thModify);
}

// 履歴項目を表示
if ($existsRecord) {
    // プレビューカラム
    $thRecord = $doc->createElement("th", toUTF8("履歴"));
    $thRecord->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thRecord);
}

// 再販項目を表示
if ($existsResale) {
    // プレビューカラム
    $thResale = $doc->createElement("th", toUTF8("再販"));
    $thResale->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thResale);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "作成日";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "担当者";
$aryTableHeaderName["lngdevelopusercode"] = "開発担当者";
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
// 検索結果件数分走査
foreach ($records as $i => $record) {
    $index = $i + 1;

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    // 項番
    $tdIndex = $doc->createElement("td", $index);
    $tdIndex->setAttribute("class", $exclude);
    $trBody->appendChild($tdIndex);

    // 詳細を表示
    if ($existsDetail) {
        // 詳細セル
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);

        // 詳細ボタンの表示
        if ($allowedDetail) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngproductno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 修正項目を表示
    if ($existsModify) {
        // 修正セル
        $tdModify = $doc->createElement("td");
        $tdModify->setAttribute("class", $exclude);

        // 修正ボタンの表示
        if ($allowedModify) {
            // 修正ボタン
            $imgModify = $doc->createElement("img");
            $imgModify->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgModify->setAttribute("id", $record["lngproductno"]);
            $imgModify->setAttribute("class", "modify button");
            // td > img
            $tdModify->appendChild($imgModify);
        }
        // tr > td
        $trBody->appendChild($tdModify);
    }

    // 履歴項目を表示
    if ($existsRecord) {
        // 履歴セル
        $tdRecord = $doc->createElement("td");
        $tdRecord->setAttribute("class", $exclude);
        // 履歴ボタン
        $imgRecord = $doc->createElement("img");
        $imgRecord->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
        $imgRecord->setAttribute("id", $record["lngproductno"]);
        $imgRecord->setAttribute("class", "record button");
        // td > img
        $tdRecord->appendChild($imgRecord);
        // tr > td
        $trBody->appendChild($tdRecord);
    }

    // 再販項目を表示
    if ($existsResale) {
        // 再販セル
        $tdResale = $doc->createElement("td");
        $tdResale->setAttribute("class", $exclude);

        // 再販ボタンの表示
        if ($allowedResale) {
            // 再販ボタン
            $imgResale = $doc->createElement("img");
            $imgResale->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgResale->setAttribute("id", $record["lngproductno"]);
            $imgResale->setAttribute("class", "resale button");
            // td > img
            $tdResale->appendChild($imgResale);
        }
        // tr > td
        $trBody->appendChild($tdResale);
    }

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 登録日
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $trBody->appendChild($td);
                    break;
                // [入力者表示コード] 入力者表示名
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品コード(日本語)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品名称(英語)
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $trBody->appendChild($td);
                    break;
                // [営業部署表示コード] 営業部署表示名
                case "lnginchargegroupcode":
                    $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // [担当者表示コード] 担当者表示名
                case "lnginchargeusercode":
                    $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lngdevelopusercode":
                    $textContent = "[" . $record["strdevelopUserdisplaycode"] . "]" . " " . $record["strdevelopUserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
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

function toUTF8($str)
{
    return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}

