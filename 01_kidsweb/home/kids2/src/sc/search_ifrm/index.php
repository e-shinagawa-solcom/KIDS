<?php
	// ----------------------------------------------------------------------------
	/**
	*       ������  �������ܲ��� ( Inline Frame )
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
	*         ���������ܲ���ɽ������ ( Inline Frame )
	*
	*       ��������
	*
	*/
	// ----------------------------------------------------------------------------



	// ������ɤ߹���
	include_once ( "conf.inc" );

	// �饤�֥���ɤ߹���
	require ( LIB_FILE );

	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// ʸ��������å�
	$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���¥����å�
	// 602 ����������帡����
	if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 603 ����������帡���������⡼�ɡ�
	if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
	{
		$aryData["AdminSet_visibility"] = "visible";
		// 607 ��������̵������
		if ( fncCheckAuthority( DEF_FUNCTION_SC7, $objAuth ) )
		{
			$aryData["btnInvalid_visibility"] = "visible";
			$aryData["btnInvalidVisible"] = "disabled";
		}
		else
		{
			$aryData["btnInvalid_visibility"] = "hidden";
			$aryData["btnInvalidVisible"] = "disabled";
		}
	}
	else
	{
		$aryData["AdminSet_visibility"] = "hidden";
		$aryData["btnInvalid_visibility"] = "hidden";
		$aryData["btnInvalidVisible"] = "";
	}
	// 604 �������ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_SC4, $objAuth ) )
	{
		$aryData["btnDetail_visibility"] = "visible";
		$aryData["btnDetailVisible"] = "checked";
	}
	else
	{
		$aryData["btnDetail_visibility"] = "hidden";
		$aryData["btnDetailVisible"] = "";
	}
	// 605 �������ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_SC5, $objAuth ) )
	{
		$aryData["btnFix_visibility"] = "visible";
		$aryData["btnFixVisible"] = "checked";
	}
	else
	{
		$aryData["btnFix_visibility"] = "hidden";
		$aryData["btnFixVisible"] = "";
	}
	// 606 �������ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_SC6, $objAuth ) )
	{
		$aryData["btnDelete_visibility"] = "visible";
		$aryData["btnDeleteVisible"] = "checked";
	}
	else
	{
		$aryData["btnDelete_visibility"] = "hidden";
		$aryData["btnDeleteVisible"] = "";
	}


	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// �ץ�������˥塼
	// 2004.04.14 suzukaze update start
	// ������
	//$aryData["lngSalesStatusCode"] 		= fncGetMultiplePulldown( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", DEF_SALES_DELIVER, '', $objDB );
	$aryData["lngSalesStatusCode"] 		= fncGetCheckBoxObject( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", "lngSalesStatusCode[]", 'where lngSalesStatusCode not in (1)', $objDB );
	// ����ե�����
	//$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );



	// 2004.04.14 suzukaze update end
	// ����ʬ
	$aryData["lngSalesClassCode"]		= fncGetPulldown( "m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", 1, '', $objDB );

	//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
	if ( !$aryData["lngSalesStatusCode"] or !$aryData["lngSalesClassCode"] )
	{
		fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
	}

	// ���å���������
	if( $_COOKIE["SalesSearch"] )
	{
		$aryCookie = fncStringToArray ( $_COOKIE["SalesSearch"], "&", ":" );
		while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
		{
			$aryData[$strKeys] = $strValues;
		}
	}

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/search_ifrm/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

?>
