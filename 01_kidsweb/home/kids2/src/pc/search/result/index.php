<?php

// ----------------------------------------------------------------------------
/**
 *       ��������  ����
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

// ��������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;

// ���³�ǧ
// 702 ���������ʻ���������
if (!fncCheckAuthority(DEF_FUNCTION_PC2, $objAuth)) {
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
$errorFlag = false;

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

// ����ɽ���ե饰
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

// ���ٸ�������
$detailConditionCount = 0;
// ���������Ω��
$aryQuery = array();
$aryQuery[] = "SELECT";
$aryQuery[] = "  s.lngStockNo as lngStockNo";
$aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
$aryQuery[] = "  , sd.lngStockDetailNo";
$aryQuery[] = "  , sd.strordercode";
$aryQuery[] = "  , sd.strProductCode";
$aryQuery[] = "  , sd.strProductname";
$aryQuery[] = "  , sd.strMoldNo";
$aryQuery[] = "  , sd.strGroupDisplayCode";
$aryQuery[] = "  , sd.strGroupDisplayName";
$aryQuery[] = "  , sd.strUserDisplayCode";
$aryQuery[] = "  , sd.strUserDisplayName";
$aryQuery[] = "  , sd.strGoodsCode";
$aryQuery[] = "  , sd.lngDeliveryMethodCode";
$aryQuery[] = "  , sd.strDeliveryMethodName";
$aryQuery[] = "  , sd.lngStockSubjectCode";
$aryQuery[] = "  , sd.strStockSubjectName";
$aryQuery[] = "  , sd.lngStockItemCode";
$aryQuery[] = "  , sd.strStockItemName";
$aryQuery[] = "  , sd.curProductPrice";
$aryQuery[] = "  , sd.lngProductUnitCode";
$aryQuery[] = "  , sd.strproductunitname";
$aryQuery[] = "  , sd.lngProductQuantity";
$aryQuery[] = "  , sd.curSubTotalPrice";
$aryQuery[] = "  , sd.lngTaxClassCode";
$aryQuery[] = "  , sd.strTaxClassName";
$aryQuery[] = "  , sd.curTax";
$aryQuery[] = "  , to_char(sd.curTaxPrice, '9,999,999,990.99') as curTaxPrice";
$aryQuery[] = "  , sd.strNote as strDetailNote";
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
if ($isDisplayDetail) {
    $aryQuery[] = "      SELECT sd1.lngStockNo";
} else {
    $aryQuery[] = "      SELECT distinct";
    $aryQuery[] = "          on (sd1.lngStockNo) sd1.lngStockNo";
}
$aryQuery[] = "        , sd1.lngStockDetailNo";
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
$aryQuery[] = "        	inner join (select max(lngproductno) lngproductno, strproductcode from m_Product group by strProductCode) p2";
$aryQuery[] = "            on p1.lngproductno = p2.lngproductno";
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

// ȯ����No
if (array_key_exists("strOrderCode", $searchColumns) &&
    array_key_exists("strOrderCode", $from) &&
    array_key_exists("strOrderCode", $to)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " o.strOrderCode" .
        " between '" . $from["strOrderCode"] . "'" .
        " AND " . "'" . $to["strOrderCode"] . "'";
}

// ���ʥ�����
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " sd1.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}

// �Ķ�����
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "mg.strGroupDisplayCode = '" . pg_escape_string($searchValue["lngInChargeGroupCode"]) . "'";
}

// ��ȯô����
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "mu.strUserDisplayCode = '" . pg_escape_string($searchValue["lngInChargeUserCode"]) . "'";
}

// ����̾��
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}

// �������ܥ�����
if (array_key_exists("lngStockSubjectCode", $searchColumns) &&
    array_key_exists("lngStockSubjectCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "sd1.lngStockSubjectCode = " . $searchValue["lngStockSubjectCode"] . "";
}

// �������ʥ�����
if (array_key_exists("lngStockItemCode", $searchColumns) &&
    array_key_exists("lngStockItemCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";

    $aryQuery[] = "sd1.lngStockItemCode = " . explode("-", $searchValue["lngStockItemCode"])[1] . "";
}
// �ܵ�����
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
}
// ������ˡ
if (array_key_exists("lngDeliveryMethodCode", $searchColumns) &&
    array_key_exists("lngDeliveryMethodCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "sd1.lngDeliveryMethodCode = " . $searchValue["lngDeliveryMethodCode"] . "";
}
// // Ǽ��
// if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
//     array_key_exists("dtmDeliveryDate", $from) &&
//     array_key_exists("dtmDeliveryDate", $to)) {
//     $aryQuery[] = "AND sd1.dtmdeliverydate" .
//         " between '" . $from["dtmDeliveryDate"] . "'" .
//         " AND " . "'" . $to["dtmDeliveryDate"] . "'";
// }
$aryQuery[] = "    ) as sd ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  s.bytInvalidFlag = FALSE ";

$aryQuery[] = " AND s.lngRevisionNo >= 0";

// ��Ͽ��
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = "AND s.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}
// ������
if (array_key_exists("dtmAppropriationDate", $searchColumns) &&
    array_key_exists("dtmAppropriationDate", $from) &&
    array_key_exists("dtmAppropriationDate", $to)) {
    $aryQuery[] = "AND s.dtmAppropriationDate" .
        " between '" . $from["dtmAppropriationDate"] . "'" .
        " AND " . "'" . $to["dtmAppropriationDate"] . "'";
}
// ����������
if (array_key_exists("dtmExpirationDate", $searchColumns) &&
    array_key_exists("dtmExpirationDate", $from) &&
    array_key_exists("dtmExpirationDate", $to)) {
    $aryQuery[] = "AND s.dtmExpirationDate" .
        " between '" . $from["dtmExpirationDate"] . "'" .
        " AND " . "'" . $to["dtmExpirationDate"] . "'";
}

// �����Σ�
if (array_key_exists("strStockCode", $searchColumns) &&
    array_key_exists("strStockCode", $from) &&
    array_key_exists("strStockCode", $to)) {
    $aryQuery[] = "AND s.strStockCode" .
        " between '" . $from["strStockCode"] . "'" .
        " AND " . "'" . $to["strStockCode"] . "'";
}
// Ǽ�ʽ�Σ�
if (array_key_exists("strSlipCode", $searchColumns) &&
    array_key_exists("strSlipCode", $searchValue)) {
    $aryQuery[] = " AND s.strSlipCode = '" . $searchValue["strSlipCode"] . "'";
}

// ���ϼ�
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}

// ������
if (array_key_exists("lngCustomerCode", $searchColumns) &&
    array_key_exists("lngCustomerCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCode"] . "'";
}

// ����
if (array_key_exists("lngStockStatusCode", $searchColumns) &&
    array_key_exists("lngStockStatusCode", $searchValue)) {
    if (is_array($searchValue["lngStockStatusCode"])) {
        $searchStatus = implode(",", $searchValue["lngStockStatusCode"]);
        $aryQuery[] = " AND s.lngStockStatusCode in (" . $searchStatus . ")";
    }
}

// ��ʧ���
if (array_key_exists("lngPayConditionCode", $searchColumns) &&
    array_key_exists("lngPayConditionCode", $searchValue)) {
    $aryQuery[] = " AND s.lngPayConditionCode = '" . $searchValue["lngPayConditionCode"] . "'";
}

$aryQuery[] = "  AND sd.lngStockNo = s.lngStockNo ";
// $aryQuery[] = "  AND s.lngRevisionNo = ( ";
// $aryQuery[] = "    SELECT";
// $aryQuery[] = "      MAX(s1.lngRevisionNo) ";
// $aryQuery[] = "    FROM";
// $aryQuery[] = "      m_Stock s1 ";
// $aryQuery[] = "    WHERE";
// $aryQuery[] = "      s1.strStockCode = s.strStockCode ";
// $aryQuery[] = "      AND s1.bytInvalidFlag = false";
// $aryQuery[] = "  ) ";
if (!array_key_exists("admin", $optionColumns)) {
    $aryQuery[] = "  AND s.strStockCode not in ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      s2.strStockCode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      ( ";
    $aryQuery[] = "        SELECT";
    $aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "          , strStockCode ";
    $aryQuery[] = "        FROM";
    $aryQuery[] = "          m_Stock ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          bytInvalidFlag = false ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          strStockCode";
    $aryQuery[] = "      ) as s2 ";
    $aryQuery[] = "    where";
    $aryQuery[] = "      s2.lngRevisionNo < 0";
    $aryQuery[] = "  ) ";
}
$aryQuery[] = "ORDER BY";
$aryQuery[] = " strStockCode, lngstockDetailNo, lngStockNo DESC";

// �������ʿ�פ�ʸ������Ѵ�
$strQuery = implode("\n", $aryQuery);
// echo $strQuery;
// return;
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
    $lngErrorCode = 703;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // ���顼���̤������
    $strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/pc/search/pc_search_result.html");

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
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// �����ɽ��
$existsDelete = array_key_exists("btndelete", $displayColumns);
// ̵��������ɽ��
$existsInvalid = array_key_exists("btninvalid", $displayColumns);

// �����⡼�ɤ�ɽ��
$allowedAdmin = fncCheckAuthority(DEF_FUNCTION_PC3, $objAuth);
// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
// ������ɽ��
$allowedFix = fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth);
// �����ɽ��
$allowedDelete = fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth);
// ̵��������ɽ��
$allowedInvalid = fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth);

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
// ������ɽ��
if ($existsFix) {
    // ���ꥫ���
    $thFix = $doc->createElement("th", toUTF8("����"));
    $thFix->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thFix);
}
// �����ɽ��
if ($existsHistory) {
    // ���򥫥��
    $thHistory = $doc->createElement("th", toUTF8("����"));
    $thHistory->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thHistory);
}
$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "��Ͽ��";
$aryTableHeaderName["dtmappropriationdate"] = "������";
$aryTableHeaderName["strstockcode"] = "�����Σ�.";
$aryTableHeaderName["strordercode"] = "ȯ���Σ�.";
$aryTableHeaderName["strslipcode"] = "Ǽ�ʽ�Σ�.";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["lngcustomercode"] = "������";
$aryTableHeaderName["lngstockstatuscode"] = "����";
$aryTableHeaderName["lngpayconditioncode"] = "��ʧ���";
$aryTableHeaderName["dtmexpirationdate"] = "����������";
$aryTableHeaderName["strnote"] = "����";
$aryTableHeaderName["curtotalprice"] = "��׶��";
$aryTableHeaderName["lngrecordno"] = "���ٹ��ֹ�";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "��ȯô����";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["lngstocksubjectcode"] = "��������";
$aryTableHeaderName["lngstockitemcode"] = "��������";
$aryTableHeaderName["strmoldno"] = "�Σ";
$aryTableHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableHeaderName["lngdeliverymethodcode"] = "������ˡ";
// $aryTableHeaderName["dtmdeliverydate"] = "Ǽ��";
$aryTableHeaderName["curproductprice"] = "ñ��";
$aryTableHeaderName["lngproductunitcode"] = "ñ��";
$aryTableHeaderName["lngproductquantity"] = "����";
$aryTableHeaderName["cursubtotalprice"] = "��ȴ���";
$aryTableHeaderName["lngtaxclasscode"] = "�Ƕ�ʬ";
$aryTableHeaderName["curtax"] = "��Ψ";
$aryTableHeaderName["curtaxprice"] = "�ǳ�";
$aryTableHeaderName["strdetailnote"] = "��������";

// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach ($aryTableHeaderName as $key => $value) {
    if (array_key_exists($key, $displayColumns)) {
        $th = $doc->createElement("th", toUTF8($value));
        $trHead->appendChild($th);
    }
}

// ������ܤ�ɽ��
if ($existsDelete) {
    // ��������
    $thDelete = $doc->createElement("th", toUTF8("���"));
    $thDelete->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDelete);
}

// ̵�����ܤ�ɽ��
if ($existsInvalid) {
    // ̵�������
    $thInvalid = $doc->createElement("th", toUTF8("̵��"));
    $thInvalid->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thInvalid);
}

// thead > tr
$thead->appendChild($trHead);

// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record) {

    unset($aryQuery);
    // ����ե饰
    $deletedFlag = false;
    // ��Х���̵ͭ�ե饰
    $revisedFlag = false;
    // �ǿ��������ɤ����Υե饰
    $isMaxStock = false;
    // ����̵ͭ�ե饰
    $historyFlag = false;
    // ��ӥ�����ֹ�
    $revisionNos = "";

    // Ʊ������NO�κǿ������ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " s.lngstockno, s.lngrevisionno ";
    $aryQuery[] = "FROM m_stock s inner join t_stockdetail sd ";
    $aryQuery[] = "on s.lngstockno = sd.lngstockno ";
    $aryQuery[] = "WHERE strstockcode ='" . $record["strstockcode"] . "' ";
    $aryQuery[] = "and lngstockdetailno =" . $record["lngstockdetailno"] . " ";
    $aryQuery[] = "order by s.lngstockno desc ";

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
                $maxStockInfo = $objDB->fetchArray($lngResultID, $j);
                // �������ʤΥ�ӥ�����ֹ�<0�ξ�硢����ѤȤʤ�
                if ($maxStockInfo["lngrevisionno"] < 0) {
                    $deletedFlag = true;
                }

                if ($maxStockInfo["lngrevisionno"] != 0) {
                    $revisedFlag = true;
                }
                if ($maxStockInfo["lngstockno"] == $record["lngstockno"]) {
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

    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxStock) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    if (!$isMaxStock) {
        $trBody->setAttribute("id", $record["strstockcode"]. "_" . $record["lngstockdetailno"]. "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    } else {
        $trBody->setAttribute("id", $record["strstockcode"]. "_" . $record["lngstockdetailno"]);
    }

    // ����
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
    $trBody->appendChild($tdIndex);

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor);

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail && $record["lngrevisionno"] >= 0) {
            // �ܺ٥ܥ���
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/pc/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngstockno"]);
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
        $tdFix = $doc->createElement("td");
        $tdFix->setAttribute("class", $exclude);
        $tdFix->setAttribute("style", $bgcolor);

        // �����ܥ����ɽ��
        if ($allowedFix && $record["lngrevisionno"] >= 0 && $recode["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
            // �����ܥ���
            $imgFix = $doc->createElement("img");
            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
            $imgFix->setAttribute("id", $record["lngstockno"]);
            $imgFix->setAttribute("class", "fix button");
            // td > img
            $tdFix->appendChild($imgFix);
        }
        // tr > td
        $trBody->appendChild($tdFix);
    }
    
    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor);

        if ($isMaxStock and $historyFlag) {
            // ����ܥ���
            $imgHistory = $doc->createElement("img");
            $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgHistory->setAttribute("id", $record["strstockcode"]. "_" . $record["lngstockdetailno"]);
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
                // ��Ͽ��
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ������
                case "dtmappropriationdate":
                    $td = $doc->createElement("td", $record["dtmappropriationdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �����Σ�.
                case "strstockcode":
                    $td = $doc->createElement("td", $record["strstockcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ȯ���Σ�.
                case "strordercode":
                    $td = $doc->createElement("td", $record["strordercode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ�ʽ�Σ�.
                case "strslipcode":
                    $td = $doc->createElement("td", $record["strslipcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [���ϼ�ɽ��������] ���ϼ�ɽ��̾
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [������ɽ��������] ���ϼ�ɽ��̾
                case "lngcustomercode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngstockstatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strstockstatusname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��ʧ���
                case "lngpayconditioncode":
                    $td = $doc->createElement("td", toUTF8($record["strpayconditionname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����������
                case "dtmexpirationdate":
                    $td = $doc->createElement("td", toUTF8($record["dtmexpirationdate"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "strnote":
                    $td = $doc->createElement("td", toUTF8($record["strnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��׶��
                case "curtotalprice":
                    $td = $doc->createElement("td", toUTF8($record["curtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ٹ��ֹ�
                case "lngrecordno":
                    $td = $doc->createElement("td", $record["lngstockdetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥ�����
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [�Ķ�����ɽ��������] �Ķ�����ɽ��̾
                case "lnginchargegroupcode":
                    $textContent = "[" . $record["strgroupdisplaycode"] . "]" . " " . $record["strgroupdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [��ȯô����ɽ��������] ��ȯô����ɽ��̾
                case "lnginchargeusercode":
                    $textContent = "[" . $record["struserdisplaycode"] . "]" . " " . $record["struserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.����̾��(���ܸ�)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "lngstockitemcode":
                    $textContent = "[" . $record["lngstockitemcode"] . "]" . " " . $record["strstockitemname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "lngstocksubjectcode":
                    $textContent = "[" . $record["lngstocksubjectcode"] . "]" . " " . $record["strstocksubjectname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // NO.
                case "strmoldno":
                    $td = $doc->createElement("td", $record["strmoldno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�����
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ������ˡ
                case "lngdeliverymethodcode":
                    $td = $doc->createElement("td", toUTF8($record["strdeliverymethodname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "curproductprice":
                    $td = $doc->createElement("td", money_format($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["strproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngproductquantity":
                    $td = $doc->createElement("td", number_format($record["lngproductquantity"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��ȴ���
                case "cursubtotalprice":
                    $td = $doc->createElement("td", money_format($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotalprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �Ƕ�ʬ
                case "lngtaxclasscode":
                    $td = $doc->createElement("td", toUTF8($record["strtaxclassname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��Ψ
                case "curtax":
                    $td = $doc->createElement("td", $record["curtax"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ǳ�
                case "curtaxprice":
                    $td = $doc->createElement("td", money_format($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtaxprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "strdetailnote":
                    $td = $doc->createElement("td", toUTF8($record["strdetailnote"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
            }
        }
    }

    // ������ܤ�ɽ��
    if ($existsDelete) {
        // �������
        $tdDelete = $doc->createElement("td");
        $tdDelete->setAttribute("class", $exclude);
        $tdDelete->setAttribute("style", $bgcolor);

        $showDeleteFlag = false;
        if ($allowedDelete) {
            if (!array_key_exists("admin", $optionColumns) or !$revisedFlag) {
                if ($recode["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
                    $showDeleteFlag = true;
                }
            } else {
                if ($isMaxStock) {
                    if ($recode["lngstockstatuscode"] != DEF_STOCK_CLOSED && !$deletedFlag) {
                        $showDeleteFlag = true;
                    }
                }
            }
        }

        // ����ܥ����ɽ��
        if ($showDeleteFlag) {
            // ����ܥ���
            $imgDelete = $doc->createElement("img");
            $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
            $imgDelete->setAttribute("id", $record["lngstockno"]);
            $imgDelete->setAttribute("class", "delete button");
            // td > img
            $tdDelete->appendChild($imgDelete);
        }
        // tr > td
        $trBody->appendChild($tdDelete);
    }

    // ̵�����ܤ�ɽ��
    if ($existsInvalid) {
        // ̵������
        $tdInvalid = $doc->createElement("td");
        $tdInvalid->setAttribute("class", $exclude);
        $tdInvalid->setAttribute("style", $bgcolor);

        // ̵���ܥ����ɽ��
        if ($allowedInvalid && $allowedAdmin && array_key_exists("admin", $optionColumns) && $recode["lngstockstatuscode"] != DEF_STOCK_CLOSED) {
            // ̵���ܥ���
            $imgInvalid = $doc->createElement("img");
            $imgInvalid->setAttribute("src", "/img/type01/pc/invalid_off_bt.gif");
            $imgInvalid->setAttribute("id", $record["lngstockno"]);
            $imgInvalid->setAttribute("class", "invalid button");
            // td > img
            $tdInvalid->appendChild($imgInvalid);
        }
        // tr > td
        $trBody->appendChild($tdInvalid);
    }

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML����
echo $doc->saveHTML();

function toUTF8($str)
{
    return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}

function money_format($lngmonetaryunitcode, $strmonetaryunitsign, $price)
{
    if ($lngmonetaryunitcode == 1) {
        return "&yen;" . " " . $price;
    } else {
        return toUTF8($strmonetaryunitsign . " " . $price);
    }
}