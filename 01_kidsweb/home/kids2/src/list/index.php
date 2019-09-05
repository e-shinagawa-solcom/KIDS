<?
/**
 *    Ģɼ���� Ģɼ�������
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// Ģɼ�������
// index.php -> strSessionID    -> index.php

// �������̤�( * �ϻ���Ģɼ�Υե�����̾ )
// index.php -> strSessionID    -> *.php

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;
// require SRC_ROOT . "list/cmn/lib_lo.php";
// require (SRC_ROOT . "m/cmn/lib_m.php");

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ($_POST) {
    $aryData = $_POST;
} elseif ($_GET) {
    $aryData = $_GET;
}


setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

if (fncCheckAuthority(DEF_FUNCTION_LO1, $objAuth)) {
    // ���ʲ�����Ģɼ���ϲ�ǽ
    $aryParts["strManagementMenu"] .= "<a href=search/" . $aryListOutputMenu[DEF_REPORT_PRODUCT]["file"] . ".php?strSessionID=" . $aryData["strSessionID"] . ">" . $aryListOutputMenu[DEF_REPORT_PRODUCT]["name"] . "</a>\n";
}
if (fncCheckAuthority(DEF_FUNCTION_LO2, $objAuth)) {
    // ȯ����P.O��Ģɼ���ϲ�ǽ
    $aryParts["strManagementMenu"] .= "<a href=search/" . $aryListOutputMenu[DEF_REPORT_ORDER]["file"] . ".php?strSessionID=" . $aryData["strSessionID"] . ">" . $aryListOutputMenu[DEF_REPORT_ORDER]["name"] . "</a>\n";
}

// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_LO0;

//-------------------------------------------------------------------------
// �ɤ߹��ߥڡ���������
//-------------------------------------------------------------------------
if (!$aryData["strListMode"]) {
    // Ģɼ����ڡ���
    $aryData["strListUrl"] = '/list/select.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';
} else {
    switch ($aryData["strListMode"]) {
        // ���ʲ����� �����ڡ���
        case 'p':
            // ���ʹԾ����ץ�������˥塼 ����
            $aryData["lngGoodsPlanProgressCode"] = "<option value=\"\"></option>\n";
            $aryData["lngGoodsPlanProgressCode"] .= fncGetPulldown("m_GoodsPlanProgress", "lngGoodsPlanProgressCode", "strGoodsPlanProgressName", "", "", $objDB);
            $strTemplatePath = "list/search/p/p_search.html";

            break;

        // ȯ��� �����ڡ���
        case 'po':
            // ���°���ץ�������˥塼 ����
            $aryData["lngAttributeCode"] = "<option value=\"\"></option>\n";
            $aryData["lngAttributeCode"] .= fncGetPulldown("m_Attribute", "lngAttributeCode", "strAttributeName", "", "", $objDB);
            $strTemplatePath = "list/search/po/po_search.html";
            break;

        // ���Ѹ����� �����ڡ���
        case 'es':
            // ���°���ץ�������˥塼 ����
            $aryData["lngAttributeCode"] = "<option value=\"\"></option>\n";
            $aryData["lngAttributeCode"] .= fncGetPulldown("m_Attribute", "lngAttributeCode", "strAttributeName", "", "", $objDB);
            $strTemplatePath = "list/search/estimate/es_search.html";
            break;

        default:
            break;
    }
}
//-------------------------------------------------------------------------

$objDB->close();
// HTML����
if (!$aryData["strListMode"]) {
    echo fncGetReplacedHtml("/list/list/parts.html", $aryData, $objAuth);
} else {
    // �ƥ�ץ졼���ɤ߹���
    echo fncGetReplacedHtmlWithBase("search/base_search.html", $strTemplatePath, $aryData, $objAuth);

    // echo fncGetReplacedHtml( "/list/list/parts_button.html", $aryData, $objAuth );
}
