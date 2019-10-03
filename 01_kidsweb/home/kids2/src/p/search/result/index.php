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
$aryQuery[] = "  , p.lngrevisionno as lngrevisionno";
$aryQuery[] = "  , p.strrevisecode as strrevisecode";
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
$aryQuery[] = "  , p.strGoodsCode";
$aryQuery[] = "  , p.strGoodsName";
$aryQuery[] = "  , p.lngCustomerCompanyCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerCompanyCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerCompanyName";
$aryQuery[] = "  , cust_u.strUserDisplayCode as strCustomerUserCode";
$aryQuery[] = "  , cust_u.strUserDisplayName as strCustomerUserName";
$aryQuery[] = "  , p.lngCustomerUserCode";
$aryQuery[] = "  , p.lngPackingUnitCode";
$aryQuery[] = "  , pack_pu.strProductUnitName as strPackingUnitName";
$aryQuery[] = "  , p.lngProductUnitCode";
$aryQuery[] = "  , proct_pu.strProductUnitName as strProductUnitName";
$aryQuery[] = "  ,trim(To_char(p.lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity";
$aryQuery[] = "  ,trim(To_char(p.lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity";
$aryQuery[] = "  ,trim(To_char(p.lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity";
$aryQuery[] = "  , p.lngProductionUnitCode";
$aryQuery[] = "  , proctn_pu.strProductUnitName as strProductionUnitName";
$aryQuery[] = "  ,trim(To_char(p.lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity";
$aryQuery[] = "  , p.lngFirstDeliveryUnitCode";
$aryQuery[] = "  , fird_pu.strProductUnitName as strFirstDeliveryUnitName";
$aryQuery[] = "  , fatry_c.strCompanyDisplayCode as strFactoryCode";
$aryQuery[] = "  , fatry_c.strCompanyDisplayName as strFactoryName ";
$aryQuery[] = "  , p.lngFactoryCode";
$aryQuery[] = "  , afatry_c.strCompanyDisplayCode as strAssemblyFactoryCode";
$aryQuery[] = "  , afatry_c.strCompanyDisplayName as strAssemblyFactoryName ";
$aryQuery[] = "  , p.lngAssemblyFactoryCode";
$aryQuery[] = "  , dp_c.strCompanyDisplayCode as strDeliveryPlaceCode";
$aryQuery[] = "  , dp_c.strCompanyDisplayName as strDeliveryPlaceName ";
$aryQuery[] = "  , p.lngDeliveryPlaceCode";
$aryQuery[] = "  ,To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate";
$aryQuery[] = "  ,trim(To_char(p.curProductPrice, '9,999,999,990.99')) as curProductPrice";
$aryQuery[] = "  ,trim(To_char(p.curRetailPrice, '9,999,999,990.99')) as curRetailPrice";
$aryQuery[] = "  , p.lngTargetAgeCode";
$aryQuery[] = "  , m_t.strTargetAgeName";
$aryQuery[] = "  ,trim(To_char(p.lngRoyalty, '990.99')) as lngRoyalty";
$aryQuery[] = "  , p.lngCertificateClassCode";
$aryQuery[] = "  , m_cc.strcertificateclassname";
$aryQuery[] = "  , p.lngCopyrightCode";
$aryQuery[] = "  , m_c.strcopyrightname";
$aryQuery[] = "  , p.strCopyrightDisplayStamp";
$aryQuery[] = "  , p.strCopyrightDisplayPrint";
$aryQuery[] = "  , p.lngProductFormCode";
$aryQuery[] = "  , m_pf.strproductformname";
$aryQuery[] = "  , p.strProductComposition";
$aryQuery[] = "  , p.strAssemblyContents";
$aryQuery[] = "  , p.strSpecificationDetails";
$aryQuery[] = "  , p.strNote";
$aryQuery[] = "  ,To_char(p.dtmInsertDate,'YYYY/MM/DD HH24:MI') as dtmInsertDate";
$aryQuery[] = "  , p.strcopyrightnote";
$aryQuery[] = "  , p.lngCategoryCode";
$aryQuery[] = "  , m_cg.strcategoryname";
$aryQuery[] = "  , To_char(p.dtmUpdateDate,'YYYY/MM/DD HH24:MI') as dtmUpdateDate";
$aryQuery[] = "  , gp.strgoodsplanprogressname";
$aryQuery[] = "  , inchg_g.strgroupdisplaycolor";
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
$aryQuery[] = "  LEFT JOIN m_User cust_u ";
$aryQuery[] = "    ON p.lngCustomerUserCode = cust_u.lngUserCode ";
$aryQuery[] = "  LEFT JOIN m_Company cust_c ";
$aryQuery[] = "    ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_Company fatry_c ";
$aryQuery[] = "    ON p.lngFactoryCode = fatry_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_Company afatry_c ";
$aryQuery[] = "    ON p.lngAssemblyFactoryCode = afatry_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_Company dp_c ";
$aryQuery[] = "    ON p.lngDeliveryPlaceCode = dp_c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_productunit pack_pu ";
$aryQuery[] = "    ON p.lngpackingunitcode = pack_pu.lngProductUnitCode ";
$aryQuery[] = "  LEFT JOIN m_productunit proct_pu ";
$aryQuery[] = "    ON p.lngproductunitcode = proct_pu.lngProductUnitCode ";
$aryQuery[] = "  LEFT JOIN m_productunit proctn_pu ";
$aryQuery[] = "    ON p.lngproductionunitcode = proctn_pu.lngProductUnitCode ";
$aryQuery[] = "  LEFT JOIN m_productunit fird_pu ";
$aryQuery[] = "    ON p.lngfirstdeliveryunitcode = fird_pu.lngProductUnitCode ";
$aryQuery[] = "  LEFT JOIN m_targetage m_t ";
$aryQuery[] = "    ON p.lngtargetagecode = m_t.lngtargetagecode ";
$aryQuery[] = "  LEFT JOIN m_CertificateClass m_cc ";
$aryQuery[] = "    ON p.lngcertificateclasscode = m_cc.lngcertificateclasscode ";
$aryQuery[] = "  LEFT JOIN m_copyright m_c ";
$aryQuery[] = "    ON p.lngcopyrightcode = m_c.lngcopyrightcode ";
$aryQuery[] = "  LEFT JOIN m_productform m_pf ";
$aryQuery[] = "    ON p.lngproductformcode = m_pf.lngproductformcode ";
$aryQuery[] = "  LEFT JOIN m_category m_cg ";
$aryQuery[] = "    ON p.lngcategorycode = m_cg.lngcategorycode ";
$aryQuery[] = "  LEFT JOIN (";
$aryQuery[] = "  SELECT";
$aryQuery[] = "  t_gp.lngproductno";
$aryQuery[] = "  , t_gp.lnggoodsplanprogresscode";
$aryQuery[] = "  , m_gpp.strgoodsplanprogressname ";
$aryQuery[] = "  FROM";
$aryQuery[] = "  t_goodsplan AS t_gp ";
$aryQuery[] = "  INNER JOIN ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      max(lnggoodsplancode) AS lnggoodsplancode";
$aryQuery[] = "      , lngproductno ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      t_goodsplan t_gp1 ";
$aryQuery[] = "    group by";
$aryQuery[] = "      lngproductno";
$aryQuery[] = "  ) t_gp2 ";
$aryQuery[] = "    ON t_gp.lngproductno = t_gp2.lngproductno ";
$aryQuery[] = "    AND t_gp.lnggoodsplancode = t_gp2.lnggoodsplancode ";
$aryQuery[] = "  LEFT JOIN m_goodsplanprogress m_gpp ";
$aryQuery[] = "    ON t_gp.lnggoodsplanprogresscode = m_gpp.lnggoodsplanprogresscode";
$aryQuery[] = " ) gp ON p.lngproductno = gp.lngproductno ";
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
// 企画進行状況
if (array_key_exists("lngGoodsPlanProgressCode", $searchColumns) &&
    array_key_exists("lngGoodsPlanProgressCode", $searchValue)) {
    $aryQuery[] = " AND gp.lnggoodsplanprogresscode = " . $searchValue["lngGoodsPlanProgressCode"] . "";
}
// 改訂日時
if (array_key_exists("dtmUpdateDate", $searchColumns) &&
    array_key_exists("dtmUpdateDate", $from) &&
    array_key_exists("dtmUpdateDate", $to)) {
    $aryQuery[] = " AND p.dtmUpdateDate" .
        " between '" . $from["dtmUpdateDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmUpdateDate"] . " 23:59:59.99999'";
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
    $aryQuery[] = " AND UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}
// 製品名称(英語)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
}
// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
// 営業部署
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = " AND inchg_g.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
}
// 担当者
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = " AND inchg_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
}

// 開発担当者
if (array_key_exists("lngDevelopUsercode", $searchColumns) &&
    array_key_exists("lngDevelopUsercode", $searchValue)) {
    $aryQuery[] = " AND devp_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngDevelopUsercode"]) . "'";
}
// カテゴリ
if (array_key_exists("lngCategoryCode", $searchColumns) &&
    array_key_exists("lngCategoryCode", $searchValue)) {
    $aryQuery[] = " AND p.lngCategoryCode = '" . pg_escape_string($searchValue["lngCategoryCode"]) . "'";
}
// 顧客品番
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $aryQuery[] = " AND p.strGoodsCode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
}
// 商品名称
if (array_key_exists("strGoodsName", $searchColumns) &&
    array_key_exists("strGoodsName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strGoodsName) like UPPER('%" . pg_escape_string($searchValue["strGoodsName"]) . "%')";
}
// 商品名称
if (array_key_exists("strGoodsName", $searchColumns) &&
    array_key_exists("strGoodsName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strGoodsName) like UPPER('%" . pg_escape_string($searchValue["strGoodsName"]) . "%')";
}
// 顧客
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngCustomerCompanyCode"]) . "'";
}
// 顧客担当者
if (array_key_exists("lngCustomerUserCode", $searchColumns) &&
    array_key_exists("lngCustomerUserCode", $searchValue)) {
    $aryQuery[] = " AND cust_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngCustomerUserCode"]) . "'";
}
// 生産工場
if (array_key_exists("lngFactoryCode", $searchColumns) &&
    array_key_exists("lngFactoryCode", $searchValue)) {
    $aryQuery[] = " AND fatry_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngFactoryCode"]) . "'";
}
// アッセンブリ工場
if (array_key_exists("lngAssemblyFactoryCode", $searchColumns) &&
    array_key_exists("lngAssemblyFactoryCode", $searchValue)) {
    $aryQuery[] = " AND afatry_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngAssemblyFactoryCode"]) . "'";
}
// 納品場所
if (array_key_exists("lngDeliveryPlaceCode", $searchColumns) &&
    array_key_exists("lngDeliveryPlaceCode", $searchValue)) {
    $aryQuery[] = " AND dp_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngDeliveryPlaceCode"]) . "'";
}

// 証紙
if (array_key_exists("lngCertificateClassCode", $searchColumns) &&
    array_key_exists("lngCertificateClassCode", $searchValue)) {
    $aryQuery[] = " AND p.lngCertificateClassCode = '" . pg_escape_string($searchValue["lngCertificateClassCode"]) . "'";
}
// 納期
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $from) &&
    array_key_exists("dtmDeliveryLimitDate", $to)) {
    $aryQuery[] = " AND p.dtmDeliveryLimitDate" .
        " between '" . $from["dtmDeliveryLimitDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmDeliveryLimitDate"] . " 23:59:59.99999'";
}

// 版権元
if (array_key_exists("lngCopyrightCode", $searchColumns) &&
    array_key_exists("lngCopyrightCode", $searchValue)) {
    $aryQuery[] = " AND p.lngCopyrightCode = '" . pg_escape_string($searchValue["lngCopyrightCode"]) . "'";
}

// $aryQuery[] = "  AND p.lngRevisionNo = ( ";
// $aryQuery[] = "    SELECT";
// $aryQuery[] = "      MAX(p1.lngRevisionNo) ";
// $aryQuery[] = "    FROM";
// $aryQuery[] = "      m_Product p1 ";
// $aryQuery[] = "    WHERE";
// $aryQuery[] = "      p1.strProductCode = p.strProductCode ";
// $aryQuery[] = "      AND p1.bytInvalidFlag = false";
// $aryQuery[] = "  ) ";
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  p.strProductCode, p.lngProductNo Desc";

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
// 再販を表示
$existsResale = array_key_exists("btnresale", $displayColumns);
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_P4, $objAuth);
// 修正ボタンを表示
$allowedFix = fncCheckAuthority(DEF_FUNCTION_P6, $objAuth);
// 再販カラムを表示
$allowedResale = fncCheckAuthority(DEF_FUNCTION_P7, $objAuth);
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
if ($existsFix) {
    // 確定カラム
    $thModify = $doc->createElement("th", toUTF8("修正"));
    $thModify->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thModify);
}

// 履歴項目を表示
if ($existsHistory) {
    // プレビューカラム
    $thHistory = $doc->createElement("th", toUTF8("履歴"));
    $thHistory->setAttribute("class", $exclude);
    // ヘッダに追加
    $trHead->appendChild($thHistory);
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
$aryTableHeaderName["lnggoodsplanprogresscode"] = "企画進行状況";
$aryTableHeaderName["dtmupdatedate"] = "改訂日時";
$aryTableHeaderName["strproductcode"] = "製品コード";
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
    // 最新製品かどうかのフラグ
    $isMaxproduct = false;
    // リビジョン番号
    $revisionNos = "";
    // 履歴有無フラグ
    $historyFlag = false;

    // 同じ仕入NOの最新仕入データのリビジョン番号を取得する
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngproductno, lngrevisionno ";
    $aryQuery[] = "FROM m_product ";
    $aryQuery[] = "WHERE strproductcode='" . $record["strproductcode"] . "' ";
    $aryQuery[] = "order by lngproductno desc";

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
                $maxProductInfo = $objDB->fetchArray($lngResultID, $j);
                // 該当製品のリビジョン番号<0の場合、削除済となる
                if ($maxProductInfo["lngrevisionno"] < 0) {
                    $deletedFlag = true;
                }
                if ($maxProductInfo["lngproductno"] == $record["lngproductno"]) {
                    $isMaxproduct = true;
                }
            } else {
                $productInfo = $objDB->fetchArray($lngResultID, $j);
                if ($revisionNos == "") {
                    $revisionNos = $productInfo["lngrevisionno"];
                } else {
                    $revisionNos = $revisionNos . "," . $productInfo["lngrevisionno"];
                }
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
    if (!$isMaxproduct) {
        $trBody->setAttribute("id", $record["strproductcode"] . "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    }

    // 項番
    if ($isMaxproduct) {
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

    // 修正項目を表示
    if ($existsFix) {
        // 修正セル
        $tdModify = $doc->createElement("td");
        $tdModify->setAttribute("class", $exclude);
        $tdModify->setAttribute("style", $bgcolor);

        // 修正ボタンの表示
        // if ($allowedModify and $record["bytinvalidflag"] == "f") {
        if ($allowedFix && $record["lngrevisionno"] >= 0 && !$deletedFlag) {
            // 修正ボタン
            $imgModify = $doc->createElement("img");
            $imgModify->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgModify->setAttribute("id", $record["lngproductno"]);
            $imgModify->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgModify->setAttribute("class", "modify button");
            // td > img
            $tdModify->appendChild($imgModify);
        }
        // tr > td
        $trBody->appendChild($tdModify);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor);

        if ($isMaxproduct and $historyFlag) {
            // 履歴ボタン
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strproductcode"]);
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
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 営業部署
                case "lnginchargegroupcode":
                    $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [担当者表示コード] 担当者表示名
                case "lnginchargeusercode":
                    $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lngdevelopusercode":
                    $textContent = "[" . $record["strdevelopuserdisplaycode"] . "]" . " " . $record["strdevelopuserdisplayname"];
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
                    $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客担当者
                case "lngcustomerusercode":
                    $textContent = "[" . $record["strcustomerusercode"] . "]" . " " . $record["strcustomerusername"];
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
                    $textContent = "[" . $record["strfactorycode"] . "]" . " " . $record["strfactoryname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // アッセンブリ工場
                case "lngassemblyfactorycode":
                    $textContent = "[" . $record["strassemblyfactorycode"] . "]" . " " . $record["strassemblyfactoryname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納品場所
                case "lngdeliveryplacecode":
                    $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
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
                    $td = $doc->createElement("td", "&yen;" . " " . $record["curproductprice"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 上代
                case "curretailprice":
                    $td = $doc->createElement("td", "&yen;" . " " . $record["curretailprice"]);
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
