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
$subStrQuery = fncGetMaxStockSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns);

$strQuery = fncGetStocksByStrStockCodeSQL($subStrQuery);

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
    // エラー画面の戻り先
    $strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/pc/search/pc_search_result.html");

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
$existsFix = array_key_exists("btnfix", $displayColumns);
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// 削除を表示
$existsDelete = array_key_exists("btndelete", $displayColumns);
// 無効カラムを表示
$existsInvalid = array_key_exists("btninvalid", $displayColumns);

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

// 詳細を表示
if ($existsDetail) {
    // 詳細カラム
    $thDetail = $doc->createElement("th", toUTF8("詳細"));
    $thDetail->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thDetail);
}
// 修正を表示
if ($existsFix) {
    // 確定カラム
    $thFix = $doc->createElement("th", toUTF8("修正"));
    $thFix->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thFix);
}
// 履歴を表示
if ($existsHistory) {
    // 履歴カラム
    $thHistory = $doc->createElement("th", toUTF8("履歴"));
    $thHistory->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thHistory);
}
$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "登録日";
$aryTableHeaderName["dtmappropriationdate"] = "仕入日";
$aryTableHeaderName["strstockcode"] = "仕入ＮＯ.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strordercode"] = "発注ＮＯ.";
$aryTableHeaderName["strslipcode"] = "納品書ＮＯ.";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["lngcustomercode"] = "仕入先";
$aryTableHeaderName["lngstockstatuscode"] = "状態";
$aryTableHeaderName["lngpayconditioncode"] = "支払条件";
$aryTableHeaderName["dtmexpirationdate"] = "製品到着日";
$aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["curtotalprice"] = "合計金額";
$aryTableDetailHeaderName["lngrecordno"] = "明細行番号";
$aryTableDetailHeaderName["strproductcode"] = "製品コード";
$aryTableDetailHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableDetailHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableDetailHeaderName["strproductname"] = "製品名";
$aryTableDetailHeaderName["lngstocksubjectcode"] = "仕入科目";
$aryTableDetailHeaderName["lngstockitemcode"] = "仕入部品";
$aryTableDetailHeaderName["strmoldno"] = "Ｎｏ．";
$aryTableDetailHeaderName["strgoodscode"] = "顧客品番";
$aryTableDetailHeaderName["lngdeliverymethodcode"] = "運搬方法";
$aryTableDetailHeaderName["curproductprice"] = "単価";
$aryTableDetailHeaderName["lngproductunitcode"] = "単位";
$aryTableDetailHeaderName["lngproductquantity"] = "数量";
$aryTableDetailHeaderName["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName["lngtaxclasscode"] = "税区分";
$aryTableDetailHeaderName["curtax"] = "税率";
$aryTableDetailHeaderName["curtaxprice"] = "税額";
$aryTableDetailHeaderName["strdetailnote"] = "明細備考";

// TODO 要リファクタリング
// 指定されたテーブル項目のカラムを作成する
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}
// 明細ヘッダーを作成する
foreach ($aryTableDetailHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}
// 削除項目を表示
if ($existsDelete) {
    // 削除カラム
    $thDelete = $doc->createElement("th", toUTF8("削除"));
    $thDelete->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thDelete);
}

// 無効項目を表示
if ($existsInvalid) {
    // 無効カラム
    $thInvalid = $doc->createElement("th", toUTF8("無効"));
    $thInvalid->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thInvalid);
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
    // 最新仕入かどうかのフラグ
    $isMaxStock = false;
    // 履歴有無フラグ
    $historyFlag = false;
    // リビジョン番号
    $revisionNos = "";

    // 同じ仕入NOの最新仕入データのリビジョン番号を取得する
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngstockno, lngrevisionno ";
    $aryQuery[] = "FROM m_stock";
    $aryQuery[] = "WHERE strstockcode='" . $record["strstockcode"] . "' ";
    $aryQuery[] = "and lngrevisionno >= 0";
    $aryQuery[] = "and bytInvalidFlag = FALSE ";
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
            if ($j == 0) {
                $maxStockInfo = $objDB->fetchArray($lngResultID, $j);
                // 該当製品のリビジョン番号<0の場合、削除済となる
                if ($maxStockInfo["lngrevisionno"] < 0) {
                    $deletedFlag = true;
                }

                if ($maxStockInfo["lngrevisionno"] != 0) {
                    $revisedFlag = true;
                }
                if ($maxStockInfo["lngrevisionno"] == $record["lngrevisionno"]) {
                    $isMaxStock = true;
                }
            } else {
                $stockInfo = $objDB->fetchArray($lngResultID, $j);
                if ($revisionNos == "") {
                    $revisionNos = $stockInfo["lngrevisionno"];
                } else {
                    $revisionNos = $revisionNos . "," . $stockInfo["lngrevisionno"];
                }
            }
            
        }
    }

    $objDB->freeResult($lngResultID);

    // 詳細データを取得する
    $detailData = fncGetDetailData($record["lngstockno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxStock) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }
    // 明細番号取得
    for ($i = $rowspan; $i > 0; $i--) {
        if ($detailnos == "") {
            $detailnos = $detailData[$i]["lngstockdetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lngstockdetailno"];
        }
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    if (!$isMaxStock) {
        $trBody->setAttribute("id", $record["strstockcode"] . "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    } else {
        $trBody->setAttribute("id", $record["strstockcode"]);
    }
    $trBody->setAttribute("detailnos", $detailnos);

    // 項番
    if ($isMaxStock) {
        $index = $index + 1;
        $subnum = 1;
        $tdIndex = $doc->createElement("td", $index);
    } else {
        $subindex = $index . "." . ($subnum++);
        $tdIndex = $doc->createElement("td", $subindex);
    }
    $tdIndex->setAttribute("class", $exclude);
    $tdIndex->setAttribute("style", $bgcolor);
    $tdIndex->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($tdIndex);

    // 詳細を表示
    if ($existsDetail) {
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
            $imgDetail->setAttribute("id", $record["lngstockno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 修正項目を表示
    if ($existsFix) {
        // 修正セル
        $tdFix = $doc->createElement("td");
        $tdFix->setAttribute("class", $exclude);
        $tdFix->setAttribute("style", $bgcolor . "text-align: center;");
        $tdFix->setAttribute("rowspan", $rowspan);

        // 修正ボタンの表示
        if ($allowedFix && $isMaxStock && $record["lngrevisionno"] >= 0 && $record["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
            // 修正ボタン
            $imgFix = $doc->createElement("img");
            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
            $imgFix->setAttribute("id", $record["lngstockno"]);
            $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgFix->setAttribute("class", "fix button");
            // td > img
            $tdFix->appendChild($imgFix);
        }
        // tr > td
        $trBody->appendChild($tdFix);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        $tdHistory->setAttribute("rowspan", $rowspan);

        if ($isMaxStock and $historyFlag and array_key_exists("admin", $optionColumns)) {
            // 履歴ボタン
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strstockcode"]);
            $imgHistory->setAttribute("revisionnos", $revisionNos);
            $imgHistory->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("maxdetailno", $detailData[$rowspan - 1]["lngstockdetailno"]);
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
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 仕入日
                case "dtmappropriationdate":
                    $td = $doc->createElement("td", $record["dtmappropriationdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 仕入ＮＯ.
                case "strstockcode":
                    $td = $doc->createElement("td", $record["strstockcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // リビジョン番号
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 発注ＮＯ.
                case "strordercode":
                    $td = $doc->createElement("td", $record["strordercode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 納品書ＮＯ.
                case "strslipcode":
                    $td = $doc->createElement("td", $record["strslipcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [入力者表示コード] 入力者表示名
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // [仕入先表示コード] 入力者表示名
                case "lngcustomercode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 状態
                case "lngstockstatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strstockstatusname"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 支払条件
                case "lngpayconditioncode":
                    $td = $doc->createElement("td", toUTF8($record["strpayconditionname"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 製品到着日
                case "dtmexpirationdate":
                    $td = $doc->createElement("td", toUTF8($record["dtmexpirationdate"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 備考
                case "strnote":
                    $td = $doc->createElement("td", toUTF8($record["strnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 合計金額
                case "curtotalprice":
                    $td = $doc->createElement("td", toUTF8($record["curtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
    

    // 明細データの設定
    fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[0]);

    // tbody > tr
    $tbody->appendChild($trBody);


    // 削除項目を表示
    if ($existsDelete) {
        // 削除セル
        $tdDelete = $doc->createElement("td");
        $tdDelete->setAttribute("class", $exclude);
        $tdDelete->setAttribute("style", $bgcolor . "text-align: center;");
        $tdDelete->setAttribute("rowspan", $rowspan);

        $showDeleteFlag = false;
        if ($allowedDelete) {
            if (!$revisedFlag) {
                if ($record["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
                    $showDeleteFlag = true;
                }
            } else {
                if ($isMaxStock) {
                    if ($record["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
                        $showDeleteFlag = true;
                    }
                }
            }
        }

        // 削除ボタンの表示
        if ($showDeleteFlag && $isMaxStock) {
            // 削除ボタン
            $imgDelete = $doc->createElement("img");
            $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
            $imgDelete->setAttribute("id", $record["lngstockno"]);
            $imgDelete->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDelete->setAttribute("class", "delete button");
            // td > img
            $tdDelete->appendChild($imgDelete);
        }
        // tr > td
        $trBody->appendChild($tdDelete);
    }

    // 無効項目を表示
    if ($existsInvalid) {
        // 無効セル
        $tdInvalid = $doc->createElement("td");
        $tdInvalid->setAttribute("class", $exclude);
        $tdInvalid->setAttribute("style", $bgcolor . "text-align: center;");
        $tdInvalid->setAttribute("rowspan", $rowspan);

        // 無効ボタンの表示
        if ($allowedInvalid && $isMaxStock && $record["lngstockstatuscode"] != DEF_STOCK_CLOSED) {
            // 無効ボタン
            $imgInvalid = $doc->createElement("img");
            $imgInvalid->setAttribute("src", "/img/type01/pc/invalid_off_bt.gif");
            $imgInvalid->setAttribute("id", $record["lngstockno"]);
            $imgInvalid->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgInvalid->setAttribute("class", "invalid button");
            // td > img
            $tdInvalid->appendChild($imgInvalid);
        }
        // tr > td
        $trBody->appendChild($tdInvalid);
    }

    // tbody > tr
    $tbody->appendChild($trBody);

    
    // 明細行のtrの追加
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");
        if (!$isMaxStock) {
            $trBody->setAttribute("style", "display: none;");
        }
        $trBody->setAttribute("id", $record["strstockcode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngstockdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i]);

        $tbody->appendChild($trBody);

    }
}

// HTML出力
echo $doc->saveHTML();
