<?php

// ----------------------------------------------------------------------------
/**
*       �������  �ܺ�
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
require (SRC_ROOT . "so/cmn/lib_sos.php");
require (SRC_ROOT . "so/cmn/lib_sos1.php");
require (SRC_ROOT . "so/cmn/column.php");

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

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngReceiveNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;


// ���³�ǧ
// 402 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 404 ��������ʾܺ�ɽ����
if ( !fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

//�ܺٲ��̤�ɽ��

$lngReceiveNo = $aryData["lngReceiveNo"];

// ��������ֹ�μ���ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveHeadNoToInfoSQL( $lngReceiveNo );

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

// ���������
if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
{
	$aryTytle = $aryTableTytleEng;
}
else
{
	$aryTytle = $aryTableTytle;
}

// �����̾������
$aryHeadColumnNames = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
// �����̾������
$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail, $aryTytle );

////////// ���ٹԤμ��� ////////////////////

// ��������ֹ�μ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetReceiveDetailNoToInfoSQL ( $lngReceiveNo );

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "�����ֹ���Ф������پ��󤬸��Ĥ���ޤ���", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetReceiveDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/result/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

$aryNewResult["strAction"] = "index2.php";
$aryNewResult["strMode"] = "detail";

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/result/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>