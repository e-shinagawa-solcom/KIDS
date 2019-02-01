<?php

// ----------------------------------------------------------------------------
/**
*       �������  �������ܲ��� ( Inline Frame )
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
*         ���������ܲ���ɽ������ ( Inline Frame )
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
$aryData = $_REQUEST;


// ʸ��������å�
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���¥����å�
// 402 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 403 ��������ʼ������������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) )
{
	$aryData["AdminSet_visibility"] = "visible";
	// 407 ���������̵������
	if ( fncCheckAuthority( DEF_FUNCTION_SO7, $objAuth ) )
	{
		$aryData["btnInvalid_visibility"] = "visible";
		$aryData["btnInvalidVisible"] = "disabled";
	}
	else
	{
		$aryData["btnInvalid_visibility"] = "hidden";
		$aryData["btnInvalidVisible"] = "disabled";
	}
}
else
{
	$aryData["AdminSet_visibility"] = "hidden";
	$aryData["btnInvalid_visibility"] = "hidden";
	$aryData["btnInvalidVisible"] = "";
}
// 404 ��������ʾܺ�ɽ����
if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	$aryData["btnDetail_visibility"] = "visible";
	$aryData["btnDetailVisible"] = "checked";
}
else
{
	$aryData["btnDetail_visibility"] = "hidden";
	$aryData["btnDetailVisible"] = "";
}
// 405 ��������ʽ�����
if ( fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
{
	$aryData["btnFix_visibility"] = "visible";
	$aryData["btnFixVisible"] = "checked";
}
else
{
	$aryData["btnFix_visibility"] = "hidden";
	$aryData["btnFixVisible"] = "";
}
// 406 ��������ʺ����
if ( fncCheckAuthority( DEF_FUNCTION_SO6, $objAuth ) )
{
	$aryData["btnDelete_visibility"] = "visible";
	$aryData["btnDeleteVisible"] = "checked";
}
else
{
	$aryData["btnDelete_visibility"] = "hidden";
	$aryData["btnDeleteVisible"] = "";
}


// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// �����å��ܥå�����˥塼
// �������
$aryData["lngReceiveStatusCode"] 	= fncGetCheckBoxObject( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", "lngReceiveStatusCode[]", 'where lngReceiveStatusCode not in (1)', $objDB );
// ����ե�����
$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );



// ����ʬ
$aryData["lngSalesClassCode"]		= fncGetPulldown( "m_SalesClass", "lngsalesclasscode", "strsalesclassname", 0, '', $objDB );

//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
if ( !$aryData["lngReceiveStatusCode"] or !$aryData["lngSalesClassCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
}

// ���å���������
if( $_COOKIE["ReceiveSearch"] )
{
	$aryCookie = fncStringToArray ( $_COOKIE["ReceiveSearch"], "&", ":" );
	while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
	{
		$aryData[$strKeys] = $strValues;
	}
}

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/search_ifrm/parts.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();

return true;

?>

