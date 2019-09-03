<?php

// ----------------------------------------------------------------------------
/**
*       �������  ������
*
*       ��������
*         ����������ֹ�ǡ����γ�������ɽ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "so/cmn/lib_so.php");
// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GET�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
// ���³�ǧ
// 402 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 406 ��������ʳ����á�
if ( !fncCheckAuthority( DEF_FUNCTION_SO6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
//�ܺٲ��̤�ɽ��
$lngReceiveNo = $aryData["lngReceiveNo"];
// ��������ֹ�μ���ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveHeadNoToInfoSQL($lngReceiveNo, DEF_RECEIVE_ORDER);

// �ܺ٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	else
	{
		fncOutputError( 403, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
}
else
{
	fncOutputError( 403, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );
////////// ���ٹԤμ��� ////////////////////
// ��������ֹ�μ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveDetailNoToInfoSQL ($lngReceiveNo);

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryDetailResult = $objDB->fetchArray( $lngResultID, 0);
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "�����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

// �̲ߵ��������
if ($aryResult["lngmonetaryunitcode"] == 1) {
    $aryResult["strmonetaryunitsign"] = "&yen;";
}

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/cancel/so_confirm_cancel.html" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryResult);
$objTemplate->replace( $aryDetailResult);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>