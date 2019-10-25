<?
/**
 *    Ģɼ���� ����� ������̲���
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
require LIB_DEBUGFILE;

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

// Ǽ�ʽ�Ģɼ����
// ���ԡ��ե������������������
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_INV;

// Ǽ�ʽ��������������
$aryQuery[] = "SELECT";
$aryQuery[] = "  distinct i.strinvoicecode";
$aryQuery[] = "  , cust.strCompanyDisplayCode";
$aryQuery[] = "  , cust.strCompanyDisplayname";
$aryQuery[] = "  , to_char(i.dtminvoicedate, 'yyyy/mm/dd') as dtminvoicedate";
$aryQuery[] = "  , i.strtaxclassname";
$aryQuery[] = "  , to_char(i.curthismonthamount, '9,999,999,990') AS curthismonthamount";
$aryQuery[] = "  , u1.strGroupDisplayCode AS strGroupDisplayCode";
$aryQuery[] = "  , u1.strGroupDisplayName AS strGroupDisplayName";
$aryQuery[] = "  , u1.strUserDisplayCode AS strUserDisplayCode";
$aryQuery[] = "  , u1.strUserDisplayName AS strUserDisplayName";
$aryQuery[] = "  , i.lnginvoiceno AS strReportKeyCode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_invoice i ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      MAX(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "      , strinvoicecode ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_invoice ";
$aryQuery[] = "    where";
$aryQuery[] = "      bytInvalidFlag = false ";
$aryQuery[] = "    group by";
$aryQuery[] = "      strinvoicecode";
$aryQuery[] = "  ) i1 ";
$aryQuery[] = "    on i.strinvoicecode = i1.strinvoicecode ";
$aryQuery[] = "    AND i.lngrevisionno = i1.lngRevisionNo ";
$aryQuery[] = "  left join (select distinct lnginvoiceno, lngrevisionno  from  t_invoicedetail id1 ";
$aryQuery[] = "    left join m_Company c ";
$aryQuery[] = "    on id1.lngdeliveryplacecode = c.lngCompanyCode ";
// Ǽ����
if (array_key_exists("lngDeliveryPlaceCode", $searchColumns) &&
    array_key_exists("lngDeliveryPlaceCode", $searchValue)) {
    $aryQuery[] = " where c.strCompanyDisplayCode = '" . $searchValue["lngDeliveryPlaceCode"] . "'";
}
$aryQuery[] = "    ) id on id.lnginvoiceno = i.lnginvoiceno and id.lngrevisionno = i.lngrevisionno";
$aryQuery[] = "  left join m_Company cust ";
$aryQuery[] = "    on i.lngcustomercode = cust.lngCompanyCode ";
$aryQuery[] = "  left join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      u.lngUserCode";
$aryQuery[] = "      , u.strUserDisplayCode";
$aryQuery[] = "      , u.strUserDisplayName";
$aryQuery[] = "      , g.strGroupDisplayCode";
$aryQuery[] = "      , g.strGroupDisplayName ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_user u";
$aryQuery[] = "      , m_Group g";
$aryQuery[] = "      , m_grouprelation gr ";
$aryQuery[] = "    where";
$aryQuery[] = "      g.lnggroupcode = gr.lnggroupcode ";
$aryQuery[] = "      and u.lngusercode = gr.lngusercode ";
$aryQuery[] = "      and gr.bytdefaultflag = true";
$aryQuery[] = "  ) u1 ";
$aryQuery[] = "    on i.lngUserCode = u1.lngUserCode ";
$aryQuery[] = "  left join m_user u2 ";
$aryQuery[] = "    on i.lngInsertUserCode = u2.lngUserCode ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  not exists ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      i1.strinvoicecode ";
$aryQuery[] = "    from";
$aryQuery[] = "      ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "          , strinvoicecode ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_invoice ";
$aryQuery[] = "        where";
$aryQuery[] = "          bytInvalidFlag = false ";
$aryQuery[] = "        group by";
$aryQuery[] = "          strinvoicecode";
$aryQuery[] = "      ) as i1 ";
$aryQuery[] = "    where";
$aryQuery[] = "      i1.strinvoicecode = i.strinvoicecode";
$aryQuery[] = "      AND i1.lngRevisionNo < 0";
$aryQuery[] = "  ) ";
/////////////////////////////////////////////////////////////////
// �������
/////////////////////////////////////////////////////////////////
// ������_from
if (array_key_exists("dtmInvoiceDate", $searchColumns) &&
    array_key_exists("dtmInvoiceDate", $from) && $from["dtmInvoiceDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInvoiceDate )" .
    " >= '" . pg_escape_string($from["dtmInvoiceDate"]) . "'";
}
// ������_to
if (array_key_exists("dtmInvoiceDate", $searchColumns) &&
    array_key_exists("dtmInvoiceDate", $to) && $to["dtmInvoiceDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInvoiceDate )" .
    " <= " . "'" . pg_escape_string($to["dtmInvoiceDate"]) . "'";
}
// ������_from
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInsertDate )" .
    " >= '" . pg_escape_string($from["dtmInsertDate"]) . "'";
}
// ������_to
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInsertDate )" .
    " <= " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}
// �����Ƕ�ʬ
if (array_key_exists("lngTaxClassCode", $searchColumns) &&
    array_key_exists("lngTaxClassCode", $searchValue)) {
    $aryQuery[] = " AND i.lngtaxclasscode = '" . $searchValue["lngTaxClassCode"] . "'";
}
// �����NO.
if (array_key_exists("strInvoiceCode", $searchColumns) &&
    array_key_exists("strInvoiceCode", $searchValue)) {
    $strInvoiceCodeArray = explode(",", $searchValue["strInvoiceCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strInvoiceCodeArray as $strInvoiceCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strInvoiceCode, '-') !== false) {
            $aryQuery[] = "(i.strInvoiceCode" .
            " between '" . explode("-", $strInvoiceCode)[0] . "'" .
            " AND " . "'" . explode("-", $strInvoiceCode)[1] . "')";
        } else {
            $aryQuery[] = "i.strInvoiceCode = '" . $strInvoiceCode . "'";
        }
    }
    $aryQuery[] = ")";
}
// �ܵ�
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND cust.strCompanyDisplayCode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
}
// ��ɼ��
if (array_key_exists("lngInsertUserCode", $searchColumns) &&
    array_key_exists("lngInsertUserCode", $searchValue)) {
    $aryQuery[] = " AND u1.strUserDisplayCode = '" . $searchValue["lngInsertUserCode"] . "'";
}
// ���ϼ�
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND u2.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
$aryQuery[] = "ORDER BY strinvoicecode DESC";

// �ʥ�С��򥭡��Ȥ���Ϣ�������Ģɼ�����ɤ����
list($lngResultID, $lngResultNum) = fncQuery($strCopyQuery, $objDB);

for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);
    $aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
}

if ($lngResultNum > 0) {
    $objDB->freeResult($lngResultID);
}

// Ģɼ�ǡ�������������¹ԡ��ơ��֥�����
$strQuery = implode("\n", $aryQuery);

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
    $lngErrorCode = 9214;
    $aryErrorMessage = "";
}
if ($errorFlag) {

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, "", $objDB);

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
for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";

    $aryParts["strResult"] .= "<td>" . $objResult->strinvoicecode  . "</td>\n";
    $aryParts["strResult"] .= "<td>" . ($objResult->strcompanydisplaycode == "" ? "&nbsp;" : ($objResult->strcompanydisplaycode . ":" . $objResult->strcompanydisplayname)) . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->dtminvoicedate . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strtaxclassname  . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->curthismonthamount . "</td>\n";
    $aryParts["strResult"] .= "<td>" . ($objResult->strgroupdisplaycode == "" ? "&nbsp;" : ($objResult->strgroupdisplaycode . ":" . $objResult->strgroupdisplayname) ). "</td>\n";
    $aryParts["strResult"] .= "<td>" . ($objResult->struserdisplaycode == "" ? "&nbsp;" : ($objResult->struserdisplaycode . ":" . $objResult->struserdisplayname)) . "</td>\n";

    $aryParts["strResult"] .= "<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��Ƥ����硢���ԡ�Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] != null) {
        // ���ԡ�Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_INV . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
    // Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth)) {
        // Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_INV . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>�����NO.</td>
						<td id=\"Column2\" nowrap>�ܵ�</td>
						<td id=\"Column1\" nowrap>������</td>
						<td id=\"Column1\" nowrap>���Ƕ�ʬ</td>
						<td id=\"Column1\" nowrap>��������</td>
						<td id=\"Column3\" nowrap>����</td>
						<td id=\"Column4\" nowrap>ô����</td>
						<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
						<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
	";

$aryParts["strListType"] = "inv";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;