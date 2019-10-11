<?php

// ----------------------------------------------------------------------------
/**
*       �������  ��������
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
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ʸ��������å�
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 402 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 403 ��������ʼ������������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) )
{
	$aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
else
{
	$aryData["AdminSet_visibility"] = 'style="visibility: hidden"';
}
// 404 ��������ʾܺ�ɽ����
if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	$aryData["btnDetail_visibility"] = 'style="visibility: visible"';
	$aryData["btnDetailVisible"] = "checked";
}
else
{
	$aryData["btnDetail_visibility"] = 'style="visibility: hidden"';
	$aryData["btnDetailVisible"] = "";
}
// 405 ��������ʳ����
if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	$aryData["btnDecide_visibility"] = 'style="visibility: visible"';
	$aryData["btnDecideVisible"] = "checked";
}
else
{
	$aryData["btnDecide_visibility"] = 'style="visibility: hidden"';
	$aryData["btnDecideVisible"] = "";
}
// 406 ��������ʳ����á�
if ( fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
{
	$aryData["btnCancel_visibility"] = 'style="visibility: visible"';
	$aryData["btnCancelVisible"] = "checked";
}
else
{
	$aryData["btnCancel_visibility"] = 'style="visibility: hidden"';
	$aryData["btnCancelVisible"] = "";
}

// �����ơ�����
// $aryData["lngReceiveStatusCode"] 	= fncGetCheckBoxObject( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", "lngReceiveStatusCode[]", 'where lngReceiveStatusCode not in (1)', $objDB );
$aryData["lngReceiveStatusCode"] 	= fncGetCheckBoxObject( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", "lngReceiveStatusCode[]", '', $objDB );


// ����ʬ
$aryData["lngSalesClassCode"] = fncGetPulldown( "m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", 1, '', $objDB );

//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
if ( !$aryData["lngReceiveStatusCode"] or !$aryData["lngSalesClassCode"] )
{
    fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
}

// �إ���б�
$aryData["lngFunctionCode"] = DEF_FUNCTION_SO2;

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "so/search/so_search.html", $aryData ,$objAuth );

$objDB->close();

return true;

?>

