<?
/**
 *    Ģɼ���� Ǽ�ʽ� ������λ����
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// �����ץ�ӥ塼����( * �ϻ���Ģɼ�Υե�����̾ )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;
require SRC_ROOT . "/list/cmn/lib_lo.php";
require SRC_ROOT . "m/cmn/lib_m.php";
require LIB_DEBUGFILE;

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
$aryCheck["strReportKeyCode"] = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// ���ꥭ�������ɤ�Ģɼ�ǡ��������
$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum === 1) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strListOutputPath = $objResult->strreportpathname;
    unset($objResult);
    $objDB->freeResult($lngResultID);
}

// Ģɼ��¸�ߤ��ʤ���硢���ԡ�Ģɼ�ե��������������¸
elseif ($lngResultNum === 0) {
    // �ǡ�������������
    $strQuery = fncGetListOutputQuery(DEF_REPORT_SLIP, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);

    $aryParts = &$objMaster->aryData[0];

    // Ǽ����ɼ���̼���
    $strQuery = fncGetSlipKindQuery($aryParts["strshippercode"]);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum < 1) {
        fncOutputError(9051, DEF_FATAL, "Ǽ����ɼ���̥ǡ�����¸�ߤ��ޤ���Ǥ�����", true, "", $objDB);
    } else {
        $slipKidObj = $objDB->fetchArray($lngResultID, 0);
    }

    $objDB->freeResult($lngResultID);

    unset($aryQuery);

    // �ܺټ���
    $strQuery = fncGetSlipDetailQuery($aryData["strReportKeyCode"]);
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
        for ($j = 0; $j < count($aryKeys); $j++) {
            $aryDetail[$i][$aryKeys[$j] . $i] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    $rowNum = $slipKidObj["lngmaxline"];

    // �ƥ�ץ졼�ȥѥ�����
    if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_EXCLUSIVE) {
        $strTemplateHeaderPath = "list/result/slip_exc_header.html";
        $strTemplatePath = "list/result/slip_exc.html";
        $strTemplateFooterPath = "list/result/slip_exc_footer.html";
    } else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
        $strTemplateHeaderPath = "list/result/slip_comm_header.html";
        $strTemplatePath = "list/result/slip_comm.html";
        $strTemplateFooterPath = "list/result/slip_comm_footer.html";
    } else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
        $strTemplateHeaderPath = "list/result/slip_debit_header.html";
        $strTemplatePath = "list/result/slip_debit.html";
        $strTemplateFooterPath = "list/result/slip_debit_footer.html";
    }

    // ���Τξ��
    if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
        // ����������
        $lngtaxclasscode = $aryParts["lngtaxclasscode"];
        $curtotalprice = $aryParts["curtotalprice_comm"];
        $curtax = $aryParts["curtax"];
        $curtaxprice = $lngtaxclasscode != 1 ? 0 : ($lngtaxclasscode = 1 ? ($curtotalprice * $curtax) : ($curtotalprice / (1 + $curtax) * $curtax));
        $aryParts["curtaxprice"] = round($curtaxprice);

        // ��׶��
        $curtotalprice = str_pad(round($curtotalprice), 8, " ", STR_PAD_LEFT);
        for ($k = 0; $k < 8; $k++) {
            $aryParts["curtotalprice" . $k] = substr($curtotalprice, $k, 1);
        }

        // �ǹ����
        $curprice = $curtotalprice + $curtaxprice;
        $curprice = str_pad(round($curprice), 8, " ", STR_PAD_LEFT);
        $len = strlen($curprice);
        for ($k = 0; $k < 8; $k++) {
            $aryParts["curprice" . $k] = substr($curprice, $k, 1);
        }

        // DEBIT��NOTE�ξ��
    } else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {

        // �ܵ������ֹ�
        $aryParts["strcustomertel"] = "Tel:" . $aryParts["strcustomertel1"] . " " . $aryParts["strcustomertel2"];

        // �ܵ�FAX�ֹ�
        $aryParts["strcustomerfax"] = "Fax.:" . $aryParts["strcustomerfax1"] . " " . $aryParts["strcustomerfax2"];

        // ��׶��
        $curTotalPrice = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"]) . " " . $aryParts["curtotalprice"];

        $aryParts["curtotalprice"] = $curTotalPrice;
        $aryParts["nameofbank"] = $aryParts["lngpaymentmethodcode"] == 1 ? "MUFG BANK, LTD." : "";
        $aryParts["nameofbranch"] = $aryParts["lngpaymentmethodcode"] == 1 ? "ASAKUSA BRANCH" : "";
        $aryParts["addressofbank1"] = $aryParts["lngpaymentmethodcode"] == 1 ? "4-2, ASAKUSA 1-CHOME, " : "";
        $aryParts["addressofbank2"] = $aryParts["lngpaymentmethodcode"] == 1 ? " TAITO-KU, TOKYO 111-0032, JAPAN" : "";
        $aryParts["swiftcode"] = $aryParts["lngpaymentmethodcode"] == 1 ? "BOTKJPJT" : "";
        $aryParts["accountname"] = $aryParts["lngpaymentmethodcode"] == 1 ? "KUWAGATA CO.,LTD." : "";
        $aryParts["accountno"] = $aryParts["lngpaymentmethodcode"] == 1 ? "1063143" : "";
    }

    // HTML����
    $objTemplateHeader = new clsTemplate();
    $objTemplateHeader->getTemplate($strTemplateHeaderPath);
    $objTemplateHeader->replace($aryData);
    $objTemplateHeader->complete();
    $strTemplateHeader = $objTemplateHeader->strTemplate;

    $objTemplateFooter = new clsTemplate();
    $objTemplateFooter->getTemplate($strTemplateFooterPath);
    $strTemplateFooter = $objTemplateFooter->strTemplate;

    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate($strTemplatePath);
    $strTemplate = $objTemplate->strTemplate;

    $objTemplate->strTemplate = $strTemplate;

    // �֤�����
    $objTemplate->replace($aryParts);

    for ($i = 0; $i < $lngResultNum; $i++) {
        // ��׶��
        if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_COMM) {
            // �������
            $cursubtotalprice = str_pad($aryDetail[$i]["cursubtotalprice_comm" . ($i)], 8, " ", STR_PAD_LEFT);
            for ($k = 0; $k < 8; $k++) {
                $aryDetail[$i]["cursubtotalprice" . $i . $k] = substr($cursubtotalprice, $k, 1);
            }
            // ����
            $aryDetail[$i]["lngquantity" . ($i)] = "";
        } else if ($slipKidObj["lngslipkindcode"] == DEF_SLIP_KIND_DEBIT) {
            // �ܵҼ����ֹ�
            if ($aryDetail[$i]["strcustomersalescode" . ($i)] != "") {
                $aryDetail[$i]["strcustomersalescode" . ($i)] = "(PO No:" . $aryDetail[$i]["strcustomersalescode" . ($i)] . ")";
            }
        }

        // �֤�����
        $objTemplate->replace($aryDetail[$i]);
    }

    $objTemplate->complete();
    $aryHtml[] = $objTemplate->strTemplate;

    $strBodyHtml = join("<br style=\"page-break-after:always;\">\n", $aryHtml);

    $strHtml = $strTemplateHeader . $strBodyHtml . $strTemplateFooter;

    $objDB->transactionBegin();

    // ��������ȯ��
    $lngSequence = fncGetSequence("t_Report.lngReportCode", $objDB);

    // Ģɼ�ơ��֥��INSERT
    $strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_SLIP . ", " . $aryParts["lngslipno"] . ", '', '$lngSequence' )";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // Ģɼ�ե����륪���ץ�
    if (!$fp = fopen(SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w")) {
        list($lngResultID, $lngResultNum) = fncQuery("ROLLBACK", $objDB);
        fncOutputError(9059, DEF_FATAL, "Ģɼ�ե�����Υ����ץ�˼��Ԥ��ޤ�����", true, "", $objDB);
    }

    // Ģɼ�ե�����ؤν񤭹���
    if (!fwrite($fp, $strHtml)) {
        list($lngResultID, $lngResultNum) = fncQuery("ROLLBACK", $objDB);
        fncOutputError(9059, DEF_FATAL, "Ģɼ�ե�����ν񤭹��ߤ˼��Ԥ��ޤ�����", true, "", $objDB);
    }

    $objDB->transactionCommit();
}

$objDB->close();



header("location: /list/result/slip/download.php?strSessionID=" . $aryData["strSessionID"]
. "&strReportKeyCode=" . $aryData["strReportKeyCode"]
    . "&lngReportCode=" . $aryData["lngReportCode"]
    . "&reprintFlag=" . $aryData["reprintFlag"]);

return true;