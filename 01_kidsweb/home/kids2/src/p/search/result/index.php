<?php

// ----------------------------------------------------------------------------
/**
 *       ���ʴ���  ����
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
 *       ��������
 *         ��������̲���ɽ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �����ɤ߹���
include_once 'conf.inc';

require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';

// �饤�֥���ɤ߹���
require LIB_FILE;
require LIB_ROOT . "clscache.php";
require SRC_ROOT . "p/cmn/lib_ps.php";
require LIB_DEBUGFILE;

// DB��³
$objDB = new clsDB();
$objAuth = new clsAuth();
$objCache = new clsCache();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;

// 302 ���ʴ����ʾ��ʸ�����
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
$options = UtilSearchForm::extractArrayByOption($_REQUEST);
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// ���������Ω�˻��Ѥ���ե�����ǡ��������
$optionColumns = array();
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// ���ץ������ܤ����
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}
// ɽ�����ܤ����
foreach ($isDisplay as $key => $flag) {
    if ($flag == "on") {
        $displayColumns[$key] = $key;
    }
}

// �������ܤ����
foreach ($isSearch as $key => $flag) {
    if ($flag == "on") {
        $searchColumns[$key] = $key;
    }
}
// ����ɽ�����ܼ���
if (empty($isDisplay)) {
    $strMessage = fncOutputError(9058, DEF_WARNING, "", false, "../so/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);

    // [strErrorMessage]�񤭽Ф�
    $aryHtml["strErrorMessage"] = $strMessage;

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();
    // HTML����
    echo $objTemplate->strTemplate;

    exit;
}

// ���������Ω��
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
// ��Ͽ��
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = " AND p.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}
// ���ʹԾ���
if (array_key_exists("lngGoodsPlanProgressCode", $searchColumns) &&
    array_key_exists("lngGoodsPlanProgressCode", $searchValue)) {
    $aryQuery[] = " AND gp.lnggoodsplanprogresscode = " . $searchValue["lngGoodsPlanProgressCode"] . "";
}
// ��������
if (array_key_exists("dtmUpdateDate", $searchColumns) &&
    array_key_exists("dtmUpdateDate", $from) &&
    array_key_exists("dtmUpdateDate", $to)) {
    $aryQuery[] = " AND p.dtmUpdateDate" .
        " between '" . $from["dtmUpdateDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmUpdateDate"] . " 23:59:59.99999'";
}
// ���ʥ�����
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
// ����̾��
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}
// ����̾��(�Ѹ�)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
}
// ���ϼ�
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
// �Ķ�����
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = " AND inchg_g.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
}
// ô����
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = " AND inchg_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
}

// ��ȯô����
if (array_key_exists("lngDevelopUsercode", $searchColumns) &&
    array_key_exists("lngDevelopUsercode", $searchValue)) {
    $aryQuery[] = " AND devp_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngDevelopUsercode"]) . "'";
}
// ���ƥ���
if (array_key_exists("lngCategoryCode", $searchColumns) &&
    array_key_exists("lngCategoryCode", $searchValue)) {
    $aryQuery[] = " AND p.lngCategoryCode = '" . pg_escape_string($searchValue["lngCategoryCode"]) . "'";
}
// �ܵ�����
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $aryQuery[] = " AND p.strGoodsCode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
}
// ����̾��
if (array_key_exists("strGoodsName", $searchColumns) &&
    array_key_exists("strGoodsName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strGoodsName) like UPPER('%" . pg_escape_string($searchValue["strGoodsName"]) . "%')";
}
// ����̾��
if (array_key_exists("strGoodsName", $searchColumns) &&
    array_key_exists("strGoodsName", $searchValue)) {
    $aryQuery[] = " AND UPPER(p.strGoodsName) like UPPER('%" . pg_escape_string($searchValue["strGoodsName"]) . "%')";
}
// �ܵ�
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngCustomerCompanyCode"]) . "'";
}
// �ܵ�ô����
if (array_key_exists("lngCustomerUserCode", $searchColumns) &&
    array_key_exists("lngCustomerUserCode", $searchValue)) {
    $aryQuery[] = " AND cust_u.strUserDisplayCode = '" . pg_escape_string($searchValue["lngCustomerUserCode"]) . "'";
}
// ��������
if (array_key_exists("lngFactoryCode", $searchColumns) &&
    array_key_exists("lngFactoryCode", $searchValue)) {
    $aryQuery[] = " AND fatry_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngFactoryCode"]) . "'";
}
// ���å���֥깩��
if (array_key_exists("lngAssemblyFactoryCode", $searchColumns) &&
    array_key_exists("lngAssemblyFactoryCode", $searchValue)) {
    $aryQuery[] = " AND afatry_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngAssemblyFactoryCode"]) . "'";
}
// Ǽ�ʾ��
if (array_key_exists("lngDeliveryPlaceCode", $searchColumns) &&
    array_key_exists("lngDeliveryPlaceCode", $searchValue)) {
    $aryQuery[] = " AND dp_c.strCompanyDisplayCode = '" . pg_escape_string($searchValue["lngDeliveryPlaceCode"]) . "'";
}

// �ڻ�
if (array_key_exists("lngCertificateClassCode", $searchColumns) &&
    array_key_exists("lngCertificateClassCode", $searchValue)) {
    $aryQuery[] = " AND p.lngCertificateClassCode = '" . pg_escape_string($searchValue["lngCertificateClassCode"]) . "'";
}
// Ǽ��
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $from) &&
    array_key_exists("dtmDeliveryLimitDate", $to)) {
    $aryQuery[] = " AND p.dtmDeliveryLimitDate" .
        " between '" . $from["dtmDeliveryLimitDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmDeliveryLimitDate"] . " 23:59:59.99999'";
}

// �Ǹ���
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

// �������ʿ�פ�ʸ������Ѵ�
$strQuery = implode("\n", $aryQuery);

// �ͤ�Ȥ� =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

// �������������ξ��
if ($lngResultNum > 0) {
    // ������ʾ�ξ�票�顼��å�������ɽ������
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
    // ���顼���̤������
    $strReturnPath = "../p/search/index.php?strSessionID=" . $aryData["strSessionID"];

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, $strReturnPath, $objDB);

    // [strErrorMessage]�񤭽Ф�
    $aryHtml["strErrorMessage"] = $strMessage;

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML����
    echo $objTemplate->strTemplate;

    exit;
}

// ���������Ǥ�����̾����
for ($i = 0; $i < $lngResultNum; $i++) {
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("/p/search/p_search_result.html");

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// �ѡ������顼����
libxml_use_internal_errors(true);
// DOM�ѡ���
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// �ѡ������顼���ꥢ
libxml_clear_errors();
// �ѡ������顼�������
libxml_use_internal_errors(false);

// ������̥ơ��֥�μ���
$table = $doc->getElementById("result");
$thead = $table->getElementsByTagName("thead")->item(0);
$tbody = $table->getElementsByTagName("tbody")->item(0);

// ����ʸ�����ʸ�����Ѵ�
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);

// -------------------------------------------------------
// �Ƽ�ܥ���ɽ�������å�/���¥����å�
// -------------------------------------------------------
// �ܺ٥�����ɽ��
$existsDetail = array_key_exists("btndetail", $displayColumns);
// ����������ɽ��
$existsFix = array_key_exists("btnfix", $displayColumns);
// ���Τ�ɽ��
$existsResale = array_key_exists("btnresale", $displayColumns);
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_P4, $objAuth);
// �����ܥ����ɽ��
$allowedFix = fncCheckAuthority(DEF_FUNCTION_P6, $objAuth);
// ���Υ�����ɽ��
$allowedResale = fncCheckAuthority(DEF_FUNCTION_P7, $objAuth);
// �ܺ�ɽ��������ǡ�����ɽ����
$allowedDetailDelete = fncCheckAuthority(DEF_FUNCTION_P5, $objAuth);
// -------------------------------------------------------
// �ơ��֥�إå�����
// -------------------------------------------------------
// thead > tr���Ǻ���
$trHead = $doc->createElement("tr");

// ����åץܡ��ɽ����оݥ��饹
$exclude = "exclude-in-clip-board-target";

// ���֥����
$thIndex = $doc->createElement("th");
$thIndex->setAttribute("class", $exclude);
// ���ԡ��ܥ���
$imgCopy = $doc->createElement("img");
$imgCopy->setAttribute("src", "/img/type01/cmn/seg/copy_off_bt.gif");
$imgCopy->setAttribute("class", "copy button");
// ���֥���� > ���ԡ��ܥ���
$thIndex->appendChild($imgCopy);
// �إå����ɲ�
$trHead->appendChild($thIndex);

// �ܺ٤�ɽ��
if ($existsDetail) {
    // �ܺ٥����
    $thDetail = $doc->createElement("th", toUTF8("�ܺ�"));
    $thDetail->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDetail);
}

// �������ܤ�ɽ��
if ($existsFix) {
    // ���ꥫ���
    $thModify = $doc->createElement("th", toUTF8("����"));
    $thModify->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thModify);
}

// ������ܤ�ɽ��
if ($existsHistory) {
    // �ץ�ӥ塼�����
    $thHistory = $doc->createElement("th", toUTF8("����"));
    $thHistory->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thHistory);
}

// ���ι��ܤ�ɽ��
if ($existsResale) {
    // �ץ�ӥ塼�����
    $thResale = $doc->createElement("th", toUTF8("����"));
    $thResale->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thResale);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "������";
$aryTableHeaderName["lnggoodsplanprogresscode"] = "���ʹԾ���";
$aryTableHeaderName["dtmupdatedate"] = "��������";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["strproductenglishname"] = "����̾�ʱѸ��";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "ô����";
$aryTableHeaderName["lngdevelopusercode"] = "��ȯô����";
$aryTableHeaderName["lngcategorycode"] = "���ƥ���";
$aryTableHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableHeaderName["strgoodsname"] = "����̾��";
$aryTableHeaderName["lngcustomercompanycode"] = "�ܵ�";
$aryTableHeaderName["lngcustomerusercode"] = "�ܵ�ô����";
$aryTableHeaderName["lngpackingunitcode"] = "�ٻ�ñ��";
$aryTableHeaderName["lngproductunitcode"] = "����ñ��";
$aryTableHeaderName["lngproductformcode"] = "���ʷ���";
$aryTableHeaderName["lngboxquantity"] = "��Ȣ���ޡ�����";
$aryTableHeaderName["lngcartonquantity"] = "�����ȥ�����";
$aryTableHeaderName["lngproductionquantity"] = "����ͽ���";
$aryTableHeaderName["lngfirstdeliveryquantity"] = "���Ǽ�ʿ�";
$aryTableHeaderName["lngfactorycode"] = "��������";
$aryTableHeaderName["lngassemblyfactorycode"] = "���å���֥깩��";
$aryTableHeaderName["lngdeliveryplacecode"] = "Ǽ�ʾ��";
$aryTableHeaderName["dtmdeliverylimitdate"] = "Ǽ��";
$aryTableHeaderName["curproductprice"] = "Ǽ��";
$aryTableHeaderName["curretailprice"] = "����";
$aryTableHeaderName["lngtargetagecode"] = "�о�ǯ��";
$aryTableHeaderName["lngroyalty"] = "�����ƥ�";
$aryTableHeaderName["lngcertificateclasscode"] = "�ڻ�";
$aryTableHeaderName["lngcopyrightcode"] = "�Ǹ���";
$aryTableHeaderName["strcopyrightnote"] = "�Ǹ�������";
$aryTableHeaderName["strcopyrightdisplaystamp"] = "�Ǹ�ɽ���ʹ����";
$aryTableHeaderName["strcopyrightdisplayprint"] = "�Ǹ�ɽ���ʰ���ʪ��";
$aryTableHeaderName["strproductcomposition"] = "���ʹ���";
$aryTableHeaderName["strassemblycontents"] = "���å���֥�����";
$aryTableHeaderName["strspecificationdetails"] = "���;ܺ�";

// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
$index = 0;
// ������̷��ʬ����
foreach ($records as $i => $record) {
    unset($aryQuery);
    // ����ե饰
    $deletedFlag = false;
    // �ǿ����ʤ��ɤ����Υե饰
    $isMaxproduct = false;
    // ��ӥ�����ֹ�
    $revisionNos = "";
    // ����̵ͭ�ե饰
    $historyFlag = false;

    // Ʊ������NO�κǿ������ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " lngproductno, lngrevisionno ";
    $aryQuery[] = "FROM m_product ";
    $aryQuery[] = "WHERE strproductcode='" . $record["strproductcode"] . "' ";
    $aryQuery[] = "order by lngproductno desc";

    // �������ʿ�פ�ʸ������Ѵ�
    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // �������������ξ��
    if ($lngResultNum > 0) {
        if ($lngResultNum > 1) {
            $historyFlag = true;
        }
        for ($j = 0; $j < $lngResultNum; $j++) {
            if ($j == 0) {
                $maxProductInfo = $objDB->fetchArray($lngResultID, $j);
                // �������ʤΥ�ӥ�����ֹ�<0�ξ�硢����ѤȤʤ�
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

    // �طʿ�����
    if ($record["strgroupdisplaycolor"]) {
        $bgcolor = "background-color: " . $record["strgroupdisplaycolor"] . ";";
    } else {
        $bgcolor = "background-color: #FFFFFF;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strproductcode"]);
    if (!$isMaxproduct) {
        $trBody->setAttribute("id", $record["strproductcode"] . "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    }

    // ����
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

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor);

        // �ܺ٥ܥ����ɽ��
        // if (($allowedDetailDelete and $record["bytinvalidflag"] != "f") or ($allowedDetail and $record["bytinvalidflag"] == "f")) {
        if (($allowedDetailDelete) or ($allowedDetail and $record["lngrevisionno"] >= 0)) {
            // �ܺ٥ܥ���
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

    // �������ܤ�ɽ��
    if ($existsFix) {
        // ��������
        $tdModify = $doc->createElement("td");
        $tdModify->setAttribute("class", $exclude);
        $tdModify->setAttribute("style", $bgcolor);

        // �����ܥ����ɽ��
        // if ($allowedModify and $record["bytinvalidflag"] == "f") {
        if ($allowedFix && $record["lngrevisionno"] >= 0 && !$deletedFlag) {
            // �����ܥ���
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

    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor);

        if ($isMaxproduct and $historyFlag) {
            // ����ܥ���
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

    // TODO �ץ�ե��������
    // ���ꤵ�줿�ơ��֥���ܤΥ�����������
    foreach ($aryTableHeaderName as $key => $value) {
        // ɽ���оݤΥ����ξ��
        if (array_key_exists($key, $displayColumns)) {
            // �����̤�ɽ���ƥ����Ȥ�����
            switch ($key) {
                // ������
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʹԾ���
                case "lnggoodsplanprogresscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodsplanprogressname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "dtmupdatedate":
                    $td = $doc->createElement("td", $record["dtmupdatedate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥ�����
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"] . "_" . $record["strrevisecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾�ʱѸ��
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ϼ�
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ķ�����
                case "lnginchargegroupcode":
                    $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [ô����ɽ��������] ô����ɽ��̾
                case "lnginchargeusercode":
                    $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [��ȯô����ɽ��������] ��ȯô����ɽ��̾
                case "lngdevelopusercode":
                    $textContent = "[" . $record["strdevelopuserdisplaycode"] . "]" . " " . $record["strdevelopuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ƥ���
                case "lngcategorycode":
                    $td = $doc->createElement("td", toUTF8($record["strcategoryname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�����
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����̾��
                case "strgoodsname":
                    $td = $doc->createElement("td", toUTF8($record["strgoodsname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�
                case "lngcustomercompanycode":
                    $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�ô����
                case "lngcustomerusercode":
                    $textContent = "[" . $record["strcustomerusercode"] . "]" . " " . $record["strcustomerusername"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ٻ�ñ��
                case "lngpackingunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strpackingunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����ñ��
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʷ���
                case "lngproductformcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductformname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��Ȣ���ޡ�����
                case "lngboxquantity":
                    $td = $doc->createElement("td", $record["lngboxquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �����ȥ�����
                case "lngcartonquantity":
                    $td = $doc->createElement("td", $record["lngcartonquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����ͽ���
                case "lngproductionquantity":
                    $td = $doc->createElement("td", $record["lngproductionquantity"] . " " . $record["strproductionunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���Ǽ�ʿ�
                case "lngfirstdeliveryquantity":
                    $td = $doc->createElement("td", $record["lngfirstdeliveryquantity"] . " " . $record["strfirstdeliveryunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "lngfactorycode":
                    $textContent = "[" . $record["strfactorycode"] . "]" . " " . $record["strfactoryname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���å���֥깩��
                case "lngassemblyfactorycode":
                    $textContent = "[" . $record["strassemblyfactorycode"] . "]" . " " . $record["strassemblyfactoryname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ�ʾ��
                case "lngdeliveryplacecode":
                    $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ��
                case "dtmdeliverylimitdate":
                    $td = $doc->createElement("td", $record["dtmdeliverylimitdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ��
                case "curproductprice":
                    $td = $doc->createElement("td", "&yen;" . " " . $record["curproductprice"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "curretailprice":
                    $td = $doc->createElement("td", "&yen;" . " " . $record["curretailprice"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �о�ǯ��
                case "lngtargetagecode":
                    $td = $doc->createElement("td", toUTF8($record["strtargetagename"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �����ƥ�
                case "lngroyalty":
                    $td = $doc->createElement("td", $record["lngroyalty"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ڻ�
                case "lngcertificateclasscode":
                    $td = $doc->createElement("td", toUTF8($record["strcertificateclassname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ���
                case "lngcopyrightcode":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�������
                case "strcopyrightnote":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�ɽ���ʹ����
                case "strcopyrightdisplaystamp":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightdisplaystamp"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ǹ�ɽ���ʰ���ʪ��
                case "strcopyrightdisplayprint":
                    $td = $doc->createElement("td", toUTF8($record["strcopyrightdisplayprint"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʹ���
                case "strproductcomposition":
                    $td = $doc->createElement("td", toUTF8("��" . $record["strproductcomposition"] . "�異�å���֥�"));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���å���֥�����
                case "strassemblycontents":
                    $td = $doc->createElement("td", toUTF8($record["strassemblycontents"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���;ܺ�
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

// HTML����
echo $doc->saveHTML();
