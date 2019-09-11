<?
/**
 *    Ģɼ���� Ǽ�ʽ� �����ץ�ӥ塼����
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
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
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth) || !fncCheckAuthority(DEF_FUNCTION_SO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// Ģɼ���ϥ��ԡ��ե�����ѥ���������������
//===================================================================

$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum > 0) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strReportPathName = $objResult->strreportpathname;
    unset($objResult);
}

$copyDisabled = "visible";

// ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ���
// Ģɼ�����ɤ�̵�� �ޤ��� ���ԡ��ե饰����(���ԡ�����ǤϤʤ�) ����
// ���ԡ�������¤������硢
// ���ԡ��ޡ�������ɽ��
if (!$strReportPathName || (!($aryData["lngReportCode"] || $aryData["bytCopyFlag"]) && fncCheckAuthority(DEF_FUNCTION_LO6, $objAuth))) {
    $copyDisabled = "hidden";
}

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
    $strQuery = fncGetListOutputQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);
    $aryParts = &$objMaster->aryData[0];

    $aryParts["copyDisabled"] = $copyDisabled;

    // �ܺټ���
    $aryQuery[] = "select";
    $aryQuery[] = "  lngslipno";
    $aryQuery[] = "  , lngslipdetailno";
    $aryQuery[] = "  , lngrevisionno";
    $aryQuery[] = "  , strcustomersalescode";
    $aryQuery[] = "  , lngsalesclasscode";
    $aryQuery[] = "  , strsalesclassname";
    $aryQuery[] = "  , strgoodscode";
    $aryQuery[] = "  , strproductcode";
    $aryQuery[] = "  , strrevisecode";
    $aryQuery[] = "  , strproductname";
    $aryQuery[] = "  , strproductenglishname";
    $aryQuery[] = "  , to_char(curproductprice, '9,999,999,990') AS curproductprice";
    $aryQuery[] = "  , lngquantity";
    $aryQuery[] = "  , to_char(lngproductquantity, '9,999,999,990') AS lngproductquantity";
    $aryQuery[] = "  , lngproductunitcode";
    $aryQuery[] = "  , strproductunitname";
    $aryQuery[] = "  , to_char(cursubtotalprice, '9,999,999,990') AS cursubtotalprice";
    $aryQuery[] = "  , strnote";
    $aryQuery[] = "  , lngreceiveno";
    $aryQuery[] = "  , lngreceivedetailno";
    $aryQuery[] = "  , lngreceiverevisionno";
    $aryQuery[] = "  , lngsortkey ";
    $aryQuery[] = "from";
    $aryQuery[] = "  t_slipdetail ";
    $aryQuery[] = "where";
    $aryQuery[] = "  lngslipno = " . $aryData["strReportKeyCode"];
    $aryQuery[] = " ORDER BY";
    $aryQuery[] = "  lngSortKey";

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

    $rowNum = $aryParts["lngmaxline"];
    // �ƥ�ץ졼�ȥѥ�����
    if ($aryParts["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
        $strTemplateHeaderPath = "list/result/slip_exc_header.html";
        $strTemplatePath = "list/result/slip_exc.html";
        $strTemplateFooterPath = "list/result/slip_exc_footer.html";
    } else if ($aryParts["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
        $strTemplateHeaderPath = "list/result/slip_comm_header.html";
        $strTemplatePath = "list/result/slip_comm.html";
        $strTemplateFooterPath = "list/result/slip_comm_footer.html";
    } else if ($aryParts["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
        $strTemplateHeaderPath = "list/result/slip_debit_header.html";
        $strTemplatePath = "list/result/slip_debit.html";
        $strTemplateFooterPath = "list/result/slip_debit_footer.html";
    }

    // �Կ������ǡ������������������
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult = $objDB->fetchArray($lngResultID, $i);
        for ($j = 3; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j] . (($i + $rowNum) % $rowNum)] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    // ��׶�۽���(�Ǹ�Υڡ���������ɽ��)���ѿ�����¸
    $curTotalPrice = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"] )  . " " . $aryParts["curtotalprice"];
    unset($aryParts["curtotalprice"]);

    // �ڡ�������
    $aryParts["lngNowPage"] = 1;
    $aryParts["lngAllPage"] = ceil($lngResultNum / $rowNum);
    $objDB->close();

    // �ܵ������ֹ�
    $aryParts["strcustomertel"] = "Tel:" .$aryParts["strcustomertel1"] . " " . $aryParts["strcustomertel2"];

    // �ܵ�FAX�ֹ�
    $aryParts["strcustomerfax"] = "Fax.:" .$aryParts["strcustomerfax1"] . " " . $aryParts["strcustomerfax2"];

    // HTML����
    $objTemplateHeader = new clsTemplate();
    $objTemplateHeader->getTemplate($strTemplateHeaderPath);
    $strTemplateHeader = $objTemplateHeader->strTemplate;

    $objTemplateFooter = new clsTemplate();
    $objTemplateFooter->getTemplate($strTemplateFooterPath);
    $strTemplateFooter = $objTemplateFooter->strTemplate;

    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate($strTemplatePath);
    $strTemplate = $objTemplate->strTemplate;

    // �ڡ�����ʬ�ƥ�ץ졼�Ȥ򷫤��֤��ɤ߹���
    for (; $aryParts["lngNowPage"] < ($aryParts["lngAllPage"] + 1); $aryParts["lngNowPage"]++) {
        $objTemplate->strTemplate = $strTemplate;

        // ɽ�����褦�Ȥ��Ƥ���ڡ������Ǹ�Υڡ����ξ�硢
        // ��׶�ۤ�����(ȯ���������̽���)
        if ($aryParts["lngNowPage"] == $aryParts["lngAllPage"]) {
            $aryParts["curTotalPrice"] = $curTotalPrice;
            $aryParts["strTotalAmount"] = "Total Amount :";
        }

        // �֤�����
        $objTemplate->replace($aryParts);

        // �ܺٹԤ򣵹�ɽ��(ȯ���������̽���)
        $lngRecordCount = 0;
        for ($j = ($aryParts["lngNowPage"] - 1) * $rowNum; $j < ($aryParts["lngNowPage"] * $rowNum); $j++) {
            $aryDetail[$j]["record" . $lngRecordCount] = $j + 1;
            $index = ($j + $rowNum) % $rowNum;

            // ñ����¸�ߤ���С�������̲�ñ�̤�Ĥ���
            if ($aryDetail[$j]["curproductprice" . ($index)] > 0) {
                $aryDetail[$j]["curproductprice" . ($index)] = $aryDetail[$j]["curproductprice" . ($index)];
            }

            // ���פ�¸�ߤ���С�������̲�ñ�̤�Ĥ���
            if ($aryDetail[$j]["cursubtotalprice" . ($index)] > 0) {
                $aryDetail[$j]["cursubtotalprice" . ($index)] = $aryDetail[$j]["cursubtotalprice" . ($index)];
            }

            // ���ʿ��̤�¸�ߤ���С����������ñ�̤�Ĥ���
            if ($aryDetail[$j]["lngproductquantity" . ($index)] > 0) {
                $aryDetail[$j]["lngproductquantity" . ($index)] .= "(" . $aryDetail[$j]["strproductunitname" . ($index)] . ")";
            }

            // ����
            $aryDetail[$j]["lngquantity" . ($index)] = "";

            $objTemplate->replace($aryDetail[$j]);
            $lngRecordCount++;
        }

        $objTemplate->complete();
        $aryHtml[] = $objTemplate->strTemplate;
    }
}
$strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

echo $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
