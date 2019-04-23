<?
/** 
*	���Ѹ������� �ܺ�ɽ������
*
*	@package   KIDS
*	@copyright Copyright (c) 2004, kuwagata 
*	@author    Kenji Chiba
*	@editor    Kazushi Saito 2009.08.30
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID	 -> ditail.php
// index.php -> lngEstimateNo	 -> ditail.php

	// �����ɤ߹���
	require ('conf.inc');
	require ( LIB_DEBUGFILE );

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	require ( CLS_TABLETEMP_FILE );	// Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );



	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GET�ǡ�������
	$aryData = $_GET;

	fncDebug( 'estimate_result_detail_01.txt', $aryData, __FILE__, __LINE__);


	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ������桼���������ɤμ���
	$lngInputUserCode = $objAuth->UserCode;


	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_E4, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}





	// ʸ��������å�
	$aryCheck["strSessionID"]	 = "null:numenglish(32,32)";
	$aryCheck["lngEstimateNo"]	 = "null:number(0,2147483647)";

	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	if ( join ( "", $aryCheckResult ) )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
	$strURL = fncGetURL( $aryData );






	// �̲ߥ졼����������
	$aryRate = fncGetMonetaryRate( $objDB );
	$aryRate[DEF_MONETARY_YEN] = 1;


	// ���Ѹ���HTML���ϥǡ�������
	$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );


	//fncDebug( 'es_detail.txt', $aryEstimateData, __FILE__, __LINE__);


	// ���֤��ֿ�����פξ��
	if( $aryEstimateData["lngEstimateStatusCode"] == DEF_ESTIMATE_APPLICATE )
	{
		//fncOutputError( 1509, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// ���֤�����ǧ�פξ��
	if( $aryEstimateData["lngEstimateStatusCode"] == DEF_ESTIMATE_DENIAL )
	{
		fncOutputError( 1510, DEF_WARNING, "", TRUE, "", $objDB );
	}





	$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryRate, $objDB );

fncDebug( 'estimate_result_detail.txt', $aryDetail, __FILE__, __LINE__);

	list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

	// added start k.saito 2005.1.28
	// �����񾮷פ���������Ƥ��ʤ��١��۵��к�������
	//$aryEstimateData["curFixedCostSubtotal"] = $aryCalculated["curFixedCostSubtotal"];
	// added end

fncDebug( 'estimate_result_detail.txt', $aryDetail, __FILE__, __LINE__);

	// �׻���̤��Ѹ���������Ȥ߹���
	$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

	// ���١����ˤ�����
	list($aryEstimateDetailSales, $curFixedCostSales) = fncGetEstimateDetail_Sales_Html( $aryDetail, "estimate/regist/plan_detail_sales.tmpl", $objDB );

	$aryEstimateDetail = array_merge ( $aryEstimateDetail, $aryEstimateDetailSales );
	$aryEstimateData["curFixedCostSales"]	= $curFixedCostSales;	// 1:���������ι��

//		$aryHiddenString = array_merge ( $aryHiddenString, $aryHiddenStringSales );

fncDebug( 'estimate_result_detail_sales.txt', $aryEstimateDetailSales, __FILE__, __LINE__);

	unset ( $aryCalculated );
	unset ( $aryHiddenString );
	unset ( $aryRate );



	// �ƥ�ݥ��ե饰ͭ��
	if( $aryEstimateData["blnTempFlag"] )
	{
		// ɸ�������
		$aryEstimateData["curStandardRate"]		= fncGetEstimateDefault( $objDB );

		// ����US�ɥ�졼�ȼ���
		$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

		// Excelɸ�������
//		$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
		// Excel����US�ɥ�졼�ȼ���
//		$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
	}
	else
	{
		// ɸ�������
		$aryEstimateData["curStandardRate"]		= fncGetEstimateDefault( $objDB );

		// ����US�ɥ�졼�ȼ���
		$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
	}



	// �׻���̤����
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	// ����޽���
	$aryEstimateData	= fncGetCommaNumber( $aryEstimateData );


	// �١����ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );

	$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/detail_exstr.js\"></script>";


		// ������
		$aryData["strRemarkDisp"]	= nl2br($aryEstimateData["strRemark"]);


	// �١����ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->replace( $aryEstimateData );
	$objTemplate->replace( $aryEstimateDetail );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;
	//echo getArrayTable( $baseData, "TABLE" )

fncDebug( 'estimate_result_detail_01.txt', $objTemplate->strTemplate, __FILE__, __LINE__);


	$objDB->close();


	return TRUE;
?>
