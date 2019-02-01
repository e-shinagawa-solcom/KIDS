<?php

// ----------------------------------------------------------------------------
/**
*       �������  ��Ͽ��ǧ����
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
	require(SRC_ROOT."so/cmn/lib_so.php");
	require(SRC_ROOT."so/cmn/lib_sos1.php");
	require(SRC_ROOT."so/cmn/column.php");
	
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
		$aryData["strDetailHidden"] = fncDetailHidden_so( $_POST["aryPoDitail"] ,"insert" , $objDB );
	}
	
	
	// ���������
	if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
	{
		$aryTytle = $aryTableTytleEng;
	}
	else
	{
		$aryTytle = $aryTableTytle2;
	}

	// �����̾������
	$aryHeadColumnNames = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
	$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail2, $aryTytle );

	for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
	{
		$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;

		$_POST["aryPoDitail"][$i]["strproductcode_DIS"] = fncGetMasterValue( "m_product", "strproductcode", "strproductname", $_POST["aryPoDitail"][$i]["strProductCode"].":str", '', $objDB );

		$_POST["aryPoDitail"][$i]["lngsalesclasscode_DIS"] = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $_POST["aryPoDitail"][$i]["lngSalesClassCode"], '', $objDB );

		$_POST["aryPoDitail"][$i]["lngproductunitcode_DIS"] = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

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
	$aryData["strProcMode"] = "regist";



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



	// ��Ͽ��
	$aryData["dtminsertdate"] = date( 'Y/m/d', time() );
	// ���ϼ�
	$aryData["lngInputUserCode"] = $UserDisplayCode;
	$aryData["strInputUserCode"] = $UserDisplayName;
	// �̲�
	$aryData["strMonetaryUnitName"] = ( $aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];


	$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $aryData["strMonetaryUnitName"] . ":str", '', $objDB );

	// �졼�ȥ�����
	$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
	$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;

	// ����
	$aryData["strActionURL"] = "/so/regist/index2.php?strSessionID=".$aryData["strSessionID"];


	// ����
	$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );



	$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
	$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
	$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��




	// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
/*
	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
*/

	$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
	$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
	//$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	//$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngLocationCode_DISCODE"] = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";




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



/*
	// �ե����륢����
	$fp = fopen( "/home/kids2/kids/log/so_debug.txt", "a" );
	foreach( $aryData as $key => $value )
	{
		fwrite( $fp, "[" . $key . "] = " . $value . "\n" );
	}
	fclose( $fp );
*/


	//var_dump( $aryData["lngWorkflowOrderCode"] );

	$objDB->close();


	// �ܵҼ����ֹ�μ���
	$aryData["strCustomerReceiveCode"] = trim( $aryData["strCustomerReceiveCode"] );
	$aryData["strCustomerReceiveCode"] = ( $aryData["strCustomerReceiveCode"] == "null" ) ? "" : $aryData["strCustomerReceiveCode"];

	// ������ξ�硢����ե���å����������ɽ��
	if( $aryData["strCustomerReceiveCode"] == "null" || $aryData["strCustomerReceiveCode"] == "" )
	{
		$aryData["strWorkflowMessage_visibility"] = "none;";
	}





	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/confirm/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHeadColumnNames );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;


	return true;

?>
