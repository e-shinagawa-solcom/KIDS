<?php

// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ���
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
*         ������Ǽ����ɼ�ֹ�ǡ����κ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "sc/cmn/lib_scd1.php");
require (SRC_ROOT . "sc/cmn/column_scd.php");
require (LIB_DEBUGFILE);
require_once (LIB_EXCLUSIVEFILE);

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
if ( !$aryData["lngSlipNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
// TODO:�׻��ͳ�ǧ
//$aryCheck["lngSlipNo"]	  = "null:number(0,10)";

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 602 ����������帡����
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 606 ���������������
if ( !fncCheckAuthority( DEF_FUNCTION_SC6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ����оݤ�Ǽ����ɼ�ֹ��Ǽ�ʾ������
$strQuery = fncGetSlipHeadNoToInfoSQL ( $aryData["lngSlipNo"], $aryData["lngRevisionNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum == 1 )
{
	$aryHeadResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 603, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// *****************************************************
//   ��������¹ԡ�Submit����
// *****************************************************
if( $aryData["strSubmit"] )
{
	
	$lngSalesNo = $aryHeadResult["lngsalesno"];
	$strSlipCode = $aryHeadResult["strslipcode"];
	$lngSlipNo = $aryHeadResult["lngslipno"];
	$strCustomerCode = $aryHeadResult["strcustomercode"];

	// --------------------------------
	//    �������
	// --------------------------------
	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

fncDebug("kids2.log", "step-0", __FILE__, __LINE__, "a");
    

	// --------------------------------
	//    �����ǽ���ɤ����Υ����å�
	// --------------------------------
	// �ܵҤι����ܤǡ�����Ǽ�ʽ�إå���ɳ�Ť���������٤�¸�ߤ�����Ϻ���Բ�
	if (fncJapaneseInvoiceExists($strCustomerCode, $lngSalesNo, $objDB)){
		MoveToErrorPage("�����ȯ�ԺѤߤΤ��ᡢ����Ǥ��ޤ���");
	}

	// Ǽ�ʽ����٤�ɳ�Ť������ơ������������Ѥߡפξ��Ϻ���Բ�
	if (fncReceiveStatusIsClosed($aryData["lngSlipNo"], $objDB))
	{
		MoveToErrorPage("���ѤߤΤ��ᡢ����Ǥ��ޤ���");
	}

	

	if( !lockSlip($aryData["lngSlipNo"], $objDB))
	{
		MoveToErrorPage("¾�桼������Ǽ�ʽ���Խ���Ǥ���");
	}

	
	if( isSlipModified($aryData["lngSlipNo"], $aryData["lngRevisionNo"], $objDB) )
	{
		MoveToErrorPage("Ǽ�ʽ�¾�桼�����ˤ�깹���ޤ��Ϻ������Ƥ��ޤ���");
	}


	// ���ǡ����κ��
	if (!fncDeleteSales($lngSalesNo, $objDB, $objAuth))
	{
		fncOutputError ( 9051, DEF_FATAL, "���������ȼ�����ޥ�����������", TRUE, "", $objDB );
	}

	// Ǽ�ʽ�ǡ����κ��
	if (!fncDeleteSlip($lngSlipNo, $objDB, $objAuth))	
	{
		fncOutputError ( 9051, DEF_FATAL, "���������ȼ��Ǽ�ʽ�ޥ�����������", TRUE, "", $objDB );
	}


	// Ǽ����ɼ���٤�ɳ�Ť�����ޥ����μ����ơ�������ּ���פ˹���
	if (!fncUpdateReceiveStatus($aryData["lngSlipNo"], $aryData["lngRevisionNo"], $objDB))
	{
		fncOutputError ( 9051, DEF_FATAL, "���������ȼ���������٥ơ��֥��������", TRUE, "", $objDB );
	}


	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����λ���̤�ɽ��
	$aryDeleteData = $aryHeadResult;
	$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	// ���쥳���ɡ����ܸ�
	$aryDeleteData["lngLanguageCode"] = 1;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

// *****************************************************
//   �����ǧ����ɽ����Submit����
// *****************************************************
// �����ǡ�����ɽ���Ѥ�����
$aryNewResult = fncSetSlipHeadTableData ( $aryHeadResult );

// �إå����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
$aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );

// �ܺ����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
$aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

// ��������ֹ��������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetSlipDetailNoToInfoSQL ( $aryData["lngSlipNo"], $aryData["lngRevisionNo"] );

// ���٥ǡ����μ���
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$arySalesDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 603, DEF_WARNING, "Ǽ����ɼ�ֹ���Ф������٤�¸�ߤ��ޤ���", FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($arySalesDetailResult); $i++)
{
	// ���٥ǡ�����ɽ���Ѥ˲ù�
	$aryNewDetailResult[$i] = fncSetSlipDetailTableData ( $arySalesDetailResult[$i], $aryNewResult );

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/result2/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDetailColumnNames_CN );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML����
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($arySalesDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/result2/parts2.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames_CN );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();
return true;

// ���顼���̤ؤ�����
function MoveToErrorPage($strMessage){
	
	// ���쥳���ɡ����ܸ�
	$aryHtml["lngLanguageCode"] = 1;

	// ���顼��å�����������
	$aryHtml["strErrorMessage"] = $strMessage;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	exit;
}

?>