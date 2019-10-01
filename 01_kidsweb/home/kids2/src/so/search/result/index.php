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
// 明細表示フラグ
$isDisplayDetail = false;
if (array_key_exists("strProductCode", $displayColumns) or
    array_key_exists("lngInChargeGroupCode", $displayColumns) or
    array_key_exists("lngInChargeUserCode", $displayColumns) or
    array_key_exists("lngRecordNo", $displayColumns) or
    array_key_exists("lngSalesClassCode", $displayColumns) or
    array_key_exists("strGoodsCode", $displayColumns) or
    array_key_exists("dtmDeliveryDate", $displayColumns) or
    array_key_exists("curProductPrice", $displayColumns) or
    array_key_exists("lngProductUnitCode", $displayColumns) or
    array_key_exists("lngProductQuantity", $displayColumns) or
    array_key_exists("curSubTotalPrice", $displayColumns) or
    array_key_exists("strDetailNote", $displayColumns) or
    array_key_exists("strProductName", $displayColumns) or
    array_key_exists("strProductEnglishName", $displayColumns)) {
    $isDisplayDetail = true;
}
// 明細検索条件数
$detailConditionCount = 0;
// クエリの組立て
$aryQuery = array();
$aryQuery[] = "SELECT";
$aryQuery[] = "  r.lngReceiveNo as lngReceiveNo";
$aryQuery[] = "  , r.lngRevisionNo as lngRevisionNo";
$aryQuery[] = "  , rd.lngReceiveDetailNo";
$aryQuery[] = "  , rd.strProductCode";
$aryQuery[] = "  , rd.strGroupDisplayCode";
$aryQuery[] = "  , rd.strGroupDisplayName";
$aryQuery[] = "  , rd.strUserDisplayCode";
$aryQuery[] = "  , rd.strUserDisplayName";
$aryQuery[] = "  , rd.strProductName";
$aryQuery[] = "  , rd.strProductEnglishName";
$aryQuery[] = "  , rd.lngSalesClassCode";
$aryQuery[] = "  , rd.strsalesclassname";
$aryQuery[] = "  , rd.strGoodsCode";
$aryQuery[] = "  , rd.curProductPrice";
$aryQuery[] = "  , rd.lngProductUnitCode";
$aryQuery[] = "  , rd.strproductunitname";
$aryQuery[] = "  , to_char(rd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
$aryQuery[] = "  , rd.curSubTotalPrice";
$aryQuery[] = "  , rd.strNote as strDetailNote";
$aryQuery[] = "  , to_char(r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
$aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
$aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
$aryQuery[] = "  , r.strCustomerReceiveCode as strCustomerReceiveCode";
$aryQuery[] = "  , r.strReceiveCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
$aryQuery[] = "  , to_char(rd.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
$aryQuery[] = "  , r.lngReceiveStatusCode as lngReceiveStatusCode";
$aryQuery[] = "  , rs.strReceiveStatusName as strReceiveStatusName";
// $aryQuery[] = "  , r.strNote as strNote";
// $aryQuery[] = "  , To_char(r.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
$aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
$aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_Receive r ";
$aryQuery[] = "  LEFT JOIN m_User input_u ";
$aryQuery[] = "    ON r.lngInputUserCode = input_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_Company cust_c ";
$aryQuery[] = "    ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_ReceiveStatus rs ";
$aryQuery[] = "    USING (lngReceiveStatusCode) ";
$aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
$aryQuery[] = "    ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
$aryQuery[] = "  , ( ";
if ($isDisplayDetail) {
    $aryQuery[] = "      SELECT rd1.lngReceiveNo";
} else {
    $aryQuery[] = "      SELECT distinct";
    $aryQuery[] = "          on (rd1.lngReceiveNo) rd1.lngReceiveNo";
}
$aryQuery[] = "        , rd1.lngReceiveDetailNo";
$aryQuery[] = "        , p.strProductCode";
$aryQuery[] = "        , mg.strGroupDisplayCode";
$aryQuery[] = "        , mg.strGroupDisplayName";
$aryQuery[] = "        , mu.struserdisplaycode";
$aryQuery[] = "        , mu.struserdisplayname";
$aryQuery[] = "        , p.strProductName";
$aryQuery[] = "        , p.strProductEnglishName";
$aryQuery[] = "        , ms.lngSalesClassCode";
$aryQuery[] = "        , ms.strsalesclassname";
$aryQuery[] = "        , p.strGoodsCode";
$aryQuery[] = "        , rd1.dtmDeliveryDate";
$aryQuery[] = "        , to_char(rd1.curProductPrice, '9,999,999,990.99') as curProductPrice";
$aryQuery[] = "        , mp.lngProductUnitCode";
$aryQuery[] = "        , mp.strproductunitname";
$aryQuery[] = "        , rd1.lngProductQuantity";
$aryQuery[] = "        , to_char(rd1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
$aryQuery[] = "        , rd1.strNote ";
$aryQuery[] = "      FROM";
$aryQuery[] = "        t_ReceiveDetail rd1 ";
$aryQuery[] = "        LEFT JOIN (";
$aryQuery[] = "            select p1.*  from m_product p1 ";
$aryQuery[] = "        	inner join (select max(lngproductno) lngproductno, strproductcode from m_Product group by strProductCode) p2";
$aryQuery[] = "            on p1.lngproductno = p2.lngproductno";
$aryQuery[] = "          ) p ";
$aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
$aryQuery[] = "        left join m_group mg ";
$aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
$aryQuery[] = "        left join m_user mu ";
$aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
$aryQuery[] = "        left join m_salesclass ms ";
$aryQuery[] = "          on ms.lngsalesclasscode = rd1.lngsalesclasscode ";
$aryQuery[] = "        left join m_productunit mp ";
$aryQuery[] = "          on mp.lngproductunitcode = rd1.lngproductunitcode ";
// 製品コード
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " rd1.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// 製品名称
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}
// 製品名称(英語)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
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

// 納期
if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
    array_key_exists("dtmDeliveryDate", $from) &&
    array_key_exists("dtmDeliveryDate", $to)) {
    $aryQuery[] = "AND rd1.dtmdeliverydate" .
        " between '" . $from["dtmDeliveryDate"] . "'" .
        " AND " . "'" . $to["dtmDeliveryDate"] . "'";
}
$aryQuery[] = "    ) as rd ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  r.bytInvalidFlag = FALSE ";
$aryQuery[] = " AND r.lngRevisionNo >= 0";

// 登録日
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = "AND r.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}

// 顧客受注番号
if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
    array_key_exists("strCustomerReceiveCode", $from) &&
    array_key_exists("strCustomerReceiveCode", $to)) {
    $aryQuery[] = "AND r.strCustomerReceiveCode" .
        " between '" . $from["strCustomerReceiveCode"] . "'" .
        " AND " . "'" . $to["strCustomerReceiveCode"] . "'";
}

// 受注Ｎｏ
if (array_key_exists("strReceiveCode", $searchColumns) &&
    array_key_exists("strReceiveCode", $from) &&
    array_key_exists("strReceiveCode", $to)) {
    $fromstrReceiveCode = strpos($from["strReceiveCode"], "-") ? preg_replace(strrchr($from["strReceiveCode"], "-"), "", $from["strReceiveCode"]) : $from["strReceiveCode"];
    $tostrReceiveCode = strpos($to["strReceiveCode"], "-") ? preg_replace(strrchr($to["strReceiveCode"], "-"), "", $to["strReceiveCode"]) : $to["strReceiveCode"];

    $aryQuery[] = "AND r.strReceiveCode" .
        " between '" . $fromstrReceiveCode . "'" .
        " AND " . "'" . $tostrReceiveCode . "'";
}

// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}

// 顧客
if (array_key_exists("lngCustomerCode", $searchColumns) &&
    array_key_exists("lngCustomerCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCode"] . "'";
}

// 状態
if (array_key_exists("lngReceiveStatusCode", $searchColumns) &&
    array_key_exists("lngReceiveStatusCode", $searchValue)) {
    if (is_array($searchValue["lngReceiveStatusCode"])) {
        $searchStatus = implode(",", $searchValue["lngReceiveStatusCode"]);
        $aryQuery[] = " AND r.lngReceiveStatusCode in (" . $searchStatus . ")";
    }
}

$aryQuery[] = "  AND rd.lngReceiveNo = r.lngReceiveNo ";
// $aryQuery[] = "  AND r.lngRevisionNo = ( ";
// $aryQuery[] = "    SELECT";
// $aryQuery[] = "      MAX(r1.lngRevisionNo) ";
// $aryQuery[] = "    FROM";
// $aryQuery[] = "      m_Receive r1 ";
// $aryQuery[] = "    WHERE";
// $aryQuery[] = "      r1.strReceiveCode = r.strReceiveCode ";
// $aryQuery[] = "      AND r1.bytInvalidFlag = false ";
// $aryQuery[] = "      AND r1.strReviseCode = ( ";
// $aryQuery[] = "        SELECT";
// $aryQuery[] = "          MAX(r2.strReviseCode) ";
// $aryQuery[] = "        FROM";
// $aryQuery[] = "          m_Receive r2 ";
// $aryQuery[] = "        WHERE";
// $aryQuery[] = "          r2.strReceiveCode = r1.strReceiveCode ";
// $aryQuery[] = "          AND r2.bytInvalidFlag = false";
// $aryQuery[] = "      )";
// $aryQuery[] = "  ) ";
if (!array_key_exists("admin", $optionColumns)) {
    $aryQuery[] = "  AND r.strReceiveCode not in ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      r1.strReceiveCode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      ( ";
    $aryQuery[] = "        SELECT";
    $aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "          , strReceiveCode ";
    $aryQuery[] = "        FROM";
    $aryQuery[] = "          m_Receive ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          bytInvalidFlag = false ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          strReceiveCode";
    $aryQuery[] = "      ) as r1 ";
    $aryQuery[] = "    where";
    $aryQuery[] = "      r1.lngRevisionNo < 0";
    $aryQuery[] = "  ) ";
}
$aryQuery[] = "ORDER BY";
$aryQuery[] = " r.strReceiveCode, lngReceiveDetailNo, r.lngReceiveNo DESC";

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
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName["lngsalesclasscode"] = "売上区分";
$aryTableHeaderName["strgoodscode"] = "顧客品番";
$aryTableHeaderName["lngcustomercode"] = "顧客";
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
    $aryQuery[] = "WHERE strreceivecode='" . $record["strreceivecode"] . "' ";
    $aryQuery[] = "and lngreceivedetailno=" . $record["lngreceivedetailno"] . " ";
    $aryQuery[] = "order by r.lngreceiveno desc";

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
                if ($maxReceiveInfo["lngreceiveno"] == $record["lngreceiveno"]) {
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
        $tdDetail->setAttribute("style", $bgcolor);

        // 詳細ボタンの表示
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngreceiveno"]);
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
        $tdDecide->setAttribute("style", $bgcolor);

        // 確定ボタンの表示
        if ($allowedDecide and $record["lngrevisionno"] >= 0 and $record["lngreceivestatuscode"] == DEF_RECEIVE_APPLICATE and !$deletedFlag) {
            // 確定ボタン
            $imgDecide = $doc->createElement("img");
            $imgDecide->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgDecide->setAttribute("id", $record["lngreceiveno"]);
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
        $tdHistory->setAttribute("style", $bgcolor);

        if ($isMaxReceive and $historyFlag) {
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
                case "lngcustomercode":
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
        $tdCancel->setAttribute("style", $bgcolor);

        // 確定取消ボタンの表示
        if ($allowedCancel and $record["lngrevisionno"] >= 0 and $record["lngreceivestatuscode"] == DEF_RECEIVE_ORDER and !$deletedFlag) {
            // 確定取消ボタン
            $imgCancel = $doc->createElement("img");
            $imgCancel->setAttribute("src", "/img/type01/so/cancel_off_bt.gif");
            $imgCancel->setAttribute("id", $record["lngreceiveno"]);
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

// function toUTF8($str)
// {
//     return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
// }

// function toMoneyFormat($lngmonetaryunitcode, $strmonetaryunitsign, $price)
// {
//     if ($lngmonetaryunitcode == 1) {
//         return "&yen;" . " " . $price;
//     } else {
//         return toUTF8($strmonetaryunitsign . " " . $price);
//     }
// }
