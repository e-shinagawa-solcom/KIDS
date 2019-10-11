<?php

// ----------------------------------------------------------------------------
/**
 *       ������  ��������
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
require( "libsql.php" );

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

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// ���³�ǧ
// 602 ����������帡����
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 603 ����������帡���������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
    // 607 ��������̵������
    if ( fncCheckAuthority( DEF_FUNCTION_SC7, $objAuth ) )
    {
        $aryData["btnInvalid_visibility"] = 'style="visibility: visible"';
        $aryData["btnInvalidVisible"] = "disabled";
    }
    else
    {
        $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
        $aryData["btnInvalidVisible"] = "disabled";
    }
}
else
{
    $aryData["AdminSet_visibility"] = 'style="visibility: hidden"';
    $aryData["btnInvalid_visibility"] = 'style="visibility: hidden"';
    $aryData["btnInvalidVisible"] = "";
}
// 604 �������ʾܺ�ɽ����
if ( fncCheckAuthority( DEF_FUNCTION_SC4, $objAuth ) )
{
    $aryData["btnDetail_visibility"] = 'style="visibility: visible"';
    $aryData["btnDetailVisible"] = "checked";
}
else
{
    $aryData["btnDetail_visibility"] = 'style="visibility: hidden"';
    $aryData["btnDetailVisible"] = "";
}


// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ��她�ơ�����
$aryData["lngSalesStatusCode"] 		= fncGetCheckBoxObject( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", "lngSalesStatusCode[]", 'where lngSalesStatusCode not in (1)', $objDB );

// ����ʬ
$aryData["lngSalesClassCode"]		= fncGetPulldown( "m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", 1, '', $objDB );

//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
if ( !$aryData["lngSalesStatusCode"] or !$aryData["lngSalesClassCode"] )
{
    fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
}

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "sc/search/sc_search.html", $aryData, $objAuth);

$objDB->close();

return true;
