<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��������
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



	// �ɤ߹���
	include('conf.inc');
	require(LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	require(SRC_ROOT."po/cmn/lib_pop.php");
	require(SRC_ROOT."po/cmn/lib_pos1.php");
	require(SRC_ROOT."po/cmn/column.php");


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngOrderNo"]      = $_REQUEST["lngOrderNo"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	

	$objDB->open("", "", "", "");
	
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngUserCode = $objAuth->UserCode;
	
	
	// 500	ȯ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	
	// 505 ȯ�������ȯ������
	if ( !fncCheckAuthority( DEF_FUNCTION_PO5, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}



	// 508 ȯ������ʾ��ʥޥ��������쥯�Ƚ�����
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}




	//-------------------------------------------------------------------------
	// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
	//-------------------------------------------------------------------------
	$strFncFlag = "PO";
	$blnCheck = fncCheckInChargeProduct( $aryData["lngOrderNo"], $lngUserCode, $strFncFlag, $objDB );

	// �桼�������о����ʤ�°���Ƥ��ʤ����
	if( !$blnCheck )
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}




// 2005.07.14 K.Saito update start
	//
	// ��ȯ��ǡ����פ�ͭ���������å���Ԥ�
	//
	include "../statuscheck.php";
	if( !fncPoDataStatusCheck( $aryData["lngOrderNo"], $objDB) )
	{
		return false;
	}
// 2005.07.14 K.Saito update end


	// check
	if( $_POST["strMode"] == "check" || $_POST["strMode"] == "renew" )
	{
		// ���ٹԤ����
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $_POST );

			if( $strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}
	
		// header�ι��ܥ����å�
		list ( $aryData, $bytErrorFlag ) = fncCheckData_po( $aryData,"header", $objDB );
		$errorCount = ( $bytErrorFlag != "") ? 1 : 0;
		
		// 2004/03/15 watanabe update start
		// ���ʥ����ɤ��տ魯�뾦�ʤ�¸�ߤ��뤫
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// ���ʥ����ɣ��������������б�
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );
			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 303, "", "", FALSE, "", $objDB );
			}
		}
		// watanabe end
		
		
		// ���ٹԤΥ����å�
		if( is_array( $_POST["aryPoDitail"] ))
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_po( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}
		}


		$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );
			
		if( $strDetailErrorMessage != "")
		{
			$aryDetailErrorMessage[] =  $strDetailErrorMessage;
		}
		
		
		// ���ٹԤΥ��顼�ؿ�
		if( !is_array( $_POST["aryPoDitail"]))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}

		// ���ٹԤΥǡ������Ф��ơ����ʥ����ɤ��㤦�ǡ�����¸�ߤ��ʤ����ɤ����Υ����å�
		$bytCheck = fncCheckOrderDetailProductCode ( $_POST["aryPoDitail"], $objDB );
		if ( $bytCheck == 99 )
		{
			$aryDetailErrorMessage[] = fncOutputError( 506, "", "", FALSE, "", $objDB );
		}

		// ��ǧ���̤�ɽ������ݤ˺ǿ���Х�����ȯ���ֿ�����פˤʤäƤ��ʤ����ɤ����γ�ǧ��Ԥ�
		$strCheckQuery = "SELECT lngOrderStatusCode FROM m_Order o WHERE o.strOrderCode = '" . $aryData["strOrderCode"] . "'";
		$strCheckQuery .= " AND o.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND o.lngRevisionNo = ( "
			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode )\n";
		$strCheckQuery .= " AND o.strReviseCode = ( "
			. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode )\n";

		// �����å������꡼�μ¹�
		list ( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngOrderStatusCode = $objResult->lngorderstatuscode;

			if ( $lngOrderStatusCode == DEF_ORDER_APPLICATE )
			{
				fncOutputError ( 505, DEF_WARNING, "", TRUE, "../po/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		$objDB->freeResult( $lngCheckResultID );




		
		// ����������������
		
		$aryData = fncChangeData3($aryData, $objDB);


		// �̲ߤ�disable�ˤ���
		if( is_array( $_POST["aryPoDitail"] ))
		{
			$aryData["strMonetaryDisable"] = "disabled";
			// ���ٹԤ�hidden�ͤ��Ѵ�����
			$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"], "insert" ,$objDB );
		}
			
		//�إå����ͤ��ü�ʸ���Ѵ�
		$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
		



		// ���顼����ä���� ==================================
		if( $errorCount != 0 || is_array( $aryDetailErrorMessage ))
		{

			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}



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



			// �ץ�������˥塼������
			// �̲�
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// �졼�ȥ�����
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// ��ʧ���
			$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// ��������
			$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, $aryData["strStockSubjectCode"], '', $objDB );

			// ������ˡ
			$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, $aryData["lngCarrierCode"], '', $objDB );
			// ����ñ��
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, $aryData["lngProductUnitCode"], '', $objDB );
			// �ٻ�ñ��
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, $aryData["lngPackingUnitCode"], '', $objDB );




			//-------------------------------------------------------------------------
			// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
			//-------------------------------------------------------------------------
			$aryData["lngOrderStatusCode"] = fncCheckNullStatus( $aryData["lngOrderStatusCode"] );

			//-------------------------------------------------------------------------
			// ȯ�����(ɽ����)�μ���
			//-------------------------------------------------------------------------
			$aryData["lngOrderStatusCode_Display"]	= ( $aryData["lngorderstatuscode"] != "" ) ? fncGetMasterValue("m_orderstatus", "lngorderstatuscode", "strorderstatusname", $aryData["lngOrderStatusCode"],'', $objDB ) : "" ;



			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
				//$aryData["PayConditionDisabled"] = " disabled";
			}
			
			$aryData["strMode"] = "check";
			$aryData["RENEW"] = TRUE;
			
// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
// 2004.04.08 suzukaze update end

// 2004.04.19 suzukaze update start
			$aryData["strPageCondition"] = "renew";
// 2004.04.19 suzukaze update end

			// submit�ؿ�
			$aryData["lngRegistConfirm"] = 0;

			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����

			echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryData ,$objAuth );
			
			$objDB->close();

			return true;
			
		}
		else
		{
			$aryData["strProcMode"] = "renew";
			$aryData["RENEW"] = TRUE;
			
			$aryData["strBodyOnload"] = "";
			
			// submit�ؿ�
			$aryData["lngRegistConfirm"] = 0;

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
			$aryDetailColumnNames = fncSetPurchaseTabelName( $aryTableViewDetail, $aryTytle );

			for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
			
				$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;
				
				// ��������
				$_POST["aryPoDitail"][$i]["strStockSubjectName"] = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname",  $_POST["aryPoDitail"][$i]["strStockSubjectCode"], '', $objDB );
				// �������� 
				$_POST["aryPoDitail"][$i]["strStockItemName"] = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryPoDitail"][$i]["strStockItemCode"], "lngstocksubjectcode = ".$_POST["aryPoDitail"][$i]["strStockSubjectCode"], $objDB );
				
				// �ܵ�����
				$_POST["aryPoDitail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
				
				// ������ˡ
				$_POST["aryPoDitail"][$i]["strCarrierName"] = fncGetMasterValue( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryPoDitail"][$i]["lngCarrierCode"],'', $objDB );
				// ñ��
				$_POST["aryPoDitail"][$i]["strProductUnitName"] = fncGetMasterValue("m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

				// ���ٹ����ͤ��ü�ʸ���Ѵ�
				$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );
				
				//2004/03/17 watanabe update start
				$strProductName = "";
				if( $strProductName = fncGetMasterValue( "m_product", "strproductcode", "strproductname",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB ) )
				{
					$_POST["aryPoDitail"][$i]["strproductname"] = $strProductName;
				}
				// watanabe end
				
				// 2004/03/11 number_format watanabe
				$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
				$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
				$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
				$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";
				// watanabe update end
				
				// 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
				$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["strstockitemcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockItemCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["strstocksubjectcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockSubjectCode"]."]" : "";
				
				// �ƥ�ץ졼���ɤ߹���
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "po/result/parts_detail2.tmpl" );
				
				// �ƥ�ץ졼������
				$objTemplate->replace( $aryDetailColumnNames );
				$objTemplate->replace( $_POST["aryPoDitail"][$i] );
				$objTemplate->complete();
				
				// HTML����
				$aryDetailTable[] = $objTemplate->strTemplate;
			}
			// exit();
			
			
			$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );
			

			$aryData["strMode"] = "regist";
			$aryData["strProcMode"] = "renew";
			// ����
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $_POST['lngInChargeGroupCode'] . ":str",'',$objDB);
			// ô����
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $_POST["lngInChargeUserCode"] . ":str",'',$objDB);
			// ��Ͽ��
			$aryData["dtminsertdate"] = date( 'Y/m/d', time());
			// ���ϼ�
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserName"] = $objAuth->UserDisplayName;
			
			// �̲�
			$_POST["strMonetaryUnitName"] = ($_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
			$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );
			// ��ʧ���
			if ( $_POST["lngPayConditionCode"] != "" )
			{
				$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB);
				$aryData["strPayConditionName"] = ( $strPayConditionName == "��" ) ? "" : $strPayConditionName;
			}

			// Ǽ�ʾ��
			$aryData["strLocationName"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "strcompanydisplayname", $_POST["lngLocationCode"].":str", '', $objDB);

			// �졼�ȥ�����
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $_POST["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;

			// ����
			$aryData["strAction"] = "/po/regist/index2.php?strSessionID=".$aryData["strSessionID"];
			
			// number_format 2004/03/11 watanabe
			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��
			// watanabe update end
			
			// 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
			$aryData["lngInputUserCode_DISCODE"] = ( $_POST["lngInputUserCode"] != "" ) ? "[".$_POST["lngInputUserCode"]."]" : "";
			$aryData["lngCustomerCode_DISCODE"] = ( $_POST["lngCustomerCode"] != "" ) ? "[".$_POST["lngCustomerCode"]."]" : "";
			//$aryData["lngInChargeGroupCode_DISCODE"] = ( $_POST["lngInChargeGroupCode"] != "" ) ? "[".$_POST["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInChargeUserCode_DISCODE"] = ( $_POST["lngInChargeUserCode"] != "" ) ? "[".$_POST["lngInChargeUserCode"]."]" : "";
			$aryData["lngLocationCode_DISCODE"] = ( $_POST["lngLocationCode"] != "" ) ? "[".$_POST["lngLocationCode"]."]" : "";
			// watanabe update end
			
			// ����ե����
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
			

			$aryData["lngRegistConfirm"] = 0;

			$aryData["RENEW"] = TRUE;

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

			$aryData["strActionURL"] = "/po/regist/index2.php?strSessionID=".$aryData["strSessionID"];

// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
// 2004.04.08 suzukaze update end



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
			
			//2007.07.23 matsuki update start				
			$aryData = fncPayConditionCodeMatch($aryData ,$aryHeadColumnNames, $_POST["aryPoDitail"] , $objDB);
			//2007.07.23 matsuki update end

			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "po/confirm/parts.tmpl" );
			$aryData["yokuwakaran"] = $yokuwakaran;
			

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





	// �ǽ�Υڡ���
	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngrevisionno, ";								// ��ӥ�����ֹ�
	$aryQuery[] = "strordercode, ";									// ȯ������
	$aryQuery[] = "strrevisecode as strReviseCode, ";				// ��Х���������
	$aryQuery[] = "To_char( dtmAppropriationDate, 'YYYY/mm/dd') as dtmOrderAppDate,";	// �׾���
	$aryQuery[] = "lngcustomercompanycode as lngCustomerCode, ";	// ��ҥ����ɡʻ������
	//$aryQuery[] = "lnggroupcode as lngInChargeGroupCode, ";		// ����
	//$aryQuery[] = "lngusercode as lngInChargeUserCode, ";			// ô����
	$aryQuery[] = "lngorderstatuscode, ";							// ȯ����֥�����
	$aryQuery[] = "lngmonetaryunitcode, ";							// �̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode, ";							// �̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";							// �����졼��
	$aryQuery[] = "lngpayconditioncode, ";							// ��ʧ��拾����
	$aryQuery[] = "curtotalprice, ";								// ��׶��
	$aryQuery[] = "lngdeliveryplacecode as lngLocationCode, ";		// Ǽ�ʾ�ꥳ���� / ��ҥ�����
	$aryQuery[] = "To_char( dtmexpirationdate, 'YYYY/mm/dd') as dtmexpirationdate, ";		// ȯ��ͭ��������
	$aryQuery[] = "strNote ";										// ����
	$aryQuery[] = "FROM m_order ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngorderno = ". $aryData["lngOrderNo"];


	$strQuery = implode("\n", $aryQuery );


	// �����꡼�¹�
	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$aryQueryResult = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );



	//-------------------------------------------------------------------------
	// ȯ����֤Υ����å�
	//-------------------------------------------------------------------------
	// ������ξ��
	if( $aryQueryResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE )
	{
		fncOutputError( 505, DEF_WARNING, "", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
	//-------------------------------------------------------------------------
	$aryQueryResult["lngorderstatuscode"] = fncCheckNullStatus( $aryQueryResult["lngorderstatuscode"] );




	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngorderdetailno, ";								// ȯ�������ֹ�
	$aryQuery[] = "lngrevisionno, ";								// ��ӥ�����ֹ�
	$aryQuery[] = "strproductcode, ";								// ���ʥ�����
	$aryQuery[] = "lngstocksubjectcode, ";							// �������ܥ�����
	$aryQuery[] = "lngstockitemcode, ";								// �������ʥ�����
	$aryQuery[] = "To_char( dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate,";	// Ǽ����
	$aryQuery[] = "lngdeliverymethodcode, ";						// ������ˡ������
	$aryQuery[] = "lngconversionclasscode, ";						// ������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
	$aryQuery[] = "curproductprice, ";								// ���ʲ���
	$aryQuery[] = "lngproductquantity, ";							// ���ʿ���
	$aryQuery[] = "lngproductunitcode, ";							// ����ñ�̥�����
	$aryQuery[] = "lngtaxclasscode, ";								// �����Ƕ�ʬ������
	$aryQuery[] = "lngtaxcode, ";									// �����ǥ�����
	$aryQuery[] = "curtaxprice, ";									// �����Ƕ��
	$aryQuery[] = "cursubtotalprice, ";								// ���׶��
	$aryQuery[] = "strnote, ";										// ����
	$aryQuery[] = "strmoldno as strserialno ";						// ���ꥢ���ֹ�
	$aryQuery[] = "FROM t_orderdetail ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngorderno = ". $aryData["lngOrderNo"];
	$aryQuery[] = " ORDER BY lngSortKey ASC";
	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );




	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
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
			$aryQueryResult2[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}



	$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryData["lngOrderNo"] . ":str", '', $objDB );


	// �ؿ� fncChangeDisplayName��ɽ���ѥǡ������Ѵ�(HEADER��
	$aryNewResult = fncChangeData2( $aryQueryResult , $objDB );


	// ���ٹԤ�hidden�ͤ��Ѵ�����
	$aryNewResult["strDetailHidden"] = fncDetailHidden( $aryQueryResult2 ,"", $objDB );



	// �ץ�������˥塼������
	// �̲�
	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode","strmonetaryunitsign", $aryQueryResult["lngmonetaryunitcode"], '', $objDB );

	$aryNewResult["lngmonetaryunitcode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );

	// �졼�ȥ�����
	$aryNewResult["lngmonetaryratecode"]		= fncPulldownMenu( 1, $aryQueryResult["lngmonetaryratecode"], '', $objDB );
	// ��ʧ���
	$aryNewResult["lngpayconditioncode"]		= fncPulldownMenu( 2, $aryQueryResult["lngpayconditioncode"], '', $objDB );
	// ��������
	$aryNewResult["strStockSubjectCode"]		= fncPulldownMenu( 3, $aryQueryResult["strStockSubjectCode"], '', $objDB );
	// ������ˡ
	$aryNewResult["lngCarrierCode"]				= fncPulldownMenu( 6, $aryQueryResult["lngCarrierCode"], '', $objDB );
	// ����ñ��
	$aryNewResult["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, $aryQueryResult["lngProductUnitCode"], '', $objDB );
	// �ٻ�ñ��
	$aryNewResult["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, $aryQueryResult["lngPackingUnitCode"], '', $objDB );


	//-------------------------------------------------------------------------
	// ȯ�����(ɽ����)�μ���
	//-------------------------------------------------------------------------
	$aryNewResult["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_orderstatus", "lngorderstatuscode", "strorderstatusname", $aryNewResult["lngorderstatuscode"],'', $objDB );



	$aryNewResult["strMode"] = "check";									// �⡼�ɡʼ���ư���check��renew
	$aryNewResult["strSessionID"] = $aryData["strSessionID"];			// ���å����
	$aryNewResult["strActionUrl"] = "renew.php"; 						// form��action
	$aryNewResult["lngOrderNo"] = $aryData["lngOrderNo"];				// �������ֹ�
	
	if( is_array( $aryQueryResult2 ) )
	{
		$aryNewResult["MonetaryUnitDisabled"] = " disabled";
	}



	// ���¥��롼�ץ����ɤμ���
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// ��ǧ�롼�Ȥ�����
	// �֥ޥ͡����㡼�װʾ�ξ��
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryNewResult["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
	}
	else
	{
		$aryNewResult["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );
	}


	//�إå����ͤ��ü�ʸ���Ѵ�
	$aryNewResult["strnote"] = fncHTMLSpecialChars( $aryNewResult["strnote"] );

// 2004.04.08 suzukaze update start
	$aryNewResult["lngCalcCode"] = DEF_CALC_KIRISUTE;
// 2004.04.08 suzukaze update end

// 2004.04.19 suzukaze update start
	$aryData["strPageCondition"] = "renew";
// 2004.04.19 suzukaze update end


	$aryNewResult["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


	$objDB->close();
	$objDB->freeResult( $lngResultID );
	
	$aryNewResult["RENEW"] = TRUE;

	// �إ���б�
	$aryNewResult["lngFunctionCode"] = DEF_FUNCTION_PO5;
	
	echo fncGetReplacedHtml( "po/regist/parts.tmpl", $aryNewResult ,$objAuth );
	
	return true;
	
?>