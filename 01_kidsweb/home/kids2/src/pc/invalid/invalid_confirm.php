<?php

// ----------------------------------------------------------------------------
/**
 *       ��������  ̵����ǧ
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
require SRC_ROOT . "pc/cmn/lib_pc.php";
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

// ���顼���̤Ǥ����URL
$strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

// ���³�ǧ
// 700 ��������
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, $strReturnPath, $objDB);
}
// 707 ���������ʻ���̵������
if ( !fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, $strReturnPath, $objDB );
}
// �����ֹ�μ���
$lngStockNo = $aryData["lngStockNo"];
// ̵���оݤλ���NO�λ����������
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryResult = $objDB->fetchArray($lngResultID, 0);
		// ���������ξ��֤�������ѡפξ��֤Ǥ����
        if ($aryResult["lngstockstatuscode"] == DEF_STOCK_CLOSED) {
            fncOutputError(711, DEF_WARNING, "", true, $strReturnPath, $objDB);
        }
    } else {
        fncOutputError(703, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", true, $strReturnPath, $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "�ǡ������۾�Ǥ�", true, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

// �����ǡ�����Ĵ��
$aryNewResult = fncSetStockHeadTabelData($aryResult);

// ��������ֹ�λ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetStockDetailNoToInfoSQL($lngStockNo);
// ���٥ǡ����μ���
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    $strMessage = fncOutputError(703, DEF_WARNING, "�����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", false, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

// ���پ���ν���
for ($i = 0; $i < count($aryDetailResult); $i++) {
    $aryNewDetailResult[$i] = fncSetStockDetailTabelData($aryDetailResult[$i], $aryNewResult);

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("pc/invalid/pc_parts_invalid.html");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryNewDetailResult[$i]);
    $objTemplate->complete();

    // HTML����
    $aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode("\n", $aryDetailTable);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("pc/invalid/pc_invalid.html");

// �ƥ�ץ졼������
$objTemplate->replace($aryNewResult);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();
return true;