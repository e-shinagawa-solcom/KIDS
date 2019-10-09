<?php

// ----------------------------------------------------------------------------
/**
 *       売上管理  検索
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
// 602 売上管理（受注検索）
if (!fncCheckAuthority(DEF_FUNCTION_SC1, $objAuth)) {
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
// 明細表示フラグ
$isDisplayDetail = false;
if (array_key_exists("lngInChargeGroupCode", $displayColumns) or
    array_key_exists("lngInChargeUserCode", $displayColumns) or
    array_key_exists("lngRecordNo", $displayColumns) or
    array_key_exists("lngSalesClassCode", $displayColumns) or
    array_key_exists("strGoodsCode", $displayColumns) or
    array_key_exists("curProductPrice", $displayColumns) or
    array_key_exists("lngProductUnitCode", $displayColumns) or
    array_key_exists("lngProductQuantity", $displayColumns) or
    array_key_exists("curSubTotalPrice", $displayColumns) or
    array_key_exists("lngTaxClassCode", $displayColumns) or
    array_key_exists("curTax", $displayColumns) or
    array_key_exists("curTaxPrice", $displayColumns) or
    array_key_exists("strDetailNote", $displayColumns) or
    array_key_exists("strProductName", $displayColumns)) {
    $isDisplayDetail = true;
}
// 明細検索条件数
$detailConditionCount = 0;

// クエリの組立て
$aryQuery = array();
$aryQuery[] = "SELECT distinct";
$aryQuery[] = "  s.lngSalesNo as lngSalesNo";
$aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
// $aryQuery[] = "  , sd.lngSalesDetailNo";
// $aryQuery[] = "  , sd.strProductCode";
// $aryQuery[] = "  , sd.struserdisplaycode";
// $aryQuery[] = "  , sd.struserdisplayname";
// $aryQuery[] = "  , sd.strGroupDisplayCode";
// $aryQuery[] = "  , sd.strGroupDisplayname";
// $aryQuery[] = "  , sd.strProductName";
// $aryQuery[] = "  , sd.strProductEnglishName";
// $aryQuery[] = "  , sd.lngSalesClassCode";
// $aryQuery[] = "  , sd.strSalesClassName";
// $aryQuery[] = "  , sd.strGoodsCode";
// $aryQuery[] = "  , To_char(sd.curProductPrice, '9,999,999,990.99') as curProductPrice";
// $aryQuery[] = "  , sd.lngProductUnitCode";
// $aryQuery[] = "  , sd.strProductUnitName";
// $aryQuery[] = "  , To_char(sd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
// $aryQuery[] = "  , To_char(sd.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
// $aryQuery[] = "  , sd.lngTaxClassCode";
// $aryQuery[] = "  , sd.strTaxClassName";
// $aryQuery[] = "  , sd.curTax";
// $aryQuery[] = "  , To_char(sd.curTaxPrice, '9,999,999,990.99') as curTaxPrice";
// $aryQuery[] = "  , sd.strNote as strDetailNote";
$aryQuery[] = "  , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
$aryQuery[] = "  , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
$aryQuery[] = "  , s.strSalesCode as strSalesCode";
$aryQuery[] = "  , r.strCustomerReceiveCode as strCustomerReceiveCode";
$aryQuery[] = "  , s.strSlipCode as strSlipCode";
$aryQuery[] = "  , s.lngInputUserCode as lngInputUserCode";
$aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
$aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
$aryQuery[] = "  , s.lngCustomerCompanyCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerCompanyCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerCompanyName";
$aryQuery[] = "  , s.lngSalesStatusCode as lngSalesStatusCode";
$aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName";
$aryQuery[] = "  , s.strNote as strNote";
$aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
$aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
$aryQuery[] = "  , s.lngMonetaryUnitCode as lngMonetaryUnitCode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_Sales s ";
$aryQuery[] = "  left join t_salesdetail tsd ";
$aryQuery[] = "    on tsd.lngsalesno = s.lngsalesno ";
$aryQuery[] = "  left join m_Receive r ";
$aryQuery[] = "    on r.lngreceiveno = tsd.lngreceiveno ";
$aryQuery[] = "  LEFT JOIN m_User input_u ";
$aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_Company cust_c ";
$aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
$aryQuery[] = "    USING (lngSalesStatusCode) ";
$aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
$aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
$aryQuery[] = "  , ( ";
$aryQuery[] = "      SELECT distinct";
$aryQuery[] = "          on (sd1.lngSalesNo) sd1.lngSalesNo";
$aryQuery[] = "        , sd1.lngSalesDetailNo";
$aryQuery[] = "        , sd1.lngRevisionNo";
$aryQuery[] = "        , p.strProductCode";
$aryQuery[] = "        , mg.strGroupDisplayCode";
$aryQuery[] = "        , mg.strGroupDisplayName";
$aryQuery[] = "        , mu.struserdisplaycode";
$aryQuery[] = "        , mu.struserdisplayname";
$aryQuery[] = "        , p.strProductName";
$aryQuery[] = "        , p.strProductEnglishName";
$aryQuery[] = "        , sd1.lngSalesClassCode";
$aryQuery[] = "        , ms.strSalesClassName";
$aryQuery[] = "        , p.strGoodsCode";
$aryQuery[] = "        , sd1.curProductPrice";
$aryQuery[] = "        , sd1.lngProductUnitCode";
$aryQuery[] = "        , mp.strproductunitname";
$aryQuery[] = "        , sd1.lngProductQuantity";
$aryQuery[] = "        , sd1.curSubTotalPrice";
$aryQuery[] = "        , sd1.lngTaxClassCode";
$aryQuery[] = "        , mtc.strtaxclassname";
$aryQuery[] = "        , mt.curtax";
$aryQuery[] = "        , sd1.curtaxprice";
$aryQuery[] = "        , sd1.strNote ";
$aryQuery[] = "      FROM";
$aryQuery[] = "        t_SalesDetail sd1 ";
$aryQuery[] = "        LEFT JOIN (";
$aryQuery[] = "            select p1.*  from m_product p1 ";
$aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode from m_Product group by strProductCode) p2";
$aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode";
$aryQuery[] = "          ) p ";
$aryQuery[] = "          ON sd1.strProductCode = p.strProductCode ";
$aryQuery[] = "        left join m_group mg ";
$aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
$aryQuery[] = "        left join m_user mu ";
$aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
$aryQuery[] = "        left join m_tax mt ";
$aryQuery[] = "          on mt.lngtaxcode = sd1.lngtaxcode ";
$aryQuery[] = "        left join m_taxclass mtc ";
$aryQuery[] = "          on mtc.lngtaxclasscode = sd1.lngtaxclasscode ";
$aryQuery[] = "        left join m_salesclass ms ";
$aryQuery[] = "          on ms.lngsalesclasscode = sd1.lngsalesclasscode";
$aryQuery[] = "        left join m_productunit mp ";
$aryQuery[] = "          on mp.lngproductunitcode = sd1.lngproductunitcode ";

// 製品コード
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE (" : "AND (";
    $strProductCodeArray = explode(",", $searchValue["strProductCode"]);
    $count = 0;
    foreach ($strProductCodeArray as $strProductCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strProductCode, '-') !== false) {
            $aryQuery[] = "(p.strProductCode" .
            " between '" . explode("-", $strProductCode)[0] . "'" .
            " AND " . "'" . explode("-", $strProductCode)[1] . "')";
        } else {
            if (strpos($strProductCode, '_') !== false) {
                $aryQuery[] = "p.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                $aryQuery[] = " AND p.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
            } else {
                $aryQuery[] = "p.strProductCode = '" . $strProductCode . "'";
            }
        }

    }
    $aryQuery[] = ")";
}

// 製品名称
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
    $aryQuery[] = " OR ";
    $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";

}

// 顧客品番
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
}

// 営業部署
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "mg.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
}

// 開発担当者
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "mu.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
}

// 売上区分
if (array_key_exists("lngSalesClassCode", $searchColumns) &&
    array_key_exists("lngSalesClassCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "rd1.lngSalesClassCode = '" . pg_escape_string($searchValue["lngSalesClassCode"]) . "'";
}

$aryQuery[] = "    ) as sd ";
$aryQuery[] = "WHERE";
$aryQuery[] = " sd.lngSalesNo = s.lngSalesNo ";

// 登録日
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = "AND s.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}

// 請求日
if (array_key_exists("dtmAppropriationDate", $searchColumns) &&
    array_key_exists("dtmAppropriationDate", $from) &&
    array_key_exists("dtmAppropriationDate", $to)) {
    $aryQuery[] = "AND s.dtmAppropriationDate" .
        " between '" . $from["dtmAppropriationDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmAppropriationDate"] . " 23:59:59.99999'";
}
// 売上No.
if (array_key_exists("strSalesCode", $searchColumns) &&
    array_key_exists("strSalesCode", $searchValue)) {

    $detailConditionCount += 1;
    $aryQuery[] = " AND (";
    $strSalesCodeArray = explode(",", $searchValue["strSalesCode"]);
    $count = 0;
    foreach ($strSalesCodeArray as $strSalesCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strSalesCode, '-') !== false) {
            $aryQuery[] = "(s.strSalesCode" .
            " between '" . explode("-", $strSalesCode)[0] . "'" .
            " AND " . "'" . explode("-", $strSalesCode)[1] . "')";
        } else {
            $aryQuery[] = "s.strSalesCode = '" . $strSalesCode . "'";
        }

    }
    $aryQuery[] = ")";
}
// 顧客受注番号
if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
    array_key_exists("strCustomerReceiveCode", $searchValue)) {

    $detailConditionCount += 1;
    $aryQuery[] = " AND (";
    $strCustomerReceiveCodeArray = explode(",", $searchValue["strCustomerReceiveCode"]);
    $count = 0;
    foreach ($strCustomerReceiveCodeArray as $strCustomerReceiveCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strCustomerReceiveCode, '-') !== false) {
            $aryQuery[] = "(s.strCustomerReceiveCode" .
            " between '" . explode("-", $strCustomerReceiveCode)[0] . "'" .
            " AND " . "'" . explode("-", $strCustomerReceiveCode)[1] . "')";
        } else {
            $aryQuery[] = "s.strCustomerReceiveCode = '" . $strCustomerReceiveCode . "'";
        }

    }
    $aryQuery[] = ")";
}

// 納品書NO.
if (array_key_exists("strSlipCode", $searchColumns) &&
    array_key_exists("strSlipCode", $searchValue)) {

    $detailConditionCount += 1;
    $aryQuery[] = " AND (";
    $strSlipCodeArray = explode(",", $searchValue["strSlipCode"]);
    $count = 0;
    foreach ($strSlipCodeArray as $strSlipCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strSalesCode, '-') !== false) {
            $aryQuery[] = "(s.strSlipCode" .
            " between '" . explode("-", $strSlipCode)[0] . "'" .
            " AND " . "'" . explode("-", $strSlipCode)[1] . "')";
        } else {
            $aryQuery[] = "s.strSlipCode = '" . $strSlipCode . "'";
        }
    }
    $aryQuery[] = ")";
}

// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}

// 顧客
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
}

// 状態
if (array_key_exists("lngSalesStatusCode", $searchColumns) &&
    array_key_exists("lngSalesStatusCode", $searchValue)) {
    if (is_array($searchValue["lngSalesStatusCode"])) {
        $searchStatus = implode(",", $searchValue["lngSalesStatusCode"]);
        $aryQuery[] = " AND s.lngSalesStatusCode in (" . $searchStatus . ")";
    }
}

// if (!array_key_exists("admin", $optionColumns)) {
//     $aryQuery[] = "  AND s.strSalesCode not in ( ";
//     $aryQuery[] = "    select";
//     $aryQuery[] = "      s1.strSalesCode ";
//     $aryQuery[] = "    from";
//     $aryQuery[] = "      ( ";
//     $aryQuery[] = "        SELECT";
//     $aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
//     $aryQuery[] = "          , strSalesCode ";
//     $aryQuery[] = "        FROM";
//     $aryQuery[] = "          m_sales ";
//     $aryQuery[] = "        group by";
//     $aryQuery[] = "          strSalesCode";
//     $aryQuery[] = "      ) as s1 ";
//     $aryQuery[] = "    where";
//     $aryQuery[] = "      s1.lngRevisionNo < 0";
//     $aryQuery[] = "  ) ";
// } else {
    $aryQuery[] = "  AND s.bytInvalidFlag = FALSE ";
    $aryQuery[] = "  AND s.lngRevisionNo >= 0 ";
// }
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  strSalesCode, lngRevisionNo DESC";

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
    $lngErrorCode = 603;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // エラー画面の戻り先
    $strReturnPath = "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/sc/search/sc_search_result.html");

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
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SC3, $objAuth);

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
$aryTableHeaderName["dtmappropriationdate"] = "請求日";
$aryTableHeaderName["strsalescode"] = "売上NO.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName["strslipcode"] = "納品書NO.";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngsalesstatuscode"] = "状態";
$aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["curtotalprice"] = "合計金額";
$aryTableDetailHeaderName["lngrecordno"] = "明細行番号";
$aryTableDetailHeaderName["strproductcode"] = "製品コード";
$aryTableDetailHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableDetailHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableDetailHeaderName["strproductname"] = "製品名";
$aryTableDetailHeaderName["lngsalesclasscode"] = "売上区分";
$aryTableDetailHeaderName["strgoodscode"] = "顧客品番";
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

// 指定されたテーブル項目のカラムを作成する
foreach ($aryTableDetailHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);

        if ($key == "strproductname") {
            $th = $doc->createElement("th", toUTF8("製品名（英語）"));
            $trHead->appendChild($th);
        }
    }
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
    // リビジョン番号
    $revisionNos = "";
    // 明細番号
    $detailnos = "";
    // 最新売上かどうかのフラグ
    $isMaxSales = false;
    // 履歴有無フラグ
    $historyFlag = false;

    // 同じ売上NOの最新売上データのリビジョン番号を取得する
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngsalesno, lngrevisionno ";
    $aryQuery[] = "FROM m_sales";
    $aryQuery[] = "WHERE strsalescode='" . $record["strsalescode"] . "' ";
    $aryQuery[] = "and lngrevisionno >= 0";
    $aryQuery[] = "and bytInvalidFlag = FALSE ";
    $aryQuery[] = "order by lngrevisionno desc";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);
// echo $strQuery;return;
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
        for ($j = 0; $j < $lngResultNum; $j++) {
            if ($j == 0) {
                $maxSalesInfo = $objDB->fetchArray($lngResultID, $j);
                // 該当製品のリビジョン番号<0の場合、削除済となる
                if ($maxSalesInfo["lngrevisionno"] < 0) {
                    $deletedFlag = true;
                }
                if ($maxSalesInfo["lngrevisionno"] == $record["lngrevisionno"]) {
                    $isMaxSales = true;
                }
            } else {
                $salesInfo = $objDB->fetchArray($lngResultID, $j);
                if ($revisionNos == "") {
                    $revisionNos = $salesInfo["lngrevisionno"];
                } else {
                    $revisionNos = $revisionNos . "," . $salesInfo["lngrevisionno"];
                }
            }
        }
    }

    $objDB->freeResult($lngResultID);

    // 詳細データを取得する
    $detailData = fncGetDetailData($record["lngsalesno"], $record["lngrevisionno"], $objDB);
    $rowspan = count($detailData);

    // 背景色設定
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxSales) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // 明細番号取得
    for ($i = $rowspan; $i > 0; $i--) {
        if ($detailnos == "") {
            $detailnos = $detailData[$i]["lngsalesdetailno"];
        } else {
            $detailnos = $detailnos . "," . $detailData[$i]["lngsalesdetailno"];
        }
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    if (!$isMaxSales) {
        $trBody->setAttribute("style", "display: none;");
        $trBody->setAttribute("id", $record["strsalescode"] . "_" . $record["lngrevisionno"]);
    } else {
        $trBody->setAttribute("id", $record["strsalescode"]);
    }
    $trBody->setAttribute("detailnos", $detailnos);

    // 項番
    if ($isMaxSales) {
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
        $tdDetail->setAttribute("style", $bgcolor. "text-align: center;");
        $tdDetail->setAttribute("rowspan", $rowspan);

        // 詳細ボタンの表示
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngsalesno"]);
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
        $tdHistory->setAttribute("style", $bgcolor. "text-align: center;");
        $tdHistory->setAttribute("rowspan", $rowspan);

        if ($isMaxSales and $historyFlag) {
            // 履歴ボタン
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strsalescode"]);
            $imgHistory->setAttribute("revisionnos", $revisionNos);
            $imgHistory->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgHistory->setAttribute("maxdetailno", $detailData[$rowspan - 1]["lngsalesdetailno"]);
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
                // 請求日
                case "dtmappropriationdate":
                    $td = $doc->createElement("td", $record["dtmappropriationdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 売上NO.
                case "strsalescode":
                    $td = $doc->createElement("td", $record["strsalescode"]);
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
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 納品書NO.
                case "strslipcode":
                    $td = $doc->createElement("td", $record["strslipcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 顧客受注番号
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
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
                // [顧客表示コード] 入力者表示名
                case "lngcustomercompanycode":
                    $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 状態
                case "lngsalesstatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strsalesstatusname"]));
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
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtotalprice"]));
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

    // 明細行のtrの追加
    for ($i = 1; $i < $rowspan; $i++) {
        $trBody = $doc->createElement("tr");
        if (!$isMaxSales) {
            $trBody->setAttribute("style", "display: none;");
        }
        $trBody->setAttribute("id", $record["strsalescode"] . "_" . $record["lngrevisionno"] . "_" . $detailData[$i]["lngsalesdetailno"]);

        fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData[$i]);

        $tbody->appendChild($trBody);

    }

}

// HTML出力
echo $doc->saveHTML();

/**
 * 明細データの取得
 *
 * @param [type] $lngSalesNo
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void
 */
function fncGetDetailData($lngSalesNo, $lngRevisionNo, $objDB)
{
    $detailData = array();
    unset($aryQuery);
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  sd.lngSalesNo";
    $aryQuery[] = "  , sd.lngSalesDetailNo";
    $aryQuery[] = "  , p.strProductCode";
    $aryQuery[] = "  , mg.strGroupDisplayCode";
    $aryQuery[] = "  , mg.strGroupDisplayName";
    $aryQuery[] = "  , mu.struserdisplaycode";
    $aryQuery[] = "  , mu.struserdisplayname";
    $aryQuery[] = "  , p.strProductName";
    $aryQuery[] = "  , p.strProductEnglishName";
    $aryQuery[] = "  , sd.lngSalesClassCode";
    $aryQuery[] = "  , ms.strSalesClassName";
    $aryQuery[] = "  , p.strGoodsCode";
    $aryQuery[] = "  , To_char(sd.curProductPrice, '9,999,999,990.99') as curProductPrice";
    $aryQuery[] = "  , sd.lngProductUnitCode";
    $aryQuery[] = "  , mp.strproductunitname";
    $aryQuery[] = "  , To_char(sd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
    $aryQuery[] = "  , To_char(sd.curSubTotalPrice, '9,999,999,990') as curSubTotalPrice";
    $aryQuery[] = "  , sd.lngTaxClassCode";
    $aryQuery[] = "  , mtc.strtaxclassname";
    $aryQuery[] = "  , mt.curtax";
    $aryQuery[] = "  , sd.curtaxprice";
    $aryQuery[] = "  , sd.strNote ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  t_SalesDetail sd ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      p1.* ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_product p1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "          , strproductcode ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Product ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          strProductCode";
    $aryQuery[] = "      ) p2 ";
    $aryQuery[] = "        on p1.lngRevisionNo = p2.lngRevisionNo ";
    $aryQuery[] = "        and p1.strproductcode = p2.strproductcode";
    $aryQuery[] = "  ) p ";
    $aryQuery[] = "    ON sd.strProductCode = p.strProductCode ";
    $aryQuery[] = "  left join m_group mg ";
    $aryQuery[] = "    on p.lnginchargegroupcode = mg.lnggroupcode ";
    $aryQuery[] = "  left join m_user mu ";
    $aryQuery[] = "    on p.lnginchargeusercode = mu.lngusercode ";
    $aryQuery[] = "  left join m_tax mt ";
    $aryQuery[] = "    on mt.lngtaxcode = sd.lngtaxcode ";
    $aryQuery[] = "  left join m_taxclass mtc ";
    $aryQuery[] = "    on mtc.lngtaxclasscode = sd.lngtaxclasscode ";
    $aryQuery[] = "  left join m_salesclass ms ";
    $aryQuery[] = "    on ms.lngsalesclasscode = sd.lngsalesclasscode ";
    $aryQuery[] = "  left join m_productunit mp ";
    $aryQuery[] = "    on mp.lngproductunitcode = sd.lngproductunitcode ";
    $aryQuery[] = "where";
    $aryQuery[] = "  sd.lngsalesno = " . $lngSalesNo;
    $aryQuery[] = "  and sd.lngrevisionno = " . $lngRevisionNo;
    $aryQuery[] = "order by sd.lngSalesDetailNo";

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        // 指定数以内であれば通常処理
        for ($i = 0; $i < $lngResultNum; $i++) {
            $detailData = pg_fetch_all($lngResultID);
        }
    }
    $objDB->freeResult($lngResultID);

    return $detailData;
}

/**
 * 明細行データの生成
 *
 * @param [type] $doc
 * @param [type] $trBody
 * @param [type] $bgcolor
 * @param [type] $aryTableDetailHeaderName
 * @param [type] $displayColumns
 * @param [type] $detailData
 * @return void
 */
function fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData)
{

// 指定されたテーブル項目のセルを作成する
    foreach ($aryTableDetailHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 明細行番号
                case "lngrecordno":
                    $td = $doc->createElement("td", $detailData["lngsalesdetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;

                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $detailData["strproductcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [営業部署表示コード] 営業部署表示名
                case "lnginchargegroupcode":
                    $textContent = "[" . $detailData["strgroupdisplaycode"] . "]" . " " . $detailData["strgroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lnginchargeusercode":
                    $textContent = "[" . $detailData["struserdisplaycode"] . "]" . " " . $detailData["struserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品名称
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($detailData["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);

                    $td = $doc->createElement("td", toUTF8($detailData["strproductenglishname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 売上区分
                case "lngsalesclasscode":
                    $textContent = "[" . $detailData["lngsalesclasscode"] . "]" . " " . $detailData["strsalesclassname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($detailData["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単価
                case "curproductprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $record["curproductprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単位
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($detailData["strproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 数量
                case "lngproductquantity":
                    $td = $doc->createElement("td", toUTF8($detailData["lngproductquantity"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税抜金額
                case "cursubtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $record["cursubtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税区分
                case "lngtaxclasscode":
                    $td = $doc->createElement("td", toUTF8($detailData["strtaxclassname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税率
                case "curtax":
                    $td = $doc->createElement("td", toUTF8($detailData["curtax"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税額
                case "curtaxprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $record["curtaxprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 明細備考
                case "strdetailnote":
                    $td = $doc->createElement("td", toUTF8($detailData["strdetailnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
    return $trBody;
}
