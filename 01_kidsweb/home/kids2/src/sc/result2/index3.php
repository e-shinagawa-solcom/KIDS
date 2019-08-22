<?php

// ----------------------------------------------------------------------------
/**
*       ������  ���
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
require (SRC_ROOT . "sc/cmn/lib_scd.php");
require (SRC_ROOT . "sc/cmn/lib_scd1.php");
require (SRC_ROOT . "sc/cmn/column.php");
require (LIB_DEBUGFILE);

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
if ( !$aryData["lngSalesNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngSalesNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

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

// ����оݤ����NO�����������
$strQuery = fncGetSalesHeadNoToInfoSQL ( $aryData["lngSalesNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$arySalesResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 603, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// ��������¹� ////////////////////
////////////////////////////////////////////////////////
if( $aryData["strSubmit"] )
{
	// �������ξ��֤�������ѡפξ��֤Ǥ����
	if ( $arySalesResult["lngsalesstatuscode"] == DEF_SALES_CLOSED )
	{
		fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// m_sales�Υ������󥹤����
	$sequence_m_sales = fncGetSequence( 'm_Sales.lngSalesNo', $objDB );

	// �Ǿ���ӥ�����ֹ�μ���
	$strSalesCode = $arySalesResult["strsalescode"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Sales WHERE strSalesCode = '" . $strSalesCode . "'";
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

	$aryQuery[] = "INSERT INTO m_sales (lngSalesNo, lngRevisionNo, ";					// ���NO����ӥ�����ֹ�
	$aryQuery[] = "strSalesCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// ��女���ɡ����ϼԥ����ɡ�̵���ե饰����Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_sales . ", ";		// 1:����ֹ�
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:��ӥ�����ֹ�
	$aryQuery[] = "'" . $strSalesCode . "', ";	// 3:��女���ɡ�
	$aryQuery[] = $objAuth->UserCode . ", ";	// 4:���ϼԥ�����
	$aryQuery[] = "false, ";					// 5:̵���ե饰
	$aryQuery[] = "now()";						// 6:��Ͽ��
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 602, DEF_FATAL, "���������ȼ���ޥ�����������", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

// 2004.03.10 suzukaze update start
	// ����������ˤ������ѹ��ؿ��ƤӽФ�
	if ( fncSalesDeleteSetStatus( $arySalesResult, $objDB ) != 0 )
	{
		fncOutputError( 9051, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
// 2004.03.10 suzukaze update end

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// �����ǧ���̤�ɽ��
	$aryDeleteData = $arySalesResult;
	$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish/remove_parts.tmpl" );

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
// �������ξ��֤��ֿ�����פξ��֤Ǥ����
if ( $arySalesResult["lngsalesstatuscode"] == DEF_SALES_APPLICATE )
{
	fncOutputError( 608, DEF_WARNING, "", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// �������ξ��֤�������ѡפξ��֤Ǥ����
if ( $arySalesResult["lngsalesstatuscode"] == DEF_SALES_CLOSED )
{
	fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$aryNewResult = fncSetSalesHeadTabelData ( $arySalesResult );

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
$aryHeadColumnNames = fncSetSalesTabelName ( $aryTableViewHead, $aryTytle );
// �����̾������
$aryDetailColumnNames = fncSetSalesTabelName ( $aryTableViewDetail, $aryTytle );

////////// ���ٹԤμ��� ////////////////////

// ��������ֹ��������٥ǡ���������SQLʸ�κ���
$strQuery = fncGetSalesDetailNoToInfoSQL ( $aryData["lngSalesNo"] );

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
	$strMessage = fncOutputError( 603, DEF_WARNING, "����ֹ���Ф������٤�¸�ߤ��ޤ���", FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($arySalesDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetSalesDetailTabelData ( $arySalesDetailResult[$i], $aryNewResult );

	//-------------------------------------------------------------------------
	// *v2* ���硦ô���Ԥμ���
	//-------------------------------------------------------------------------
	$aryQuery   = array();
	$aryQuery[] = "SELECT DISTINCT";
	$aryQuery[] = "	mg.strgroupdisplaycode";
	$aryQuery[] = "	,mg.strgroupdisplayname";
	$aryQuery[] = "	,mu.struserdisplaycode";
	$aryQuery[] = "	,mu.struserdisplayname";
	$aryQuery[] = "FROM";
	$aryQuery[] = "	m_group mg";
	$aryQuery[] = "	,m_user mu";
	$aryQuery[] = "WHERE";
	$aryQuery[] = "	mg.lnggroupcode =";
	$aryQuery[] = "	(";
	$aryQuery[] = "		SELECT mp1.lnginchargegroupcode";
	$aryQuery[] = "		FROM m_product mp1";
	$aryQuery[] = "		WHERE mp1.strproductcode = '" . $arySalesDetailResult[$i]["strproductcode"] . "'";
	$aryQuery[] = "	)";
	$aryQuery[] = "	AND mu.lngusercode =";
	$aryQuery[] = "	(";
	$aryQuery[] = "		SELECT mp2.lnginchargeusercode";
	$aryQuery[] = "		FROM m_product mp2";
	$aryQuery[] = "		WHERE mp2.strproductcode = '" . $arySalesDetailResult[$i]["strproductcode"] . "'";
	$aryQuery[] = "	)";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );


	// �����꡼�¹�
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// ���祳���ɡ�̾��
		$aryNewDetailResult[$i]["strInChargeGroup"] = "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupdisplayname;
		// ô���ԥ����ɡ�̾��
		$aryNewDetailResult[$i]["strInChargeUser"]  = "[" . $objResult->struserdisplaycode . "] " . $objResult->struserdisplayname;
	}
	else
	{
		fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	//-------------------------------------------------------------------------


	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/result2/parts_detail.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDetailColumnNames );
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
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>