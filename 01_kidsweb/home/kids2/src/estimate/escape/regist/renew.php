<?
/** 
*	���Ѹ������� �ǡ������ϲ��̡ʹ��������ѡ�
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// ������Ͽ
// index.php -> strSessionID           -> renew.php
// index.php -> lngFunctionCode        -> renew.php
//
// ����
// result/index.php -> strSessionID		-> renew.php
// result/index.php -> lngFunctionCode	-> renew.php
// result/index.php -> lngEstimateNo	-> renew.php
//
// ��ǧ���̤�������
// confirm.php -> strSessionID           -> renew.php
// confirm.php -> lngFunctionCode        -> renew.php
// confirm.php -> lngEstimateNo			-> renew.php���Ѹ����ֹ�
// confirm.php -> strProductCode		-> renew.php���ʥ�����
// confirm.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][lngStockItemCode]		-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][curProductRate]		-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]		-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][strNote]				-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> renew.php
// confirm.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> renew.php
//
// ��ǧ��
// renew.php -> strSessionID			-> confirm.php
// renew.php -> lngFunctionCode		-> confirm.php
// renew.php -> lngEstimateNo		-> confirm.php���Ѹ����ֹ�
// renew.php -> strProductCode		-> confirm.php���ʥ�����
// renew.php -> bytDecisionFlag		-> confirm.php����ե饰
// renew.php -> lngWorkflowOrderCode	-> confirm.php��ǧ�롼��
// renew.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][lngStockItemCode]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][curProductRate]		-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][strNote]				-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> confirm.php
// renew.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> confirm.php
//
// ����¸��
// renew.php -> strSessionID			-> action.php
// renew.php -> lngFunctionCode		-> action.php
// renew.php -> lngEstimateNo		-> action.php���Ѹ����ֹ�
// renew.php -> strProductCode		-> action.php���ʥ�����
// renew.php -> bytDecisionFlag		-> action.php����ե饰
// renew.php -> lngWorkflowOrderCode	-> action.php��ǧ�롼��
// renew.php -> aryDitail[��������][���ٹ�][lngStockSubjectCode]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][lngStockItemCode]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][bytPayOffTargetFlag]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][lngCustomerCode]		-> action.php
// renew.php -> aryDitail[��������][���ٹ�][bytPercentInputFlag]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][lngProductQuantity]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][curProductRate]		-> action.php
// renew.php -> aryDitail[��������][���ٹ�][curProductPrice]		-> action.php
// renew.php -> aryDitail[��������][���ٹ�][curSubTotalPrice]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][strNote]				-> action.php
// renew.php -> aryDitail[��������][���ٹ�][lngMonetaryUnitCode]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][curSubTotalPriceJP]	-> action.php
// renew.php -> aryDitail[��������][���ٹ�][curConversionRate]	-> action.php





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
	if ( $_GET )
	{
		$aryData = $_GET;
	}
	else
	{
		$aryData = $_POST;
	}
	$aryDetail = $aryData["aryDitail"];



		// Temp����
		$g_aryTemp	= $aryData;

fncDebug( 'estimate_regist_renew.txt', $aryData, __FILE__, __LINE__);



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
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}



	$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E3 . ")";


	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );



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



	// ���ϥߥ��ˤ�����ʳ��ξ�硢���顼ɽ������ɽ��������
	if ( !preg_match ( "/confirm\.php/", $_SERVER["HTTP_REFERER"] ) )
	{
		$aryData["strProductCode_Error"] = "visibility:hidden;";
	}

//fncDebug( 'estimate_regist_renew.txt', $_SERVER["HTTP_REFERER"], __FILE__, __LINE__);


	/*---------------------------------------------------------------------------*/
	// �����Ǥ�ɬ�פʤ�

/*
	// ���ʥ����ɻ������
	if ( $aryData["strMode"] == "onchange" and $aryData["strProductCode"] != "" )
	{
		// ���ʥ����ɤ����ꤵ�줿���֤�ȿ�ǥܥ��󤬲����줿��硢���ʾ�������ꤹ��
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			// ���Ϥ��줿���ʥ����ɤ�¸�ߤ��ʤ���硢���顼���Ƥ�إå�����ɽ�����������Ԥ�
			$strErrorMessage = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "", $objDB );

			// ��å�����ɽ���ս�˥�å�����������
			$aryData["strHeaderErrorMessage"] = $strErrorMessage;
		}
		else
		{
			// �������ʥ����ɤˤƴ��˸��Ѿ��󤬺�������Ƥ��ʤ����ɤ����Υ����å�

			// ���Ѹ����ǡ�������
			$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );
			if ( $aryEstimate != FALSE )
			{
				// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
				$strErrorMessage = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "", $objDB );

				// ��å�����ɽ���ս�˥�å�����������
				$aryData["strHeaderErrorMessage"] = $strErrorMessage;
			}

			// ����Υޡ�������
			$aryData = array_merge( $aryData, $aryProduct );

			// ���Ѹ����ǥե�������٥ǡ�������
			$aryDetail = fncGetEstimateDefaultValue( $aryData["lngProductionQuantity"], $aryData["curProductPrice"], $aryRate, $objDB );
		}

		unset( $aryProduct );

		// ����HIDDENʸ�������
		list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
		if ( is_array($aryHiddenString) )
		{
			$aryData["strDetailData"] = join ( "", $aryHiddenString );
		}

		$aryData["strMode"] = "";

//fncDebug( 'temp_renew.txt', $aryData, __FILE__, __LINE__);
	}
	else if ( $aryData["strProductCode"] != "" )
	{
		// ���ʾ������
		//$aryData = array_merge( $aryData, fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode ) );
	}
*/
	/*---------------------------------------------------------------------------*/





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


	// ���ʥ����ɤ�¸�ߤ��ʤ���硢���顼
	if ( $aryData["strProductCode"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "", $objDB );
	}
	// Ǽ�����������ͽ��������ꤵ��Ƥ��ʤ�������ʾ���ȿ�Ǥ���Ƥ��ʤ��Ȥߤʤ�
	else if ( $aryData["curProductPrice_hidden"] == "" and $aryData["lngProductionQuantity_hidden"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "", $objDB );
	}
	else
	{
		// ���ʥ����ɾ�����������
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}

	// ��Ͽ�ˤ����ʥ����ɤ����ꤵ��Ƥ�����Τ�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// ���ʾ���¸�ߤ���������ʻ��Ѳ�ǽ���ɤ����Υ����å�
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );
		if ( $aryEstimate != FALSE )
		{
			$lngErrorCount++;
			// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}


	// �����ξ�硢�������¥����å�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
	{
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );


		// �����ȡʥХåե��˼���
		$strBuffRemark	= $aryEstimateData["strRemark"];


//fncDebug( 'temp_renew.txt', $aryData, __FILE__, __LINE__);

		// (������桼���������Ϥ�����Τ��Ĳ���¸����)�ʳ��Τ�Ρ�
		// �ޤ��ϡ�������Τ�Τϡ������ԲĤȤ��ƥ��顼����
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
		{
			$lngErrorCount++;
			// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1503, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}


//fncDebug( 'es_renew.txt', $aryEstimateData, __FILE__, __LINE__);

	if ( $lngErrorCount == 0 )
	{
///////////////////////////////////////
//////////// ��Ͽ��ǧ���� /////////////
///////////////////////////////////////

		if ( $aryData["strActionName"] == "regist" )
		{
			// ���ʾ������
			$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );

//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);

			// �ƥ�ݥ������ξ��
			if( $aryEstimateData["blnTempFlag"] )
			{
				// ɸ�������
				$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

				// ����US�ɥ�졼�ȼ���
				$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

				// Excelɸ�������
//				$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
				// Excel����US�ɥ�졼�ȼ���
//				$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
			}
			else
			{
				// ɸ�������
				$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

				// ����US�ɥ�졼�ȼ���
				$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
			}




//�졼��ɽ�������������λ���б�			
			$aryEstimateData["dtmInsertDate"] = date("Y-m-d");




			// ���Ѹ����׻�����HTML����ʸ�������

			list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

			// �׻���̤��Ѹ���������Ȥ߹���
			$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

			unset ( $aryDetail );
			unset ( $aryCalculated );

			// �׻���̤����
			$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);

			// ��ǧ�ǤϤʤ��ä��顢��������ɤ�ܥ���ɽ��
			if ( $aryData["strActionName"] != "confirm" )
			{
				$aryData["bytReturnFlag"] = "true";

				$aryHiddenString[] = getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );

				$strHiddenString = join( "", $aryHiddenString );
				unset ( $aryHiddenString );

				$aryForm[] = "<form name=frmAction action=\"action.php\" method=POST>\n";
				$aryForm[] = $strHiddenString;

				// �ƥ�ݥ��ե饰
				if( $aryEstimateData["blnTempFlag"] )
				{
					// �ƥ�ݥ��ե饰
					$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
					// ������
					$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";

					// Excelɸ����
//					$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryEstimateData["curStandardRate"]. "\" />\n";
					// Excel����US�ɥ�졼��
//					$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryEstimateData["curConversionRate"]. "\" />\n";
				}

				$aryForm[] = "</form>\n";


				$aryForm[] = "<form name=frmEdit action=\"renew.php\" method=POST>\n";
				$aryForm[] = $strHiddenString;

				// �ƥ�ݥ��ե饰
				if( $aryEstimateData["blnTempFlag"] )
				{
					// �ƥ�ݥ��ե饰
					$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
					// ������
					$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";

					// Excelɸ����
//					$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryEstimateData["curStandardRate"]. "\" />\n";
					// Excel����US�ɥ�졼��
//					$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryEstimateData["curConversionRate"]. "\" />\n";
				}

				$aryForm[] = "<input type=hidden name=bytReturnFlag value=true>\n";
				$aryForm[] = "</form>\n";

				$aryEstimateData["FORM"] = join ( "", $aryForm );
				unset ( $aryForm );
			}

			unset ( $strHiddenString );

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

			if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
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


			if ( $aryData["strActionName"] != "confirm" )
			{
				$aryData["strScrollType"] = "ScrollAuto";
			}
			else
			{
				$aryData["strScrollType"] = "ScrollHidden";
			}


		 	// ����޽���
			$aryEstimateData	= fncGetCommaNumber( $aryEstimateData );
//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����

//fncDebug( 'es_renew.txt', $aryEstimateData, __FILE__, __LINE__);


			// ������
			$aryData["strRemarkDisp"]	= nl2br($strBuffRemark);


			// �֤�����
			$objTemplate->replace( $aryData );

		//echo getArrayTable( $aryData, "TABLE" );exit;
			$objTemplate->replace( $aryEstimateData );
			$objTemplate->replace( $aryEstimateDetail );

			$objTemplate->complete();
			echo $objTemplate->strTemplate;

			unset ( $aryEstimateData );
			unset ( $aryEstimateDetail );
			unset ( $aryData );

			$objDB->close();

			return TRUE;

		}

///////////////////////////////////////
//////////// ����Ͽ���� ///////////////
///////////////////////////////////////

		else if ( $aryData["strActionName"] == "temporary" )
		{

			/////////////////////////////////////////////////////////////
			// ������ɬ�פʥǡ����μ���
			/////////////////////////////////////////////////////////////
			// ���ɽ�������ɤ򥭡��Ȥ����ҥ�����Ϣ����������
			$aryCompanyCode = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "lngCompanyCode", "Array", "", $objDB );

			$aryMonetaryUnitCode = Array ( "\\" => DEF_MONETARY_YEN, "$" => DEF_MONETARY_USD, "HKD" => DEF_MONETARY_HKD );

			// �̲ߥ졼����������
			$aryRate = fncGetMonetaryRate( $objDB );
			$aryRate[DEF_MONETARY_YEN] = 1;

			$objDB->transactionBegin();

			// ��ӥ�����ֹ�
			// �����ξ��Ʊ�����ʥ����ɤθ��Ѹ������Ф��ƥ�ӥ�����ֹ�κ����ͤ��������
		/////   ��ӥ�����ֹ�򸽺ߤκ����ͤ�Ȥ�褦�˽������롡���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ���������Ф��ƥ�å����֤ˤ���
			$strLockQuery = "SELECT lngRevisionNo FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "' FOR UPDATE";

			// ��å������꡼�μ¹�
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

			$lngMaxRevision = 0;
			if ( $lngLockResultNum )
			{
				for ( $i = 0; $i < $lngLockResultNum; $i++ )
				{
					$objRevision = $objDB->fetchObject( $lngLockResultID, $i );
					if ( $lngMaxRevision < $objRevision->lngrevisionno )
					{
						$lngMaxRevision = $objRevision->lngrevisionno;
					}
				}
				$lngRevisionNo = $lngMaxRevision + 1;
			}
			else
			{
				$lngRevisionNo = $lngMaxRevision;
			}
			$objDB->freeResult( $lngLockResultID );


			/////////////////////////////////////////////////////////////
			// ��Ͽ�оݥǡ��������ȼ���
			/////////////////////////////////////////////////////////////
			// ���ʾ������
			$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );

			// ɸ�������
			$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

			// ���Ѹ����׻�����HTML����ʸ�������
			list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

			// �׻���̤��Ѹ���������Ȥ߹���

			$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

			unset ( $aryEstimateDetail );
			unset ( $aryCalculated );
			unset ( $aryHiddenString );

			// �׻���̤����
			$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

			// ����¸�����å�
			$bytDecisionFlag = "FALSE";
			$lngEstimateStatusCode = DEF_ESTIMATE_TEMPORARY;


			/////////////////////////////////////////////////////////////
			// ���Ѹ�����Ϣ��������������
			/////////////////////////////////////////////////////////////
			// ���Ѹ����ޥ�����Ͽ����������
			$aryEstimateQuery[] = "INSERT INTO m_Estimate VALUES ( " . $aryData["lngEstimateNo"];
			$aryEstimateQuery[] = $lngRevisionNo;
			$aryEstimateQuery[] = "'" . $aryData["strProductCode"] . "'";
			$aryEstimateQuery[] = $bytDecisionFlag;
			$aryEstimateQuery[] = $lngEstimateStatusCode;
			$aryEstimateQuery[] = $aryEstimateData["curFixedCost"];
			$aryEstimateQuery[] = $aryEstimateData["curMemberCost"];
			$aryEstimateQuery[] = $aryEstimateData["curTargetProfit"];
			$aryEstimateQuery[] = $aryEstimateData["curManufacturingCost"];
			$aryEstimateQuery[] = $aryEstimateData["curAmountOfSales"];
			$aryEstimateQuery[] = $aryEstimateData["curProfitOnSales"];
			$aryEstimateQuery[] = $objAuth->UserCode;
			$aryEstimateQuery[] = "FALSE";
			$aryEstimateQuery[] = "NOW()";
			$aryEstimateQuery[] = $aryEstimateData["lngProductionQuantity"] . ")";
			$aryQuery[] = join ( ", ", $aryEstimateQuery );
			unset ( $aryEstimateQuery );


			// ��Ͽ�������ξ�硢���Ѹ����ܺ١�����ե��˴ؤ��륯��������
			if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
			{
				// ���Ѹ��������ֹ楤�󥯥����
				$lngEstimateDetailNo++;

				// ���Ѹ����ܺ���Ͽ����������
				for ( $i = 0; $i < 8; $i++ )
				{
					for ( $j = 0; $j < count ( $aryDetail[$i] ); $j++ )
					{
						// �̲ߥ졼�ȥ����� 2���������
						$aryDetail[$i][$j]["lngMonetaryRateCode"] = 2;

						// ɽ���ѥ����ȥ���NULL����
						$aryDetail[$i][$j]["lngSortKey"] = "NULL";

						// �ѡ���������ϥե饰��Off�ξ�硢�ײ�Ψ��NULL������
						if ( $aryDetail[$i][$j]["bytPercentInputFlag"] != "true" )
						{
							$aryDetail[$i][$j]["curProductRate"] = "NULL";
						}

						$aryRowsValues = $aryDetail[$i][$j];

			// 2004.10.05 suzukaze update start
						// ��������󤬻��ꤵ��Ƥ��ʤ����NULL������
						if ( !is_numeric( $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]] ) )
						{
							$aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]] = "NULL";
						}
						// �ײ�Ψ�ξ��ñ�����ܤ�NULL�����ꤹ��
						if ( !is_numeric( $aryRowsValues["curProductPrice"] ) or ($aryRowsValues["curProductPrice"] == "" ) )
						{
							$aryRowsValues["curProductPrice"] = "NULL";
						}
			// 2004.10.05 suzukaze update end

						// �̲�ñ�̥�����
						if ( !is_numeric( $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] ) or $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] == "" )
						{
							$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] = 1;
						}
						if ( !is_numeric( $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]] ) or $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]] == "" )
						{
							$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] = 1.000000;
						}

						$aryEstimateQuery[] = "INSERT INTO t_EstimateDetail VALUES ( " . $aryData["lngEstimateNo"];
						$aryEstimateQuery[] = $lngEstimateDetailNo;
						$aryEstimateQuery[] = $lngRevisionNo;
						$aryEstimateQuery[] = $aryRowsValues["lngStockSubjectCode"];
						$aryEstimateQuery[] = $aryRowsValues["lngStockItemCode"];
						$aryEstimateQuery[] = $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]];
						$aryEstimateQuery[] = $aryRowsValues["bytPayOffTargetFlag"];
						$aryEstimateQuery[] = $aryRowsValues["bytPercentInputFlag"];
						$aryEstimateQuery[] = $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]];
						$aryEstimateQuery[] = $aryRowsValues["lngMonetaryRateCode"];
						$aryEstimateQuery[] = $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]];
						$aryEstimateQuery[] = $aryRowsValues["lngProductQuantity"];
						$aryEstimateQuery[] = $aryRowsValues["curProductPrice"];
						$aryEstimateQuery[] = $aryRowsValues["curProductRate"];
						$aryEstimateQuery[] = $aryRowsValues["curSubTotalPrice"];
						$aryEstimateQuery[] = "'" . $aryRowsValues["strNote"] . "'";
						$aryEstimateQuery[] = $aryRowsValues["lngSortKey"] . ")";

						$aryQuery[] = join ( ", ", $aryEstimateQuery );
						unset ( $aryEstimateQuery );
						unset ( $aryRowsValues );

						// ���Ѹ��������ֹ楤�󥯥����
						$lngEstimateDetailNo++;
					}
				}
			}

			unset ( $aryEstimateData );
			unset ( $aryCompanyCode );
			unset ( $aryMonetaryUnitCode );
			unset ( $aryRate );
			unset ( $bytDecisionFlag );
			unset ( $aryDetail );
			unset ( $lngEstimateDetailNo );


			//////////////////////////////////////////////////////////////////////////
			// ������¹�(���Ѹ����ɲ�)
			//////////////////////////////////////////////////////////////////////////
			for ( $i = 0; $i < count ( $aryQuery ); $i++ )
			{
			//	echo "<p>$aryQuery[$i]</p>\n";
				list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
			}

			$objDB->transactionCommit();


			//////////////////////////////////////////////////////////////////////////
			// ��̼��������Ͻ���
			//////////////////////////////////////////////////////////////////////////


			$aryData["lngInputUserCode"] = $lngUserCode; // ���ϼԥ�����

			if ( $lngEstimateStatusCode == DEF_ESTIMATE_TEMPORARY )
			{
				$aryData["lngSaveType"] = 1;
			}
			else
			{
				$aryData["lngSaveType"] = 0;
			}

			// Ģɼ����ɽ������
			// ����ʳ��ˤ�Ģɼ���ϥܥ����ɽ������
			if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
			{
				$aryData["PreviewVisible"] = "hidden";
			}
			else
			{
				$aryData["PreviewVisible"] = "visible";
				$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $lngEstimateDetailNo . "&bytCopyFlag=TRUE";
			}

			// ���Ѹ�������ξ��
			if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
			{
				// �����������Υ��ɥ쥹����
				$aryData["strAction"] = "/estimate/regist/renew.php?lngFunctionCode=" . DEF_FUNCTION_E1 ."&strSessionID=";

				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
				header("Content-type: text/plain; charset=EUC-JP");
				$objTemplate->replace( $aryData );
				$objTemplate->complete();
				echo $objTemplate->strTemplate;
			}
			// ���Ѹ�������������ξ��
			elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
			{
				// �����������Υ��ɥ쥹���� �ʰ�̵̣�������ͽ���
				$aryData["strAction"] = "/estimate/search/index.php?lngFunctionCode=" . $aryData["lngFunctionCode"] ."&strSessionID=";

				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
				header("Content-type: text/plain; charset=EUC-JP");
				$objTemplate->replace( $aryData );
				$objTemplate->complete();
				echo $objTemplate->strTemplate;
			}

			unset ( $lngEstimateStatusCode );
			$objDB->close();

			return TRUE;

		}
	}
	else
	{
		$aryData["strProcess"] = "regist";
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





// 2004.09.27 suzukaze update start
$aryData["ProductSubmit"] = "";
$aryData["strProcess"]    = "regist";

// �����ξ������ʥ����ɤ��Խ��Բ�ǽ�Ȥ���
$aryData["strProductCode_Editable"] = "contenteditable=\"false\"";

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
	// ���ʾ������
	$aryData = array_merge( $aryData, fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode ) );

	// ����HIDDENʸ�������
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
	if ( is_Array( $aryHiddenString ) )
	{
		$aryData["strDetailData"] = join ( "", $aryHiddenString );
	}

// 2004.10.02 suzukaze update start
	$aryData["strPageCondition"] = "regist";
// 2004.10.02 suzukaze update end
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
		fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// ����̵����硢���٥ǡ�������
	if ( $aryData["bytReturnFlag"] != "true" )
	{
		// ���Ѹ������٥ǡ�������
		$aryDetail = fncGetEstimateDetailRenew( $aryData["lngEstimateNo"], $aryRate, $aryData["lngProductionQuantity"], $aryData["lngOldProductionQuantity"], $aryData["curProductPrice"], $aryData["curRetailPrice"], $objDB );
		unset ( $aryCalculated );
	}

//fncDebug( 'es_renew.txt', $aryDetail, __FILE__, __LINE__);


	// ����HIDDENʸ�������
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

	$aryData["strDetailData"] = join ( "", $aryHiddenString );

	$aryData["strPageCondition"] = "renew";

	$aryData["RENEW"] = TRUE;
}

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
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "", $objDB );
	}
	// Ǽ�����������ͽ��������ꤵ��Ƥ��ʤ�������ʾ���ȿ�Ǥ���Ƥ��ʤ��Ȥߤʤ�
	else if ( $aryData["curProductPrice"] == "" and $aryData["lngProductionQuantity"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "", $objDB );
	}
	else
	{
		// ���ʥ����ɾ�����������
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}

	// ��Ͽ�ˤ����ʥ����ɤ����ꤵ��Ƥ�����Τ�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// ���ʾ���¸�ߤ���������ʻ��Ѳ�ǽ���ɤ����Υ����å�
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );

		if ( $aryEstimate != FALSE )
		{
			$lngErrorCount++;
			// ��������ʥ����ɤ��Ф��Ƹ��Ѥ�꤬¸�ߤ�����ϡ����顼���Ƥ�إå�����ɽ�����������Ԥ�
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "", $objDB );
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
$aryData["filename"] = "renew.php";


$aryData["strActionFile"] = "renew.php";


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


//fncDebug( 'es_renew.txt', $aryData, __FILE__, __LINE__);

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo getArrayTable( $aryData, "TABLE" );exit;
echo fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth );


$objDB->close();
unset ( $aryData );
unset ( $objAuth );
unset ( $objDB );


return TRUE;
?>
