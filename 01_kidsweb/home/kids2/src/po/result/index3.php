<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ���
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
*         ������ȯ���ֹ�ǡ����κ������
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
if ( !isset($aryData["lngOrderNo"]) )
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

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;


// ���³�ǧ
// 502 ȯ�������ȯ������
if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 506 ȯ�������ȯ������
if ( !fncCheckAuthority( DEF_FUNCTION_PO6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}



//-------------------------------------------------------------------------
// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
//-------------------------------------------------------------------------
$strFncFlag = "PO";
$blnCheck = fncCheckInChargeProduct( $aryData["lngOrderNo"], $lngInputUserCode, $strFncFlag, $objDB );

// �桼�������о����ʤ�°���Ƥ��ʤ����
if( !$blnCheck )
{
	fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
}



// ����оݤ�ȯ��NO��ȯ��������
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
////////////////////// �����ǧ���� ////////////////////
////////////////////////////////////////////////////////
// �����ǡ����γ�ǧ
$strOrderCode = $aryOrderResult["strrealordercode"];
$aryCode = fncGetDeleteCodeToMaster ( $strOrderCode, 1, $objDB );
if ( $aryCode )
{
	$lngStockCount = count($aryCode);
}
else
{
	$lngStockCount = 0;
}

////////////////////////////////////////////////////////
////////////////////// ��������¹� ////////////////////
////////////////////////////////////////////////////////
// ����ȯ��Σ����ꤷ�Ƥ�������ǡ�����¸�ߤ��ʤ����
if ( $aryData["strSubmit"] == "submit" and $lngStockCount == 0 )
{
	// ����ȯ��ξ��֤��ֿ�����ס�����ѡפξ��֤Ǥ����
	if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
	{
		fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// m_order�Υ������󥹤����
	$sequence_m_order = fncGetSequence( 'm_Order.lngOrderNo', $objDB );

	// �Ǿ���ӥ�����ֹ�μ���
	$strOrderCode = $aryOrderResult["strrealordercode"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Order WHERE strOrderCode = '" . $strOrderCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	$lngMinRevisionNo--;

	// �����Х��������ɤμ���
	$strReviseGetQuery = "SELECT MAX(strReviseCode) as maxrevise FROM m_Order WHERE strOrderCode = '" . $strOrderCode . "' AND bytInvalidFlag = FALSE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strReviseGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$strMaxReviseCode = $objResult->maxrevise;
	}
	else
	{
		$strMaxReviseCode = "00";
	}
	$objDB->freeResult( $lngResultID );

	$aryQuery[] = "INSERT INTO m_order (lngOrderNo, lngRevisionNo, strReviseCode, ";	// ȯ��NO����ӥ�����ֹ桢��Х����ֹ�
	$aryQuery[] = "strOrderCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// ȯ�����ɡ����ϼԥ����ɡ�̵���ե饰����Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_order . ", ";		// 1:ȯ���ֹ�
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:��ӥ�����ֹ�
	$aryQuery[] = "'" . $strMaxReviseCode . "', ";	// 3:��Х���������
	$aryQuery[] = "'" . $strOrderCode . "', ";	// 4:ȯ�����ɡ�
	$aryQuery[] = $objAuth->UserCode . ", ";	// 5:���ϼԥ�����
	$aryQuery[] = "false, ";					// 6:̵���ե饰
	$aryQuery[] = "now()";						// 7:��Ͽ��
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 502, DEF_FATAL, "���������ȼ���ޥ�����������", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryOrderResult;
	$aryDeleteData["strAction"] = "/po/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/finish/remove_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
////////////////////// ����Ǥ��ʤ� ////////////////////
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
//////////////////// �����ǧ����ɽ�� //////////////////
////////////////////////////////////////////////////////
// ����ȯ��ξ��֤��ֿ�����ס�����ѡפξ��֤Ǥ����
if ( $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryOrderResult["lngorderstatuscode"] == DEF_ORDER_CLOSED )
{
	fncOutputError( 505, DEF_WARNING, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// �����ǡ�����Ĵ��
// �졼�ȥ�����
if ( $aryOrderResult["lngmonetaryratecode"] and $aryOrderResult["lngmonetaryratecode"] != DEF_MONETARY_YEN )
{
	$aryOrderResult["strmonetaryratename"] = fncGetMasterValue(m_monetaryrateclass, lngMonetaryRateCode, strMonetaryRateName, $aryOrderResult["lngmonetaryratecode"], '', $objDB);
}

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

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

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