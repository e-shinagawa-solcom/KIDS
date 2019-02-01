<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ̵����
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


/** 
*	������̵������ǧ����
*
*	����̵������ǧ���̤�ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	�������̤�����򤵤줿�����ֹ��̵������ǧ���̤�ɽ������
*
*/

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "pc/cmn/lib_pcs.php");
require (SRC_ROOT . "pc/cmn/lib_pcs1.php");
require (SRC_ROOT . "pc/cmn/column.php");

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
if ( !$aryData["lngStockNo"] )
{
	fncOutputError ( 9018, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngStockNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 702 ���������ʻ���������
if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 707 ���������ʻ���̵������
if ( !fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ̵�����оݤλ���NO�λ����������
$strQuery = fncGetStockHeadNoToInfoSQL ( $aryData["lngStockNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryStockResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 703, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// ̵������ǧ���� //////////////////
////////////////////////////////////////////////////////
// ̵�����оݤλ����ǡ�����̵�����ˤ�äƤɤ��ʤ뤫�γ�ǧ
$lngCase = fncGetInvalidCodeToMaster ( $aryStockResult, $objDB );

////////////////////////////////////////////////////////
////////////////////// ̵���������¹� //////////////////
////////////////////////////////////////////////////////
if( $aryData["strSubmit"] )
{
	// ���������ξ��֤�������ѡפξ��֤Ǥ����
	if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
	{
		fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �����оݻ����ǡ������å�����
	$strLockQuery = "SELECT lngStockNo FROM m_Stock WHERE lngStockNo = " . $aryData["lngStockNo"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strLockQuery, $objDB );
	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "̵�����������顼", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// ̵������ǧ
	$strQuery = "UPDATE m_Stock SET bytInvalidFlag = TRUE WHERE lngStockNo = " . $aryData["lngStockNo"] . " AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objDB->freeResult( $lngResultID );

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryStockResult;
	$aryDeleteData["strAction"] = "/pc/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/finish/invalid_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
//////////////////// ̵������ǧ����ɽ�� //////////////////
////////////////////////////////////////////////////////
// ���������ξ��֤�������ѡפξ��֤Ǥ����
if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
{
	fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// �����ǡ�����Ĵ��
$aryNewResult = fncSetStockHeadTabelData ( $aryStockResult );

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
$aryHeadColumnNames = fncSetStockTabelName ( $aryTableViewHead, $aryTytle );
// �����̾������
$aryDetailColumnNames = fncSetStockTabelName ( $aryTableViewDetail, $aryTytle );

////////// ���ٹԤμ��� ////////////////////

// ��������ֹ�λ������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetStockDetailNoToInfoSQL ( $aryData["lngStockNo"] );

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$aryStockDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryStockDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetStockDetailTabelData ( $aryStockDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/result/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryStockDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index4.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "Invalid0" . $lngCase;

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "pc/result/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>