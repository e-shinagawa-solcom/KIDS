<?php

// ----------------------------------------------------------------------------
/**
 *       受注管理  検索
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
require SRC_ROOT . "so/cmn/lib_sos.php";

// ライブラリ読み込み
require LIB_FILE;
require LIB_ROOT . "clscache.php";
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

// 権限確認
// 401 受注管理（受注検索）
if (!fncCheckAuthority(DEF_FUNCTION_SO1, $objAuth)) {
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


// 検索項目から一致する最新の受注データを取得するSQL文の作成関数
$subStrQuery = fncGetMaxReceiveSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);

$strQuery = fncGetReceivesByStrReceiveCodeSQL($subStrQuery);

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
    $lngErrorCode = 403;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // エラー画面の戻り先
    $strReturnPath = "../so/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/so/search/so_search_result.html");

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
$aryTableHeaderName["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName["strreceivecode"] = "受注ＮＯ.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName["lngsalesclasscode"] = "売上区分";
$aryTableHeaderName["strgoodscode"] = "顧客品番";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["dtmdeliverydate"] = "納期";
$aryTableHeaderName["lngreceivestatuscode"] = "状態";
// $aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["lngrecordno"] = "明細行番号";
$aryTableHeaderName["curproductprice"] = "単価";
$aryTableHeaderName["lngproductunitcode"] = "単位";
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
    // 削除フラグ
    $deletedFlag = false;
    // リバイズ有無フラグ
    $revisedFlag = false;
    // 最新受注かどうかのフラグ
    $isMaxReceive = false;
    // 履歴有無フラグ
    $historyFlag = false;
    // リビジョン番号
    $revisionNos = "";

    // 同じ受注NOの最新受注データのリビジョン番号を取得する
    $aryQuery[] = "SELECT";
    $aryQuery[] = " r.lngreceiveno, r.lngrevisionno ";
    $aryQuery[] = "FROM m_receive r inner join t_receivedetail rd ";
    $aryQuery[] = "on r.lngreceiveno = rd.lngreceiveno ";
    $aryQuery[] = "AND r.lngrevisionno = rd.lngrevisionno ";
    $aryQuery[] = "WHERE r.strreceivecode='" . $record["strreceivecode"] . "' ";
    $aryQuery[] = "and rd.lngreceivedetailno=" . $record["lngreceivedetailno"] . " ";
    $aryQuery[] = "and r.lngrevisionno >= 0";
    $aryQuery[] = "and r.bytInvalidFlag = FALSE ";
    $aryQuery[] = "order by r.lngrevisionno desc";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
        for ($j = 0; $j < $lngResultNum; $j++) {
            if ($j == 0) {
                $maxReceiveInfo = $objDB->fetchArray($lngResultID, $j);
                // 該当製品のリビジョン番号<0の場合、削除済となる
                if ($maxReceiveInfo["lngrevisionno"] < 0) {
                    $deletedFlag = true;
                }
                if ($maxReceiveInfo["lngrevisionno"] != 0) {
                    $revisedFlag = true;
                }
                if ($maxReceiveInfo["lngrevisionno"] == $record["lngrevisionno"]) {
                    $isMaxReceive = true;
                }
            } else {
                $receiveInfo = $objDB->fetchArray($lngResultID, $j);
                if ($revisionNos == "") {
                    $revisionNos = $receiveInfo["lngrevisionno"];
                } else {
                    $revisionNos = $revisionNos . "," . $receiveInfo["lngrevisionno"];
                }
            }
        }
    }

    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxReceive) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strreceivecode"]. "_" . $record["lngreceivedetailno"]);
    if (!$isMaxReceive) {
        $trBody->setAttribute("id", $record["strreceivecode"] . "_" . $record["lngreceivedetailno"]. "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    }

    // 項番
    if ($isMaxReceive) {
        $index = $index + 1;
        $subnum = 1;
        $tdIndex = $doc->createElement("td", $index);
    } else {
        $subindex = $index . "." . ($subnum++);
        $tdIndex = $doc->createElement("td", $subindex);
    }
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $trBody->appendChild($tdIndex);

    // 詳細を表示
    if ($existsDetail) {
        // 詳細セル
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor. "text-align: center;");

        // 詳細ボタンの表示
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngreceiveno"]);
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
        $tdDecide->setAttribute("style", $bgcolor. "text-align: center;");

        // 確定ボタンの表示
        if ($allowedDecide and $isMaxReceive and $record["lngrevisionno"] >= 0 and $record["lngreceivestatuscode"] == DEF_RECEIVE_APPLICATE and !$deletedFlag) {
            // 確定ボタン
            $imgDecide = $doc->createElement("img");
            $imgDecide->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgDecide->setAttribute("id", $record["lngreceiveno"]);
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
        $tdHistory->setAttribute("style", $bgcolor. "text-align: center;");

        if ($isMaxReceive and $historyFlag and array_key_exists("admin", $optionColumns)) {
            // 履歴ボタン
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strreceivecode"]. "_". $record["lngreceivedetailno"]);
            $imgHistory->setAttribute("revisionnos", $revisionNos);
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
                // 登録日
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [入力者表示コード] 入力者表示名
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 受注ＮＯ.
                case "strreceivecode":
                    $td = $doc->createElement("td", toUTF8($record["strreceivecode"]));
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
                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品コード(日本語)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品マスタ.製品名称(英語)
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [営業部署表示コード] 営業部署表示名
                case "lnginchargegroupcode":
                    $textContent = "[" . $record["strgroupdisplaycode"] . "]" . " " . $record["strgroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lnginchargeusercode":
                    $textContent = "[" . $record["struserdisplaycode"] . "]" . " " . $record["struserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 売上区分
                case "lngsalesclasscode":
                    $textContent = "[" . $record["lngsalesclasscode"] . "]" . " " . $record["strsalesclassname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [顧客表示コード] 顧客表示名
                case "lngcustomercompanycode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納期
                case "dtmdeliverydate":
                    $td = $doc->createElement("td", toUTF8($record["dtmdeliverydate"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 状態
                case "lngreceivestatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strreceivestatusname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細行番号
                case "lngrecordno":
                    $td = $doc->createElement("td", $record["lngreceivedetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単価
                case "curproductprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単位
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["lngproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 数量
                case "lngproductquantity":
                    $td = $doc->createElement("td", toUTF8($record["lngproductquantity"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税抜金額
                case "cursubtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細備考
                case "strdetailnote":
                    $td = $doc->createElement("td", toUTF8($record["strdetailnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }

    // 確定取消項目を表示
    if ($existsCancel) {
        // 確定取消セル
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);
        $tdCancel->setAttribute("style", $bgcolor. "text-align: center;");

        // 確定取消ボタンの表示
        if ($allowedCancel and $isMaxReceive and $record["lngrevisionno"] >= 0 and $record["lngreceivestatuscode"] == DEF_RECEIVE_ORDER and !$deletedFlag) {
            // 確定取消ボタン
            $imgCancel = $doc->createElement("img");
            $imgCancel->setAttribute("src", "/img/type01/so/cancel_off_bt.gif");
            $imgCancel->setAttribute("id", $record["lngreceiveno"]);
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