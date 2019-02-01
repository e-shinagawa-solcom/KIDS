<?php

// ----------------------------------------------------------------------------
/**
*       �������  ̵����
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
*         ����������ֹ�ǡ�����̵��������
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
if ( !$aryData["lngReceiveNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngReceiveNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 402 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 407 ��������ʼ���̵������
if ( !fncCheckAuthority( DEF_FUNCTION_SO7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ̵�����оݤμ���NO�μ���������
$strQuery = fncGetReceiveHeadNoToInfoSQL ( $aryData["lngReceiveNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 403, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// ̵������ǧ���� //////////////////
////////////////////////////////////////////////////////
// ̵�����оݤμ���ǡ�����̵�����ˤ�äƤɤ��ʤ뤫�γ�ǧ
$lngCase = fncGetInvalidCodeToMaster ( $aryReceiveResult, $objDB );

// ���ǡ����γ�ǧ
$strReceiveCode = $aryReceiveResult["strreceivecode2"];
$aryCode = fncGetDeleteNoToMaster ( $aryReceiveResult["lngreceiveno"], 1, $objDB );
if ( $aryCode )
{
	$lngSalesCount = count($aryCode);
}
else
{
	$lngSalesCount = 0;
}

////////////////////////////////////////////////////////
////////////////////// ̵���������¹� //////////////////
////////////////////////////////////////////////////////
// ��������Σ����ꤷ�Ƥ������ǡ�����¸�ߤ��ʤ����
if ( $aryData["strSubmit"] == "submit" and $lngSalesCount == 0 )
{
	// ��������ξ��֤�������ѡפξ��֤Ǥ����
	if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �����оݼ���ǡ������å�����
	$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "̵�����������顼", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// ̵������ǧ
	$strQuery = "UPDATE m_Receive SET bytInvalidFlag = TRUE WHERE lngReceiveNo = " . $aryData["lngReceiveNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryReceiveResult;
	$aryDeleteData["strAction"] = "/so/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/finish/invalid_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
////////////////////// ̵�����Ǥ��ʤ� //////////////////
////////////////////////////////////////////////////////
if ( $lngSalesCount )
{
	// �ִ���ʸ���������
	for( $i = 0; $i < $lngSalesCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "������";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML����
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] = implode ("\n", $aryDetail );
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "error/use/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
//////////////////// ̵������ǧ����ɽ�� //////////////////
////////////////////////////////////////////////////////
// ��������ξ��֤�������ѡפξ��֤Ǥ����
if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
{
	fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// �����ǡ�����Ĵ��
$aryNewResult = fncSetReceiveHeadTabelData ( $aryReceiveResult );

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
$strQuery = fncGetReceiveDetailNoToInfoSQL ( $aryData["lngReceiveNo"] );

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryReceiveDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 403, DEF_WARNING, "�����ֹ���Ф������٤�¸�ߤ��ޤ���", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryReceiveDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetReceiveDetailTabelData ( $aryReceiveDetailResult[$i], $aryNewResult );

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

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryReceiveDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

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