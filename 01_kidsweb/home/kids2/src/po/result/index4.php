<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ̵����
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
*         ������ȯ���ֹ�ǡ�����̵��������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "po/cmn/lib_pos.php");
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "po/cmn/column.php");

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
if ( !$aryData["lngOrderNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngOrderNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 502 ȯ�������ȯ������
if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 507 ȯ�������ȯ��̵������
if ( !fncCheckAuthority( DEF_FUNCTION_PO7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ̵�����оݤ�ȯ��NO��ȯ��������
$strQuery = fncGetPurchaseHeadNoToInfoSQL ( $aryData["lngOrderNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryOrderResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 503, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// ̵������ǧ���� //////////////////
////////////////////////////////////////////////////////
// ̵�����оݤ�ȯ��ǡ�����̵�����ˤ�äƤɤ��ʤ뤫�γ�ǧ
$lngCase = fncGetInvalidCodeToMaster ( $aryOrderResult, $objDB );

// �����ǡ����γ�ǧ
$strOrderCode = $aryOrderResult["strrealordercode"];
$aryCode = fncGetDeleteNoToMaster ( $aryOrderResult["lngorderno"], 1, $objDB );
if ( $aryCode )
{
	$lngStockCount = count($aryCode);
}
else
{
	$lngStockCount = 0;
}

////////////////////////////////////////////////////////
////////////////////// ̵���������¹� //////////////////
////////////////////////////////////////////////////////
// ����ȯ��Σ����ꤷ��������¸�ߤ��ʤ����̵�����¹�
if ( $aryData["strSubmit"] == "submit" and $lngStockCount == 0 )
{
	// ����ȯ��ξ��֤��ֿ�����ס�����ѡפξ��֤Ǥ����
	if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
	{
		fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �����о�ȯ��ǡ������å�����
	$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $aryData["lngOrderNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "̵�����������顼", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// ̵������ǧ
	$strQuery = "UPDATE m_Order SET bytInvalidFlag = TRUE WHERE lngOrderNo = " . $aryData["lngOrderNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// ���ߥåȽ���
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryOrderResult;
	$aryDeleteData["strAction"] = "/po/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/finish/invalid_parts.tmpl" );

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
if ( $lngStockCount )
{
	// �ִ���ʸ���������
	for( $i = 0; $i < $lngStockCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "��������";
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
// ����ȯ��ξ��֤��ֿ�����ס�����ѡפξ��֤Ǥ����
if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
{
	fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// �����ǡ�����Ĵ��
$aryNewResult = fncSetPurchaseHeadTabelData ( $aryOrderResult );

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
$aryHeadColumnNames = fncSetPurchaseTabelName ( $aryTableViewHead, $aryTytle );
// �����̾������
$aryDetailColumnNames = fncSetPurchaseTabelName ( $aryTableViewDetail, $aryTytle );

////////// ���ٹԤμ��� ////////////////////

// ����ȯ���ֹ��ȯ�����٥ǡ���������SQLʸ�κ���
$strQuery = fncGetPurchaseDetailNoToInfoSQL ( $aryData["lngOrderNo"] );

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 503, DEF_WARNING, "ȯ���ֹ���Ф������٤�¸�ߤ��ޤ���", FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryOrderDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData ( $aryOrderDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/result/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryOrderDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>