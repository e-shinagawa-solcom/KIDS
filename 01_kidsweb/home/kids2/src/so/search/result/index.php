<?php

// ----------------------------------------------------------------------------
/**
 *       �������  ����
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

// ���³�ǧ
// 401 ��������ʼ�������
if (!fncCheckAuthority(DEF_FUNCTION_SO1, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
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
// ���ٸ�������
$detailConditionCount = 0;
// ���������Ω��
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
// ���ʥ�����
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " rd1.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// ����̾��
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductname) like UPPER('%" . pg_escape_string($searchValue["strProductName"]) . "%')";
}
// ����̾��(�Ѹ�)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "UPPER(p.strproductenglishname) like UPPER('%" . pg_escape_string($searchValue["strProductEnglishName"]) . "%')";
}

// �ܵ�����
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "p.strgoodscode = '" . pg_escape_string($searchValue["strGoodsCode"]) . "'";
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

// ����ʬ
if (array_key_exists("lngSalesClassCode", $searchColumns) &&
    array_key_exists("lngSalesClassCode", $searchValue)) {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = "rd1.lngSalesClassCode = '" . pg_escape_string($searchValue["lngSalesClassCode"]) . "'";
}

// Ǽ��
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

// ��Ͽ��
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = "AND r.dtmInsertDate" .
        " between '" . $from["dtmInsertDate"] . " 00:00:00'" .
        " AND " . "'" . $to["dtmInsertDate"] . " 23:59:59.99999'";
}

// �ܵҼ����ֹ�
if (array_key_exists("strCustomerReceiveCode", $searchColumns) &&
    array_key_exists("strCustomerReceiveCode", $from) &&
    array_key_exists("strCustomerReceiveCode", $to)) {
    $aryQuery[] = "AND r.strCustomerReceiveCode" .
        " between '" . $from["strCustomerReceiveCode"] . "'" .
        " AND " . "'" . $to["strCustomerReceiveCode"] . "'";
}

// ����Σ�
if (array_key_exists("strReceiveCode", $searchColumns) &&
    array_key_exists("strReceiveCode", $from) &&
    array_key_exists("strReceiveCode", $to)) {
    $fromstrReceiveCode = strpos($from["strReceiveCode"], "-") ? preg_replace(strrchr($from["strReceiveCode"], "-"), "", $from["strReceiveCode"]) : $from["strReceiveCode"];
    $tostrReceiveCode = strpos($to["strReceiveCode"], "-") ? preg_replace(strrchr($to["strReceiveCode"], "-"), "", $to["strReceiveCode"]) : $to["strReceiveCode"];

    $aryQuery[] = "AND r.strReceiveCode" .
        " between '" . $fromstrReceiveCode . "'" .
        " AND " . "'" . $tostrReceiveCode . "'";
}

// ���ϼ�
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND input_u.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}

// �ܵ�
if (array_key_exists("lngCustomerCode", $searchColumns) &&
    array_key_exists("lngCustomerCode", $searchValue)) {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCode"] . "'";
}

// ����
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
    $lngErrorCode = 403;
    $aryErrorMessage = "";
}

if ($errorFlag) {
    // ���顼���̤������
    $strReturnPath = "../so/search/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate->getTemplate("/so/search/so_search_result.html");

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
// ���ꥫ����ɽ��
$existsDecide = array_key_exists("btndecide", $displayColumns);
// ���򥫥���ɽ��
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// �����å�����ɽ��
$existsCancel = array_key_exists("btncancel", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
// ����ܥ����ɽ��
$allowedDecide = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
// �����å�����ɽ��
$allowedCancel = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);

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

// ������ܤ�ɽ��
if ($existsDecide) {
    // ���ꥫ���
    $thDecide = $doc->createElement("th", toUTF8("����"));
    $thDecide->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thDecide);
}

// ������ܤ�ɽ��
if ($existsHistory) {
    // ���򥫥��
    $thHistory = $doc->createElement("th", toUTF8("����"));
    $thHistory->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thHistory);
}

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "��Ͽ��";
$aryTableHeaderName["lnginputusercode"] = "���ϼ�";
$aryTableHeaderName["strcustomerreceivecode"] = "�ܵҼ����ֹ�";
$aryTableHeaderName["strreceivecode"] = "����Σ�.";
$aryTableHeaderName["strproductcode"] = "���ʥ�����";
$aryTableHeaderName["strproductname"] = "����̾";
$aryTableHeaderName["strproductenglishname"] = "����̾�ʱѸ��";
$aryTableHeaderName["lnginchargegroupcode"] = "�Ķ�����";
$aryTableHeaderName["lnginchargeusercode"] = "��ȯô����";
$aryTableHeaderName["lngsalesclasscode"] = "����ʬ";
$aryTableHeaderName["strgoodscode"] = "�ܵ�����";
$aryTableHeaderName["lngcustomercode"] = "�ܵ�";
$aryTableHeaderName["dtmdeliverydate"] = "Ǽ��";
$aryTableHeaderName["lngreceivestatuscode"] = "����";
// $aryTableHeaderName["strnote"] = "����";
$aryTableHeaderName["lngrecordno"] = "���ٹ��ֹ�";
$aryTableHeaderName["curproductprice"] = "ñ��";
$aryTableHeaderName["lngproductunitcode"] = "ñ��";
$aryTableHeaderName["lngproductquantity"] = "����";
$aryTableHeaderName["cursubtotalprice"] = "��ȴ���";
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
if ($existsCancel) {
    // ��������
    $thCancel = $doc->createElement("th", toUTF8("������"));
    $thCancel->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thCancel);
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
    // �ǿ������ɤ����Υե饰
    $isMaxReceive = false;
    // ����̵ͭ�ե饰
    $historyFlag = false;
    // ��ӥ�����ֹ�
    $revisionNos = "";

    // Ʊ������NO�κǿ�����ǡ����Υ�ӥ�����ֹ���������
    $aryQuery[] = "SELECT";
    $aryQuery[] = " r.lngreceiveno, r.lngrevisionno ";
    $aryQuery[] = "FROM m_receive r inner join t_receivedetail rd ";
    $aryQuery[] = "on r.lngreceiveno = rd.lngreceiveno ";
    $aryQuery[] = "WHERE strreceivecode='" . $record["strreceivecode"] . "' ";
    $aryQuery[] = "and lngreceivedetailno=" . $record["lngreceivedetailno"] . " ";
    $aryQuery[] = "order by r.lngreceiveno desc";

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
                $maxReceiveInfo = $objDB->fetchArray($lngResultID, $j);
                // �������ʤΥ�ӥ�����ֹ�<0�ξ�硢����ѤȤʤ�
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

    // �طʿ�����
    if ($record["lngrevisionno"] < 0) {
        $bgcolor = "background-color: #B3E0FF;";
    } else if ($isMaxReceive) {
        $bgcolor = "background-color: #FFB2B2;";
    } else {
        $bgcolor = "background-color: #FEEF8B;";
    }

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strreceivecode"]. "_" . $record["lngreceivedetailno"]);
    if (!$isMaxReceive) {
        $trBody->setAttribute("id", $record["strreceivecode"] . "_" . $record["lngreceivedetailno"]. "_" . $record["lngrevisionno"]);
        $trBody->setAttribute("style", "display: none;");
    }

    // ����
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

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor);

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail and $record["lngrevisionno"] >= 0) {
            // �ܺ٥ܥ���
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

    // ������ܤ�ɽ��
    if ($existsDecide) {
        // ���ꥻ��
        $tdDecide = $doc->createElement("td");
        $tdDecide->setAttribute("class", $exclude);
        $tdDecide->setAttribute("style", $bgcolor);

        // ����ܥ����ɽ��
        if ($allowedDecide and $record["lngrevisionno"] >= 0 and $record["lngreceivestatuscode"] == DEF_RECEIVE_APPLICATE and !$deletedFlag) {
            // ����ܥ���
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

    // ������ܤ�ɽ��
    if ($existsHistory) {
        // ���򥻥�
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor);

        if ($isMaxReceive and $historyFlag) {
            // ����ܥ���
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
                // [���ϼ�ɽ��������] ���ϼ�ɽ��̾
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵҼ����ֹ�
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����Σ�.
                case "strreceivecode":
                    $td = $doc->createElement("td", toUTF8($record["strreceivecode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥ�����
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.���ʥ�����(���ܸ�)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.����̾��(�Ѹ�)
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
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
                // ����ʬ
                case "lngsalesclasscode":
                    $textContent = "[" . $record["lngsalesclasscode"] . "]" . " " . $record["strsalesclassname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // �ܵ�����
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [�ܵ�ɽ��������] �ܵ�ɽ��̾
                case "lngcustomercode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // Ǽ��
                case "dtmdeliverydate":
                    $td = $doc->createElement("td", toUTF8($record["dtmdeliverydate"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngreceivestatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strreceivestatusname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ���ٹ��ֹ�
                case "lngrecordno":
                    $td = $doc->createElement("td", $record["lngreceivedetailno"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "curproductprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["lngproductunitname"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngproductquantity":
                    $td = $doc->createElement("td", toUTF8($record["lngproductquantity"]));
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ��ȴ���
                case "cursubtotalprice":
                    $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotalprice"]));
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

    // �����ù��ܤ�ɽ��
    if ($existsCancel) {
        // �����å���
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);
        $tdCancel->setAttribute("style", $bgcolor);

        // �����åܥ����ɽ��
        if ($allowedCancel and $record["lngrevisionno"] >= 0 and $record["lngreceivestatuscode"] == DEF_RECEIVE_ORDER and !$deletedFlag) {
            // �����åܥ���
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

// HTML����
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
