<?
/** 
*	���Ѹ������� �ǡ������ϲ���
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// ������Ͽ
// index.php -> strSessionID           -> edit.php
// index.php -> lngFunctionCode        -> edit.php
//
// ����
// result/index.php -> strSessionID		-> edit.php
// result/index.php -> lngFunctionCode	-> edit.php
// result/index.php -> lngEstimateNo	-> edit.php
//
// ��ǧ���̤�������
// confirm.php -> strSessionID           -> edit.php
// confirm.php -> lngFunctionCode        -> edit.php
// confirm.php -> lngEstimateNo			-> edit.php���Ѹ����ֹ�
// confirm.php -> strProductCode		-> edit.php���ʥ�����
// confirm.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][lngStockItemCode]		-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][curProductRate]		-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]		-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][strNote]				-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> edit.php
// confirm.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> edit.php
//
// ��ǧ��
// edit.php -> strSessionID			-> confirm.php
// edit.php -> lngFunctionCode		-> confirm.php
// edit.php -> lngEstimateNo		-> confirm.php���Ѹ����ֹ�
// edit.php -> strProductCode		-> confirm.php���ʥ�����
// edit.php -> bytDecisionFlag		-> confirm.php����ե饰
// edit.php -> lngWorkflowOrderCode	-> confirm.php��ǧ�롼��
// edit.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][lngStockItemCode]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][curProductRate]		-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][strNote]				-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> confirm.php
// edit.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> confirm.php
//
// ����¸��
// edit.php -> strSessionID			-> action.php
// edit.php -> lngFunctionCode		-> action.php
// edit.php -> lngEstimateNo		-> action.php���Ѹ����ֹ�
// edit.php -> strProductCode		-> action.php���ʥ�����
// edit.php -> bytDecisionFlag		-> action.php����ե饰
// edit.php -> lngWorkflowOrderCode	-> action.php��ǧ�롼��
// edit.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][lngStockItemCode]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> action.php
// edit.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][curProductRate]		-> action.php
// edit.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> action.php
// edit.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][strNote]				-> action.php
// edit.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> action.php
// edit.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> action.php

// �����ɤ߹���
include_once('conf.inc');
require( LIB_DEBUGFILE );

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "estimate/cmn/lib_e.php");

// ��ǧ�롼�ȥץ������������ɬ��
require(SRC_ROOT."po/cmn/lib_po.php");


require ( CLS_TABLETEMP_FILE );	// Temporary DB Object
require ( LIB_ROOT . "tabletemp/excel2temp.php" );



// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GET�ǡ�������
/*
if ( $_GET )
{
	$aryData = $_GET;
}
else
{
	$aryData = $_POST;
}
*/
$aryData = $_REQUEST;

$aryDetail = $aryData["aryDitail"];



fncDebug( 'estimate_regist_edit.txt', $aryData["aryDitail"], __FILE__, __LINE__);


	// Temp����
	$g_aryTemp	= $aryData;

	// �ե�����ƥ�ݥ��ե饰ͭ���ξ�硢
	// ��ǧ���̤ؾ��������Ѥ����ᡢ�ե��������ʥإå�������ݻ�
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
//		$aryTempHidden	= array();


// lngCartonQuantity
// lngProductionQuantity


		$aryData["temp_bytTemporaryFlg"]			= "<input type=\"hidden\" name=\"bytTemporaryFlg\" value=\"" .$g_aryTemp["bytTemporaryFlg"]. "\" />";
		$aryData["temp_curStandardRate"]			= "<input type=\"hidden\" name=\"curStandardRate\" value=\"" .$g_aryTemp["curStandardRate"]. "\" />";
		$aryData["temp_curConversionRate"]			= "<input type=\"hidden\" name=\"curConversionRate\" value=\"" .$g_aryTemp["curConversionRate"]. "\" />";

		$aryData["temp_strProductName"]				= "<input type=\"hidden\" name=\"strProductName\" value=\"" .$g_aryTemp["strProductName"]. "\" />";
		$aryData["temp_dtmDeliveryLimitDate"]		= "<input type=\"hidden\" name=\"dtmDeliveryLimitDate\" value=\"" .$g_aryTemp["dtmDeliveryLimitDate"]. "\" />";
		$aryData["temp_strGroupDisplayCode"]		= "<input type=\"hidden\" name=\"strGroupDisplayCode\" value=\"" .$g_aryTemp["strGroupDisplayCode"]. "\" />";
		$aryData["temp_strUserDiplayCode"]			= "<input type=\"hidden\" name=\"strUserDiplayCode\" value=\"" .$g_aryTemp["strUserDiplayCode"]. "\" />";
		$aryData["temp_strUserDisplayName"]			= "<input type=\"hidden\" name=\"strUserDisplayName\" value=\"" .$g_aryTemp["strUserDisplayName"]. "\" />";
		$aryData["temp_curRetailPrice"]				= "<input type=\"hidden\" name=\"curRetailPrice\" value=\"" .$g_aryTemp["curRetailPrice"]. "\" />";

		$aryData["temp_lngCartonQty"]				= "<input type=\"hidden\" name=\"lngCartonQuantity\" value=\"" .$g_aryTemp["lngCartonQuantity"]. "\" />";

		$aryData["temp_lngPlanCartonProduction"]	= "<input type=\"hidden\" name=\"lngPlanCartonProduction\" value=\"" .$g_aryTemp["lngPlanCartonProduction"]. "\" />";

		$aryData["curProductPrice"]					= "<input type=\"hidden\" name=\"curProductPrice\" value=\"" .$g_aryTemp["curProductPrice"]. "\" />";

		$aryData["temp_lngProductionQuantity_hidden"]	= "<input type=\"hidden\" name=\"lngProductionQuantity_hidden\" value=\"" .$g_aryTemp["lngProductionQuantity_hidden"]. "\" />";
		$aryData["temp_curProductPrice_hidden"]			= "<input type=\"hidden\" name=\"curProductPrice_hidden\" value=\"" .$g_aryTemp["curProductPrice_hidden"]. "\" />";


		$aryData["temp_strRemark_hidden"]			= "<input type=\"hidden\" name=\"strRemark\" value=\"" .$g_aryTemp["strRemark"]. "\" />";

		$aryData["RENEW"]	= true;	// ����ɽ���⡼�ɡ����� �����������ǤϤʤ�
	}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);

unset ( $aryData["aryDitail"] );





// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

$lngUserCode = $objAuth->UserCode;

// ���³�ǧ
// ��Ͽ�ξ��
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )
{
}

// �����ξ��
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 && fncCheckAuthority( DEF_FUNCTION_E3, $objAuth ) )
{
	$aryCheck["lngEstimateNo"] = "null:number(0,32767)";
}

// ����ʳ�
else
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
}


$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E3 . ")";

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// ���ϥߥ��ˤ�����ʳ��ξ�硢���顼ɽ������ɽ��������
if ( !preg_match ( "/confirm\.php/", $_SERVER["HTTP_REFERER"] ) )
{
	$aryData["strProductCode_Error"] = "visibility:hidden;";
}



//fncDebug( 'estimate_regist_edit.txt', $_SERVER["HTTP_REFERER"], __FILE__, __LINE__);


// ���ʥ����ɻ������
if ( $aryData["strMode"] == "onchange" and $aryData["strProductCode"] != "" )
{
	// ���ʥ����ɤ����ꤵ�줿���֤�ȿ�ǥܥ��󤬲����줿��硢���ʾ�������ꤹ��
	$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );


	// ���ʾ��֥����å� -> �ֿ�����פξ�硢������λ
	if( $aryProduct["lngproductstatuscode"] == DEF_PRODUCT_APPLICATE )
	{
		fncOutputError ( 308, DEF_WARNING, "", TRUE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}


	// ���ʤ�¸�ߤ��ʤ����
	if ( $aryProduct == FALSE )
	{
		// ���Ϥ��줿���ʥ����ɤ�¸�ߤ��ʤ���硢���顼���Ƥ�إå�����ɽ�����������Ԥ�
		$strErrorMessage = fncOutputError( 1504, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );

		// ��å�����ɽ���ս�˥�å�����������
		$aryData["strHeaderErrorMessage"] = $strErrorMessage;
	}
	// ���ʤ�¸�ߤ�����
	else
	{
		// �������ʥ����ɤˤƴ��˸��Ѿ��󤬺�������Ƥ��ʤ����ɤ����Υ����å�

		// ���Ѹ����ǡ�������
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );


fncDebug( 'es_edit.txt', $g_aryTemp, __FILE__, __LINE__);

		// ���Ѹ����ǡ�����¸�ߤ�����
		if ( $aryEstimate != FALSE )
		{
			// �ե�����ƥ�ݥ������ʳ��ξ��
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
				$strErrorMessage = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );

				// ��å�����ɽ���ս�˥�å�����������
				$aryData["strHeaderErrorMessage"] = $strErrorMessage;
			}
		}


		//========================================================================
		// 050223 by kou ���ʤξ��塢Ǽ��������ͽ�����Ƴ�ǧ��å�������Ф�
		//���Ѻ������줿���ϥǡ������ɤ߹��ޤʤ��褦���ѹ�
		else
		{
			//���ʤξ��塢Ǽ��������ͽ�����Ƴ�ǧ��å�������Ф�
			$strErrorMessage = fncOutputError ( 1508, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );

			// ��å�����ɽ���ս�˥�å�����������
			$aryData["strHeaderErrorMessage"] = $strErrorMessage;

			// ����Υޡ�������
			$aryData = array_merge( $aryData, $aryProduct );


			// ���Ѹ����ǥե�������٥ǡ�������
			$aryDetail = fncGetEstimateDefaultValue( $aryData["lngProductionQuantity"], $aryData["curProductPrice"], $aryRate, $objDB, $aryData["strSessionID"] );
		}
//============================================================================
	}


	unset( $aryProduct );

	// ����HIDDENʸ�������
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );


//fncDebug( 'es_array.txt', $aryDetail, __FILE__, __LINE__);


	if ( is_array($aryHiddenString) )
	{
		$aryData["strDetailData"] = join ( "", $aryHiddenString );
	}

	$aryData["strMode"] = "";

	$aryData["dtmInsertDate"] = date("Y/m/d");

}
else if ( $aryData["strProductCode"] != "" )
{
	// ���ʾ������
	$aryData = array_merge( $aryData, fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode ) );

	$aryData["dtmInsertDate"] = date("Y/m/d");
}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


// 2004.10.05 suzukaze update start
// ��Ͽ������¸�ܥ��󲡲����Υ���ɤ���ӳ�ǧ���¹Բ������ӽ���
if ( $aryData["strActionName"] != "" )
{
	// ���ϥ����å���Ԥ�
	// �����å�����

	// ��Ͽ������������
	// ���ʥ����ɤ����ꤵ��Ƥ��뤫�ɤ�����
	// ���ʥ����ɤ�����ʤ�ΤʤΤ��ɤ���
	// ���ʥ����ɤ����ꤵ��Ƥ����Ǽ������Ͽ�ե���������ꤵ��Ƥ��뤫�ɤ���

	// ��Ͽ��
	// ���ꤵ��Ƥ������ʥ����ɤˤƸ��Ѿ������ꤵ��Ƥ��ʤ����ɤ���������Ƥ���Х��顼

	$lngErrorCount = 0;

	if ( $aryData["strProductCode"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	// Ǽ�����������ͽ��������ꤵ��Ƥ��ʤ�������ʾ���ȿ�Ǥ���Ƥ��ʤ��Ȥߤʤ�
	else if ( $aryData["curProductPrice_hidden"] == "" and $aryData["lngProductionQuantity_hidden"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	else
	{
		// ���ʥ����ɾ�����������
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
		}
	}

	// ��Ͽ�ˤ����ʥ����ɤ����ꤵ��Ƥ�����Τ�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// ���ʾ���¸�ߤ���������ʻ��Ѳ�ǽ���ɤ����Υ����å�
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );

		if ( $aryEstimate != FALSE )
		{
			// �ե�����ƥ�ݥ������ʳ��ξ��
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				$lngErrorCount++;
				// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
				$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
			}
		}
	}


//fncDebug( 'temp_edit.txt', $aryData, __FILE__, __LINE__);


	// �����ξ�硢�������¥����å�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
	{
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

		// (������桼���������Ϥ�����Τ��Ĳ���¸����)�ʳ��Τ�Ρ�
		// �ޤ��ϡ�������Τ�Τϡ������ԲĤȤ��ƥ��顼����
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
		{
			$lngErrorCount++;
			// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1503, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
		}
	}



	if ( $lngErrorCount == 0 )
	{
		// ����HIDDENʸ�������
		list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
		if ( is_Array( $aryHiddenString ) )
		{
			$aryData["strDetailData"] = join ( "", $aryHiddenString );
		}

		$aryData["strProcess"] = "confirm";

		$aryData["lngRegistConfirm"] = 1;

		$aryData["strPageCondition"] = "regist";

		// URL���å�
		$aryData["filename"] = "confirm.php";



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
			$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode, $objDB , $aryData["lngWorkflowOrderCode"] );
		}



// 2004.10.06 tomita update start
		if ( $aryData["strActionName"] == "regist" )
		{
			$aryData["strurl"]        = "/estimate/regist/confirm.php";
			$aryData["strActionName"] = "regist";
		}
		else if ( $aryData["strActionName"] == "temporary" )
		{
			$aryData["strurl"]        = "/estimate/regist/action.php";
			$aryData["strActionName"] = "temporary";
		}
		//$aryData["strurl"] = "/estimate/regist/confirm.php?strSessionID=".$aryData["strSessionID"];
		//echo getArrayTable( $aryData, "TABLE" );exit;
// 2004.10.06 tomita update end

		$aryData["lngSetValue"] = 1;

		$aryData["strActionFile"] = "edit.php";


		$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����




		// �ե�����ƥ�ݥ��ե饰ͭ���ξ�硢
		// ��ǧ���̤ؾ��������Ѥ����ᡢ�ե��������ʥإå�������ݻ�
		if( $g_aryTemp["bytTemporaryFlg"] )
		{
			$aryData["temp_lngCartonQty"]				= "<input type=\"hidden\" name=\"lngCartonQuantity\" value=\"" .$aryData["lngCartonQuantity"]. "\" />";

//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);
		}



		echo fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth );

		$objDB->close();

		return true;
	}
	else
	{
		$aryData["strProcess"] = "regist";
	}
}



//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);

// 2004.09.27 suzukaze update start
// ���ʥ����ɤ���ꤷ��ȿ�ǥܥ��󤬲������줿���ν���������
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
{
	$aryData["ProductSubmit"] = "fncProductSubmit();";
	$aryData["strProcess"]    = "regist";

	// ��Ͽ�ξ������ʸ����ܥ������Ѳ�ǽ�Ȥ���
	$aryData["btnMSWBt03_Editable"] = "fncGetObjectName( window.MGwin , strProductCode , strProductName );DisplayerM03( '' , document.all.Mdata03 , window.MGwin.document.all.strProductCode  );ExchangeM03( 0 , window.self );fncFocusType( window.MGwin , 'productsTop' );";

	// �ե�����ƥ�ݥ������ξ��
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
		$aryData["ProductSubmit"] = "";

		// ���ʥ����ɤ��Խ��Բ�ǽ�Ȥ���
		$aryData["strProductCode_Editable"] = "contenteditable=\"false\"";
	}
}
else
{
	$aryData["ProductSubmit"] = "";
	$aryData["strProcess"]    = "regist";

	// �����ξ������ʥ����ɤ��Խ��Բ�ǽ�Ȥ���
	$aryData["strProductCode_Editable"] = "contenteditable=\"false\"";
}

// �̲ߥ�����->�̲ߵ���(JAVASCRIPT����)
$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );

// �̲ߥ졼����������
$aryRate = fncGetMonetaryRate( $objDB );
$aryRate[DEF_MONETARY_YEN] = 1;


// �̲ߥ졼�����󤫤�HIDDEN����
$aryMonetaryUnitData = Array();
$aryKeys = array_keys ( $aryRate );
foreach ( $aryKeys as $strKey )
{
	$aryMonetaryUnitData[] = "<input type='hidden' name='lngMonetaryUnitCode[" . $aryMonetaryUnit[$strKey] . "]' value='" . $aryRate[$strKey] . "' >\n";
}
unset ( $aryKeys );
unset ( $strKey );


// ��Ͽ���Ĥ�ɤ�ξ��
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && $aryData["strProductCode"] != "" )
{
	$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
	// ���ʾ������
	$aryData = array_merge( $aryData, is_array($aryProduct) ? $aryProduct : array($aryProduct) );

	// ����HIDDENʸ�������
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
	if ( is_Array( $aryHiddenString ) )
	{
		$aryData["strDetailData"] = join ( "", $aryHiddenString );
	}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


// 2004.10.02 suzukaze update start
	$aryData["strPageCondition"] = "regist";
// 2004.10.02 suzukaze update end

	$aryData["dtmInsertDate"] = date("Y/m/d");
}

// �����ξ��
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
{
	// ���Ѹ����ǡ�������
	$aryData = array_merge ( $aryData, fncGetEstimate( $aryData["lngEstimateNo"], $objDB ) );

	// ((������桼���������Ϥ�����Τ��Ĳ���¸���֤Τ��)��
	// �ޤ��ϡ�������ʳ��Τ��)�ʳ��ϡ������ԲĤȤ��ƥ��顼����
	if ( !( ( $aryData["bytDecisionFlag"] == "f" && $aryData["lngInputUserCode"] == $objAuth->UserCode ) || $aryData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
	{
		fncOutputError ( 1503, DEF_WARNING, "", TRUE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}

	// ����̵����硢���٥ǡ�������
	if ( $aryData["bytReturnFlag"] != "true" )
	{
		// ���Ѹ������٥ǡ�������
		$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryRate, $objDB );
		unset ( $aryCalculated );
	}

	// ����HIDDENʸ�������
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
	$aryData["strDetailData"] = join ( "", $aryHiddenString );

// 2004.10.02 suzukaze update start
	$aryData["strPageCondition"] = "renew";
// 2004.10.02 suzukaze update end

// 2004.10.04 tomita update start
	$aryData["RENEW"] = TRUE;
// 2004.10.04 tomita update end
}
else
{
	$aryData["dtmInsertDate"] = date("Y/m/d");
}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


// ����޽���
$aryData = fncGetCommaNumber( $aryData );








unset ( $aryDetail );
unset ( $aryCalculated );
unset ( $aryHiddenString );


// �̲ߥ졼��HIDDEN����
$aryData["strMonetaryUnitData"] = join ( "", $aryMonetaryUnitData );
unset ( $aryMonetaryUnitData );





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
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode, $objDB , $aryData["lngWorkflowOrderCode"] );
	}



unset ( $lngResultID );
unset ( $lngResultNum );
unset ( $aryMonetaryUnit );


// 2004.09.29 suzukaze update start
// �������ƤΥ����å�
if( $aryData["strProcess"] == "check" )
{

	// �����å�����

	// ��Ͽ������������
	// ���ʥ����ɤ����ꤵ��Ƥ��뤫�ɤ�����
	// ���ʥ����ɤ�����ʤ�ΤʤΤ��ɤ���
	// ���ʥ����ɤ����ꤵ��Ƥ����Ǽ������Ͽ�ե���������ꤵ��Ƥ��뤫�ɤ���

	// ��Ͽ��
	// ���ꤵ��Ƥ������ʥ����ɤˤƸ��Ѿ������ꤵ��Ƥ��ʤ����ɤ���������Ƥ���Х��顼

	$lngErrorCount = 0;

	if ( $aryData["strProductCode"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	// Ǽ�����������ͽ��������ꤵ��Ƥ��ʤ�������ʾ���ȿ�Ǥ���Ƥ��ʤ��Ȥߤʤ�
	else if ( $aryData["curProductPrice"] == "" and $aryData["lngProductionQuantity"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	else
	{
		// ���ʥ����ɾ�����������
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
		}
	}

	// ��Ͽ�ˤ����ʥ����ɤ����ꤵ��Ƥ�����Τ�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// ���ʾ���¸�ߤ���������ʻ��Ѳ�ǽ���ɤ����Υ����å�
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );

		if ( $aryEstimate != FALSE )
		{
			// �ե�����ƥ�ݥ������ʳ��ξ��
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				$lngErrorCount++;
				// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
				$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
			}
		}
	}

	if ( $lngErrorCount == 0 )
	{
		$aryData["strProcess"] = "confirm";

		$aryData["lngRegistConfirm"] = 1;
	}
	else
	{
		$aryData["strProcess"] = "regist";
	}
}

// URL���å�
$aryData["filename"] = "edit.php";

$aryData["strActionFile"] = "edit.php";


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����







//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo getArrayTable( $aryData, "TABLE" );exit;

//fncDebug( 'estimate.txt', fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth ), __FILE__, __LINE__);
echo fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth );


$objDB->close();
unset ( $aryData );
unset ( $objAuth );
unset ( $objDB );


return TRUE;
?>
