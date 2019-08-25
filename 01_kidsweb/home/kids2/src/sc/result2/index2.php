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
	require (SRC_ROOT . "sc/cmn/column.php");
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

	// ����Ǽ����ɼ�ֹ�����ǡ���������SQLʸ�κ���
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

	// �����ǡ�����Ĵ��
	$aryNewResult = fncSetSlipHeadTableData ( $aryResult );

//fncDebug('sc_result_index2.txt', $aryNewResult, __FILE__, __LINE__);

	// ���������
	if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
	{
		$aryTytle = $aryTableTytleEng;
	}
	else
	{
		$aryTytle = $aryTableTytle;
	}

	// �إå����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
	$aryHeadColumnNames = fncSetSlipTableColumnName ( $aryTableViewHead, $aryTytle );
	// �ܺ����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
	$aryDetailColumnNames = fncSetSlipTableColumnName ( $aryTableViewDetail, $aryTytle );

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
		$aryQuery[] = "		WHERE mp1.strproductcode = '" . $aryDetailResult[$i]["strproductcode"] . "'";
		$aryQuery[] = "	)";
		$aryQuery[] = "	AND mu.lngusercode =";
		$aryQuery[] = "	(";
		$aryQuery[] = "		SELECT mp2.lnginchargeusercode";
		$aryQuery[] = "		FROM m_product mp2";
		$aryQuery[] = "		WHERE mp2.strproductcode = '" . $aryDetailResult[$i]["strproductcode"] . "'";
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

	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

	$aryNewResult["strAction"] = "index2.php";
	$aryNewResult["strMode"] = "detail";

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