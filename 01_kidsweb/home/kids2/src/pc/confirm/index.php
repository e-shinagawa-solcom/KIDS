<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ��Ͽ��ǧ����
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
*         ����Ͽ��ǧ���̤�ɽ��
*         �����顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ������
*
*       ��������
*	  ����Ψ�������������ʴ��������Ψ�����Ǥ��ʤ���硢�ǿ����֤���Ψ��������� 20130531��
*
*/
// ----------------------------------------------------------------------------



	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
// 2004.12.28 suzukaze update start
	require(LIB_ROOT."libcalc.php");
// 2004.12.28 suzukaze update end
	require(SRC_ROOT."pc/cmn/lib_pc.php");
	require(SRC_ROOT."pc/cmn/lib_pcs1.php");
	require(SRC_ROOT."pc/cmn/lib_pcs.php");
	require(SRC_ROOT."pc/cmn/column.php");
	
	$objDB          = new clsDB();
	$objAuth        = new clsAuth();
	
	$objDB->open("", "", "", "");
	
	$aryData["strSessionID"]    = $_GET["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	// ���å�����ǧ
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );
	$UserDisplayName = $objAuth->UserDisplayName;
	
	// ���ٹԤ����
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}
	
	//�إå����ͤ��ü�ʸ���Ѵ�
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

// 2004.12.28 suzukaze update start
	// ���ٹ��Ѥ˾����ǥ����ɤ��������
	// �����ǥ�����
	// �׾�����ꤽ�λ�����Ψ���Ȥ��
	$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
		. "FROM m_tax "
		. "WHERE dtmapplystartdate <= '" . $aryData["dtmOrderAppDate"] . "' "
		. "AND dtmapplyenddate >= '" . $aryData["dtmOrderAppDate"] . "' "
		. "GROUP BY lngtaxcode, curtax "
		. "ORDER BY 3 ";

	// ��Ψ�ʤɤμ��������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngTaxCode = $objResult->lngtaxcode;
		$curTax = $objResult->curtax;
	}
	else
	{
		$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
				. "FROM m_tax "
				. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
				. "GROUP BY lngtaxcode, curtax ";
		// ��Ψ�ʤɤμ��������꡼�μ¹�
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$lngTaxCode = $objResult->lngtaxcode;
			$curTax = $objResult->curtax;
		}
		else
		{
			fncOutputError ( 9051, DEF_ERROR, "�����Ǿ���μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		}
	}
	$objDB->freeResult( $lngResultID );

	$lngCalcCode = DEF_CALC_KIRISUTE;

	// ���������̲�ñ�̥����ɤ������оݷ��������
	if ( $aryData["lngMonetaryUnitCode"] == "\\" or $aryData["lngMonetaryUnitCode"] == "\\\\" )
	{
		$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
	}
	else
	{
		$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
	}


	// ���ٹԽ��� ===========================================================================================
	// ���ٹԤ�hidden����
	if( is_array( $_POST["aryPoDitail"] ) )
	{
		// hidden�������κݤ��ǳ۰۾��Ĵ����Ԥ�
		for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// ���Ǥκݤ����ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤ�ʤ������ǳۤκƷ׻���Ԥ�
			if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_UCHIZEI ) 
			{
				// ���ʲ��� = ����ñ�� �� ����
				$curProductTotalPrice = $_POST["aryPoDitail"][$i]["curProductPrice"] * $_POST["aryPoDitail"][$i]["lngGoodsQuantity"];

				// ���ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤäƤ��ʤ����
				if ( $curProductTotalPrice != ( $_POST["aryPoDitail"][$i]["curTaxPrice"] + $_POST["aryPoDitail"][$i]["curTotalPrice"] ) )
				{
					// �����ǳ� �� ��ȴ��� �� ��Ψ
					$curTaxPrice = $_POST["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
					// ü��������Ԥ�
					$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );

					$_POST["aryPoDitail"][$i]["curTaxPrice"] = $curTaxPrice;
				}
			}
		}

		$aryData["strDetailHidden"] = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert" , $objDB );
	}



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
	$aryDetailColumnNames = fncSetStockTabelName( $aryTableViewDetail, $aryTytle );
	



	$lngAllPrice = 0;

	for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
	{
	
		$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;
		
		// ��������
		if ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strStockSubjectName"] 
				= fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", 
					$_POST["aryPoDitail"][$i]["strStockSubjectCode"], '', $objDB );
		}
		
		// �������� 
		if ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strStockItemName"] 
				= fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname", 
					$_POST["aryPoDitail"][$i]["strStockItemCode"], "lngstocksubjectcode = ".$_POST["aryPoDitail"][$i]["strStockSubjectCode"], $objDB );
		}
		
		// ������ˡ
		//echo "carriercode : ".$_POST["aryPoDitail"][$i]["lngCarrierCode"]."<br>";
		//exit();
		if ( $_POST["aryPoDitail"][$i]["lngCarrierCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strCarrierName"] 
				= fncGetMasterValue( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", 
					$_POST["aryPoDitail"][$i]["lngCarrierCode"],'', $objDB );
		}
		
		// �ܵ�����
		if ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strGoodsName"] 
				= fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", 
					$_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );
		}
		
		// ñ��
		if ( $_POST["aryPoDitail"][$i]["lngProductUnitCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strProductUnitName"] 
				= fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", 
					$_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );
		}
		
		// �Ƕ�ʬ
		if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["strTaxClassName"] 
				= fncGetMasterValue( "m_taxclass", "lngtaxclasscode", "strtaxclassname", $_POST["aryPoDitail"][$i]["lngTaxClassCode"], '', $objDB );
		}


		// ��Ψ
		if ( $_POST["aryPoDitail"][$i]["lngTaxCode"] != "" )
		{
			$_POST["aryPoDitail"][$i]["curTax"] = $curTax;
		}


		// ���ٹ����ͤ��ü�ʸ���Ѵ�
		$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );
		

		$strProductName = "";

		if( $strProductName = fncGetMasterValue( "m_product", "strproductcode", "strproductname",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB ) )
		{
			$_POST["aryPoDitail"][$i]["strproductname"] = $strProductName;
		}

		
		// ���Ǥκݤ����ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤ�ʤ������ǳۤκƷ׻���Ԥ�
		if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_UCHIZEI ) 
		{
			// ���ʲ��� = ����ñ�� �� ����
			$curProductTotalPrice = $_POST["aryPoDitail"][$i]["curProductPrice"] * $_POST["aryPoDitail"][$i]["lngGoodsQuantity"];

			// ���ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤäƤ��ʤ����
			if ( $curProductTotalPrice != ( $_POST["aryPoDitail"][$i]["curTaxPrice"] + $_POST["aryPoDitail"][$i]["curTotalPrice"] ) )
			{
				// �����ǳ� �� ��ȴ��� �� ��Ψ
				$curTaxPrice = $_POST["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
				// ü��������Ԥ�
				$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );

				$_POST["aryPoDitail"][$i]["curTaxPrice"] = $curTaxPrice;
			}
		}



		// ��׶�ۤμ���
		$lngAllPrice += $_POST["aryPoDitail"][$i]["curTotalPrice"];



		// number_format
		$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
		$_POST["aryPoDitail"][$i]["curproductprice_DIS"] =  ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
		$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] =  ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
		$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] =  ($_POST["aryPoDitail"][$i]["curTaxPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";
		$_POST["aryPoDitail"][$i]["curTotalPrice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";

		// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
		$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["strstockitemcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockItemCode"]."]" : "";
		$_POST["aryPoDitail"][$i]["strstocksubjectcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockSubjectCode"]."]" : "";






		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "pc/result/parts_detail2.tmpl" );
		
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDetailColumnNames );
		$objTemplate->replace( $_POST["aryPoDitail"][$i] );
		$objTemplate->complete();
		
		// HTML����
		$aryDetailTable[] = $objTemplate->strTemplate;
	}




	$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );
	
	$aryData["strProcMode"] = "regist";

	$aryData["strMode"] = "regist";
	
	// ��Ͽ��
	$aryData["dtminsertdate"] = date( 'Y/m/d', time());
	// ����
	$aryData["strAction"] = "/pc/regist/index2.php?strSessionID=".$aryData["strSessionID"];
	// ���ϼ�
	$aryData["lngInputUserCode"] = $objAuth->UserID;
	$aryData["strInputUserName"] = $objAuth->UserDisplayName;



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



	// �̲�
	$aryData["strMonetaryUnitName"]   = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
	$aryData["strMonetaryUnitName"]   = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );
	// �졼�ȥ�����
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"]   = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;
	// ��ʧ���
	$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $aryData["lngPayConditionCode"], '', $objDB );
	$aryData["strPayConditionName"]   = ( $strPayConditionName == "��" ) ? "" : $strPayConditionName;
	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
	$aryData["strMonetaryrate"]       = $aryData["lngMonetaryUnitCode"];


	//$aryData["curAllTotalPrice_DIS"]  = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��
	$aryData["curAllTotalPrice_DIS"] = number_format( $lngAllPrice, 2 );		// ��׶��



	// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"]  = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	//$aryData["lngInchargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	//$aryData["lngInchargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngLocationCode_DISCODE"]  = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";








	// *v2* ����ե����
	if( $aryData["lngWorkflowOrderCode"] != "" and $aryData["lngWorkflowOrderCode"] != 0 )
	{
		$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $aryData["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB );

		$aryData["strWorkflowMessage_visibility"] = "block;";

	}
	else
	{
		$aryData["strWorkflowOrderName"] = "��ǧ�ʤ�";

		$aryData["strWorkflowMessage_visibility"] = "none;";
	}

	// ����ե���å�����ɽ������ɽ������
	$aryData["strWorkflowMessage_visibility"] = 'none';





	$objDB->close();

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/confirm/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;


	return true;

?>