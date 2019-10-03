<?php

// ----------------------------------------------------------------------------
/**
 *       ������  �ܺ�
 *
 *       ��������
 *         ����������ֹ�ǡ����ξܺ�ɽ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// �����ɤ߹���
include_once 'conf.inc';

// �饤�֥���ɤ߹���
require LIB_FILE;

require SRC_ROOT . "sc/cmn/lib_scs.php";
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
// 602 ����������帡����
if (!fncCheckAuthority(DEF_FUNCTION_SC2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}
// 604 �������ʾܺ�ɽ����
if (!fncCheckAuthority(DEF_FUNCTION_SC4, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

$lngSalesNo = $aryData["lngSalesNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

// ��������ֹ�����ǡ���������SQLʸ�κ���
$strQuery = fncGetSalesHeadNoToInfoSQL($lngSalesNo, $lngRevisionNo);

// �ܺ٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
    } else {
        fncOutputError(603, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", true, "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    }
} else {
    fncOutputError(603, DEF_ERROR, "�ǡ������۾�Ǥ�", true, "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// �����ǡ�����Ĵ��
$aryNewResult = fncSetSalesHeadTabelData($aryResult);

////////// ���ٹԤμ��� ////////////////////
// ��������ֹ��������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetSalesDetailNoToInfoSQL($lngSalesNo, $lngRevisionNo);
// ���٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(603, DEF_WARNING, "����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", false, "../sc/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

$objDB->freeResult($lngResultID);

// ���پ���ν���
for ($i = 0; $i < count($aryDetailResult); $i++) {

    $aryNewDetailResult[$i] = fncSetSalesDetailTabelData($aryDetailResult[$i], $aryNewResult);

    $aryNewDetailResult[$i]["lngmonetaryunitcode"] = $aryNewResult["strmonetaryunitname"];
    $aryNewDetailResult[$i]["curconversionrate"] = $aryNewResult["curconversionrate"];

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("sc/detail/sc_parts_detail.html");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryNewDetailResult[$i]);
    $objTemplate->complete();

    // HTML����
    $aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode("\n", $aryDetailTable);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("sc/detail/sc_detail.html");

// �ƥ�ץ졼������
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();
return true;
