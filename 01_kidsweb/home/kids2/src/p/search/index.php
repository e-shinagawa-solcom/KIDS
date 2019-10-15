<?php

// ----------------------------------------------------------------------------
/**
 *       ���ʴ���  ��������
 *
 *
 *       @package    K.I.D.S.
 *       @license    http://www.kuwagata.co.jp/
 *       @copyright  KUWAGATA CO., LTD.
 *       @author     K.I.D.S. Groups <info@kids-groups.com>
 *       @access     public
 *       @version    2.00
 *
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
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// 302 ���ʴ����ʾ��ʸ�����
if (!fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}


// 303 ���ʴ����ʾ��ʸ����ˡ������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_P3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
else
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
// 304 ���ʴ����ʾܺ�ɽ����
if (fncCheckAuthority(DEF_FUNCTION_P4, $objAuth)) {
    $aryData["btnDetail_visibility"] = "visible";
    $aryData["btnDetailVisible"] = "checked";
} else {
    $aryData["btnDetail_visibility"] = "hidden";
    $aryData["btnDetailVisible"] = "";
}
// ����
$aryData["lngInChargeGroupCodeSelect"]	= fncGetPulldown( "m_group", "lnggroupcode", "strgroupdisplaycode || ' ' || strgroupdisplayname as strgroupdisplayname", 0,'WHERE bytgroupdisplayflag = true and lngcompanycode in (0,1)', $objDB );
// ���ƥ��꡼
$lngUserCode = $objAuth->UserCode;
$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory2(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB, 2);
// ���ʹԾ���
$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", 0,'', $objDB );
// �ڻ�
$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", 0, '', $objDB );
// �Ǹ���
$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", 0, '', $objDB );

//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
if ( !$aryData["lngGoodsPlanProgressCode"] or !$aryData["lngCertificateClassCode"] or !$aryData["lngCopyrightCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
}
// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "p/search/p_search.html", $aryData, $objAuth);

$objDB->close();

return true;
