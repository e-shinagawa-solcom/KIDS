<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ��Ͽ��ǧ����
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
	require(SRC_ROOT."p/cmn/lib_p3.php");
	require (LIB_DEBUGFILE);

	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$objDB->open("", "", "", "");
	
	
	$aryData["strSessionID"]    = $_GET["strSessionID"];
	
	$aryData = $_POST;




	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	
	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	
	$lngInChargeGroupCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $aryData["lngInChargeGroupCode"] . ":str",'bytGroupDisplayFlag=true',$objDB);

	// ����̾�ʱѸ�ˤ��������
//	$lngInChargeGroupCode = fncGetMasterValue(m_group, strgroupdisplaycode, lnggroupcode,  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
	$strOptionValue = fncNearName ( $aryData["strProductEnglishName"], $lngInChargeGroupCode , "", $objDB );
	$aryData["strOptionValue"] = $strOptionValue;

	// ���ƥ��꡼̾��
	// /kids21/tmp/p/confirm/parts.tmpl 

	$aryData["lngCategoryCode_DIS"] = fncGetMasterValue( "m_Category", "lngCategoryCode", "strCategoryName", $aryData["lngCategoryCode"], '', $objDB);

	// �ٻ�ñ��
	$aryData["lngPackingUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], '', $objDB);
	
	
	// ����ñ��
	$aryData["lngProductUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductUnitCode"], '', $objDB);
	
	// ���ʷ���
	$aryData["lngProductFormCode_DIS"] = fncGetMasterValue( "m_productform", "lngproductformcode", "strproductformname", $aryData["lngProductFormCode"], '', $objDB);
	
	
	// �о�ǯ��
	$aryData["lngTargetAgeCode_DIS"] = fncGetMasterValue( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
	
	// �ڻ�
	$aryData["lngCertificateClassCode_DIS"] = fncGetMasterValue( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
	
	// �Ǹ���
	$aryData["lngCopyrightCode_DIS"] = fncGetMasterValue( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
	
	// ����ͽ���
	$aryData["lngProductionUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
	
	// ���Ǽ�ʿ�
	$aryData["lngFirstDeliveryUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryUnitCode"], '', $objDB);
	
	// ���ʹԾ���
	$aryData["lngGoodsPlanProgressCode_DIS"] = fncGetMasterValue( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname" ,$aryData["lngGoodsPlanProgressCode"], '',  $objDB );

	// ���å���֥�
	$aryData["strAssemblyFactoryName"]		= addslashes( $aryData["strAssemblyFactoryName"] );


	// --------------------------
	// ���塞�����⥵���С��Ǥϡ�POST���줿�ǡ����Υ��֥륯�����Ȥˡ�ޡ������դ��Ƥ��ޤ����ᡢ�����������
	// confirm/index.php �� regist/renew.php �ˤ��б���2006/10/08 K.Saito
	$aryData["strSpecificationDetails"] = StripSlashes($aryData["strSpecificationDetails"]);
	// --------------------------

	// http:// ���� https:// �Υۥ��Ȥ��ޤޤ�Ƥ����硢�������
	$aryData["strSpecificationDetails"] = preg_replace("/(http:\/\/?[^\/]+)|(https:\/\/?[^\/]+)/i", "" , $aryData["strSpecificationDetails"]);


	// ���;ܺ٤��ü�ʸ������
	//$aryData["strSpecificationDetails"] = fncHTMLSpecialChars( $aryData["strSpecificationDetails"] );
	// ���;ܺ�ɽ����
	$aryData["strSpecificationDetails_DIS"] = nl2br( $aryData["strSpecificationDetails"] );

	// ���;ܺ�HIDDEN�ѡ�HIDDEN�������ि���;ʬ�ʥ����ʤɤ��������
	if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
	{
		$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
		$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
	}

//fncDebug("pdetail2.txt", $aryData["strSpecificationDetails_DIS"], __FILE__, __LINE__ );


/**
	���;ܺٲ����ե�����HIDDEN����
*/
if( $aryData["uploadimages"] )
{
	for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
	{
		$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
	}

	// �Ƽ����Ѥ�����
	$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
	$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
}
else
{
	$aryData["re_uploadimages"]	= "";
	$aryData["re_editordir"]	= "";
}



	$aryData["strMonetaryrate"] = DEF_EN_MARK; //�̲ߥޡ���

	// 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
	$aryData["strCustomerUserCode_DISCODE"] = ( $aryData["strCustomerUserCode"] != "" ) ? "[".$aryData["strCustomerUserCode"]."]" : "";
	$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
	$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
	$aryData["lngCompanyCode_DISCODE"] = ( $aryData["lngCompanyCode"] != "" ) ? "[".$aryData["lngCompanyCode"]."]" : "";
	$aryData["lngFactoryCode_DISCODE"] = ( $aryData["lngFactoryCode"] != "" ) ? "[".$aryData["lngFactoryCode"]."]" : "";
	$aryData["lngAssemblyFactoryCode_DISCODE"] = ( $aryData["lngAssemblyFactoryCode"] != "" ) ? "[".$aryData["lngAssemblyFactoryCode"]."]" : "";
	$aryData["lngDeliveryPlaceCode_DISCODE"] = ( $aryData["lngDeliveryPlaceCode"] != "" ) ? "[".$aryData["lngDeliveryPlaceCode"]."]" : "";
	// watanabe update end



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


	$aryData["strActionURL"] = "/p/regist/index3.php?strSessionID=".$aryData["strSessionID"];

	//print_r($aryData);
	
	$objDB->close();
	
	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/confirm/parts.tmpl" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();


// debug file����
//fncDebug("pdetail.txt", $objTemplate->strTemplate, __FILE__, __LINE__ );


	// HTML����
	echo $objTemplate->strTemplate;


	return true;
	
?>

