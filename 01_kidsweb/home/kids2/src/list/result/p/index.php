<?
/**
 *    Ģɼ���� ���ʴ��� ������̲���
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 *    ��������
 *    2004.05.21    ���ʲ����񸡺���̰����ˤ����ʥ����ɤȤ���ɽ�����Ƥ������Ƥ������ֹ�Ǥ��ä��Х��ν���
 *
 */
// ������̲���( * �ϻ���Ģɼ�Υե�����̾ )
// *.php -> strSessionID       -> index.php

// �������̤�
// index.php -> strSessionID       -> frameset.php
// index.php -> lngReportClassCode -> frameset.php
// index.php -> strReportKeyCode   -> frameset.php
// index.php -> lngReportCode      -> frameset.php

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

// ���ʴ������
// ���ԡ��ե������������������
$strCopyQuery = "SELECT gp.lngProductNo AS strReportKeyCode, r.lngReportCode FROM t_GoodsPlan gp inner join (SELECT max(lngRevisionNo) AS lngRevisionNo, lngproductno from t_goodsplan gp1 group by lngproductno ) gp2  ON gp.lngproductno = gp2.lngproductno and gp.lngRevisionNo = gp2.lngRevisionNo, t_Report r WHERE r.lngReportClassCode = " . DEF_REPORT_PRODUCT . " AND to_number ( r.strReportKeyCode, '9999999') = gp.lngGoodsPlanCode";

// ���ʴ����������������
$aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
$aryQuery[] = " u1.strUserDisplayCode AS strInputUserDisplayCode,";
$aryQuery[] = " u1.strUserDisplayName AS strInputUserDisplayName,";
$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
$aryQuery[] = " g.strGroupDisplayName AS strInChargeGroupDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
$aryQuery[] = " gp.lngProductNo AS strReportKeyCode ";
$aryQuery[] = "FROM ( ";
$aryQuery[] = " select p1.*  from m_product p1 ";
$aryQuery[] = " inner join (select max(lngproductno) lngproductno, strproductcode from m_Product group by strProductCode) p2";
$aryQuery[] = " on p1.lngproductno = p2.lngproductno";
$aryQuery[] = " ) p ";
$aryQuery[] = " LEFT JOIN m_User u1 ON p.lngInputUserCode = u1.lngUserCode";
$aryQuery[] = " LEFT JOIN m_User u2 ON p.lngInChargeUserCode = u2.lngUserCode";
$aryQuery[] = " LEFT JOIN m_Group g ON p.lngInChargeGroupCode = g.lngGroupCode";
$aryQuery[] = ", ( ";
$aryQuery[] = "      select";
$aryQuery[] = "        gp1.* ";
$aryQuery[] = "      from";
$aryQuery[] = "        t_GoodsPlan gp1 ";
$aryQuery[] = "        inner join ( ";
$aryQuery[] = "          SELECT";
$aryQuery[] = "            MAX(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "            , lngProductNo ";
$aryQuery[] = "          FROM";
$aryQuery[] = "            t_GoodsPlan ";
$aryQuery[] = "          group by";
$aryQuery[] = "            lngProductNo";
$aryQuery[] = "        ) gp2 ";
$aryQuery[] = "          on gp1.lngProductNo = gp2.lngProductNo ";
$aryQuery[] = "          and gp1.lngRevisionNo = gp2.lngRevisionNo";
$aryQuery[] = "    ) gp ";
$aryQuery[] = " WHERE p.lngProductNo = gp.lngProductNo";
/////////////////////////////////////////////////////////////////
// �������
/////////////////////////////////////////////////////////////////
// ��������
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = " AND date_trunc('day', p.dtmInsertDate)" .
    " between '" . pg_escape_string($from["dtmInsertDate"]) . "'" .
    " AND " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}
// ���ʹԾ���
if (array_key_exists("lngGoodsPlanProgressCode", $searchColumns) &&
    array_key_exists("lngGoodsPlanProgressCode", $searchValue)) {
		$aryQuery[] = " AND gp.lngGoodsPlanProgressCode = " . $searchValue["lngGoodsPlanProgressCode"];
}
// ��������
if (array_key_exists("dtmRevisionDate", $searchColumns) &&
    array_key_exists("dtmRevisionDate", $from) &&
    array_key_exists("dtmRevisionDate", $to)) {
    $aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate)" .
    " between '" . pg_escape_string($from["dtmRevisionDate"]) . "'" .
    " AND " . "'" . pg_escape_string($to["dtmRevisionDate"]) . "'";
}
// ���ʥ�����
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $aryQuery[] = " AND p.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// ����̾
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
		$aryQuery[] = " AND p.strProductName LIKE '%" . $searchValue["strProductName"] . "%'";
}
// ����̾(�Ѹ�)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
		$aryQuery[] = " AND p.strProductEnglishName LIKE '%" . $searchValue["strProductEnglishName"] . "%'";
}
// ���ϼԥ�����
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND u1.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
// ���祳����
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = " AND g.strGroupDisplayCode = '" . $searchValue["lngInChargeGroupCode"] . "'";
}
// ô���ԥ�����
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = " AND u2.strUserDisplayCode = '" . $searchValue["lngInChargeUserCode"] . "'";
}
$aryQuery[] = " AND p.bytinvalidflag = false";
$aryQuery[] = " ORDER BY p.strProductCode ASC";
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
$strQuery = join("\n", $aryQuery);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��Ƥ����硢���ԡ�Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] != null) {
        // ���ԡ�Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
    // Ģɼ���ϥܥ���ɽ��
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO3, $objAuth)) {
        // Ģɼ���ϥܥ���ɽ��
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&strActionList=p' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

// �����ɽ��
$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>���ʥ�����</td>
						<td id=\"Column1\" nowrap>����̾��</td>
						<td id=\"Column2\" nowrap>���ϼ�</td>
						<td id=\"Column3\" nowrap>����</td>
						<td id=\"Column4\" nowrap>ô����</td>
						<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
						<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
	";

$aryParts["strListType"] = "p";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
