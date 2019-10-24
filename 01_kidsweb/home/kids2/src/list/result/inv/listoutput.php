<?
/**
 *    Ģɼ���� ����� �����ץ�ӥ塼����
 *
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

$strQuery = fncGetCopyFilePathQuery(DEF_REPORT_INV, $aryData["strReportKeyCode"], $aryData["lngReportCode"]);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum > 0) {
    $objResult = $objDB->fetchObject($lngResultID, 0);
    $strReportPathName = $objResult->strreportpathname;
    unset($objResult);
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
            $aryDetail[$i][$aryKeys[$j] . $i] = $aryResult[$j];
        }
    }
    $objDB->freeResult($lngResultID);

    $objDB->close();

    // HTML����
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("list/result/inv.html");
    $aryParts["totalprice_unitsign"] = ($aryParts["lngmonetaryunitcode"] == 1 ? "&yen; " : $aryParts["strmonetaryunitsign"]) . " " . $aryParts["totalprice"];
    $aryParts["dtminvoicedate"] = convert_jpdt($aryParts["dtminvoicedate"], 'ǯm��');
    $aryParts["dtminsertdate"] = convert_jpdt($aryParts["dtminsertdate"],'.m.d',false);
    // �֤�����
    $objTemplate->replace($aryParts);
    $objTemplate->replace($aryDetail);
    $objTemplate->complete();
    $strHtml = $objTemplate->strTemplate;

}

echo $strHtml;

