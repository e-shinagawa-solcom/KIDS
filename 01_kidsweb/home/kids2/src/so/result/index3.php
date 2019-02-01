<?php

// ----------------------------------------------------------------------------
/**
*       �������  ���
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

// ������桼���������ɤμ���
$lngInputUserCode = $objAuth->UserCode;


// ���³�ǧ
// 502 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}
// 506 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}



//-------------------------------------------------------------------------
// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
//-------------------------------------------------------------------------
$strFncFlag = "SO";
$blnCheck = fncCheckInChargeProduct( $aryData["lngReceiveNo"], $lngInputUserCode, $strFncFlag, $objDB );

// �桼�������о����ʤ�°���Ƥ��ʤ����
if( !$blnCheck )
{
	fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
}




// ����оݤμ���NO�μ���������
$strQuery = fncGetReceiveHeadNoToInfoSQL ( $aryData["lngReceiveNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 503, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// �����ǧ���� ////////////////////
////////////////////////////////////////////////////////
// ���ǡ����γ�ǧ
$strReceiveCode = $aryReceiveResult["strreceivecode2"];
$aryCode = fncGetDeleteCodeToMaster ( $strReceiveCode, 1, $objDB );
if ( $aryCode )
{
	$lngSalesCount = count($aryCode);
}
else
{
	$lngSalesCount = 0;
}







////////////////////////////////////////////////////////
////////////////////// ��������¹� ////////////////////
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

// 2004.03.24 suzukaze update start
	// Ʊ������No����ꤷ�Ƥ���ǡ������å�����
	$strQuery = "SELECT strReceiveCode FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "' FOR UPDATE ";
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 403, DEF_FATAL, "����оݼ���ǡ����Υ�å������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
// 2004.03.24 suzukaze update end

	// m_receive�Υ������󥹤����
	$sequence_m_receive = fncGetSequence( 'm_Receive.lngReceiveNo', $objDB );

	// �Ǿ���ӥ�����ֹ�μ���
	$strReceiveCode = $aryReceiveResult["strreceivecode2"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "'";
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
	$strReviseGetQuery = "SELECT MAX(strReviseCode) as maxrevise FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "' AND bytInvalidFlag = FALSE";

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

	$aryQuery[] = "INSERT INTO m_receive (lngReceiveNo, lngRevisionNo, strReviseCode, ";	// ����NO����ӥ�����ֹ桢��Х���������
	$aryQuery[] = "strReceiveCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// �������ɡ����ϼԥ����ɡ�̵���ե饰����Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_receive . ", ";		// 1:�����ֹ�
	$aryQuery[] = $lngMinRevisionNo . ", ";			// 2:��ӥ�����ֹ�
	$aryQuery[] = "'" . $strMaxReviseCode . "', ";	// 3:��Х���������
	$aryQuery[] = "'" . $strReceiveCode . "', ";	// 4:�������ɡ�
	$aryQuery[] = $objAuth->UserCode . ", ";		// 5:���ϼԥ�����
	$aryQuery[] = "false, ";						// 6:̵���ե饰
	$aryQuery[] = "now()";							// 7:��Ͽ��
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9051, DEF_FATAL, "���������ȼ���ޥ�����������", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

// 2004.03.24 suzukaze update start
	// Ʊ������No����ꤷ�Ƥ���ǡ����μ������ɤ� define �ˤƻ��ꤷ�Ƥ���ʸ������Ϳ���ƺ����ѤǤ���褦�˹�������
	$strNewReceiveCode = DEF_RECEIVE_DEL_START . $strReceiveCode . DEF_RECEIVE_DEL_END;
	$strQuery = "UPDATE m_Receive SET strReceiveCode = '" . $strNewReceiveCode . "' WHERE strReceiveCode = '" . $strReceiveCode . "'";
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9051, DEF_FATAL, "���������ȼ������No����̾�����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
// 2004.03.24 suzukaze update end

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $aryReceiveResult;
	$aryDeleteData["strAction"] = "/so/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/finish/remove_parts.tmpl" );

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
//////////////////// �����ǧ����ɽ�� //////////////////
////////////////////////////////////////////////////////
// ��������ξ��֤��ֿ�����פξ��֤Ǥ����
if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_APPLICATE )
{
	fncOutputError( 406, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// ��������ξ��֤�������ѡפξ��֤Ǥ����
if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
{
	fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}



// �����ǡ�����Ĵ��
// �졼�ȥ�����
if ( $aryReceiveResult["lngmonetaryratecode"] and $aryReceiveResult["lngmonetaryratecode"] != DEF_MONETARY_YEN )
{
	$aryReceiveResult["strmonetaryratename"] = fncGetMasterValue(m_monetaryrateclass, lngMonetaryRateCode, strMonetaryRateName, $aryReceiveResult["lngmonetaryratecode"], '', $objDB);
}

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
	$strMessage = fncOutputError( 503, DEF_WARNING, "�����ֹ���Ф������٤�¸�ߤ��ޤ���", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

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