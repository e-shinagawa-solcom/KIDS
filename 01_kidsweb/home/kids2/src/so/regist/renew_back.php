<?php

// ----------------------------------------------------------------------------
/**
*       �������  ��������
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
*         ����������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	require(SRC_ROOT."pc/cmn/lib_pc.php");
	require(SRC_ROOT."so/cmn/lib_so.php");
	require(SRC_ROOT."so/cmn/lib_sos1.php");
	require(SRC_ROOT."so/cmn/column.php");
	require( LIB_DEBUGFILE );

	require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php'); // ���ʥޥ����桼�ƥ���ƥ�
	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData["lngReceiveNo"]    = $_REQUEST["lngReceiveNo"];
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	$strProcMode = $_POST["strProcMode"]; // �����⡼��


	//var_dump( $aryData["lngReceiveNo"] ); exit();

	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// �� ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;


	// 400 �������
	if( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 401 ��������ʼ�������
	if( !fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}



	// 408 ��������ʾ��ʥޥ��������쥯�Ƚ�����
	if( !fncCheckAuthority( DEF_FUNCTION_SO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	//-------------------------------------------------------------------------
	// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
	//-------------------------------------------------------------------------
	$strFncFlag = "SO";
	$blnCheck = fncCheckInChargeProduct( $aryData["lngReceiveNo"], $lngUserCode, $strFncFlag, $objDB );

	// �桼�������о����ʤ�°���Ƥ��ʤ����
	if( !$blnCheck )
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}



	//-------------------------------------------------------------------------
	// ���ּ���ǡ����פ�ͭ���������å���Ԥ�
	//-------------------------------------------------------------------------
	include "../statuscheck.php";

	if( !fncSoDataStatusCheck( $aryData["lngReceiveNo"], $objDB) )
	{
		return false;
	}


	//-------------------------------------------------------------------------
	// �� ���ϥ����å�
	//-------------------------------------------------------------------------
	if( $strProcMode == "check" )
	{
		// ���ٹԤ����
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each ( $_POST );
			if($strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}

		//-----------------------------------------------------------
		// �إå������ܥ����å�
		//-----------------------------------------------------------
		// ȯ��ͭ��������������å����ʤ�����Υե饰
		$aryData["renew"] = "true";


		//-----------------------------------------------------------
		// DB -> SELECT : m_Receive
		//-----------------------------------------------------------
		// ����No��Disabled�ʤΤ��ͤ��Ϥ�ʤ����ᡢ����No���������
		$strReceiveCodeQuery = "SELECT distinct strReceiveCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

		list ( $lngReceiveResultID, $lngReceiveResultNum ) = fncQuery( $strReceiveCodeQuery, $objDB );

		if( $lngReceiveResultNum == 1 )
		{
			$objReceiveResult = $objDB->fetchObject( $lngReceiveResultID, 0 );
			$strReceiveCode   = $objReceiveResult->strreceivecode;
		}
		else
		{
			fncOutputError ( 403, DEF_ERROR, "", TRUE, "", $objDB );
		}

		// ���ID�����
		$objDB->freeResult( $lngReceiveResultID );


		// �����ֹ�μ���
		$aryData["strReceiveCode"] = $strReceiveCode;

		list ( $aryData, $bytErrorFlag ) = fncCheckData_so( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag == "TRUE" ) ? 1 : 0;


		//-----------------------------------------------------------
		// ���ٹԤΥ����å�
		//-----------------------------------------------------------
		$aryQueryResult2 = $_POST["aryPoDitail"];

		for( $i = 0; $i < count( $aryQueryResult2 ); $i++ )
		{
			list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_so( $_POST["aryPoDitail"][$i], "detail", $objDB );
		}

		// ���ʥ����ɤ��տ魯�뾦�ʤ�¸�ߤ��뤫
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// ���ʥ�����
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );

			// ���ʥ����ɤ������Ǥ��ʤ��ä����
			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError( 303, "", "", FALSE, "", $objDB );
			}
			// ���ʥ����ɤ������Ǥ������
			else
			{
				$utilProduct = UtilProduct::getInstance();
				$product = $utilProduct->selectProductByProductCode($strProductCode);

				// �ܵҼ����ֹ椬���ꤵ��Ƥ��꾰��ĸܵ����֤����ꤵ��Ƥ��ʤ����
				// 0�����ꤹ���false�ˤʤ�Τ����
				if($_REQUEST["strCustomerReceiveCode"] && !$product["strgoodscode"])
				{
					$aryDetailErrorMessage[] = fncOutputError( 303, "", ": �ܵ�����̤����ξ��ʡ�", FALSE, "", $objDB );
				}
			}
		}

		// ���ٹԤΥ��顼�ؿ�
		$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );

		if( $strDetailErrorMessage != "" )
		{
			$aryDetailErrorMessage = $strDetailErrorMessage;
		}

		if( !is_array( $_POST["aryPoDitail"] ) )
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		//-------------------------------------------------
		// �ǿ���Х����ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
		//-------------------------------------------------
		$strCheckQuery = "SELECT lngReceiveStatusCode FROM m_Receive r WHERE r.strReceiveCode = '" . $aryData["strReceiveCode"] . "'";
		$strCheckQuery .= " AND r.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND r.lngRevisionNo = ( "
			. "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode )\n";
		$strCheckQuery .= " AND r.strReviseCode = ( "
			. "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode )\n";

		// �����å������꡼�μ¹�
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngReceiveStatusCode = $objResult->lngreceivestatuscode;

			if( $lngOrderStatusCode == DEF_ORDER_APPLICATE )
			{
				fncOutputError( 409, DEF_WARNING, "", TRUE, "../so/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngCheckResultID );


		//-----------------------------------------------------------
		// ���ϥ��顼
		//-----------------------------------------------------------
		if( $errorCount != 0  || is_array( $aryDetailErrorMessage ) )
		{
			// ���¥��롼�ץ����ɤμ���
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// ��ǧ�롼�Ȥ�����
			// �֥ޥ͡����㡼�װʾ�ξ��
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , '');
			}



			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode( " : ", $aryDetailErrorMessage);
			}

			if( is_array( $_POST["aryPoDitail"]) )
			{
				// ���ٹԤ�hidden�ͤ��Ѵ�����
				$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert", $objDB);
			}

			// ���顼����ä����
			// �ؿ� fncChangeDisplayName��ɽ���ѥǡ������Ѵ�(HEADER��
			$aryData = fncChangeData3( $aryData , $objDB );
			$aryData["strMonetaryUnitCodeDis"] = ( is_array( $_POST["aryPoDitail"] ) ) ? "disabled" : "";

			// ����
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			// �ץ�������˥塼������
			// �̲�
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// �졼�ȥ�����
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// ����ñ��
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
			// �ٻ�ñ��
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );
			// ����ʬ
			$aryData["lngSalesClassCode"]			= fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );



			$aryData["lngRegistConfirm"]           = 0;




			//-------------------------------------------------------------------------
			// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
			//-------------------------------------------------------------------------
			$aryData["lngReceiveStatusCode"] = fncCheckNullStatus( $aryData["lngReceiveStatusCode"] );

			//---------------------------------------------
			// �������(ɽ����)�μ���
			//---------------------------------------------
			$aryData["strReceiveStatusCodeDisplay"] = fncGetMasterValue( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", $aryData["lngReceiveStatusCode"], '', $objDB );



			$aryData["lngOrderCode"]                = $_GET["strOrderCode"];

			//$aryData["OrderSubmit"] = "fncOrderSubmit();";



			$aryData["RENEW"] = TRUE;

			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;

			$aryData["strPageCondition"] = "renew";


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����

			$objDB->close();

			// ���ID�����
			$objDB->freeResult( $lngResultID );

			echo fncGetReplacedHtml( "so/regist/parts.tmpl", $aryData ,$objAuth);
			return true;
		}
		//-----------------------------------------------------------
		// ��ǧ����ɽ��
		//-----------------------------------------------------------
		else
		{
			$aryData["strBodyOnload"] = "";

			// ���ٹԤ�hidden�ͤ��Ѵ�����
			$aryData["strProcMode"] = "renew"; // �⡼�ɡʼ���ư���check��insert
			$aryData["RENEW"]       = TRUE;

			// ����
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
			$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert", $objDB );

			// submit�ؿ�
			$aryData["lngRegistConfirm"] = 0;

			// ���������
			if( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
			{
				$aryTytle = $aryTableTytleEng;
			}
			else
			{
				$aryTytle = $aryTableTytle2;
			}

			// �����̾������
			$aryHeadColumnNames   = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
			$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail, $aryTytle );

			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;
				$_POST["aryPoDitail"][$i]["lngsalesclasscode_DIS"] = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $_POST["aryPoDitail"][$i]["lngSalesClassCode"], '', $objDB );
				$_POST["aryPoDitail"][$i]["lngproductunitcode_DIS"] = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );
				$_POST["aryPoDitail"][$i]["strproductcode_DIS"] = fncGetMasterValue( "m_product", "strproductcode", "strproductname", $_POST["aryPoDitail"][$i]["strProductCode"].":str", '', $objDB );

				// ���ٹ����ͤ��ü�ʸ���Ѵ�
				$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );

				$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
				$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
				$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
				$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";

				// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
				$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["lngsalesclasscode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["lngSalesClassCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["lngSalesClassCode"]."]" : "";

				// �ƥ�ץ졼���ɤ߹���
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "so/result/parts_detail2.tmpl" );

				// �ƥ�ץ졼������
				$objTemplate->replace( $aryDetailColumnNames );
				$objTemplate->replace( $_POST["aryPoDitail"][$i] );
				$objTemplate->complete();

				// HTML����
				$aryDetailTable[] = $objTemplate->strTemplate;
			}

			$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );
			$aryData["strMode"] = "regist";
			$aryData["strProcMode"] = "renew";

			// ����
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $_POST['lngInChargeGroupCode'] . ":str",'',$objDB);
			// ô����
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $_POST["lngInChargeUserCode"] . ":str",'',$objDB);
			// ��Ͽ��
			$aryData["dtminsertdate"] = $_POST["dtmInsertDate"];
			// ���ϼ�
			$UserDisplayName = "";
			$UserDisplayCode = "";
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserCode"] = $objAuth->UserDisplayName;


			$_POST["strMonetaryUnitName"] = ( $aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];

			$aryData["strMonetaryUnitName"] = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );


			//---------------------------------------------
			// ������֤μ���
			//---------------------------------------------
			//$aryData["strReceiveStatusCodeDisplay"] = fncGetMasterValue( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", $_POST["lngReceiveStatusCode"].":str", '', $objDB );


			// �졼�ȥ�����
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;

			$aryData["lngRegistConfirm"] = 0;
			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��

			// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
			$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
			$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
			//$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";


			//---------------------------------------------
			// ��ǧ�롼��
			//---------------------------------------------
			if ( $_POST["lngWorkflowOrderCode"] != "" and $_POST["lngWorkflowOrderCode"] != 0 )
			{
				$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $_POST["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB);

				$aryData["strWorkflowMessage_visibility"] = "block;";
			}
			else
			{
				$aryData["strWorkflowOrderName"] = "��ǧ�ʤ�";

				$aryData["strWorkflowMessage_visibility"] = "none;";
			}


			$aryData["RENEW"] = TRUE;

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

			$aryData["strActionURL"] = "/so/regist/index2.php?strSessionID=".$aryData["strSessionID"];



			//-------------------------------------------------------------------------
			// *v2* ���硦ô���Ԥμ���
			//-------------------------------------------------------------------------
			$strProductCode       = $_POST["aryPoDitail"][0]["strProductCode"];

			$lngInChargeGroupCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargegroupcode", $strProductCode . ":str", '', $objDB );
			$strInChargeGroupCode = fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode", $lngInChargeGroupCode . '', '', $objDB );
			$strInChargeGroupName = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $strInChargeGroupCode . ":str",'',$objDB );

			$lngInChargeUserCode  = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );
			$strInChargeUserCode  = fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $lngInChargeUserCode . '', '', $objDB );
			$strInChargeUserName  = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $strInChargeUserCode . ":str",'',$objDB );

			// ���祳���ɡ�̾��
			$aryData["strInChargeGroup"] = "[" . $strInChargeGroupCode . "] " . $strInChargeGroupName;
			// ô���ԥ����ɡ�̾��
			$aryData["strInChargeUser"]  = "[" . $strInChargeUserCode . "] " . $strInChargeUserName;
			//-------------------------------------------------------------------------



			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "so/confirm/parts.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryHeadColumnNames );
			$objTemplate->replace( $aryData );
			$objTemplate->complete();


			// HTML����
			echo $objTemplate->strTemplate;

			$objDB->close();

			return true;
		}
	}






	// ���¥��롼�ץ�����(�桼�����ʲ�)�����å�
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// �֥桼�����װʲ��ξ��
	if( $blnAG )
	{
		// ��ǧ�롼��¸�ߥ����å�
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// ��ǧ�롼�Ȥ�¸�ߤ��ʤ����
		if( !$blnWF )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}
	}



	//-------------------------------------------------------------------------
	// �� ���ɽ�� -> ���������
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "SELECT  ";
	$aryQuery[] = "lngreceiveno, ";												// 1:�����ֹ�
	$aryQuery[] = "lngrevisionno, ";											// 2:��ӥ�����ֹ�
	$aryQuery[] = "strreceivecode, ";											// 3:��������
	$aryQuery[] = "strrevisecode, ";											// 4:��Х���������

	$aryQuery[] = "To_char( dtmAppropriationDate, 'YYYY/mm/dd') as dtmOrderAppDate, ";		// 5:�׾���

	$aryQuery[] = "lngcustomercompanycode as lngCustomerCode, ";				// 6:��ҥ�����
	//$aryQuery[] = "lnggroupcode as lngInChargeGroupCode, ";						// 7:���롼�ץ�����
	//$aryQuery[] = "lngusercode as lngInChargeUserCode, ";						// 8:�桼����������
	$aryQuery[] = "lngreceivestatuscode as lngReceiveStatusCode, ";				// 9:������֥�����
	$aryQuery[] = "lngmonetaryunitcode as MonetaryUnitCode, ";					// 10:�̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode as lngMonetaryRateCode, ";				// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";										// 12:�����졼��
	$aryQuery[] = "curtotalprice, ";											// 13:��׶��
	$aryQuery[] = "strnote, ";													// 14:����
	$aryQuery[] = "lnginputusercode, ";											// 15:���ϼԥ�����
	$aryQuery[] = "bytinvalidflag, ";											// 16:̵���ե饰
	$aryQuery[] = "strcustomerreceivecode ";									// �ܵҼ����ֹ�
	$aryQuery[] = "FROM ";
	$aryQuery[] = "m_receive ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngreceiveno = " . $aryData["lngReceiveNo"];

	$strQuery = implode( "\n", $aryQuery );

	// �����꡼�¹�
	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );


	//var_dump( $aryData2["strcustomerreceivecode"] ); exit();



	//-------------------------------------------------------------------------
	// ������֤Υ����å�
	//-------------------------------------------------------------------------
	// ������ξ��
	if( $aryData2["lngReceiveStatusCode"] == DEF_ORDER_APPLICATE )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
	//-------------------------------------------------------------------------
	$aryData2["lngReceiveStatusCode"] = fncCheckNullStatus( $aryData2["lngReceiveStatusCode"] );


//var_dump( $aryData2["lngReceiveStatusCode"] ); exit();

	$aryData2 = fncChangeData2( $aryData2, $objDB );
	$aryData2["strnote"] = fncHTMLSpecialChars( $aryData2["strnote"] );


	$aryData = array_merge( $aryData, $aryData2 );




	// �ץ�������˥塼������
	// �̲�
	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode","strmonetaryunitsign", $aryData2["monetaryunitcode"], '', $objDB );
	$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );
	// �졼�ȥ�����
	$aryData["lngmonetaryratecode"]			= fncPulldownMenu( 1, $aryData2["lngmonetaryratecode"], '', $objDB );
	// ����ñ��
	$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
	// �ٻ�ñ��
	$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );
	// ����ʬ
	$aryData["lngSalesClassCode"]			= fncPulldownMenu( 10, $aryData2["lngsalesclasscode"], '', $objDB );
	// ����



	// ���ٹ�
	$aryQuery = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngreceiveno, ";													// 1:�����ֹ�

	$aryQuery[] = "lngreceivedetailno as lngorderdetailno, ";						// 2:���������ֹ� JavaScript̾�Τδ�Ϣ��lngOrderDetailNo�����

	$aryQuery[] = "lngrevisionno, ";												// 3:��ӥ�����ֹ�
	$aryQuery[] = "strproductcode, ";												// 4:���ʥ�����
	$aryQuery[] = "lngsalesclasscode, ";											// 5:����ʬ������
	$aryQuery[] = "To_char( dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate, ";	// 6:Ǽ����
	$aryQuery[] = "lngconversionclasscode, ";										// 7:������ʬ������
	$aryQuery[] = "curproductprice, ";												// 8:���ʲ���
	$aryQuery[] = "lngproductquantity, ";											// 9:���ʿ���
	$aryQuery[] = "lngproductunitcode, ";											// 10:����ñ�̥�����
	$aryQuery[] = "cursubtotalprice, ";												// 14:���׶��
	$aryQuery[] = "strnote ";														// 15:����
	$aryQuery[] = "FROM t_receivedetail";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngreceiveno = " . $aryData["lngReceiveNo"];
	$aryQuery[] = " ORDER BY lngSortKey ASC";


	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );

	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	if( !$lngResultNum = pg_num_rows( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	else
	{
		for( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryQueryResult[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}



	// ���ٹԤ�hidden�ͤ��Ѵ�����
	$aryData["strDetailHidden"] = fncDetailHidden_so( $aryQueryResult ,"", $objDB);
	$aryData["strProcMode"] = "check";
	$aryData["strSessionID"] = $aryData["strSessionID"];


	$aryData["strActionUrl"] = "renew.php";			// form��action

	$aryData["curConversionRate"] = "1.000000";



	//---------------------------------------------------------------
	// ������֤μ���
	//---------------------------------------------------------------
	$aryData["strReceiveStatusCodeDisplay"] = fncGetMasterValue( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", $aryData["lngReceiveStatusCode"], '', $objDB );


	// �졼�ȥ�����
	$aryData["strMonetaryUnitCodeDis"] = ( is_array( $aryQueryResult ) ) ? "disabled" : "";
	$aryData["RENEW"] = TRUE;


	$aryData["strReceiveCodeDis"] = "disabled";

	$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;

	$aryData["strPageCondition"] = "renew";



	// ��ǧ�롼�Ȥμ���
	$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryData["lngReceiveNo"].":str", '', $objDB );


	// ���¥��롼�ץ����ɤμ���
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// ��ǧ�롼�Ȥ�����
	// �֥ޥ͡����㡼�װʾ�ξ��
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
	}
	else
	{
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );
	}




	// �ܵҼ����ֹ�μ���
	$aryData["strCustomerReceiveCode"] = $aryData["strcustomerreceivecode"];

	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����

	$objDB->close();

	$objDB->freeResult( $lngResultID );

	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_SO5;

	echo fncGetReplacedHtml( "/so/regist/parts.tmpl", $aryData ,$objAuth );

	return true;

?>
