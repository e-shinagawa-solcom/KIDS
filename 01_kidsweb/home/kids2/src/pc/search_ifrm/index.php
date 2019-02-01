<?php

	// ----------------------------------------------------------------------------
	/**
	*       ��������  �������ܲ��� ( Inline Frame )
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
	require(SRC_ROOT."pc/cmn/lib_pc.php");

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
	// 702 ���������ʻ���������
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 703 ���������ʻ��������������⡼�ɡ�
	if ( fncCheckAuthority( DEF_FUNCTION_PC3, $objAuth ) )
	{
		$aryData["AdminSet_visibility"] = "visible";
		// 707 ����������̵������
		if ( fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) )
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
	// 704 ���������ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_PC4, $objAuth ) )
	{
		$aryData["btnDetail_visibility"] = "visible";
		$aryData["btnDetailVisible"] = "checked";
	}
	else
	{
		$aryData["btnDetail_visibility"] = "hidden";
		$aryData["btnDetailVisible"] = "";
	}
	// 705 ���������ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_PC5, $objAuth ) )
	{
		$aryData["btnFix_visibility"] = "visible";
		$aryData["btnFixVisible"] = "checked";
	}
	else
	{
		$aryData["btnFix_visibility"] = "hidden";
		$aryData["btnFixVisible"] = "";
	}
	// 706 ���������ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_PC6, $objAuth ) )
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
	// ��������
	//$aryData["lngStockStatusCode"] 		= fncGetMultiplePulldown( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", DEF_STOCK_DELIVER, '', $objDB );
	$aryData["lngStockStatusCode"] 		= fncGetCheckBoxObject( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", "lngStockStatusCode[]", 'where lngStockStatusCode not in (1)', $objDB );
	// ����ե�����
//	$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );



	// 2004.04.14 suzukaze update start
	// ��ʧ���
	$aryData["lngPayConditionCode"] 	= fncGetPulldown( "m_paycondition", "lngpayconditioncode", "strpayconditionname", 0, '', $objDB );
	// ��������
	$aryData["lngStockSubjectCode"]		= fncGetPulldown( "m_stocksubject", "lngstocksubjectcode", "lngstocksubjectcode, strstocksubjectname", 1, '', $objDB );
	// ��������
	$aryData["lngStockItemCode"] 		= fncGetPulldown( "m_stockitem", "lngstocksubjectcode || '-' || lngstockitemcode", "lngstockitemcode, strstockitemname", 0, '', $objDB );
	// ������ˡ
	$aryData["lngDeliveryMethodCode"] 	= fncGetPulldown( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", 0, '', $objDB );

	//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
	if ( !$aryData["lngStockStatusCode"] or !$aryData["lngPayConditionCode"] or !$aryData["lngStockSubjectCode"] or !$aryData["lngStockItemCode"] )
	{
		fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
	}

	// ���å���������
	if( $_COOKIE["PurchaseControlSearch"] )
	{
		$aryCookie = fncStringToArray ( $_COOKIE["PurchaseControlSearch"], "&", ":" );
		while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
		{
			$aryData[$strKeys] = $strValues;
		}
	}

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/search_ifrm/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

?>
