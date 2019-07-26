<?
/** 
*	���Ѹ������� �¹Բ���
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// confirm.php -> strSessionID			-> action.php
// confirm.php -> lngFunctionCode		-> action.php
// confirm.php -> lngEstimateNo			-> action.php���Ѹ����ֹ�
// confirm.php -> strProductCode		-> action.php���ʥ�����
// confirm.php -> strActionName			-> action.php�¹Խ���̾(temporary)
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


	mb_http_output ( 'EUC-JP' );



	require('conf.inc');
	require( LIB_DEBUGFILE );

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	include ( LIB_ROOT . "diff/conf_diff_product.inc" );		// ���ʥޥ�������ʬ��������
	require ( CLS_TABLETEMP_FILE );								// Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );



	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// POST�ǡ�������
	$aryData = $_POST;



	// Temp����
	$g_aryTemp	= $aryData;


fncDebug( 'estimate_regist_action_data.txt', $aryData["aryDetail"], __FILE__, __LINE__);

	$aryDetail = $aryData["aryDetail"];
	unset ( $aryData["aryDetail"] );
	//echo getArrayTable( $aryData, "TABLE" );exit;





	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]		= "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E5 . ")";

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );
	unset ( $aryCheck );

	$aryCheck["lngWorkflowOrderCode"]	= "null:number(0,32767)";


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;


	// ���³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���Ѹ�����Ͽ�ξ��
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
	fncPutStringCheckError( $aryCheckResult, $objDB );
	unset ( $aryCheck );
	unset ( $aryCheckResult );


	//////////////////////////////////////////////////////////////////////
	// DB��������
	//////////////////////////////////////////////////////////////////////
	$objDB->transactionBegin();


	// ���Ѹ�����Ͽ�ξ�硢INSERT
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
	{
		// ���ʥ�����5�岽��ȼ�äơ�4��θ��ѥե����뤬���åץ����ɤ��줿�ݤ�
		// 5��˳�ĥ���ƥޥ���������»ܤ���
		if (strlen($aryData["strProductCode"]) == 4)
		{
			$aryData["strProductCode"] = '0'.$aryData["strProductCode"];
		}

		// �������ʤ����Ѥ���Ƥ��ʤ����ɤ��������å�
		list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT * FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "'", $objDB );

		// ���Ѿ���¸�ߤ�����
		if ( $lngResultNum > 0 )
		{
			// �ե�����ƥ�ݥ������ʳ��ξ��
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				$objDB->freeResult( $lngResultID );
				$objDB->execute( "ROLLBACK" );
				fncOutputError ( 1501, DEF_WARNING, "���˸��Ѹ�������Ͽ�Τ������ʤǤ���", TRUE, "", $objDB );
			}
			// �ե�����ƥ�ݥ����� -> ��ӥ�����ֹ�����
			else
			{
				// ���Ѹ����ֹ����
				$aryData["lngEstimateNo"]	= fncGetMasterValue( "m_estimate", "strproductcode", "lngestimateno", $aryData["strProductCode"].":str", '', $objDB );

				$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

				// ((��������桼���������Ϥ�����Τ��Ĳ���¸���֤Τ��)��
				// �ޤ��ϡ�������ʳ��Τ��)�ʳ��ϡ������ԲĤȤ��ƥ��顼����
				if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) 
						|| ( $aryEstimateData["bytDecisionFlag"] == "t" && $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) ) )
				{
					unset ( $aryEstimateData );
					unset ( $aryData );
					fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
				}

				// ���ߤΥ�ӥ����ʥ�С�����¸
				$lngRevisionNo = $aryEstimateData["lngRevisionNo"];

				// ���ߤ����ʥ����ɤ���¸
				$aryData["strProductCode"] = $aryEstimateData["strProductCode"];


				// ��ӥ�����ֹ�
				// �����ξ��Ʊ�����ʥ����ɤθ��Ѹ������Ф��ƥ�ӥ�����ֹ�κ����ͤ��������
				// ��ӥ�����ֹ�򸽺ߤκ����ͤ�Ȥ�褦�˽������롡���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ���������Ф��ƥ��å����֤ˤ���
				$strLockQuery = "SELECT lngRevisionNo FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "' FOR UPDATE";

				// ���å������꡼�μ¹�
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

				unset ( $aryEstimateData );
			}
		}
		// ����
		else
		{
			// ���Ѹ����ֹ����
			$aryData["lngEstimateNo"] = fncGetSequence( "m_Estimate.lngEstimateNo", $objDB );

			// ��ӥ�����ֹ�����
			$lngRevisionNo = 0;
		}

	}
	// ���Ѹ�������������ξ�硢
	// �����θ��Ѹ����ǡ��������Ⱥ���Τ����DB�����å�
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 || $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
	{
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

		// ((��������桼���������Ϥ�����Τ��Ĳ���¸���֤Τ��)��
		// �ޤ��ϡ�������ʳ��Τ��)�ʳ��ϡ������ԲĤȤ��ƥ��顼����
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) 
				|| ( $aryEstimateData["bytDecisionFlag"] == "t" && $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) ) )
		{
			unset ( $aryEstimateData );
			unset ( $aryData );
			fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
		}

		// ���ߤΥ�ӥ����ʥ�С�����¸
		$lngRevisionNo = $aryEstimateData["lngRevisionNo"];

		// ���ߤ����ʥ����ɤ���¸
		$aryData["strProductCode"] = $aryEstimateData["strProductCode"];

		// ����ξ�硢DB�����å�
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && $aryEstimateData["bytDecisionFlag"] == "t" )
		{
			$aryQuery = Array();

			// ȯ���ܺ٤��о����ʥ����ɤ����뤫�ɤ����Υ����å�������
			$aryQuery[] = "SELECT 1 FROM t_OrderDetail WHERE strProductCode = '" . $aryData["strProductCode"]. "'";

			// �����ޥ������оݸ��Ѹ����ֹ椬���뤫�ɤ����Υ����å�������
			//$aryQuery[] = "SELECT 1 FROM m_Stock WHERE lngEstimateNo = " . $aryData["lngEstimateNo"];
			list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " UNION ", $aryQuery ), $objDB );

			if ( $lngResultNum > 0 )
			{
	//			unset ( $aryEstimateData );
	//			unset ( $aryData );
				$objDB->freeResult( $lngResultID );
	//			fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// ��ӥ�����ֹ�
		// �����ξ��Ʊ�����ʥ����ɤθ��Ѹ������Ф��ƥ�ӥ�����ֹ�κ����ͤ��������
	/////   ��ӥ�����ֹ�򸽺ߤκ����ͤ�Ȥ�褦�˽������롡���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ���������Ф��ƥ��å����֤ˤ���
		$strLockQuery = "SELECT lngRevisionNo FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "' FOR UPDATE";

		// ���å������꡼�μ¹�
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

		unset ( $aryEstimateData );
	}




	/////////////////////////////////////////////////////////////
	// ������ɬ�פʥǡ����μ���
	/////////////////////////////////////////////////////////////
	// ���ɽ�������ɤ򥭡��Ȥ����ҥ�����Ϣ����������
	$aryCompanyCode = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "lngCompanyCode", "Array", "", $objDB );

	$aryMonetaryUnitCode = Array ( "\\" => DEF_MONETARY_YEN, "$" => DEF_MONETARY_USD, "HKD" => DEF_MONETARY_HKD );

	// �̲ߥ졼����������
	$aryRate = fncGetMonetaryRate( $objDB );
	$aryRate[DEF_MONETARY_YEN] = 1;

	// ����ξ�硢��ӥ����ʥ�С���-1�˥��å�
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
	{
		$lngRevisionNo = -1;
	}


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

	if ( $aryData["strActionName"] != "temporary" )
	{
		$bytDecisionFlag = "TRUE";

		if( $aryData["lngWorkflowOrderCode"] == 0 )
		{
			$lngEstimateStatusCode = DEF_ESTIMATE_APPROVE;
		}
		else
		{
			$lngEstimateStatusCode = DEF_ESTIMATE_APPLICATE;
		}
	}





//fncDebug( 'es_temp.txt', $aryData, __FILE__, __LINE__);exit();
	/*-------------------------------------------------------------------------
		�ե�����ƥ�ݥ��DB���� <- ���ʥޥ�������
	-------------------------------------------------------------------------*/
	// �ֺ�������פǤϤʤ���硢�ƥ�ݥ��ǡ�������
	if( $aryData["lngFunctionCode"] != DEF_FUNCTION_E4 )
	{
		// �ƥ�ݥ�����
		if( $aryData["blnTempFlag"] )
		{
			// �ƥ�ݥ���ֹ����
			$lngTempNo = fncGetMasterValue( "m_estimate", "lngestimateno", "lngtempno", $aryData["lngEstimateNo"], '', $objDB );

			// �ƥ�ݥ���ֹ椬¸�ߤ�����
			if( $lngTempNo )
			{
				// �ƥ�ݥ��ǡ�������
				$aryTempData = fncGetTempData($objDB, $lngTempNo);

				// �ƥ�ݥ��ǡ����������Ԥξ��
				if( !$aryTempData ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

				// ����
				else $blnTempFlag	= true;
			}

			// �ƥ�ݥ��ơ��֥����Ͽ����Ͽ����lngTempNo�����
			$lngTempNo	= fncArray2Temp( $objDB, $aryTempData );


			// �ʲ����ޥ͡����㡼�ʾ�
			// ���¥��롼�ץ����ɤμ���
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// �֥ޥ͡����㡼�װʾ�ξ�硢���ʥޥ�������
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				// ���ʥޥ����񤭴������ĥե饰��ͭ���ξ�硢���ʥޥ�������
				if( PRODUCT_MASTER_UPDATE_FLAG )
				{
					// ���ʾ��ּ���
					$lngBuffProductStatusCode	= fncGetMasterValue( "m_product", "strproductcode", "lngproductstatuscode", $aryData["strProductCode"].":str", '', $objDB );

					// ���ʥޥ������֤��־�ǧ�װʳ��ξ�硢������λ
					if( $lngBuffProductStatusCode != DEF_PRODUCT_NORMAL )
					{
						fncOutputError( 308, DEF_WARNING, "�ʾ�ǧ����Ƥ��ʤ����ʾ���Ǥ�����", TRUE, "", $objDB );
					}

					// �¹�
					$blnCheck	= fncTemp2ProductUpdate($objDB, $lngTempNo);

					// ���Ԥξ��
					if( !$blnCheck ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

					// TempNo �ν����
					$lngTempNo	= null;
				}
			}


			// ��Ͽ�����塢���ϲ��̤���ä��Ȥ��˻���
			$aryData["RENEW"]	= "&RENEW=true";	// ����ɽ���⡼�ɡ����� �����������ǤϤʤ�
		}
		// �ե�����ƥ�ݥ�����
		else if( $aryData["bytTemporaryFlg"] )
		{
			while( list($index, $value) = each($aryDiffProduct["tempdb"]) )
			{
				if( !$value )
				{
					continue;
				}

				$aryTempData[$index]	= $aryData[$index];
			}

			// �ƥ�ݥ��ơ��֥����Ͽ����Ͽ����lngTempNo�����
			$lngTempNo	= fncArray2Temp( $objDB, $aryTempData );


			// �ʲ����ޥ͡����㡼�ʾ�
			// ���¥��롼�ץ����ɤμ���
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// �֥ޥ͡����㡼�װʾ�ξ�硢���ʥޥ�������
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				// ���ʥޥ����񤭴������ĥե饰��ͭ���ξ�硢���ʥޥ�������
				if( PRODUCT_MASTER_UPDATE_FLAG )
				{
					// ���ʾ��ּ���
					$lngBuffProductStatusCode	= fncGetMasterValue( "m_product", "strproductcode", "lngproductstatuscode", $aryData["strProductCode"].":str", '', $objDB );

					// ���ʥޥ������֤��־�ǧ�װʳ��ξ�硢������λ
					if( $lngBuffProductStatusCode != DEF_PRODUCT_NORMAL )
					{
						fncOutputError( 308, DEF_WARNING, "�ʾ�ǧ����Ƥ��ʤ����ʾ���Ǥ�����", TRUE, "", $objDB );
					}

					// �¹�
					$blnCheck	= fncTemp2ProductUpdate($objDB, $lngTempNo);

					// ���Ԥξ��
					if( !$blnCheck ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

					// TempNo �ν����
					$lngTempNo	= null;
				}
			}


			// ��Ͽ�����塢���ϲ��̤���ä��Ȥ��˻���
			$aryData["RENEW"]	= "&RENEW=true";	// ����ɽ���⡼�ɡ����� �����������ǤϤʤ�
		}
	}
	/*-----------------------------------------------------------------------*/








/////////////////////////////////////////////////////////////
// ���Ѹ�����Ϣ��������������
/////////////////////////////////////////////////////////////
	// ���Ѹ����ޥ�����Ͽ����������
	$lngTempNo	= ( is_null($lngTempNo) || empty($lngTempNo) ) ? "null" : $lngTempNo;	// �ƥ�ݥ���ֹ�
	$strRemark	= ( is_null($aryData["strRemark"]) || empty($aryData["strRemark"]) ) ? "null" : "'".$aryData["strRemark"]."'";	// ������

	$aryEstimateQuery[] = "INSERT INTO m_Estimate VALUES ( " . $aryData["lngEstimateNo"];
	$aryEstimateQuery[] = "," . $lngRevisionNo;
	$aryEstimateQuery[] = ",'" . $aryData["strProductCode"] . "'";
	$aryEstimateQuery[] = "," . $bytDecisionFlag;
	$aryEstimateQuery[] = "," . $lngEstimateStatusCode;
	$aryEstimateQuery[] = "," . $aryEstimateData["curFixedCost"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curMemberCost"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curTargetProfit"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curManufacturingCost"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curAmountOfSales"];
	$aryEstimateQuery[] = "," . $aryEstimateData["curProfitOnSales"];
	$aryEstimateQuery[] = "," . $objAuth->UserCode;
	$aryEstimateQuery[] = "," . "FALSE";
	$aryEstimateQuery[] = "," . "NOW()";
	$aryEstimateQuery[] = "," . $aryEstimateData["lngProductionQuantity"];

	$aryEstimateQuery[] = "," . $lngTempNo;		// �ƥ�ݥ���ֹ�
	$aryEstimateQuery[] = "," . $strRemark;		// ������
	$aryEstimateQuery[] = ")";

	$aryQuery[] = join ( "\n", $aryEstimateQuery );
	unset ( $aryEstimateQuery );




fncDebug( 'action_aryDetail.txt', $aryDetail, __FILE__, __LINE__);
//exit();



// ��Ͽ�������ξ�硢���Ѹ����ܺ١�����ե����˴ؤ��륯��������
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
{
	// ���Ѹ��������ֹ楤�󥯥����
	$lngEstimateDetailNo++;

	// ���Ѹ����ܺ���Ͽ����������
	for ( $i = 0; $i <= 11; $i++ )
	{
		if( !isset($aryDetail[$i]) ) continue;
		
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
			$aryEstimateQuery[] = isset($aryRowsValues["lngStockSubjectCode"]) ? $aryRowsValues["lngStockSubjectCode"] : '0';
			$aryEstimateQuery[] = isset($aryRowsValues["lngStockItemCode"]) ? $aryRowsValues["lngStockItemCode"] : '0';
			$aryEstimateQuery[] = $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]];
			$aryEstimateQuery[] = isset($aryRowsValues["lngSalesDivisionCode"]) ? 'NULL' : $aryRowsValues["bytPayOffTargetFlag"];	// lngSalesDivisionCode ��¸�ߤ򸵤ˡ������ʽ��Ѷ�ʬ�ˤ�Ƚ�ꤹ��
			$aryEstimateQuery[] = $aryRowsValues["bytPercentInputFlag"];
			$aryEstimateQuery[] = $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]];
			$aryEstimateQuery[] = $aryRowsValues["lngMonetaryRateCode"];
			$aryEstimateQuery[] = $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]];
			$aryEstimateQuery[] = $aryRowsValues["lngProductQuantity"];
			$aryEstimateQuery[] = $aryRowsValues["curProductPrice"];
			$aryEstimateQuery[] = $aryRowsValues["curProductRate"];
			$aryEstimateQuery[] = $aryRowsValues["curSubTotalPrice"];
			$aryEstimateQuery[] = "'" . $aryRowsValues["strNote"] . "'";
			$aryEstimateQuery[] = $aryRowsValues["lngSortKey"];
			$aryEstimateQuery[] = isset($aryRowsValues["lngSalesDivisionCode"]) ? $aryRowsValues["lngSalesDivisionCode"] : 'NULL';
			$aryEstimateQuery[] = isset($aryRowsValues["lngSalesClassCode"]) ? $aryRowsValues["lngSalesClassCode"] : 'NULL';

			$aryQuery[] = join ( ", ", $aryEstimateQuery ) . ")";
			unset ( $aryEstimateQuery );
			unset ( $aryRowsValues );

			// ���Ѹ��������ֹ楤�󥯥����
			$lngEstimateDetailNo++;
		}
	}

fncDebug( 'action.txt', $aryQuery, __FILE__, __LINE__);


	/////////////////////////////////////////////////////////////
	// ����ե�����Ϣ��������������
	/////////////////////////////////////////////////////////////
	// ����¸��̵����硢����ե����ޥ������ɲ�
	if ( $aryData["lngWorkflowOrderCode"] != 0 && $aryData["strActionName"] != "temporary" )
	{
		// ��ǧ�ԤΥǡ��������
		$aryWorkflowQuery[] = "SELECT";
		$aryWorkflowQuery[] = " wo.lngLimitDays,";
		$aryWorkflowQuery[] = " u.bytMailTransmitFlag,";
		$aryWorkflowQuery[] = " u.strUserDisplayName,";
		$aryWorkflowQuery[] = " u.strMailAddress";
		$aryWorkflowQuery[] = "FROM m_WorkflowOrder wo";
		$aryWorkflowQuery[] = "INNER JOIN m_User u ON ( wo.lngInChargeCode = u.lngUserCode AND u.bytInvalidFlag = FALSE )";
		$aryWorkflowQuery[] = "WHERE wo.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"];
		$aryWorkflowQuery[] = " AND wo.lngWorkflowOrderNo = 1";
		$aryWorkflowQuery[] = " AND wo.bytWorkflowOrderDisplayFlag = TRUE";

		list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryWorkflowQuery ), $objDB );
		unset ( $aryWorkflowQuery );

		if ( $lngResultNum < 1 )
		{
			$objDB->execute( "ROLLBACK" );
			fncOutputError ( 9051, DEF_WARNING, "", TRUE, "", $objDB );
		}

		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$objDB->freeResult( $lngResultID );

//		$aryInChargeUser["strUserDisplayName"]	= $objResult->struserdisplayname;	
// 2004.10.09 suzukaze update start
		$aryInChargeUser["strWorkflowName"]		= "���Ѹ��� [" . $aryData["strProductCode"] . "]";
// 2004.10.09 suzukaze update end
		$bytMailTransmitFlag	= $objResult->bytmailtransmitflag;
		$lngLimitDays			= $objResult->lnglimitdays;
		$strMailAddress			= $objResult->strmailaddress;
//======================================================================================================
// 05.03.15 by kou 
//		$aryInChargeUser["strUserDisplayName"]	= $objResult->UserDisplayName;
		$aryInChargeUser["strURL"] = LOGIN_URL;
		// ���ϼԤΥ᡼�륢�ɥ쥹��̾������
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress, strUserDisplayName  FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;
		
		list ( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );
		if ( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag 	= $objResult->bytmailtransmitflag;
			$strFromMail	= $objResult->strmailaddress;
			$aryInChargeUser["strUserDisplayName"]	= $objResult->struserdisplayname;
		}
		else
		{
			$objDB->execute( "ROLLBACK" );
			fncOutputError ( 9051, DEF_WARNING, "", TRUE, "", $objDB );

		}
		$objDB->freeResult( $lngUserMailResultID );
//======================================================================================================
		unset ( $objResult );

		// ����ե��������ɤΥ��󥯥����
		$lngWorkflowCode = fncGetSequence( "m_Workflow.lngworkflowcode", $objDB );
		// ����ե����ޥ�����Ͽ����������
		$aryWorkflowQuery[] = "INSERT INTO m_Workflow VALUES ( " . $lngWorkflowCode;
		$aryWorkflowQuery[] = $aryData["lngWorkflowOrderCode"];
		$aryWorkflowQuery[] = "'" . $aryInChargeUser["strWorkflowName"] . "'";
		$aryWorkflowQuery[] = DEF_FUNCTION_E1;
		$aryWorkflowQuery[] = "'" . $aryData["lngEstimateNo"] . "'";
		$aryWorkflowQuery[] = "NOW()";
		$aryWorkflowQuery[] = "NULL";
		$aryWorkflowQuery[] = $objAuth->UserCode;
		$aryWorkflowQuery[] = $objAuth->UserCode;
		$aryWorkflowQuery[] = "FALSE";
		$aryWorkflowQuery[] = "NULL )";

		$aryQuery[] = join ( ", ", $aryWorkflowQuery );
		unset ( $aryWorkflowQuery );

		// ����ե�����Ͽ����������
		$aryWorkflowQuery[] = "INSERT INTO t_Workflow VALUES ( " . $lngWorkflowCode;
		$aryWorkflowQuery[] = 1;
		$aryWorkflowQuery[] = 1;
		$aryWorkflowQuery[] = DEF_STATUS_ORDER;
		$aryWorkflowQuery[] = "NULL";
		$aryWorkflowQuery[] = "NOW()";
		$aryWorkflowQuery[] = "NOW() + interval '" . $lngLimitDays . " days' )";
		unset ( $lngLimitDays );

		$aryQuery[] = join ( ", ", $aryWorkflowQuery );
		unset ( $aryWorkflowQuery );
	}
}

// ����ξ�硢���Ѹ����ޥ��������ʥ����ɹ�������������
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
{
	$aryEstimateQuery[] = "UPDATE m_Estimate SET";
	$aryEstimateQuery[] = "strProductCode = '" . $aryData["strProductCode"] . "_del'";
	$aryEstimateQuery[] = "WHERE lngEstimateNo = " . $aryData["lngEstimateNo"];
	$aryQuery[] = join ( " ", $aryEstimateQuery );
	unset ( $aryEstimateQuery );
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


//////////////////////////////////////////////////////////////////////////
// �᡼������(���Ѹ����ɲ�)
//////////////////////////////////////////////////////////////////////////
if ( $bytMailTransmitFlag == "t" && $strMailAddress )
{

	list ( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_E1, $aryInChargeUser, $objDB );
//	$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );


	$blnSendMailFlag = fncSendMail( $strMailAddress, $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" );

	if ( !$strMailAddress || !$blnSendMailFlag )
	{
		$objDB->execute( "ROLLBACK" );
		fncOutputError ( 9053, DEF_WARNING, "�᡼���������ԡ�", TRUE, "", $objDB );
	}
}

unset ( $aryInChargeUser );
unset ( $bytMailTransmitFlag );

$objDB->transactionCommit();


//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////


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
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && $lngEstimateStatusCode == DEF_ESTIMATE_APPLICATE )
{
	$aryData["PreviewVisible"] = "hidden";
}
else
{
	$aryData["PreviewVisible"] = "hidden";
//	$aryData["PreviewVisible"] = "visible";
	$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $lngEstimateDetailNo . "&bytCopyFlag=TRUE";
}






	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// ���쥳����





// ���Ѹ�������ξ��
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
{
	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/estimate/regist/edit.php?lngFunctionCode=" . DEF_FUNCTION_E1 ."&strSessionID=";

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/finish/parts.tmpl" );

//	$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
//	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;
}
// ���Ѹ�������������ξ��
elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
{
	// �����������Υ��ɥ쥹���� �ʰ�̵̣�������ͽ���
	$aryData["strAction"] = "/estimate/search/index.php?lngFunctionCode=" . $aryData["lngFunctionCode"] ."&strSessionID=";

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/finish/parts.tmpl" );

//	$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
//	header("Content-type: text/plain; charset=EUC-JP");
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

//fncDebug( 'es_finish.txt', $objTemplate->strTemplate, __FILE__, __LINE__);
	echo $objTemplate->strTemplate;
}


unset ( $lngEstimateStatusCode );
unset ( $g_aryTemp );

$objDB->close();


return TRUE;
?>