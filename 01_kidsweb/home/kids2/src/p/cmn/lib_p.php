<?
// ----------------------------------------------------------------------------
/**
 *       商品管理  検索関連関数群
 *
 *
 *       処理概要
 *         ・検索結果関連の関数
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

/**
 * 検索項目から一致する最新の商品上データを取得するSQL文の作成関数
 *
 *    検索項目から SQL文を作成する
 *
 *    @param  Array     $displayColumns             表示対象カラム名の配列
 *    @param  Array     $searchColumns         検索対象カラム名の配列
 *    @param  Array     $from     検索内容(from)の配列
 *    @param  Array     $to       検索内容(to)の配列
 *    @param  Array     $searchValue  検索内容の配列
 *    @return Array     $strSQL 検索用SQL文
 *    @access public
 */
function fncGetMaxProductSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
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
$aryQuery[] = "  , inchg_u.strUserDisplayName as strInChargeUserDisplayName";
$aryQuery[] = "  , p.lngdevelopusercode as lngdevelopusercode";
$aryQuery[] = "  , devp_u.strUserDisplayCode as strDevelopUserDisplayCode";
$aryQuery[] = "  , devp_u.strUserDisplayName as strDevelopUserDisplayName";
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
$aryQuery[] = "  , trim(To_char(p.lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity";
$aryQuery[] = "  , trim(To_char(p.lngCartonQuantity, '9,999,999,999')) as lngCartonQuantity";
$aryQuery[] = "  , trim( ";
$aryQuery[] = "    To_char(p.lngProductionQuantity, '9,999,999,999')";
$aryQuery[] = "  ) as lngProductionQuantity";
$aryQuery[] = "  , p.lngProductionUnitCode";
$aryQuery[] = "  , proctn_pu.strProductUnitName as strProductionUnitName";
$aryQuery[] = "  , trim( ";
$aryQuery[] = "    To_char(p.lngFirstDeliveryQuantity, '9,999,999,999')";
$aryQuery[] = "  ) as lngFirstDeliveryQuantity";
$aryQuery[] = "  , p.lngFirstDeliveryUnitCode";
$aryQuery[] = "  , fird_pu.strProductUnitName as strFirstDeliveryUnitName";
$aryQuery[] = "  , fatry_c.strCompanyDisplayCode as strFactoryCode";
$aryQuery[] = "  , fatry_c.strCompanyDisplayName as strFactoryName";
$aryQuery[] = "  , p.lngFactoryCode";
$aryQuery[] = "  , afatry_c.strCompanyDisplayCode as strAssemblyFactoryCode";
$aryQuery[] = "  , afatry_c.strCompanyDisplayName as strAssemblyFactoryName";
$aryQuery[] = "  , p.lngAssemblyFactoryCode";
$aryQuery[] = "  , dp_c.strCompanyDisplayCode as strDeliveryPlaceCode";
$aryQuery[] = "  , dp_c.strCompanyDisplayName as strDeliveryPlaceName";
$aryQuery[] = "  , p.lngDeliveryPlaceCode";
$aryQuery[] = "  , To_char(dtmDeliveryLimitDate, 'YYYY/MM') as dtmDeliveryLimitDate";
$aryQuery[] = "  , trim(To_char(p.curProductPrice, '9,999,999,990.99')) as curProductPrice";
$aryQuery[] = "  , trim(To_char(p.curRetailPrice, '9,999,999,990.99')) as curRetailPrice";
$aryQuery[] = "  , p.lngTargetAgeCode";
$aryQuery[] = "  , m_t.strTargetAgeName";
$aryQuery[] = "  , trim(To_char(p.lngRoyalty, '990.99')) as lngRoyalty";
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
$aryQuery[] = "  , To_char(p.dtmInsertDate, 'YYYY/MM/DD HH24:MI') as dtmInsertDate";
$aryQuery[] = "  , p.strcopyrightnote";
$aryQuery[] = "  , p.lngCategoryCode";
$aryQuery[] = "  , m_cg.strcategoryname";
$aryQuery[] = "  , To_char(p.dtmUpdateDate, 'YYYY/MM/DD HH24:MI') as dtmUpdateDate";
$aryQuery[] = "  , gp.strgoodsplanprogressname";
$aryQuery[] = "  , inchg_g.strgroupdisplaycolor ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_Product p ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      max(lngrevisionno) lngrevisionno";
$aryQuery[] = "      , strProductCode ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_Product ";
$aryQuery[] = "    group by";
$aryQuery[] = "      strProductCode";
$aryQuery[] = "  ) p1 ";
$aryQuery[] = "    on p.lngrevisionno = p1.lngrevisionno ";
$aryQuery[] = "    and p.strProductCode = p1.strProductCode ";
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
// 登録日_from
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] !='') {
    $aryQuery[] = " AND p.dtmInsertDate" .
        " >= '" . $from["dtmInsertDate"] . " 00:00:00'";
}
// 登録日_to
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] !='') {
    $aryQuery[] = " AND p.dtmInsertDate" .
        " <= " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}
// 企画進行状況
if (array_key_exists("lngGoodsPlanProgressCode", $searchColumns) &&
    array_key_exists("lngGoodsPlanProgressCode", $searchValue)) {
    $aryQuery[] = " AND gp.lnggoodsplanprogresscode = " . $searchValue["lngGoodsPlanProgressCode"] . "";
}
// 改訂日時_from
if (array_key_exists("dtmUpdateDate", $searchColumns) &&
    array_key_exists("dtmUpdateDate", $from) && $from["dtmUpdateDate"] !='') {
    $aryQuery[] = " AND p.dtmUpdateDate" .
        " >= '" . $from["dtmUpdateDate"] . " 00:00:00'";
}
// 改訂日時_to
if (array_key_exists("dtmUpdateDate", $searchColumns) &&
    array_key_exists("dtmUpdateDate", $to) && $to["dtmUpdateDate"] !='') {
    $aryQuery[] = " AND p.dtmUpdateDate" .
        " <= " . "'" . $to["dtmUpdateDate"] . " 23:59:59.99999'";
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
if (array_key_exists("lngDevelopUserCode", $searchColumns) &&
    array_key_exists("lngDevelopUserCode", $searchValue)) {
    $aryQuery[] = " AND devp_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngDevelopUserCode"]) . "'";
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
// 納期_from
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $from) && $from["dtmDeliveryLimitDate"] != '') {
    $aryQuery[] = " AND p.dtmDeliveryLimitDate" .
        " <= '" . $from["dtmDeliveryLimitDate"] . "'";
}
// 納期_to
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $to) && $to["dtmDeliveryLimitDate"] != '') {
    $aryQuery[] = " AND p.dtmDeliveryLimitDate" .
        " >= " . "'" . $to["dtmDeliveryLimitDate"] . "'";
}

// 版権元
if (array_key_exists("lngCopyrightCode", $searchColumns) &&
    array_key_exists("lngCopyrightCode", $searchValue)) {
    $aryQuery[] = " AND p.lngCopyrightCode = '" . pg_escape_string($searchValue["lngCopyrightCode"]) . "'";
}

if (!array_key_exists("admin", $optionColumns)) {
    $aryQuery[] = "      AND not exists ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          lngRevisionNo";
    $aryQuery[] = "          , strProductCode ";
    $aryQuery[] = "        FROM";
    $aryQuery[] = "          m_product p1 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          p1.lngRevisionNo < 0 ";
    $aryQuery[] = "          and p1.strproductcode = p.strproductcode";
    $aryQuery[] = "      )";
} else {
    $aryQuery[] = "  AND p.bytInvalidFlag = FALSE ";
    $aryQuery[] = "  AND p.lngRevisionNo >= 0 ";
}

// クエリを平易な文字列に変換
$strQuery = implode("\n", $aryQuery);

return $strQuery;

}


function fncGetProductsByStrProductCodeSQL($strProductCode, $lngRevisionNo)
{
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
$aryQuery[] = " AND p.strProductCode = '" . $strProductCode . "'";
$aryQuery[] = "  AND p.bytInvalidFlag = FALSE ";
$aryQuery[] = "  AND p.lngRevisionNo <> " . $lngRevisionNo . "";
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  p.strProductCode, p.lngRevisionNo Desc";

// クエリを平易な文字列に変換
$strQuery = implode("\n", $aryQuery);
    return $strQuery;

}