<?
/** 
*	���Ѹ������� �ܺ�ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID	 -> ditail.php
// index.php -> lngEstimateNo	 -> ditail.php


	// �����ɤ߹���
	include_once('conf.inc');
	require_once( LIB_DEBUGFILE );


	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GET�ǡ�������
	$aryData = $_GET;

fncDebug( 'bbb.txt', $aryData, __FILE__, __LINE__);
exit;


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

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

	// ���Ѹ���HTML���ϥǡ�������
	$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

	// �̲ߥ졼����������
	$aryRate = fncGetMonetaryRate( $objDB );
	$aryRate[DEF_MONETARY_YEN] = 1;


		$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryData, $objDB );

		list ( $aryEstimateDetailStock, $aryCalculated ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
		unset ( $aryCalculated );

		// ���١����ˤ�����
//		list($aryEstimateDetailSales, $curFixedCostSales) = fncGetEstimateDetail_Sales_Html( $aryDetail, "estimate/regist/plan_detail_sales.tmpl", $objDB );

//		$aryEstimateDetail	= array_merge( $aryEstimateDetailStock, $aryEstimateDetailSales );


	// ɸ�������
	$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

	// ����US�ɥ�졼�ȼ���
	$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

	// �׻���̤����
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	$aryEstimateData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];



	// �١����ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );

	// �١����ƥ�ץ졼������
	$objTemplate->replace( $aryEstimateData );
	$objTemplate->replace( $aryEstimateDetail );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;
	//echo getArrayTable( $baseData, "TABLE" )

	$objDB->close();


	return TRUE;
?>
