<?
/**
 *    Ģɼ���� ���Ѹ����׻� ������̲���
 *
 *    @package   KIDS
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 *
 */
// ������̲���( * �ϻ���Ģɼ�Υե�����̾ )
// *.php -> strSessionID       -> index.php

// �������̤�
// index.php -> strSessionID       -> index.php
// index.php -> lngReportCode      -> index.php

// �����ɤ߹���
include_once 'conf.inc';

require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';

// �饤�֥���ɤ߹���
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// ���������Ω�˻��Ѥ���ե�����ǡ��������
$searchColumns = array();
$conditions = array();

// �������ܤ����
foreach ($isSearch as $key => $flag) {
    if ($flag == "on") {
        $searchColumns[$key] = $key;
    }
}

// ���Ѹ���Ģɼ����
// ���ԡ��ե������������������
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_ESTIMATE;

// ���Ѹ������������������
$detailConditionCount = 0;
$aryQuery[] = "SELECT";
$aryQuery[] = " e.lngestimatestatuscode,";
$aryQuery[] = " e.lngrevisionno,";
$aryQuery[] = " e.lngEstimateNo AS strReportKeyCode,";
$aryQuery[] = " e.strProductCode,";
$aryQuery[] = " e.strrevisecode,";
$aryQuery[] = " e.lngInputUserCode,";
$aryQuery[] = " e.lngprintcount,";
$aryQuery[] = " p.strProductName,";
$aryQuery[] = " g.strGroupDisplayCode AS strInchargeGroupDisplayCode,";
$aryQuery[] = " g.strGroupDisplayName AS strInchargeGroupDisplayName,";
$aryQuery[] = " u1.strUserDisplayCode AS strInchargeUserDisplayCode,";
$aryQuery[] = " u1.strUserDisplayName AS strInchargeUserDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInputUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInputUserDisplayName";
$aryQuery[] = "FROM m_Estimate e";
$aryQuery[] = " INNER JOIN m_estimatehistory emh on emh.lngestimateno = e.lngestimateno and emh.lngrevisionno = e,lngrevisionno";
$aryQuery[] = " INNER JOIN ( ";
$aryQuery[] = " select p1.*  from m_product p1 ";
$aryQuery[] = " inner join (select max(lngrevisionno) lngrevisionno, strproductcode,strrevisecode from m_Product group by strProductCode,strrevisecode) p2";
$aryQuery[] = " on p1.strproductcode = p2.strproductcode";
$aryQuery[] = " and p1.lngrevisionno = p2.lngrevisionno";
$aryQuery[] = " and p1.strrevisecode = p2.strrevisecode";
$aryQuery[] = " ) p ON p.strProductCode       = e.strProductCode";
$aryQuery[] = " AND p.strrevisecode       = e.strrevisecode";
$aryQuery[] = "  AND p.bytInvalidFlag = FALSE";
$aryQuery[] = "  LEFT OUTER JOIN ( ";
$aryQuery[] = "    select distinct";
$aryQuery[] = "      lngestimateno";
$aryQuery[] = "      , lngrevisionno ";
$aryQuery[] = "    from";
$aryQuery[] = "      t_estimatedetail ";
// Ǽ��_from
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $from) && $from["dtmDeliveryLimitDate"] != '') {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " date_trunc('day', dtmdelivery)" .
    " >= '" . pg_escape_string($from["dtmDeliveryLimitDate"]) . "'";
}
// Ǽ��_to
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $to) && $to["dtmDeliveryLimitDate"] != '') {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " date_trunc('day', dtmdelivery)" .
    " <= " . "'" . pg_escape_string($to["dtmDeliveryLimitDate"]) . "'";
}
$aryQuery[] = "  ) ed ";
$aryQuery[] = "    on emh.lngestimateno = ed.lngestimateno ";
$aryQuery[] = "    and emh.lngestimatedetailno = ed.lngestimatedetailno ";
$aryQuery[] = "    and emh.lngestimatedetailrevisionno = ed.lngrevisionno ";
$aryQuery[] = " LEFT OUTER JOIN m_Group g   ON p.lngInChargeGroupCode = g.lngGroupCode";
$aryQuery[] = " LEFT OUTER JOIN m_User u1   ON p.lngInChargeUserCode  = u1.lngUserCode";
$aryQuery[] = " INNER JOIN m_User u2   ON e.lngInputUserCode     = u2.lngUserCode";
$aryQuery[] = " AND (";
// A:�־�ǧ�׾��֤���礭�����֤�ȯ��ǡ���
$aryQuery[] = "  e.lngestimatestatuscode > " . 0;
$aryQuery[] = "  OR";
$aryQuery[] = "  (";
$aryQuery[] = "    e.lngestimatestatuscode = " . 2;
$aryQuery[] = "  )";
$aryQuery[] = ")";
$aryQuery[] = "AND e.lngestimatestatuscode != " . DEF_ESTIMATE_DENIAL;
/////////////////////////////////////////////////////////////////
// �������
/////////////////////////////////////////////////////////////////
// ��������_from
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
    $aryQueryWhere[] = " date_trunc('day', e.dtmInsertDate)" .
    " >= '" . pg_escape_string($from["dtmInsertDate"]) . "'";
}
// ��������_to
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
    $aryQueryWhere[] = " date_trunc('day', e.dtmInsertDate)" .
    " <= " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}

// ���ʥ�����_from
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) && $from["strProductCode"] != '') {
    $aryQueryWhere[] = " e.strProductCode" .
    " >= '" . pg_escape_string($from["strProductCode"]) . "'";
}

// ���ʥ�����_to
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $to) && $to["strProductCode"] != '') {
    $aryQueryWhere[] = " e.strProductCode" .
    " <= " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// ���ϼ�
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQueryWhere[] = " u2.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
// ����
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQueryWhere[] = "g.strGroupDisplayCode = '" . $searchValue["lngInChargeGroupCode"] . "'";}
// ô����
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQueryWhere[] = "u1.strUserDisplayCode = '" . $searchValue["lngInChargeUserCode"] . "'";
}
$aryQueryWhere[] = " e.lngRevisionNo = ( SELECT MAX ( e2.lngRevisionNo ) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo )";
$aryQueryWhere[] = " 0 <= ( SELECT MIN ( e3.lngRevisionNo ) FROM m_Estimate e3 WHERE e.lngEstimateNo = e3.lngEstimateNo )";
$aryQuery[] = " WHERE " . join(" AND ", $aryQueryWhere);
unset($aryQueryWhere);
$aryQuery[] = "ORDER BY p.strProductCode DESC";

// �ʥ�С��򥭡��Ȥ���Ϣ�������Ģɼ�����ɤ����
list($lngResultID, $lngResultNum) = fncQuery($strCopyQuery, $objDB);

for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);
    $aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
}

if ($lngResultNum > 0) {
    $objDB->freeResult($lngResultID);
}

$strQuery = join("\n", $aryQuery);

// Ģɼ�ǡ�������������¹ԡ��ơ��֥�����
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
    $lngErrorCode = 1507;
    $aryErrorMessage = "";
}
if ($errorFlag) {
    // ���顼���̤������
    // $strReturnPath = "../list/po/index.php?strSessionID=" . $aryData["strSessionID"];

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

    $objDB->close();

    exit;
}

for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";

    $aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "_". $objResult->strrevisecode . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";

    $aryParts["strResult"] .= "<td align=center>";

    // ���������0����礭����硢���ԡ�Ģɼ���ϥܥ���ɽ��
    if (intval($objResult->lngprintcount) > 0) {
        // ���ԡ�Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
    // Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth)) {
        // Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

$aryParts["strColumn"] = "
					<td id=\"Column0\" nowrap>���ʥ�����</td>
					<td id=\"Column1\" nowrap>����̾��</td>
					<td id=\"Column2\" nowrap>���ϼ�</td>
					<td id=\"Column3\" nowrap>����</td>
					<td id=\"Column4\" nowrap>ô����</td>
					<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
					<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
";

$aryParts["strListType"] = "estimate";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
