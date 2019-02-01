<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ���
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
*         ����������ֹ�ǡ����κ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



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
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
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
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 706 ���������ʻ��������
if ( !fncCheckAuthority( DEF_FUNCTION_PC6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ����оݤλ���NO�λ����������
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
////////////////////// ��������¹� ////////////////////
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

	// m_stock�Υ������󥹤����
	$sequence_m_stock = fncGetSequence( 'm_Stock.lngStockNo', $objDB );

	// �Ǿ���ӥ�����ֹ�μ���
	$strStockCode = $aryStockResult["strstockcode"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Stock WHERE strStockCode = '" . $strStockCode . "'";
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

	$aryQuery[] = "INSERT INTO m_stock (lngStockNo, lngRevisionNo, ";					// ����NO����ӥ�����ֹ�
	$aryQuery[] = "strStockCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// ���������ɡ����ϼԥ����ɡ�̵���ե饰����Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_stock . ", ";		// 1:�����ֹ�
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:��ӥ�����ֹ�
	$aryQuery[] = "'" . $strStockCode . "', ";	// 3:���������ɡ�
	$aryQuery[] = $objAuth->UserCode . ", ";	// 4:���ϼԥ�����
	$aryQuery[] = "false, ";					// 5:̵���ե饰
	$aryQuery[] = "now()";						// 6:��Ͽ��
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 702, DEF_FATAL, "���������ȼ���ޥ�����������", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

// 2004.03.10 suzukaze update start
	// ������������ˤ������ѹ��ؿ��ƤӽФ�
	if ( fncStockDeleteSetStatus( $aryStockResult, $objDB ) != 0 )
	{
		fncOutputError( 9051, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
// 2004.03.10 suzukaze update end

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryStockResult;
	$aryDeleteData["strAction"] = "/pc/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/finish/remove_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
//////////////////// �����ǧ����ɽ�� //////////////////
////////////////////////////////////////////////////////
// ���������ξ��֤�������ѡפξ��֤Ǥ����
if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_APPLICATE )
{
	fncOutputError( 712, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// ���������ξ��֤�������ѡפξ��֤Ǥ����
if ( $aryStockResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
{
	fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

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

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryStockDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 703, DEF_WARNING, "�����ֹ���Ф������٤�¸�ߤ��ޤ���", FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

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