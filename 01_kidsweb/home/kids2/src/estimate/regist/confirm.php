<?
/** 
*	���Ѵ��� ��ǧ����
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*	��������
*	2004.04.14	���Ѥ��ܵҤǤ��ä����ˡ�ɽ������ɽ�����ڤ��ؤ����ʤ��Х��ν���
*
*/
// edit.php -> strSessionID			-> confirm.php
// edit.php -> lngFunctionCode		-> confirm.php
// edit.php -> lngEstimateNo		-> confirm.php���Ѹ����ֹ�
// edit.php -> strProductCode		-> confirm.php���ʥ�����
// edit.php -> strActionName		-> confirm.php�¹Խ���̾(confirm or temporary)
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

// �¹Ԥ�
// confirm.php -> strSessionID			-> action.php
// confirm.php -> lngFunctionCode		-> action.php
// confirm.php -> lngEstimateNo			-> action.php���Ѹ����ֹ�
// confirm.php -> strProductCode		-> action.php���ʥ�����
// confirm.php -> strActionName			-> action.php�¹Խ���̾(confirm or temporary)
// confirm.php -> lngWorkflowOrderCode	-> action.php��ǧ�롼��
// confirm.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][lngStockItemCode]		-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][curProductRate]		-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]		-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][strNote]				-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> action.php
// confirm.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> action.php




	// �����ɤ߹���
	include_once('conf.inc');
	require_once( LIB_DEBUGFILE );



	//echo mb_internal_encoding();
	//mb_http_output( "EUC-JP" );
	//echo mb_http_output();



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

	// ���ʥ�����5�岽��ȼ�äơ�4��θ��ѥե����뤬���åץ��ɤ��줿�ݤ�
	// 5��˳�ĥ���ƥޥ���������»ܤ���
	if (strlen($aryData["strProductCode"]) == 4)
	{
		$aryData["strProductCode"] = '0'.$aryData["strProductCode"];
	}

		// Temp����
		$g_aryTemp	= $aryData;

	//fncDebug( 'temp_renew.txt', $aryData, __FILE__, __LINE__);



	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	$aryDetail = $aryData["aryDetail"];
	if ( !is_Array( $aryData ) )
	{
		$aryDetail = $aryData["strDetailData"];
	}
	unset ( $aryData["aryDetail"] );
	//echo getArrayTable( $aryDetail[0][1], "TABLE" );exit;


	//fncDebug( 'estimate_regist_confirm.txt', $aryData, __FILE__, __LINE__);

	// ---------------------------------------------------------------------------------------------------------------------------------------

	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]		= "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E5 . ")";
	$aryCheck["lngWorkflowOrderCode"]	= "null:number(0,32767)";


	//$aryData["bytInvalidFlag_Error"]     = "visibility:hidden;";

	$lngErrorCount = 0;


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;

	// ���³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ������Ͽ�ξ��
	//////////////////////////////////////////////////////////////////////////
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )
	{
		$aryCheck["strProductCode"] = "null:numenglish(1,6)";
	}

	//////////////////////////////////////////////////////////////////////////
	// ���ѽ����ξ��
	//////////////////////////////////////////////////////////////////////////
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 && fncCheckAuthority( DEF_FUNCTION_E3, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(1,2147483647)";

	}

	//////////////////////////////////////////////////////////////////////////
	// ���Ѻ���ξ��
	//////////////////////////////////////////////////////////////////////////
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && fncCheckAuthority( DEF_FUNCTION_E4, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(1,2147483647)";
		unset ( $aryCheck["lngWorkflowOrderCode"] );
	}

	//////////////////////////////////////////////////////////////////////////
	// ����ʳ�(����ERROR)
	//////////////////////////////////////////////////////////////////////////
	else
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}


	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	//fncPutStringCheckError( $aryCheckResult, $objDB );

	//fncDebug( 'estimate_regist_confirm.txt', $aryCheckResult, __FILE__, __LINE__);



	// �ե�����ƥ�ݥ������ʳ��ξ��
	if( !$g_aryTemp["bytTemporaryFlg"] )
	{
		//////////////////////////////////////////////////////////////////////////
		// ��Ͽ�ξ��δ��˥ǡ��������뤫�����å�
		//////////////////////////////////////////////////////////////////////////
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
		{
			if ( $aryData["strProductCode"] == "" )
			{
				fncOutputError ( 1505, DEF_WARNING, "���Ѹ������ɽ���Ǥ��ޤ���", TRUE, "", $objDB );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT * FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "'", $objDB );

			if ( $lngResultNum > 0 )
			{
				$objDB->freeResult( $lngResultID );
				fncOutputError ( 1501, DEF_WARNING, "���˸��Ѹ�������Ͽ�Τ������ʤǤ���", TRUE, "", $objDB );
			}
		}
	}


	//////////////////////////////////////////////////////////////////////////
	// ����ξ�硢�����θ��Ѹ����ǡ�������
	//////////////////////////////////////////////////////////////////////////
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
	{
		//-------------------------------------------------------------------------
		// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
		//-------------------------------------------------------------------------
		$strFncFlag = "ES";
		$blnCheck = fncCheckInChargeProduct( $aryData["lngEstimateNo"], $lngUserCode, $strFncFlag, $objDB );

		// �桼�������о����ʤ�°���Ƥ��ʤ����
		if( !$blnCheck )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}



		// �̲ߥ졼����������
		$aryRate = fncGetMonetaryRate( $objDB );
		$aryRate[DEF_MONETARY_YEN] = 1;

		// ���Ѹ���HTML���ϥǡ�������
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );
		$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryRate, $objDB );


		// �����ȡʥХåե��˼���
		$strBuffRemark	= $aryEstimateData["strRemark"];


	//fncDebug( 'es_delete.txt', $aryEstimateData, __FILE__, __LINE__);

		list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );


		// �׻���̤��Ѹ���������Ȥ߹���
		$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );


		unset ( $aryDetail );
		unset ( $aryCalculated );

		// (������桼���������Ϥ�����Τ��Ĳ���¸����)�ʳ��Τ�Ρ�
		// �ޤ��ϡ�������Τ�Τϡ������ԲĤȤ��ƥ��顼����
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
		{
			fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
		}



		// �ƥ�ݥ������ξ��
		if( $aryEstimateData["blnTempFlag"] )
		{
			// ɸ�������
			$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

			// ����US�ɥ�졼�ȼ���
			$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

			// Excelɸ�������
	//		$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
			// Excel����US�ɥ�졼�ȼ���
	//		$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
		}
		// �̾�
		else
		{
			// ɸ�������
			$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

			// ����US�ɥ�졼�ȼ���
			$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
		}


		// �׻���̤����
		$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	//fncDebug( 'es_delete.txt', $aryEstimateData, __FILE__, __LINE__);


	}
	// --------------------------------------------------------------------------------------------------------------------------------------------------
	// ����ʳ��ǥ��顼��̵����Хǡ�������
	// --------------------------------------------------------------------------------------------------------------------------------------------------
	elseif ( $lngErrorCount < 1 )
	{


		// �����ξ�硢�������¥����å�
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
		{
			$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

			// (������桼���������Ϥ�����Τ��Ĳ���¸����)�ʳ��Τ�Ρ�
			// �ޤ��ϡ�������Τ�Τϡ������ԲĤȤ��ƥ��顼����


			// �����ȡʥХåե��˼���
			$strBuffRemark	= $aryEstimateData["strRemark"];


	/*
			if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
			{
				fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
			}
			unset ( $aryEstimateData );
	*/
		}


//fncDebug( 'es_renew.txt', $aryEstimateData, __FILE__, __LINE__);



		/*-----------------------------------------------------------------------*/
		// ���ʾ������
		$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );


fncDebug( 'estimate_regist_confirm_03.txt', $aryEstimateData, __FILE__, __LINE__);



//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);


		// ��Ͽ���������γ�ǧ���̤ǤϺ������ϼ¹����Ȥ���
		$aryEstimateData["dtmInsertDate"]	= date("Y/m/d");


		// �ƥ�ݥ������ξ��
		if( $aryEstimateData["blnTempFlag"] )
		{
			// ɸ�������
			$aryEstimateData["curStandardRate"]	= fncGetEstimateDefault( $objDB );

			// ����US�ɥ�졼�ȼ���
			$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

			// Excelɸ�������
	//		$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
			// Excel����US�ɥ�졼�ȼ���
	//		$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
		}
		// �ե�����ƥ�ݥ������ξ��
		else if( $g_aryTemp["bytTemporaryFlg"] )
		{
			// �����ȡʥХåե��˼���
			$strBuffRemark	= $aryEstimateData["strRemark"];


			// ɸ�������
			$aryEstimateData["curStandardRate"]	= fncGetEstimateDefault( $objDB );

			// ����US�ɥ�졼�ȼ���
			$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

			// Excelɸ�������
	//		$aryEstimateData["curStandardRate"]		= $g_aryTemp["curStandardRate"];
			// Excel����US�ɥ�졼�ȼ���
	//		$aryEstimateData["curConversionRate"]	= $g_aryTemp["curConversionRate"];
		}
		// �̾�
		else
		{
			// ɸ�������
			$aryEstimateData["curStandardRate"]	= fncGetEstimateDefault( $objDB );

			// ����US�ɥ�졼�ȼ���
			$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
		}

fncDebug( 'estimate_regist_confirm_03.1.txt', $aryDetail, __FILE__, __LINE__);





	//	// ��Ͽ���������γ�ǧ���̤ǤϺ������ϼ¹����Ȥ���
	//	$aryEstimateData["dtmInsertDate"]     = date("Y/m/d");

		// ���Ѹ����׻�����HTML����ʸ�������
		list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

		// �׻���̤��Ѹ���������Ȥ߹���
		$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

//fncDebug( 'es_temp.txt', $aryHiddenString, __FILE__, __LINE__);

//		unset ( $aryDetail );
		unset ( $aryCalculated );

		// ���١����ˤ�����
		list($aryEstimateDetailSales, $curFixedCostSales, $aryHiddenStringSales) = fncGetEstimateDetail_Sales_Html( $aryDetail, "estimate/regist/plan_detail_sales.tmpl", $objDB );


		$aryEstimateDetail	= array_merge( $aryEstimateDetail, $aryEstimateDetailSales );
		$aryEstimateData["curFixedCostSales"]	= $curFixedCostSales;	// 1:���������ι��
		$aryHiddenString	= array_merge( is_array($aryHiddenString)?$aryHiddenString:(array)$aryHiddenString, 	$aryHiddenStringSales );

fncDebug( 'estimate_retist_confirm_aryEstimateData.txt', $aryEstimateData, __FILE__, __LINE__);

		// �׻���̤����
		$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

//fncDebug( 'es_temp.txt', $aryEstimateData, __FILE__, __LINE__);
		/*-----------------------------------------------------------------------*/
	}



	// ���顼����ɽ������
	list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
	$lngErrorCount += $bytErrorFlag;


	//////////////////////////////////////////////////////////////////////////
	// ��̼��������Ͻ���
	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å��˥��顼�������硢���ϲ��̤����

	//���顼��¸�ߤ��뤫��(��ǧ�ܥ���ˤ��ɽ���ޤ��Ϻ������)�ξ�硢���顼����
	//if( $lngErrorCount > 0 && ( $aryData["lngActionCode"] == "confirm" || $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 ) )
	if( $lngErrorCount > 0 && ( $aryData["strActionName"] == "confirm" || $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 ) )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
		exit;


	}
	//���顼�����ä���
	elseif( $lngErrorCount > 0 )
	{
		//echo getArrayTable( $aryData, "TABLE" );exit;
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">\n";
		echo "<form action=\"/estimate/regist/edit.php\" method=\"POST\">\n";
		echo getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );
		echo getArrayTable( fncToHTMLString( $aryEstimateData ), "HIDDEN" );
		echo getArrayTable( fncToHTMLString( $aryEstimateDetail ), "HIDDEN" );
		echo "</form>\n";
		echo "<script language=\"javascript\">document.forms[0].submit();</script>";
	}
	//���顼���ʤ��ä���
	else
	{

//fncDebug( 'estimate_regist_confirm_02.txt', $aryData["strActionName"], __FILE__, __LINE__);

		// ��ǧ�ǤϤʤ��ä��顢��������ɤ�ܥ���ɽ��
		if ( $aryData["strActionName"] != "confirm" )
		{
			$aryData["bytReturnFlag"] = "true";

			$aryHiddenString[] = getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );

fncDebug( 'estimate_regist_confirm_04.txt', $aryHiddenString, __FILE__, __LINE__);


			$strHiddenString = join( "", $aryHiddenString );
			unset ( $aryHiddenString );

			/*
			$aryForm[] = "<form action=\"action.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;
			$aryForm[] = "<input type=submit value='�¹�'>\n";
			$aryForm[] = "</form>\n";
			$aryForm[] = "<form action=\"edit.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;
			$aryForm[] = "<input type=hidden name=bytReturnFlag value=true>\n";
			$aryForm[] = "<input type=submit value='��ɤ�'>\n";
			$aryForm[] = "</form>\n";
			*/





			/*---------------------------------------------------------------------
				FORM����
			---------------------------------------------------------------------*/
			$aryForm[] = "<form name=frmAction action=\"action.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;

			// �ƥ�ݥ�����
			if( $aryEstimateData["blnTempFlag"] )
			{
				// �ƥ�ݥ��ե饰
				$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
				// ������
				$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";
			}
			// �ե�����ƥ�ݥ�����
			else if( $aryData["bytTemporaryFlg"] )
			{
				// �ƥ�ݥ��ե饰
	//			$aryForm[] = "<input type=\"hidden\" name=\"bytTemporaryFlg\"	value=\"" .$aryData["bytTemporaryFlg"]. "\" />\n";
				// ������
	//			$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";
				// Excelɸ����
	//			$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryData["curStandardRate"]. "\" />\n";
				// Excel����US�ɥ�졼��
	//			$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryData["curConversionRate"]. "\" />\n";
			}

			$aryForm[] = "</form>\n";

			$aryForm[] = "<form name=frmEdit action=\"edit.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;

			// �ƥ�ݥ�����
			if( $aryEstimateData["blnTempFlag"] )
			{
				// �ƥ�ݥ��ե饰
				$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
				// ������
				$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";
			}
			// �ե�����ƥ�ݥ�����
			else if( $aryData["bytTemporaryFlg"] )
			{
				// �ƥ�ݥ��ե饰
	//			$aryForm[] = "<input type=\"hidden\" name=\"bytTemporaryFlg\"	value=\"" .$aryData["bytTemporaryFlg"]. "\" />\n";
				// ������
	//			$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$astrBuffRemark. "\" />\n";
				// Excelɸ����
	//			$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryData["curStandardRate"]. "\" />\n";
				// Excel����US�ɥ�졼��
	//			$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryData["curConversionRate"]. "\" />\n";
			}

			$aryForm[] = "<input type=hidden name=bytReturnFlag value=true>\n";
			$aryForm[] = "</form>\n";





			$aryEstimateData["FORM"] = join ( "", $aryForm );
			unset ( $aryForm );
		}

		unset ( $strHiddenString );

		$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

		// ����ξ��
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
		{
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "estimate/regist/plan_button_delete.tmpl" );

			$objTemplate->complete();
			$aryData["BUTTON"] = $objTemplate->strTemplate;
			$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/confirm_delete_exstr.js\"></script>";

			$aryData["strScrollType"] = "ScrollAuto";
		}
		// ��Ͽ�������ξ��
		else if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
		{
			if ( $aryData["strActionName"] != "confirm" )
			{
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "estimate/regist/plan_button_regist.tmpl" );

				$objTemplate->complete();
				$aryData["BUTTON"] = $objTemplate->strTemplate;
				$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/confirm_exstr.js\"></script>";
			}
			else
			{
				$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/detail_exstr.js\"></script>";
			}
		}

	/*
	// 2004.10.05 suzukaze update start
		if ( $aryData["strActionName"] != "confirm" )
		{
			$aryData["strScrollType"] = "ScrollAuto";
		}
		else
		{
			$aryData["strScrollType"] = "ScrollHidden";
		}
	// 2004.10.05 suzukaze update end
	*/


		// ������ <br /> �ղ�
		$aryData["strRemarkDisp"]	= nl2br($strBuffRemark);

	 	// ����޽���
		$aryEstimateData	= fncGetCommaNumber( $aryEstimateData );

		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );

		// �֤�����
	// 2004.10.05 tomita update start
		$objTemplate->replace( $aryData );
	// 2004.10.05 tomita update end

	//echo getArrayTable( $aryData, "TABLE" );exit;
		$objTemplate->replace( $aryEstimateData );
		$objTemplate->replace( $aryEstimateDetail );

		$objTemplate->complete();
		echo $objTemplate->strTemplate;

fncDebug( 'estimate_regist_confirm.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

// debug
//fncDebug( 'es_detial.txt', $aryData, __FILE__, __LINE__);
//fncDebug( 'es_post.txt', $_REQUEST, __FILE__, __LINE__);

	}


	unset ( $aryEstimateData );
	unset ( $aryEstimateDetailData );
	unset ( $aryData );
	unset ( $g_aryTemp );


	$objDB->close();


	return TRUE;
?>


