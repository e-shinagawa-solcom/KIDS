<?
/**
 *    Ģɼ���� ȯ��� �����ץ�ӥ塼����
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 *    ��������
 *    2004.03.05    �����β�Ҥΰ�����դ��� TO �β��� : ���ɲä���褦�˽�������
 *    2004.03.30    ���ٹԤΥ����Ƚ��ɽ���ѥ����ȥ����ν����ɽ������褦���ѹ������ٹ��ֹ�ι��ܤ�ɽ���ѥ����ȥ������ѹ���
 *
 */
// Ģɼ���� �����ץ�ӥ塼����
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";

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

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngReportCode"] = "ascii(1,7)";
$aryCheck["strReportKeyCode"] = "null:number(0,9999999)";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_PO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// Ģɼ���ϥ��ԡ��ե�����ѥ���������������
//===================================================================

$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);
echo $strQuery;
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum > 0) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strReportPathName = $objResult->strreportpathname;
    unset($objResult);
}

$copyDisabled = "visible";
var_dump($strReportPathName);
var_dump(!$strReportPathName);
var_dump($aryData["bytCopyFlag"]);
var_dump(!$strReportPathName);
var_dump(!$strReportPathName);
// ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ���
// Ģɼ�����ɤ�̵�� �ޤ��� ���ԡ��ե饰����(���ԡ�����ǤϤʤ�) ����
// ���ԡ�������¤������硢
// ���ԡ��ޡ�������ɽ��
if (!$strReportPathName || (!($aryData["lngReportCode"] || $aryData["bytCopyFlag"]) && fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth))) {
    $copyDisabled = "hidden";
}

var_dump($copyDisabled);
///////////////////////////////////////////////////////////////////////////
// Ģɼ�����ɤ����ξ�硢�ե�����ǡ��������
///////////////////////////////////////////////////////////////////////////
if ($aryData["lngReportCode"]) {
    if (!$lngResultNum) {
        fncOutputError(9056, DEF_FATAL, "Ģɼ���ԡ�������ޤ���", true, "", $objDB);
    }

    if (!$aryHtml[] = file_get_contents(SRC_ROOT . "list/result/cash/" . $strReportPathName . ".tmpl")) {
        fncOutputError(9059, DEF_FATAL, "Ģɼ�ǡ����ե����뤬�����ޤ���Ǥ�����", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);
}

///////////////////////////////////////////////////////////////////////////
// �ƥ�ץ졼�Ȥ��֤������ǡ�������
///////////////////////////////////////////////////////////////////////////
else {
    // �ǡ�������������
    $strQuery = fncGetListOutputQuery(DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $objDB);
    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);
    $aryParts = &$objMaster->aryData[0];

    $aryParts["copyDisabled"] = $copyDisabled;

    // �ܺټ���
    $aryQuery[] = "select";
    $aryQuery[] = "  pod.lngpurchaseorderno";
    $aryQuery[] = "  , pod.lngpurchaseorderdetailno";
    $aryQuery[] = "  , pod.lngrevisionno";
    $aryQuery[] = "  , pod.lngorderno";
    $aryQuery[] = "  , pod.lngorderdetailno";
    $aryQuery[] = "  , pod.lngorderrevisionno";
    $aryQuery[] = "  , pod.lngstocksubjectcode";
    $aryQuery[] = "  , pod.lngstockitemcode";
    $aryQuery[] = "  , pod.strstockitemname";
    $aryQuery[] = "  , pod.lngdeliverymethodcode";
    $aryQuery[] = "  , pod.strdeliverymethodname";
    $aryQuery[] = "  , to_char(pod.curproductprice, '9,999,999,990') AS curproductprice";
    $aryQuery[] = "  , to_char(pod.lngproductquantity, '9,999,999,990') AS lngproductquantity";
    $aryQuery[] = "  , pod.lngproductunitcode";
    $aryQuery[] = "  , pod.strproductunitname";
    $aryQuery[] = "  , to_char(pod.cursubtotalprice, '9,999,999,990') AS cursubtotalprice";
    $aryQuery[] = "  , to_char(pod.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
    $aryQuery[] = "  , pod.strnote";
    $aryQuery[] = "  , pod.lngsortkey ";
    $aryQuery[] = "from";
    $aryQuery[] = "  t_purchaseorderdetail pod ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  pod.lngpurchaseorderno = " . $aryData["strReportKeyCode"];
    $aryQuery[] = "  AND pod.lngrevisionno = " . $aryParts["lngrevisionno"];
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  pod.lngSortKey";

    $strQuery = join("", $aryQuery);
    
    unset($aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum < 1) {
        fncOutputError(9051, DEF_FATAL, "Ģɼ�ܺ٥ǡ�����¸�ߤ��ޤ���Ǥ�����", true, "", $objDB);
    }

    // �ե������̾����
    for ($i = 0; $i < pg_num_fields($lngResultID); $i++) {
        $aryKeys[] = pg_field_name($lngResultID, $i);
    }

    // �Կ������ǡ������������������
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = $objDB->fetchArray($lngResultID, $i);
        for ($j = 3; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j] . (($i + 5) % 5)] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    // ��׶�۽���(�Ǹ�Υڡ���������ɽ��)���ѿ�����¸
    $curTotalPrice = $aryParts["strmonetaryunitsign"] . " " . $aryParts["curtotalprice"];
    unset($aryParts["curtotalprice"]);

    // �ڡ�������
    $aryParts["lngNowPage"] = 1;
    $aryParts["lngAllPage"] = ceil($lngResultNum / 5);
    $objDB->close();

    // HTML����
    $objTemplateHeader = new clsTemplate();
    $objTemplateHeader->getTemplate("list/result/po_header.tmpl");
    $strTemplateHeader = $objTemplateHeader->strTemplate;

    $objTemplateFooter = new clsTemplate();
    $objTemplateFooter->getTemplate("list/result/po_footer.tmpl");
    $strTemplateFooter = $objTemplateFooter->strTemplate;

    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("list/result/po.tmpl");
    $strTemplate = $objTemplate->strTemplate;

    // �ڡ�����ʬ�ƥ�ץ졼�Ȥ򷫤��֤��ɤ߹���
    for (; $aryParts["lngNowPage"] < ($aryParts["lngAllPage"] + 1); $aryParts["lngNowPage"]++) {
        $objTemplate->strTemplate = $strTemplate;

        // ɽ�����褦�Ȥ��Ƥ���ڡ������Ǹ�Υڡ����ξ�硢
        // ��׶�ۤ�����(ȯ���������̽���)
        if ($aryParts["lngNowPage"] == $aryParts["lngAllPage"]) {
            $aryParts["curTotalPrice"] = $curTotalPrice;
            $aryParts["strTotalAmount"] = "Total Amount";
        }

        // �֤�����
        $objTemplate->replace($aryParts);

        // �ܺٹԤ򣵹�ɽ��(ȯ���������̽���)
        $lngRecordCount = 0;
        for ($j = ($aryParts["lngNowPage"] - 1) * 5; $j < ($aryParts["lngNowPage"] * 5); $j++) {
            $aryDetail[$j]["record" . $lngRecordCount] = $j + 1;

            // ñ����¸�ߤ���С�������̲�ñ�̤�Ĥ���
            if ($aryDetail[$j]["curproductprice" . (($j + 5) % 5)] > 0) {
                $aryDetail[$j]["curproductprice" . (($j + 5) % 5)] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["curproductprice" . (($j + 5) % 5)];
            }

            // ���פ�¸�ߤ���С�������̲�ñ�̤�Ĥ���
            if ($aryDetail[$j]["cursubtotalprice" . (($j + 5) % 5)] > 0) {
                $aryDetail[$j]["cursubtotalprice" . (($j + 5) % 5)] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["cursubtotalprice" . (($j + 5) % 5)];
            }

            // ���ʿ��̤�¸�ߤ���С����������ñ�̤�Ĥ���
            if ($aryDetail[$j]["lngproductquantity" . (($j + 5) % 5)] > 0) {
                $aryDetail[$j]["lngproductquantity" . (($j + 5) % 5)] .= "(" . $aryDetail[$j]["strproductunitname" . (($j + 5) % 5)] . ")";
            }

            // �����ȥ�������¸�ߤ���С����������ñ�̤�Ĥ���
            if ($aryDetail[$j]["lngconversionclasscode" . (($j + 5) % 5)] == 2) {
                $aryDetail[$j]["lngcartonquantity" . (($j + 5) % 5)] = "1(c/t) = " . $aryDetail[$j]["lngcartonquantity" . (($j + 5) % 5)] . "(pcs)";
            } else {
                unset($aryDetail[$j]["lngcartonquantity" . (($j + 5) % 5)]);
            }

            // �ⷿ�ֹ椬¸�ߤ���С������()��Ĥ���
            if ($aryDetail[$j]["strmoldno" . (($j + 5) % 5)] != "") {
                $aryDetail[$j]["strmoldno" . (($j + 5) % 5)] = "(" . $aryDetail[$j]["strmoldno" . (($j + 5) % 5)] . ")";
            } else {
                unset($aryDetail[$j]["strmoldno" . (($j + 5) % 5)]);
            }

            $objTemplate->replace($aryDetail[$j]);
            $lngRecordCount++;
        }

        $objTemplate->complete();
        $aryHtml[] = $objTemplate->strTemplate;
    }
}
$strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

echo $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
