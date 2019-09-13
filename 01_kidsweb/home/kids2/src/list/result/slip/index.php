<?
/**
 *    Ģɼ���� Ǽ�ʽ� ������̲���
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2019, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
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
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_SLIP;

// Ǽ�ʽ��������������
$aryQuery[] = "SELECT";
$aryQuery[] = "  s.strslipcode";
$aryQuery[] = "  , u1.strUserDisplayCode AS strinsertuserdisplaycode";
$aryQuery[] = "  , u1.strUserDisplayName AS strinsertuserdisplayname";
$aryQuery[] = "  , s.strcustomercode";
$aryQuery[] = "  , s.strcustomername";
$aryQuery[] = "  , u2.strGroupDisplayCode AS strGroupDisplayCode";
$aryQuery[] = "  , u2.strGroupDisplayName AS strGroupDisplayName";
$aryQuery[] = "  , u2.strUserDisplayCode AS strUserDisplayCode";
$aryQuery[] = "  , u2.strUserDisplayName AS strUserDisplayName";
$aryQuery[] = "  , s.lngslipno AS strReportKeyCode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_slip s ";
$aryQuery[] = "  left join t_slipdetail sd ";
$aryQuery[] = "    on s.lngslipno = sd.lngslipno ";
$aryQuery[] = "  left join m_Company c ";
$aryQuery[] = "    on s.lngdeliveryplacecode = c.lngCompanyCode ";
$aryQuery[] = "  left join m_user u1 ";
$aryQuery[] = "    on s.strinsertusercode = trim(to_char(u1.lngUserCode, '9999999')) ";
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
$aryQuery[] = "  ) u2 ";
$aryQuery[] = "    on s.strusercode = trim(to_char(u2.lngUserCode, '9999999')) ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  s.lngRevisionNo = ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      MAX(s1.lngRevisionNo) ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_slip s1 ";
$aryQuery[] = "    WHERE";
$aryQuery[] = "      s1.strslipcode = s.strslipcode ";
$aryQuery[] = "      AND s1.bytInvalidFlag = false";
$aryQuery[] = "  ) ";
$aryQuery[] = "  AND 0 <= ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      MIN(s2.lngRevisionNo) ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_slip s2 ";
$aryQuery[] = "    WHERE";
$aryQuery[] = "      s2.bytInvalidFlag = false ";
$aryQuery[] = "      AND s2.strslipcode = s.strslipcode";
$aryQuery[] = "  ) ";
/////////////////////////////////////////////////////////////////
// �������
/////////////////////////////////////////////////////////////////
// Ǽ����
if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
    array_key_exists("dtmDeliveryDate", $from) &&
    array_key_exists("dtmDeliveryDate", $to)) {
    $aryQuery[] = " AND date_trunc('day', s.dtmDeliveryDate )" .
    " between '" . pg_escape_string($from["dtmDeliveryDate"]) . "'" .
    " AND " . "'" . pg_escape_string($to["dtmDeliveryDate"]) . "'";
}
// ����ʬ
if (array_key_exists("lngsalesclasscode", $searchColumns) &&
    array_key_exists("lngsalesclasscode", $searchValue)) {
    $aryQuery[] = " AND sd.lngsalesclasscode = '" . $searchValue["lngsalesclasscode"] . "'";
}
// �����Ƕ�ʬ
if (array_key_exists("lngtaxclasscode", $searchColumns) &&
    array_key_exists("lngtaxclasscode", $searchValue)) {
    $aryQuery[] = " AND s.lngtaxclasscode = '" . $searchValue["lngtaxclasscode"] . "'";
}
// Ǽ�ʽ�NO.
if (array_key_exists("strSlipCode", $searchColumns) &&
    array_key_exists("strSlipCode", $searchValue)) {
    $strSlipCodeArray = explode(",", $searchValue["strSlipCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strSlipCodeArray as $strSlipCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strSlipCode, '-') !== false) {
            $aryQuery[] = "(s.strSlipCode" .
            " between '" . explode("-", $strSlipCode)[0] . "'" .
            " AND " . "'" . explode("-", $strSlipCode)[1] . "')";
        } else {
            $aryQuery[] = "s.strSlipCode = '" . $strSlipCode . "'";
        }
    }
    $aryQuery[] = ")";
}
// ��ʸ��NO.
if (array_key_exists("strCustomerSalesCode", $searchColumns) &&
    array_key_exists("strCustomerSalesCode", $searchValue)) {
    $strCustomerSalesCodeArray = explode(",", $searchValue["strCustomerSalesCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strCustomerSalesCodeArray as $strCustomerSalesCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strCustomerSalesCode, '-') !== false) {
            $aryQuery[] = "(sd.strCustomerSalesCode" .
            " between '" . explode("-", $strCustomerSalesCode)[0] . "'" .
            " AND " . "'" . explode("-", $strCustomerSalesCode)[1] . "')";
        } else {
            $aryQuery[] = "sd.strCustomerSalesCode = '" . $strCustomerSalesCode . "'";
        }
    }
    $aryQuery[] = ")";
}
// �ܵ�����
if (array_key_exists("strGoodsCode", $searchColumns) &&
    array_key_exists("strGoodsCode", $searchValue)) {
    $strGoodsCodeArray = explode(",", $searchValue["strGoodsCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strGoodsCodeArray as $strGoodsCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strGoodsCode, '-') !== false) {
            $aryQuery[] = "(sd.strGoodsCode" .
            " between '" . explode("-", $strGoodsCode)[0] . "'" .
            " AND " . "'" . explode("-", $strGoodsCode)[1] . "')";
        } else {
            $aryQuery[] = "sd.strGoodsCode = '" . $strGoodsCode . "'";
        }
    }
    $aryQuery[] = ")";
}
// �ܵ�
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND s.strcustomercode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
}
// Ǽ����
if (array_key_exists("lngDeliveryPlaceCode", $searchColumns) &&
    array_key_exists("lngDeliveryPlaceCode", $searchValue)) {
    $aryQuery[] = " AND c.strCompanyDisplayCode = '" . $searchValue["lngDeliveryPlaceCode"] . "'";
}
// ��ɼ��
if (array_key_exists("lngInsertUserCode", $searchColumns) &&
    array_key_exists("lngInsertUserCode", $searchValue)) {
    $aryQuery[] = " AND u1.strUserDisplayCode = '" . $searchValue["lngInsertUserCode"] . "'";
}
$aryQuery[] = "ORDER BY strSlipCode DESC";

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
for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";

    $aryParts["strResult"] .= "<td>" . $objResult->strslipcode . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinsertuserdisplaycode . ":" . $objResult->strinsertuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strcustomercode . ":" . $objResult->strcustomername . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strgroupdisplaycode . ":" . $objResult->strgroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->struserdisplaycode . ":" . $objResult->struserdisplayname . "</td>\n";

    $aryParts["strResult"] .= "<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��Ƥ����硢���ԡ�Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] != null) {
        // ���ԡ�Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_SLIP . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
    // Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth)) {
        // Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_SLIP . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>Ǽ�ʽ�NO.</td>
						<td id=\"Column1\" nowrap>���ϼ�</td>
						<td id=\"Column2\" nowrap>�ܵ�</td>
						<td id=\"Column3\" nowrap>����</td>
						<td id=\"Column4\" nowrap>ô����</td>
						<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
						<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
	";

$aryParts["strListType"] = "slip";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
