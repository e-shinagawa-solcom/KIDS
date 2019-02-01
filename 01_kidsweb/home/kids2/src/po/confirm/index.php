<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��Ͽ��ǧ����
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
*
*/
// ----------------------------------------------------------------------------



	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	//2007.07.23 matsuki update start
	require(SRC_ROOT."po/cmn/lib_pop.php");
	//2007.07.23 matsuki update start
	require(SRC_ROOT."po/cmn/lib_pos1.php");
	require(SRC_ROOT."po/cmn/column.php");
	
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
	$UserDisplayCode = $objAuth->UserID;

	
	// ���ٹԤ����
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}
	
	
	
	// ���ٹԽ��� ===========================================================================================
	// ���ٹԤ�hidden����
	if( is_array( $_POST["aryPoDitail"] ) )
	{
		$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" , $objDB );
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
	$aryData["strProcMode"] = $_POST["strProcMode"];



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

/*
	// ����
	$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
	// ô����
	$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
*/



	// ��Ͽ��
	$aryData["dtminsertdate"] = date( 'Y/m/d', time());
	// ���ϼ� 
	$aryData["lngInputUserCode"] = $UserDisplayCode;
	$aryData["strInputUserName"] = $UserDisplayName;
	// �̲�
	$aryData["strMonetaryUnitName"] = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
	$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );
	// ��ʧ���
	$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $aryData["lngPayConditionCode"], '', $objDB);
	$aryData["strPayConditionName"] = ( $strPayConditionName == "��" ) ? "" : $strPayConditionName;
	
	// Ǽ�ʾ��
	$aryData["strLocationName"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "strcompanydisplayname", $aryData["lngLocationCode"].":str", '', $objDB);

	// �졼�ȥ�����
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;

	// ����
	$aryData["strAction"] = "/po/regist/index2.php?strSessionID=".$aryData["strSessionID"];

	//�إå����ͤ��ü�ʸ���Ѵ�
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
	
	// number_format 2004/03/11 watanabe
	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
	$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
	$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��
	// watanabe update end
	
	// 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	//$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	//$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngLocationCode_DISCODE"] = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";
	// watanabe update end
	

	// ����ե����
	if ( $aryData["lngWorkflowOrderCode"] != "" and $aryData["lngWorkflowOrderCode"] != 0 )
	{
		$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $aryData["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB);
// 2004.03.24 suzukaze update start
		$aryData["strWorkflowMessage_visibility"] = "block;";
// 2004.03.24 suzukaze update end
	}
	else
	{
		$aryData["strWorkflowOrderName"] = "��ǧ�ʤ�";
// 2004.03.24 suzukaze update start
		$aryData["strWorkflowMessage_visibility"] = "none;";
// 2004.03.24 suzukaze update end
	}
	
	
//2007.07.23 matsuki update start
		$aryData = fncPayConditionCodeMatch($aryData ,$aryHeadColumnNames, $_POST["aryPoDitail"] , $objDB);
//2007.07.23 matsuki update end


	$objDB->close();
	
	

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	//var_dump($aryData);
	
	$objTemplate->getTemplate( "po/confirm/parts.tmpl" );

	// �ƥ�ץ졼������
	
	$objTemplate->replace( $aryHeadColumnNames );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML���� ���ٹԤ�_%strDetailTable%_�Ǽ����Ϥ�
	echo $objTemplate->strTemplate;
	


	return true;

	

?>