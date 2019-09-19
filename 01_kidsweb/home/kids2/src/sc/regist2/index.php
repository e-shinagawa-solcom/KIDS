<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��Ͽ����
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
*         �������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");

	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	if( strcmp( $_GET["strSessionID"],"" ) != 0 )
	{
		$aryData["strSessionID"] = $_GET["strSessionID"];
		$aryData["lngOrderNo"]   = 1;
	}
	else
	{
		$aryData["strSessionID"] = $_POST["strSessionID"];
		$aryData["lngOrderNo"]   = $_POST["lngOrderNo"];
	}
	$aryData["lngLanguageCode"]	= $_COOKIE["lngLanguageCode"];

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
	$objDB->open("", "", "", "");

	// ʸ��������å�
	$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	
	// ���ٸ���
	if($_POST["strMode"] == "search-detail"){

		$aryReceiveDetail = fncGetReceiveDetail($_POST["condition"], $objDB);
		$strHtml = fncGetReceiveDetailHtml($aryReceiveDetail);

		echo $strHtml;

		return true;
	}

	// ���¥����å�
	$lngUserCode = $objAuth->UserCode;
	/*
	
	// 500	ȯ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
	        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	
	// 501 ȯ�������ȯ����Ͽ��
	if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}


	// 508 ȯ������ʾ��ʥޥ��������쥯�Ƚ�����
	if( !fncCheckAuthority( DEF_FUNCTION_PO8, $objAuth ) )
	{
		$aryData["popenview"] = 'hidden';
	}

	// �����⡼��
	if($_POST["strMode"] == "update"){
		// �����ǡ�������
		$aryUpdate["lngorderno"]           = $_POST["lngOrderNo"];
		$aryUpdate["lngrevisionno"]        = $_POST["lngRevisionNo"];
		$aryUpdate["dtmexpirationdate"]    = $_POST["dtmExpirationDate"];
		$aryUpdate["lngpayconditioncode"]  = $_POST["lngPayConditionCode"];
		$aryUpdate["lngdeliveryplacecode"] = $_POST["lngLocationCode"];
		$aryUpdate["lngorderstatuscode"]   = 2;
		for($i = 0; $i < count($_POST["aryDetail"]); $i++){
			$aryUpdateDetail[$i]["lngorderdetailno"]       = $_POST["aryDetail"][$i]["lngOrderDetailNo"];
			$aryUpdateDetail[$i]["lngsortkey"]             = $_POST["aryDetail"][$i]["lngSortKey"];
			$aryUpdateDetail[$i]["lngdeliverymethodcode"]  = $_POST["aryDetail"][$i]["lngDeliveryMethodCode"];
			$aryUpdateDetail[$i]["strdeliverymethodname"]  = $_POST["aryDetail"][$i]["strDeliveryMethodName"];
			$aryUpdateDetail[$i]["lngproductunitcode"]     = $_POST["aryDetail"][$i]["lngProductUnitCode"];
			$aryUpdateDetail[$i]["lngorderno"]             = $_POST["aryDetail"][$i]["lngOrderNo"];
			$aryUpdateDetail[$i]["lngrevisionno"]          = $_POST["aryDetail"][$i]["lngRevisionNo"];
			$aryUpdateDetail[$i]["lngstocksubjectcode"]    = $_POST["aryDetail"][$i]["lngStockSubjectCode"];
			$aryUpdateDetail[$i]["lngstockitemcode"]       = $_POST["aryDetail"][$i]["lngStockItemCode"];
			$aryUpdateDetail[$i]["lngmonetaryunitcode"]    = $_POST["aryDetail"][$i]["lngMonetaryUnitCode"];
			$aryUpdateDetail[$i]["lngcustomercompanycode"] = $_POST["aryDetail"][$i]["lngCustomerCompanyCode"];
			$aryUpdateDetail[$i]["curproductprice"]        = $_POST["aryDetail"][$i]["curProductPrice"];
			$aryUpdateDetail[$i]["lngproductquantity"]     = $_POST["aryDetail"][$i]["lngProductQuantity"];
			$aryUpdateDetail[$i]["cursubtotalprice"]       = $_POST["aryDetail"][$i]["curSubtotalPrice"];
			$aryUpdateDetail[$i]["dtmseliverydate"]        = $_POST["aryDetail"][$i]["dtmDeliveryDate"];
		}
		
		
		$objDB->transactionBegin();
		// ȯ��ޥ�������
		if(!fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
		// ȯ�����ٹ���
		if(!fncUpdateOrderDetail($aryUpdate, $aryUpdateDetail, $objDB)) { return false; }
		// ȯ���ޥ�������
		//if(!fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB)){ return false; }
		$aryResult = fncUpdatePurchaseOrder($aryUpdate, $aryUpdateDetail, $objAuth, $objDB);
		//echo implode(",", $aryResult);
		// TODO:���Ȥǥ��ߥåȤ��ѹ�����
		// $objDB->transactionRollback();
		$objDB->transactionCommit();

		// ������ȯ���ǡ�������
		$aryPurcharseOrder = fncGetPurchaseOrder($aryResult, $objDB);
		if(!$aryPurcharseOrder){
			fncOutputError ( 9051, DEF_ERROR, "ȯ���μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
			return FALSE;
		}
		
		$strHtml = fncCreatePurchaseOrderHtml($aryPurcharseOrder);
		$aryData["aryPurchaseOrder"] = $strHtml;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		
		header("Content-type: text/plain; charset=EUC-JP");
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryData );
		// $objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;

		return true;
	}

	// �إå����եå���
	$aryOrderHeader = fncGetOrder($aryData["lngOrderNo"], $objDB);

	// $aryData["strOrderCode"]          = $aryOrderHeader[0]["strordercode"];
	// $aryData["strReviseCode"]         = str_pad($aryOrderHeader[0]["lngrevisionno"],2,"0",STR_PAD_LEFT);
	$aryData["dtmExpirationDate"]     = str_replace("-", "/", $aryOrderHeader[0]["dtmexpirationdate"]);
	$aryData["strProductCode"]        = $aryOrderHeader[0]["strproductcode"];
	$aryData["strNote"]               = $aryOrderHeader[0]["strnote"];
	// $aryData["lngCustomerCode"]       = $aryOrderHeader[0]["strcompanydisplaycode"];
	// $aryData["strCustomerName"]       = $aryOrderHeader[0]["strcompanydisplayname"];
	$aryData["strGroupDisplayCode"]   = $aryOrderHeader[0]["strgroupdisplaycode"];
	$aryData["strGroupDisplayName"]   = $aryOrderHeader[0]["strgroupdisplayname"];
	$aryData["strProductName"]        = $aryOrderHeader[0]["strproductname"];
	$aryData["strProductEnglishName"] = $aryOrderHeader[0]["strproductenglishname"];
	$aryData["lngCountryCode"]        = $aryOrderHeader[0]["lngcountrycode"];
	$aryData["lngLocationCode"]       = $aryOrderHeader[0]["strcompanydisplaycode2"];
	$aryData["strLocationName"]       = $aryOrderHeader[0]["strcompanydisplayname2"];
	$aryData["lngRevisionNo"]         = $aryOrderHeader[0]["lngrevisionno"];
	
	// ����
	// $aryDetail = [];
	// for($i = 0; $i < count($aryOrderHeader); $i++){
	// 	$aryDetail[] = fncGetOrderDetail($aryOrderHeader[$i], $objDB);
	// }
	$aryDetail = fncGetOrderDetail($aryData["lngOrderNo"], $objDB);

	// ��ʧ���ץ������
	$strPulldownPaycondition = fncPulldownMenu(0, 0, "", $objDB);
	$aryData["optPayCondition"] = $strPulldownPaycondition;
	// �̲ߥץ������
	$strPulldownMonetaryUnit = fncPulldownMenu(1, $aryOrderHeader["lngmonetaryunitcode"], "", $objDB);
	$aryData["optMonetaryUnit"] = $strPulldownMonetaryUnit;
	// ������ˡ�ץ������
	$strPulldownDeliveryMethod = fncPulldownMenu(2, null, "", $objDB);

	$aryData["strOrderDetail"] = fncGetOrderDetailHtml($aryDetail, $strPulldownDeliveryMethod);




	if(false){
	if($_POST["strMode"] == "check")
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
		
		
		// header�ι��ܥ����å�
		list ( $aryData, $bytErrorFlag )  = fncCheckData_po( $aryData,"header", $objDB );
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
		if(  count( $_POST["aryPoDitail"] ) > 0 )
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_po( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}
			
			// ���ٹԤΥ��顼�ؿ�
			$strDetailErrorMessage = fncDetailError( $bytErrorFlag2 );
			
			if( $strDetailErrorMessage != "" )
			{
				$aryDetailErrorMessage[] = $strDetailErrorMessage;
			}
			
		}
		else
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}

		// 2004.03.30 suzukaze update start
		// ���ٹԤΥǡ������Ф��ơ����ʥ����ɤ��㤦�ǡ�����¸�ߤ��ʤ����ɤ����Υ����å�
		$bytCheck = fncCheckOrderDetailProductCode ( $_POST["aryPoDitail"], $objDB );
		if ( $bytCheck == 99 )
		{
			$aryDetailErrorMessage[] = fncOutputError( 506, "", "", FALSE, "", $objDB );
		}
		// 2004.03.30 suzukaze update end

		// ���顼�����ä���� ==============================================================================
		if( $errorCount != 0 || is_array( $aryDetailErrorMessage ))
		{
			
			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}
			
			// ���ٹԤ��ͤ����äƤ�������̲ߤ�disabled�ˤ���
			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
			}

			//�ü�ʸ���Ѵ�
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
			
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
			
			$aryData["strMode"]			= "check";			// �⡼�ɡʼ���ư���check��insert
			$aryData["strActionUrl"]	= "index2.php";		// form��action



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
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"]);
			}




			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" , $objDB );
			}
			
			// submit�ؿ�
			$aryData["lngRegistConfirm"] = 0;
			$aryData["strMode"] = "check";
			
			// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
			// 2004.04.08 suzukaze update end

			// 2004.04.19 suzukaze update start
			$aryData["strPageCondition"] = "regist";
			// 2004.04.19 suzukaze update end


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


			$objDB->close();
			$objDB->freeResult( $lngResultID );

			echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);

			return true;
			
		}
		else
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
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );
			}


			// ��Ȥޤä���Ʊ��(�����Ƥ���������������
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


			// ��ǧ����ɽ�� =======================================
			$aryData["strBodyOnload"] = "";
			$aryData["strMode"] = "check";
			$aryData["strProcMode"] = "regist";
			
			// submit�ؿ�
			$aryData["lngRegistConfirm"] = 1;
			
			// ���ٹԤ�hidden�ͤ��Ѵ�����
			//$aryData["strHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" ,$objDB );

			$aryData["strDetailHidden"] = fncDetailHidden( $_POST["aryPoDitail"] ,"insert" , $objDB );

			
			//�ü�ʸ���Ѵ�
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );
			




			//	$aryData["strButton"] = "<input type=\"button\" value=\"���ľ��\" onClick=\"fncPageback( 'index.php' )\">&nbsp;&nbsp;<input type=\"button\" value=\"��Ͽ\" onClick=\"fncPagenext( 'index2.php' )\">";

			$objDB->close();
			
			$aryData["strurl"] = "/po/confirm/index.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "index.php";
			
			// 2004.04.08 suzukaze update start
			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
			// 2004.04.08 suzukaze update end

			// 2004.04.19 suzukaze update start
			$aryData["strPageCondition"] = "regist";
			// 2004.04.19 suzukaze update end


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����

			// �ƥ�ץ졼���ɤ߹���
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData, $objAuth );
			return true;
			
		}
	
	}

	// �ǽ�β���
	// �ץ�������˥塼������
	// �̲�
	$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, "\\", '', $objDB );
	// �졼�ȥ�����
	$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, 0, '', $objDB );
	// ��ʧ���
	$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, 0, '', $objDB );
	// ��������
	$aryData["strStockSubjectCode"]			= fncPulldownMenu( 3, 0, '', $objDB );
	// ������ˡ
	$aryData["lngCarrierCode"]				= fncPulldownMenu( 6, 0, '', $objDB );
	// ����ñ��
	$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
	// �ٻ�ñ��
	$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );

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
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB ,"" );
	}
	}



	$aryData["strMode"] = "update";				// �⡼�ɡʼ���ư���check��renew
	$aryData["strActionUrl"] = "index2.php";		// form��action
	
	//echo "value :".$aryData["lngWorkflowOrderCode"]."<br>";
	$dtmNowDate = date( 'Y/m/d', time());
	$aryData["dtmOrderAppDate"] = $dtmNowDate;
	
	// submit�ؿ�
	$aryData["lngRegistConfirm"] = 0;
	
	$aryData["curConversionRate"] = "1.000000";
	
	// 2004.04.08 suzukaze update start
	$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;
	// 2004.04.08 suzukaze update end

	// 2004.04.19 suzukaze update start
	$aryData["strPageCondition"] = "regist";
	// 2004.04.19 suzukaze update end


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


	$objDB->close();
	$objDB->freeResult( $lngResultID );

	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_PO1;
*/
	echo fncGetReplacedHtml( "sc/regist2/parts.tmpl", $aryData ,$objAuth);
	
	
	return true;

?>