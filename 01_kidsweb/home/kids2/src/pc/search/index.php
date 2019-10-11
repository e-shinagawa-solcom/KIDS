<?php

// ----------------------------------------------------------------------------
/**
 *       ��������  ��������
 *
 *       ��������
 *         ����������ɽ������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

// ������ɤ߹���
include_once "conf.inc";

// �饤�֥���ɤ߹���
require LIB_FILE;

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

// ���¥����å�
// 702 ���������ʻ���������
if (!fncCheckAuthority(DEF_FUNCTION_PC2, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// 703 ���������ʻ��������������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
    // 707 ����������̵������
    if (fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth)) {
        $aryData["btnInvalid_visibility"] = 'style="visibility: visible"';
        $aryData["btnInvalidVisible"] = "disabled";
    } else {
        $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
        $aryData["btnInvalidVisible"] = "disabled";
    }
}
else
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
    $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
    $aryData["btnInvalidVisible"] = "";
}

// 704 ���������ʾܺ�ɽ����
if (fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth)) {
    $aryData["btnDetail_visibility"] = 'style="visibility: visible"';
    $aryData["btnDetailVisible"] = "checked";
} else {
    $aryData["btnDetail_visibility"] = 'style="visibility: hidden"';
    $aryData["btnDetailVisible"] = "";
}
// 705 ���������ʽ�����
if (fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth)) {
    $aryData["btnFix_visibility"] = 'style="visibility: visible"';
    $aryData["btnFixVisible"] = "checked";
} else {
    $aryData["btnFix_visibility"] = 'style="visibility: hidden"';
    $aryData["btnFixVisible"] = "";
}
// 706 ���������ʺ����
if (fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth)) {
    $aryData["btnDelete_visibility"] = 'style="visibility: visible"';
    $aryData["btnDeleteVisible"] = "checked";
} else {
    $aryData["btnDelete_visibility"] = 'style="visibility: hidden"';
    $aryData["btnDeleteVisible"] = "";
}
// ��������
$aryData["lngStockStatusCode"] = fncGetCheckBoxObject("m_stockstatus", "lngstockstatuscode", "strstockstatusname", "lngStockStatusCode[]", 'where lngStockStatusCode not in (1)', $objDB);
// ��ʧ���
$aryData["lngPayConditionCode"] = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", 0, '', $objDB);
// ������ˡ
$aryData["lngDeliveryMethodCode"] = fncGetPulldown("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", 0, '', $objDB);
// ��������
$aryData["lngStockSubjectCode"] = fncGetPulldown("m_stocksubject", "lngstocksubjectcode", "lngstocksubjectcode,	strstocksubjectname", 1, '', $objDB);
// ��������
$aryData["lngStockItemCode"] = fncGetPulldown("m_stockitem", "lngstocksubjectcode || '-' || lngstockitemcode", "lngstockitemcode, 	strstockitemname", 0, '', $objDB);

// ��������������
$TmpAry = explode("\n", $aryData["lngStockItemCode"]);

foreach ($TmpAry as $key => $value) {
    if ($value) {
        $ValuePosS = 15;
        $ValuePosE = mb_strpos($value, ">", $ValuePosS) - 1;
        $DispPosS = $ValuePosE + 2;
        $DispPosE = mb_strpos($value, "OPTION", $DispPosS) - 2;
        if (array_key_exists('lngStockItemCodeValue', $aryData)) {
            $aryData["lngStockItemCodeValue"] = $aryData["lngStockItemCodeValue"] . ",," . substr($value, $ValuePosS, $ValuePosE - $ValuePosS);
            $aryData["lngStockItemCodeDisp"] = $aryData["lngStockItemCodeDisp"] . ",," . mb_ereg_replace("</OPTION>", "", substr($value, $DispPosS));
        } else {
            $aryData["lngStockItemCodeValue"] = substr($value, $ValuePosS, $ValuePosE - $ValuePosS);
            $aryData["lngStockItemCodeDisp"] = mb_ereg_replace("</OPTION>", "", substr($value, $DispPosS));
        }
    }
}

$aryData["lngStockItemCodeValue"] = "<input type=\"hidden\" name=\"lngStockItemCodeValue\" value=\"" . $aryData["lngStockItemCodeValue"] . "\"</option>";
$aryData["lngStockItemCodeDisp"] = mb_convert_encoding("<input type=\"hidden\" name=\"lngStockItemCodeDisp\" value=\"" . $aryData["lngStockItemCodeDisp"] . "\"</option>", "EUC-JP", "auto");

//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
if (!$aryData["lngStockStatusCode"] or !$aryData["lngPayConditionCode"] or !$aryData["lngStockSubjectCode"] or !$aryData["lngStockItemCode"]) {
    fncOutputError(9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", true, "", $objDB);
}

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "pc/search/pc_search.html", $aryData, $objAuth);

$objDB->close();

return true;
