<?php

// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ�ܺ�
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
*         ������Ǽ����ɼ�ֹ�ǡ����ξܺ�ɽ������
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
	require( LIB_DEBUGFILE );
	
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
	$aryCheck["lngSlipNo"]	  = "null:number(0,10)";

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	// 602 ����������帡����
	if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 604 �������ʾܺ�ɽ����
	if ( !fncCheckAuthority( DEF_FUNCTION_SC4, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//�ܺٲ��̤�ɽ��
	$lngSlipNo = $aryData["lngSlipNo"];

	// ����Ǽ����ɼ�ֹ��Ǽ�ʽ�ǡ���������SQLʸ�κ���
	$strQuery = fncGetSlipHeadNoToInfoSQL ( $lngSlipNo );

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
			fncOutputError( 603, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
	}
	else
	{
		fncOutputError( 603, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	$objDB->freeResult( $lngResultID );

	// �����ǡ�����ɽ���Ѥ�����
	$aryNewResult = fncSetSlipHeadTableData ( $aryResult );

//fncDebug('sc_result_index2.txt', $aryNewResult, __FILE__, __LINE__);

	// �إå����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
	$aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );
	// �ܺ����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
	$aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

	////////// ���ٹԤμ��� ////////////////////

	// ����Ǽ����ɼ�ֹ��������٥ǡ���������SQLʸ�κ���
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo );

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
		$strMessage = fncOutputError( 603, DEF_WARNING, "Ǽ����ɼ�ֹ���Ф������پ��󤬸��Ĥ���ޤ���", FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	$objDB->freeResult( $lngResultID );

	for ( $i = 0; $i < count($aryDetailResult); $i++)
	{
		$aryNewDetailResult[$i] = fncSetSlipDetailTableData ( $aryDetailResult[$i], $aryNewResult );

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

	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

	$aryNewResult["strAction"] = "index2.php";
	$aryNewResult["strMode"] = "detail";

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

?>