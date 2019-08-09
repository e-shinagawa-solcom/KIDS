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
$aryQuery[] = "  , rd.lngProductQuantity";
$aryQuery[] = "  , rd.curSubTotalPrice";
$aryQuery[] = "  , rd.lngTaxClassCode";
$aryQuery[] = "  , rd.curTax";
$aryQuery[] = "  , rd.curTaxPrice";
$aryQuery[] = "  , rd.strNote as strDetailNote";
$aryQuery[] = "  , to_char(r.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS') as dtmInsertDate";
$aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
$aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
$aryQuery[] = "  , r.strCustomerReceiveCode as strCustomerReceiveCode";
$aryQuery[] = "  , r.strReceiveCode || '-' || r.strReviseCode as strReceiveCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
$aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
$aryQuery[] = "  , to_char(rd.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
$aryQuery[] = "  , r.lngReceiveStatusCode as lngReceiveStatusCode";
$aryQuery[] = "  , rs.strReceiveStatusName as strReceiveStatusName";
$aryQuery[] = "  , r.strNote as strNote";
$aryQuery[] = "  , To_char(r.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
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
$aryQuery[] = "      SELECT distinct";
$aryQuery[] = "          on (rd1.lngReceiveNo) rd1.lngReceiveNo";
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
$aryQuery[] = "        , rd1.lngTaxClassCode";
$aryQuery[] = "        , mt.curTax";
$aryQuery[] = "        , rd1.curTaxPrice";
$aryQuery[] = "        , rd1.strNote ";
$aryQuery[] = "      FROM";
$aryQuery[] = "        t_ReceiveDetail rd1 ";
$aryQuery[] = "        LEFT JOIN m_Product p ";
$aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
$aryQuery[] = "        left join m_group mg ";
$aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
$aryQuery[] = "        left join m_user mu ";
$aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
$aryQuery[] = "        left join m_tax mt ";
$aryQuery[] = "          on mt.lngtaxcode = rd1.lngtaxcode ";
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
// �����ԥ⡼��
if (array_key_exists("admin", $optionColumns)) {
    $aryQuery[] = "  AND r.lngRevisionNo < 0 ";
} else {
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

}
$aryQuery[] = "  AND rd.lngReceiveNo = r.lngReceiveNo ";
$aryQuery[] = "  AND r.lngRevisionNo = ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      MAX(r1.lngRevisionNo) ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_Receive r1 ";
$aryQuery[] = "    WHERE";
$aryQuery[] = "      r1.strReceiveCode = r.strReceiveCode ";
$aryQuery[] = "      AND r1.bytInvalidFlag = false ";
$aryQuery[] = "      AND r1.strReviseCode = ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          MAX(r2.strReviseCode) ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_Receive r2 ";
$aryQuery[] = "        WHERE";
$aryQuery[] = "          r2.strReceiveCode = r1.strReceiveCode ";
$aryQuery[] = "          AND r2.bytInvalidFlag = false";
$aryQuery[] = "      )";
$aryQuery[] = "  ) ";
if (!array_key_exists("admin", $optionColumns)) {
    $aryQuery[] = "  AND 0 <= ( ";
    $aryQuery[] = "    SELECT";
    $aryQuery[] = "      MIN(r3.lngRevisionNo) ";
    $aryQuery[] = "    FROM";
    $aryQuery[] = "      m_Receive r3 ";
    $aryQuery[] = "    WHERE";
    $aryQuery[] = "      r3.bytInvalidFlag = false ";
    $aryQuery[] = "      AND r3.strReceiveCode = r.strReceiveCode";
    $aryQuery[] = "  ) ";
}
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  lngReceiveNo DESC";

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
    $lngErrorCode = 9057;
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
// ���Τ�ɽ��
$existsResale = array_key_exists("btnresale", $displayColumns);
// ���򥫥���ɽ��
$existsRecord = array_key_exists("btnrecord", $displayColumns);
// �����å�����ɽ��
$existsCancel = array_key_exists("btncancel", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
// ����ܥ����ɽ��
$allowedDecide = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
// ���Υ�����ɽ��
$allowedResale = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);
// �����å�����ɽ��
$allowedCancel = fncCheckAuthority(DEF_FUNCTION_SO6, $objAuth);

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
if ($existsRecord) {
    // �ץ�ӥ塼�����
    $thRecord = $doc->createElement("th", toUTF8("����"));
    $thRecord->setAttribute("class", $exclude);
    // �إå����ɲ�
    $trHead->appendChild($thRecord);
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
$aryTableHeaderName["strnote"] = "����";
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
    $index = $i + 1;

    // tbody > tr���Ǻ���
    $trBody = $doc->createElement("tr");

    // ����
    $tdIndex = $doc->createElement("td", $index);
    $tdIndex->setAttribute("class", $exclude);
    $trBody->appendChild($tdIndex);

    // �ܺ٤�ɽ��
    if ($existsDetail) {
        // �ܺ٥���
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);

        // �ܺ٥ܥ����ɽ��
        if ($allowedDetail) {
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

        // ����ܥ����ɽ��
        if ($allowedDecide) {
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
    if ($existsRecord) {
        // ����ܥ����ɽ��
        if (array_key_exists("admin", $optionColumns)) {
            // ���򥻥�
            $tdRecord = $doc->createElement("td");
            $tdRecord->setAttribute("class", $exclude);
            // ����ܥ���
            $imgRecord = $doc->createElement("img");
            $imgRecord->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgRecord->setAttribute("id", $record["lngreceiveno"]);
            $imgRecord->setAttribute("class", "record button");
            // td > img
            $tdRecord->appendChild($imgRecord);
            // tr > td
            $trBody->appendChild($tdRecord);
        } else {
            $td = $doc->createElement("td",  toUTF8("��"));
            $trBody->appendChild($td);
        }
    }

    // ���ι��ܤ�ɽ��
    if ($existsResale) {
        // ���Υ���
        $tdResale = $doc->createElement("td");
        $tdResale->setAttribute("class", $exclude);

        // ���Υܥ����ɽ��
        if ($allowedResale) {
            // ���Υܥ���
            $imgResale = $doc->createElement("img");
            $imgResale->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
            $imgResale->setAttribute("id", $record["lngreceiveno"]);
            $imgResale->setAttribute("class", "resale button");
            // td > img
            $tdResale->appendChild($imgResale);
        }
        // tr > td
        $trBody->appendChild($tdResale);
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
                    $trBody->appendChild($td);
                    break;
                // [���ϼ�ɽ��������] ���ϼ�ɽ��̾
                case "lnginputusercode":
                    $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // �ܵҼ����ֹ�
                case "strcustomerreceivecode":
                    $td = $doc->createElement("td", $record["strcustomerreceivecode"]);
                    $trBody->appendChild($td);
                    break;
                // ����Σ�.
                case "strreceivecode":
                    $td = $doc->createElement("td", toUTF8($record["strreceivecode"]));
                    $trBody->appendChild($td);
                    break;
                // ���ʥ�����
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"]);
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.���ʥ�����(���ܸ�)
                case "strproductname":
                    $td = $doc->createElement("td", toUTF8($record["strproductname"]));
                    $trBody->appendChild($td);
                    break;
                // ���ʥޥ���.����̾��(�Ѹ�)
                case "strproductenglishname":
                    $td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
                    $trBody->appendChild($td);
                    break;
                // [�Ķ�����ɽ��������] �Ķ�����ɽ��̾
				case "lnginchargegroupcode":
					$textContent = "[" . $record["strgroupdisplaycode"] . "]" . " " . $record["strgroupdisplayname"];
					$td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // [��ȯô����ɽ��������] ��ȯô����ɽ��̾
                case "lnginchargeusercode":
                    $textContent = "[" . $record["struserdisplaycode"] . "]" . " " . $record["struserdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
					break;
				// ����ʬ
				case "lngsalesclasscode":					
                    $textContent = "[" . $record["lngsalesclasscode"] . "]" . " " . $record["strsalesclassname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // �ܵ�����
                case "strgoodscode":
                    $td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
                    $trBody->appendChild($td);
                    break;
                // [�ܵ�ɽ��������] �ܵ�ɽ��̾
                case "lngcustomercode":
                    $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
                    $td = $doc->createElement("td", toUTF8($textContent));
                    $trBody->appendChild($td);
                    break;
                // Ǽ��
                case "dtmdeliverydate":
                    $td = $doc->createElement("td", toUTF8($record["dtmdeliverydate"]));
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngreceivestatuscode":
                    $td = $doc->createElement("td", toUTF8($record["strreceivestatusname"]));
                    $trBody->appendChild($td);
                    break;
                // ����
                case "strnote":
                    $td = $doc->createElement("td", toUTF8($record["strnote"]));
                    $trBody->appendChild($td);
                    break;
                // ���ٹ��ֹ�
				case "lngrecordno":
                    $td = $doc->createElement("td", $record["lngreceivedetailno"]);
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "curproductprice":
                    $td = $doc->createElement("td", money_format($record["lngmonetaryunitcode"],$record["strmonetaryunitsign"],$record["curproductprice"]));
                    $trBody->appendChild($td);
                    break;
                // ñ��
                case "lngproductunitcode":
                    $td = $doc->createElement("td", toUTF8($record["lngproductunitname"]));
                    $trBody->appendChild($td);
                    break;
                // ����
                case "lngproductquantity":
                    $td = $doc->createElement("td", toUTF8($record["lngproductquantity"]));
                    $trBody->appendChild($td);
                    break;
                // ��ȴ���
                case "cursubtotalprice":
                    $td = $doc->createElement("td", money_format($record["lngmonetaryunitcode"],$record["strmonetaryunitsign"],$record["cursubtotalprice"]));
                    $trBody->appendChild($td);
                    break;
                // ��������
                case "strdetailnote":
                    $td = $doc->createElement("td", toUTF8($record["strdetailnote"]));
                    $trBody->appendChild($td);
                    break;
            }
        }
    }

    // �����ù��ܤ�ɽ��
    if ($existsCancel) {
        // �������
        $tdCancel = $doc->createElement("td");
        $tdCancel->setAttribute("class", $exclude);

        // ����ܥ����ɽ��
        if ($allowedCancel) {
            // ����ܥ���
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

function toUTF8($str)
{
    return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}

function money_format($lngmonetaryunitcode, $strmonetaryunitsign, $price)
{
    if ($lngmonetaryunitcode == 1) {
        return "&yen;" . " " . $price;
    } else {
        return toUTF8($strmonetaryunitsign . " ". $price);
    }
}
