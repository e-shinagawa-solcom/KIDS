<?php
// ----------------------------------------------------------------------------
/**
 *       仕入検索 履歴取得イベント
 *
 *       処理概要
 *         ・仕入コード、リビジョン番号により仕入履歴情報を取得する
 *
 *       更新履歴
 *
 */

// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "search/cmn/lib_search.php";

//値の取得
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();
//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

// パラメータ取得
$type = $aryData["type"];
$strCode = $aryData["strCode"];
$lngRevisionNo = $aryData["lngRevisionNo"];
$lngDetailNo = $aryData["lngDetailNo"];
$displayColumns = array();
// 表示項目の抽出
foreach ($aryData["displayColumns"] as $key) {
    $displayColumns[$key] = $key;
}
// キー文字列を小文字に変換
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);
// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);
// コード、履歴データにより履歴取得SQL
$records = fncGetHistoryDataByPKSQL($type, $strCode, $lngRevisionNo, $lngDetailNo, $objDB);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();

// 詳細ボタンを表示
if ($type == 'purchaseorder') { // 発注書
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_PO12, $objAuth);
} else if ($type == 'po') { // 発注
    // 詳細ボタンを表示
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
} else if ($type == 'so') { // 受注
    // 詳細ボタンを表示
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
} else if ($type == 'sc') { // 売上
    // 詳細ボタンを表示
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_SC11, $objAuth);
} else if ($type == 'slip') { //納品書
    // 詳細ボタンを表示
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_SC4, $objAuth);
} else if ($type == 'pc') { // 仕入
    // 詳細ボタンを表示
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
} else if ($type == 'inv') {
    // 詳細ボタンを表示
    $allowedDetail = fncCheckAuthority(DEF_FUNCTION_INV4, $objAuth);
} else if ($type == 'estimate') {
}

$aryTableBtnHeaderBeforeName = array();
$aryTableBtnHeaderBeforeName["btndetail"] = "詳細";
$aryTableBtnHeaderBeforeName["btnfix"] = "修正";
$aryTableBtnHeaderBeforeName["btnhistory"] = "履歴";
$aryTableBtnHeaderBeforeName["btndecide"] = "確定";
$aryTableBtnHeaderBackName["btninvalid"] = "無効";
$aryTableBtnHeaderBackName["btncancel"] = "確定取消";
$aryTableBtnHeaderBackName["btndelete"] = "削除";
$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "登録日";
$aryTableHeaderName["strstockcode"] = "仕入ＮＯ.";
$aryTableHeaderName["strsalescode"] = "売上NO.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName["strslipcode"] = "納品書NO.";
$aryTableHeaderName["strordercode"] = "発注書ＮＯ.";
$aryTableHeaderName["strslipcode"] = "納品書ＮＯ.";
$aryTableHeaderName["strreceivecode"] = "受注ＮＯ.";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["lngcustomercode"] = "仕入先";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngpayconditioncode"] = "支払条件";
$aryTableHeaderName["dtmexpirationdate"] = "製品到着日";
$aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["curtotalprice"] = "合計金額";
$aryTableHeaderName["lngtaxclasscode"] = "課税区分";
$aryTableHeaderName["dtmdeliverydate"] = "納品日";
$aryTableHeaderName["strinvoicecode"] = "請求書No";
$aryTableHeaderName["dtminvoicedate"] = "請求日";
$aryTableHeaderName["curlastmonthbalance"] = "先月請求残額";
$aryTableHeaderName["curthismonthamount"] = "当月請求金額";
$aryTableHeaderName["dtminsertdate"] = "作成日";
$aryTableHeaderName["lngusercode"] = "担当者";
$aryTableHeaderName["lnginsertusercode"] = "入力者";
$aryTableHeaderName["lngprintcount"] = "印刷回数";
if ($type == 'pc') {
    $aryTableHeaderName["dtmappropriationdate"] = "仕入日";
    $aryTableHeaderName["lngstockstatuscode"] = "状態";
} else if ($type == 'sc') {
    $aryTableHeaderName["dtmappropriationdate"] = "請求日";
    $aryTableHeaderName["lngsalesstatuscode"] = "状態";
} else if ($type == 'slip') {
    $aryTableHeaderName["lngcustomercode"] = "顧客";
    $aryTableHeaderName["lngdeliveryplacecode"] = "納品先";
    $aryTableHeaderName["lnginsertusercode"] = "起票者";
} else if ($type == 'po') {
    $aryTableHeaderName["lngorderstatuscode"] = "状態";
} else if ($type == 'so') {
    $aryTableHeaderName["lngreceivestatuscode"] = "状態";
    $aryTableHeaderName["strproductcode"] = "製品コード";
}

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
$aryTableDetailHeaderName["strsalesclassname"] = "売上区分";
$aryTableDetailHeaderName["lngquantity"] = "入数";
$aryTableDetailHeaderName["strcustomersalescode"] = "注文書NO";
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
$index = 0;
// 検索結果件数分走査
foreach ($records as $i => $record) {
    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    $detailData = array();
    $rowspan == 0;

    // 請求書・仕入・売上・納品書の場合詳細データを取得する
    if ($type == 'inv' || $type == 'pc' || $type == 'sc' || $type == 'slip') {
        $lngrevisionno = $record["lngrevisionno"];
        $lngpkno = $record["lngpkno"];
        $detailData = fncGetDetailData($lngpkno, $record["lngrevisionno"], $objDB);
        $rowspan = count($detailData);
    }

    if ($rowspan == 0) {
        $rowspan = 1;
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    if ($type == 'so' || $type== 'po') {
        $trBody->setAttribute("id", $record["strcode"] . "_" . $record["lngreceivedetailno"] . "_" . $record["lngrevisionno"]);
    } else {
        $trBody->setAttribute("id", $record["strcode"] . "_" . $record["lngrevisionno"]);
    }
    $trBody->setAttribute("class", 'detail');

    // 項番
    $index = $index + 1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"] . "." . $index);
    $tdIndex->setAttribute("style", $bgcolor);
    $tdIndex->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($tdIndex);

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($displayColumns as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $aryTableBtnHeaderBeforeName)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 詳細
                case "btndetail":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    // 詳細ボタンの表示
                    if ($allowedDetail && $record["lngrevisionno"] >= 0) {
                        // 詳細ボタン
                        $imgDetail = $doc->createElement("img");
                        $imgDetail->setAttribute("src", "/img/type01/pc/detail_off_bt.gif");
                        $imgDetail->setAttribute("id", $record["lngpkno"]);
                        $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgDetail->setAttribute("class", "detail button");
                        // td > img
                        $td->appendChild($imgDetail);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
                // 修正・履歴・確定・無効・削除・確定取消
                case "btnfix":
                case "btnhistory":
                case "btndecide":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    // tr > td
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($displayColumns as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $aryTableHeaderName)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 登録日
                case "dtminsertdate":
                // 仕入日
                case "dtmappropriationdate":
                // 売上ＮＯ.
                case "strsalescode":
                // 受注ＮＯ.
                case "strreceivecode":
                // 仕入ＮＯ.
                case "strstockcode":
                // リビジョン番号
                case "lngrevisionno":
                // 納品書ＮＯ.
                case "strslipcode":
                // 請求書ＮＯ.
                case "strinvoicecode":
                // 製品到着日
                case "dtmexpirationdate":
                // 納品日
                case "dtmdeliverydate":
                // 請求日
                case "dtminvoicedate":
                // 製品コード
                case "strproductcode":
                // 備考
                case "strnote":
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $textContent = $record[$key];
                    break;
                // [入力者表示コード] 入力者表示名
                case "lnginputusercode":
                case "lnginsertusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    break;
                // [仕入先表示コード]・[顧客表示コード] 入力者表示名
                case "lngcustomercode":
                case "lngcustomercompanycode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    break;
                // 状態
                case "lngstockstatuscode":
                case "lngsalesstatuscode":
                case "lngorderstatuscode":
                case "lngreceivestatuscode":
                    $textContent = $record["strstatusname"];
                    break;
                // 支払条件
                case "lngpayconditioncode":
                    $textContent = $record["strpayconditionname"];
                    break;
                // 合計金額
                case "curtotalprice":
                    $textContent = $record["curtotalprice"];
                    break;
                // 先月請求残額
                case "curLastMonthBalance":
                    $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curlastmonthbalance"]);
                    break;
                // 当月請求金額.
                case "curThisMonthAmount":
                    $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curthismonthamount"]);
                    break;
                // 税区分
                case "lngtaxclasscode":
                    $textContent = $record["strtaxclassname"];
                    break;
                // 担当者
                case "lngusercode":
                    if ($record["strusercode"] != '') {
                        $textContent = "[" . $record["strusercode"] . "]" . " " . $record["strusername"];
                    } else {
                        $textContent .= "     ";
                    }
                    break;
                // 印刷回数
                case "lngprintcount":
                    if (empty($record["lngprintcount"])) {
                        $textContent = '0';
                    } else {
                        $textContent = $record["lngprintcount"];
                    }
                    break;
            }
            $td = $doc->createElement("td", $textContent);
            $td->setAttribute("style", $bgcolor);
            $td->setAttribute("rowspan", $rowspan);
            $trBody->appendChild($td);
        }
    }

    // 明細データの設定
    if (count($detailData) > 0) {
        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[0], $record, false);
    }
    // tbody > tr
    // $strHtml .= $doc->saveXML($trBody);

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($displayColumns as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $aryTableBtnHeaderBackName)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                case "btncancel":
                case "btndelete":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    // tr > td
                    $trBody->appendChild($td);
                    break;
            }
        }
    }

    // tbody > tr
    $strHtml .= $doc->saveXML($trBody);

    if (count($detailData) > 0) {
        // 明細行のtrの追加
        for ($i = 1; $i < $rowspan; $i++) {
            $trBody = $doc->createElement("tr");
            $trBody->setAttribute("id", $record["strcode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngrecodeno"]);
            fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i], $record, false);
            $strHtml .= $doc->saveXML($trBody);
        }
    }
}

// HTML出力
echo $strHtml;
