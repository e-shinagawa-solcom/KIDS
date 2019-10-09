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

// 明細表示フラグ
$isDisplayDetail = false;
if (array_key_exists("strProductCode", $displayColumns) or
    array_key_exists("lngInChargeGroupCode", $displayColumns) or
    array_key_exists("lngInChargeUserCode", $displayColumns) or
    array_key_exists("lngRecordNo", $displayColumns) or
    array_key_exists("lngStockSubjectCode", $displayColumns) or
    array_key_exists("lngStockItemCode", $displayColumns) or
    array_key_exists("strGoodsCode", $displayColumns) or
    array_key_exists("lngDeliveryMethodCode", $displayColumns) or
    array_key_exists("curProductPrice", $displayColumns) or
    array_key_exists("lngProductUnitCode", $displayColumns) or
    array_key_exists("lngProductQuantity", $displayColumns) or
    array_key_exists("curSubTotalPrice", $displayColumns) or
    array_key_exists("lngTaxClassCode", $displayColumns) or
    array_key_exists("curTax", $displayColumns) or
    array_key_exists("curTaxPrice", $displayColumns) or
    array_key_exists("strDetailNote", $displayColumns) or
    // array_key_exists("dtmDeliveryDate", $displayColumns) or
    array_key_exists("strProductName", $displayColumns) or
    array_key_exists("strProductEnglishName", $displayColumns)) {
    $isDisplayDetail = true;
}

// 明細検索条件数
$detailConditionCount = 0;
// クエリの組立て
$aryQuery = array();
$aryQuery[] = "SELECT";
$aryQuery[] = "  s.lngStockNo as lngStockNo";
$aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
$aryQuery[] = "  , sd.strordercode";
$aryQuery[] = "  , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
$aryQuery[] = "  , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
$aryQuery[] = "  , to_char(s.dtmexpirationdate, 'YYYY/MM/DD') as dtmexpirationdate";
$aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
$aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
$aryQuery[] = "  , s.strStockCode as strStockCode";
$aryQuery[] = "  , s.strslipcode as strslipcode";
$aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
$aryQuery[] = "  , s.lngStockStatusCode as lngStockStatusCode";
$aryQuery[] = "  , rs.strStockStatusName as strStockStatusName";
$aryQuery[] = "  , s.lngpayconditioncode as lngpayconditioncode";
$aryQuery[] = "  , mp.strpayconditionname as strpayconditionname";
$aryQuery[] = "  , s.strNote as strNote";
$aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
$aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign";
$aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_Stock s ";
$aryQuery[] = "  LEFT JOIN m_User input_u ";
$aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_Company cust_c ";
$aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_StockStatus rs ";
$aryQuery[] = "    USING (lngStockStatusCode) ";
$aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
$aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
$aryQuery[] = "  LEFT JOIN m_paycondition mp ";
$aryQuery[] = "    ON s.lngpayconditioncode = mp.lngpayconditioncode";
$aryQuery[] = "  , ( ";
$aryQuery[] = "      SELECT distinct";
$aryQuery[] = "          on (sd1.lngStockNo) sd1.lngStockNo";
$aryQuery[] = "        , sd1.lngStockDetailNo";
$aryQuery[] = "        , sd1.lngRevisionNo ";
$aryQuery[] = "        , o.strordercode";
$aryQuery[] = "        , p.strProductCode";
$aryQuery[] = "        , mg.strGroupDisplayCode";
$aryQuery[] = "        , mg.strGroupDisplayName";
$aryQuery[] = "        , mu.struserdisplaycode";
$aryQuery[] = "        , mu.struserdisplayname";
$aryQuery[] = "        , p.strProductName";
$aryQuery[] = "        , p.strProductEnglishName";
$aryQuery[] = "        , sd1.lngStockSubjectCode";
$aryQuery[] = "        , ss.strStockSubjectName";
$aryQuery[] = "        , sd1.lngStockItemCode";
$aryQuery[] = "        , si.strStockItemName";
$aryQuery[] = "        , sd1.strMoldNo";
$aryQuery[] = "        , p.strGoodsCode";
$aryQuery[] = "        , sd1.lngDeliveryMethodCode";
$aryQuery[] = "        , dm.strDeliveryMethodName";
$aryQuery[] = "        , sd1.curProductPrice";
$aryQuery[] = "        , sd1.lngProductUnitCode";
$aryQuery[] = "        , pu.strProductUnitName";
$aryQuery[] = "        , sd1.lngProductQuantity";
$aryQuery[] = "        , sd1.curSubTotalPrice";
$aryQuery[] = "        , sd1.lngTaxClassCode";
$aryQuery[] = "        , mtc.strTaxClassName";
$aryQuery[] = "        , mt.curtax";
$aryQuery[] = "        , sd1.curtaxprice";
$aryQuery[] = "        , sd1.strNote ";
$aryQuery[] = "      FROM";
$aryQuery[] = "        t_StockDetail sd1 ";
$aryQuery[] = "        LEFT JOIN (";
$aryQuery[] = "            select p1.*  from m_product p1 ";
$aryQuery[] = "        	inner join (select max(lngrevisionno) lngrevisionno, strproductcode from m_Product group by strProductCode) p2";
$aryQuery[] = "            on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode";
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
$aryQuery[] = "        LEFT JOIN m_Stocksubject ss ";
$aryQuery[] = "          on ss.lngStocksubjectcode = sd1.lngStocksubjectcode ";
$aryQuery[] = "        LEFT JOIN m_Stockitem si ";
$aryQuery[] = "          on si.lngStocksubjectcode = sd1.lngStocksubjectcode ";
$aryQuery[] = "          and si.lngStockitemcode = sd1.lngStockitemcode ";
$aryQuery[] = "        LEFT JOIN m_deliverymethod dm ";
$aryQuery[] = "          on dm.lngdeliverymethodcode = sd1.lngdeliverymethodcode ";
$aryQuery[] = "        LEFT JOIN m_productunit pu ";
$aryQuery[] = "          on pu.lngproductunitcode = sd1.lngproductunitcode";
$aryQuery[] = "        LEFT JOIN m_Order o on sd1.lngOrderNo = o.lngOrderNo";

// 発注書No
if (array_key_exists("strOrderCode", $searchColumns) &&
    array_key_exists("strOrderCode", $from) &&
    array_key_exists("strOrderCode", $to)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " o.strOrderCode" .
        " between '" . $from["strOrderCode"] . "'" .
        " AND " . "'" . $to["strOrderCode"] . "'";
}

// 製品コード
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " sd1.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
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

// 製品名称
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}

// 仕入科目コード
if (array_key_exists("lngStockSubjectCode", $searchColumns) &&
    array_key_exists("lngStockSubjectCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "sd1.lngStockSubjectCode = " . $searchValue["lngStockSubjectCode"] . "";
}

// 仕入部品コード
if (array_key_exists("lngStockItemCode", $searchColumns) &&
    array_key_exists("lngStockItemCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";

    $aryQuery[] = "sd1.lngStockItemCode = " . explode("-", $searchValue["lngStockItemCode"])[1] . "";
}
// 顧客品番
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
}
// 運搬方法
if (array_key_exists("lngDeliveryMethodCode", $searchColumns) &&
    array_key_exists("lngDeliveryMethodCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "sd1.lngDeliveryMethodCode = " . $searchValue["lngDeliveryMethodCode"] . "";
}
// // 納期
// if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
//     array_key_exists("dtmDeliveryDate", $from) &&
//     array_key_exists("dtmDeliveryDate", $to)) {
//     $aryQuery[] = "AND sd1.dtmdeliverydate" .
//         " between '" . $from["dtmDeliveryDate"] . "'" .
//         " AND " . "'" . $to["dtmDeliveryDate"] . "'";
// }
$aryQuery[] = "    ) as sd ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  sd.lngStockNo = s.lngStockNo ";
// $aryQuery[] = "  AND sd.lngRevisionNo = s.lngRevisionNo ";
// 登録日
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = "AND s.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}
// 仕入日
if (array_key_exists("dtmAppropriationDate", $searchColumns) &&
    array_key_exists("dtmAppropriationDate", $from) &&
    array_key_exists("dtmAppropriationDate", $to)) {
    $aryQuery[] = "AND s.dtmAppropriationDate" .
        " between '" . $from["dtmAppropriationDate"] . "'" .
        " AND " . "'" . $to["dtmAppropriationDate"] . "'";
}
// 製品到着日
if (array_key_exists("dtmExpirationDate", $searchColumns) &&
    array_key_exists("dtmExpirationDate", $from) &&
    array_key_exists("dtmExpirationDate", $to)) {
    $aryQuery[] = "AND s.dtmExpirationDate" .
        " between '" . $from["dtmExpirationDate"] . "'" .
        " AND " . "'" . $to["dtmExpirationDate"] . "'";
}

// 仕入Ｎｏ
if (array_key_exists("strStockCode", $searchColumns) &&
    array_key_exists("strStockCode", $from) &&
    array_key_exists("strStockCode", $to)) {
    $aryQuery[] = "AND s.strStockCode" .
        " between '" . $from["strStockCode"] . "'" .
        " AND " . "'" . $to["strStockCode"] . "'";
}
// 納品書Ｎｏ
if (array_key_exists("strSlipCode", $searchColumns) &&
    array_key_exists("strSlipCode", $searchValue)) {
    $aryQuery[] = " AND s.strSlipCode = '" . $searchValue["strSlipCode"] . "'";
}

// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}

// 仕入先
if (array_key_exists("lngCustomerCode", $searchColumns) &&
    array_key_exists("lngCustomerCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCode"] . "'";
}

// 状態
if (array_key_exists("lngStockStatusCode", $searchColumns) &&
    array_key_exists("lngStockStatusCode", $searchValue)) {
    if (is_array($searchValue["lngStockStatusCode"])) {
        $searchStatus = implode(",", $searchValue["lngStockStatusCode"]);
        $aryQuery[] = " AND s.lngStockStatusCode in (" . $searchStatus . ")";
    }
}

// 支払条件
if (array_key_exists("lngPayConditionCode", $searchColumns) &&
    array_key_exists("lngPayConditionCode", $searchValue)) {
    $aryQuery[] = " AND s.lngPayConditionCode = '" . $searchValue["lngPayConditionCode"] . "'";
}

// if (!array_key_exists("admin", $optionColumns)) {
//     $aryQuery[] = "  AND s.strStockCode not in ( ";
//     $aryQuery[] = "    select";
//     $aryQuery[] = "      s2.strStockCode ";
//     $aryQuery[] = "    from";
//     $aryQuery[] = "      ( ";
//     $aryQuery[] = "        SELECT";
//     $aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
//     $aryQuery[] = "          , strStockCode ";
//     $aryQuery[] = "        FROM";
//     $aryQuery[] = "          m_Stock ";
//     $aryQuery[] = "        group by";
//     $aryQuery[] = "          strStockCode";
//     $aryQuery[] = "      ) as s2 ";
//     $aryQuery[] = "    where";
//     $aryQuery[] = "      s2.lngRevisionNo < 0";
//     $aryQuery[] = "  ) ";
// } else {
    $aryQuery[] = " AND s.bytInvalidFlag = FALSE ";
    $aryQuery[] = " AND s.lngRevisionNo >= 0";
// }
$aryQuery[] = "ORDER BY";
$aryQuery[] = " strStockCode, lngRevisionNo DESC";

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

        if ($isMaxStock and $historyFlag) {
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

/**
 * 明細データの取得
 *
 * @param [type] $lngSalesNo
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void
 */
function fncGetDetailData($lngStockNo, $lngRevisionNo, $objDB)
{
    $detailData = array();
    unset($aryQuery);
    $aryQuery[] = "SELECT sd.lngStockNo";
    $aryQuery[] = "  , sd.lngStockDetailNo";
    $aryQuery[] = "  , p.strProductCode";
    $aryQuery[] = "  , mg.strGroupDisplayCode";
    $aryQuery[] = "  , mg.strGroupDisplayName";
    $aryQuery[] = "  , mu.struserdisplaycode";
    $aryQuery[] = "  , mu.struserdisplayname";
    $aryQuery[] = "  , p.strProductName";
    $aryQuery[] = "  , p.strProductEnglishName";
    $aryQuery[] = "  , sd.lngStockSubjectCode";
    $aryQuery[] = "  , ss.strStockSubjectName";
    $aryQuery[] = "  , sd.lngStockItemCode";
    $aryQuery[] = "  , si.strStockItemName";
    $aryQuery[] = "  , sd.strMoldNo";
    $aryQuery[] = "  , p.strGoodsCode";
    $aryQuery[] = "  , sd.lngDeliveryMethodCode";
    $aryQuery[] = "  , dm.strDeliveryMethodName";
    $aryQuery[] = "  , sd.curProductPrice";
    $aryQuery[] = "  , sd.lngProductUnitCode";
    $aryQuery[] = "  , pu.strProductUnitName";
    $aryQuery[] = "  , sd.lngProductQuantity";
    $aryQuery[] = "  , sd.curSubTotalPrice";
    $aryQuery[] = "  , sd.lngTaxClassCode";
    $aryQuery[] = "  , mtc.strTaxClassName";
    $aryQuery[] = "  , mt.curtax";
    $aryQuery[] = "  , to_char(sd.curTaxPrice, '9,999,999,990.99') as curTaxPrice";
    $aryQuery[] = "  , sd.strNote ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  t_StockDetail sd ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      p1.* ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_product p1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "          , strproductcode ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Product ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          strProductCode";
    $aryQuery[] = "      ) p2 ";
    $aryQuery[] = "        on p1.lngrevisionno = p2.lngrevisionno ";
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
    $aryQuery[] = "  LEFT JOIN m_Stocksubject ss ";
    $aryQuery[] = "    on ss.lngStocksubjectcode = sd.lngStocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_Stockitem si ";
    $aryQuery[] = "    on si.lngStocksubjectcode = sd.lngStocksubjectcode ";
    $aryQuery[] = "    and si.lngStockitemcode = sd.lngStockitemcode ";
    $aryQuery[] = "  LEFT JOIN m_deliverymethod dm ";
    $aryQuery[] = "    on dm.lngdeliverymethodcode = sd.lngdeliverymethodcode ";
    $aryQuery[] = "  LEFT JOIN m_productunit pu ";
    $aryQuery[] = "    on pu.lngproductunitcode = sd.lngproductunitcode ";
    $aryQuery[] = "where";
    $aryQuery[] = "  sd.lngStockNo = " . $lngStockNo;
    $aryQuery[] = "  and sd.lngrevisionno = " . $lngRevisionNo;
    $aryQuery[] = "order by sd.lngStockDetailNo";

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
                    $td = $doc->createElement("td", $detailData["lngstockdetailno"]);
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
                // 製品マスタ.製品名称(日本語)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($detailData["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 仕入科目
                case "lngstockitemcode":
                    $textContent = "[" . $detailData["lngstockitemcode"] . "]" . " " . $detailData["strstockitemname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 仕入部品
                case "lngstocksubjectcode":
                    $textContent = "[" . $detailData["lngstocksubjectcode"] . "]" . " " . $detailData["strstocksubjectname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // NO.
                case "strmoldno":
                    $td = $doc->createElement("td", $detailData["strmoldno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($detailData["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 運搬方法
                case "lngdeliverymethodcode":
                    $td = $doc->createElement("td", toUTF8($detailData["strdeliverymethodname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 単価
                case "curproductprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $detailData["curproductprice"]));
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
                    $td = $doc->createElement("td", number_format($detailData["lngproductquantity"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税抜金額
                case "cursubtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $detailData["cursubtotalprice"]));
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
                    $td = $doc->createElement("td", $detailData["curtax"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 税額
                case "curtaxprice":
                    $td = $doc->createElement("td", toMoneyFormat($detailData["lngmonetaryunitcode"], $detailData["strmonetaryunitsign"], $detailData["curtaxprice"]));
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
