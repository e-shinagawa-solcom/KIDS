<?php

// ----------------------------------------------------------------------------
/**
 *       ���ʴ���  �ܺ�
 *
 *       ��������
 *         �����������ֹ�ǡ����ξܺ�ɽ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;

// require (LIB_ROOT . "clscache.php" );
require (SRC_ROOT . "p/cmn/lib_ps1.php");
// DB��³
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//////////////////////////////////////////////////////////////////////////
// GET�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ($_GET) {
    $aryData = $_GET;
} else if ($_POST) {
    $aryData = $_POST;
}

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
// ���³�ǧ
// 302 ���ʴ����ʾ��ʸ�����
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
// 304 ���ʴ����ʾܺ�ɽ����
if (!fncCheckAuthority(DEF_FUNCTION_P4, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

$lngProductNo = $aryData["lngProductNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// ���꾦���ֹ�ξ��ʥǡ���������SQLʸ�κ���
$strQuery = fncGetProductNoToInfoSQL($lngProductNo, $lngRevisionNo);

// �ܺ٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(303, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", true, "../p/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    }
} else {
    fncOutputError(303, DEF_ERROR, "�ǡ������۾�Ǥ�", true, "../p/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// �����ǡ�����Ĵ��
$aryNewResult = fncSetProductTableData($aryResult, $objDB);

// Ģɼ�����б�
// ɽ���оݤ�����ǡ����ξ��ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
// �ʤ����¤���äƤʤ�����ץ�ӥ塼�ܥ����ɽ�����ʤ�
if (!$aryResult["bytInvalidFlag"] && fncCheckAuthority(DEF_FUNCTION_LO1, $objAuth)) {
    $aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $lngProductNo . "&bytCopyFlag=TRUE";

    $aryNewResult["listview"] = 'style="visibility: visible"';
} else {
    $aryNewResult["listview"] = 'style="visibility: hidden"';
}

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("p/detail/p_detail.html");

// �ƥ�ץ졼������
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();
return true;
