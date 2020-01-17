<?php

// ----------------------------------------------------------------------------
/**
*       �������  �ܺ�
*
*       ��������
*         ����������ֹ�ǡ����ξܺ�ɽ������
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
// 404 ��������ʾܺ�ɽ����
if ( !fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
//�ܺٲ��̤�ɽ��
$lngReceiveNo = $aryData["lngReceiveNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];
// ��������ֹ�μ���ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveHeadNoToInfoSQL( $lngReceiveNo, $lngRevisionNo);
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
// �����ǡ�����Ĵ��
$aryNewResult = fncSetReceiveHeadTabelData ( $aryResult );
////////// ���ٹԤμ��� ////////////////////
// ��������ֹ�μ������٥ǡ���������SQLʸ�κ���
// $aryData["strreceivecode2"] = $aryResult["strreceivecode2"];
// $aryData["lngrevisionno"] = $aryResult["lngrevisionno"];
$strQuery = fncGetReceiveDetailNoToInfoSQL ($lngReceiveNo, $lngRevisionNo);

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		// var_dump($aryDetailResult);
		// echo "tst";
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "�����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

// ���پ���ν���
for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetReceiveDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/detail/so_parts_detail.html" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/detail/so_detail.html" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>