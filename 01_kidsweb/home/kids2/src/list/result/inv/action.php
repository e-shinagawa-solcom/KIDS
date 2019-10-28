<?
/**
 *    Ģɼ���� ����� ������λ����
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

$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// ���ꥭ�������ɤ�Ģɼ�ǡ��������
$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

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
    $strQuery = fncGetListOutputQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $objDB);

    $objMaster = new clsMaster();
    $objMaster->setMasterTableData($strQuery, $objDB);

    $aryParts = &$objMaster->aryData[0];

    unset($aryQuery);

    // �ܺټ���
    $strQuery = fncGetInvDetailQuery($aryData["strReportKeyCode"], $aryParts["lngrevisionno"]);
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
            $aryParts[$aryKeys[$j] . $i] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    // HTML����
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("list/result/inv.html");
    $aryParts["totalprice_unitsign"] = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"]) . " " . $aryParts["totalprice"];
    $aryParts["dtminvoicedate"] = convert_jpdt($aryParts["dtminvoicedate"], 'ǯm��');
    $aryParts["dtminsertdate"] = convert_jpdt($aryParts["dtminsertdate"],'.m.d',false);
    if ($aryData["reprintFlag"]) {
        $aryParts["reprintMsg"] = "�ư���";
    } else {
        $aryParts["reprintMsg"] = "";
    }
    // �֤�����
    $objTemplate->replace($aryParts);
    $objTemplate->complete();   

    $strHtml = $objTemplate->strTemplate;
    
    $objDB->transactionBegin();

    // ��������ȯ��
    $lngSequence = fncGetSequence("t_Report.lngReportCode", $objDB);

    // Ģɼ�ơ��֥��INSERT
    $strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_INV . ", " . $aryParts["lnginvoiceno"] . ", '', '$lngSequence' )";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    
    $objDB->freeResult($lngResultID);

    // �������������
    $aryParts["lngprintcount"] += 1;

    // ��������ι���    
    $strQuery = "update m_invoice set lngprintcount = ".$aryParts["lngprintcount"] ." where lnginvoiceno = " .$aryParts["lnginvoiceno"] . " and lngrevisionno = " .$aryParts["lngrevisionno"];
    echo $strQuery;
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    
    $objDB->freeResult($lngResultID);

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

echo "<script language=javascript>parent.window.close();</script>";

$objDB->close();

return true;
