<?php

// ----------------------------------------------------------------------------
/**
*       ������  ��������
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
*         ����������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
*
*       ��������
*       2013.05.31����������Ψ�������������ʴ��������Ψ�����Ǥ��ʤ���硢�ǿ����֤���Ψ��������� ��
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc');
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."sc/cmn/lib_sc.php" );
	require( SRC_ROOT."sc/cmn/lib_scs1.php" );
	require( SRC_ROOT."sc/cmn/lib_scp.php" );
	require( SRC_ROOT."sc/cmn/column.php" );
	require( LIB_DEBUGFILE );


	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];


	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// �� ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;


	// 600 ������
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 605 ��彤��
	if( fncCheckAuthority( DEF_FUNCTION_SC5, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// 610 �������ʹ��ɲá��Ժ����
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}






	//-------------------------------------------------------------------------
	// �� ����ֹ����
	//-------------------------------------------------------------------------
	$lngSalesNo = $_REQUEST["lngSalesNo"];




	//-------------------------------------------------------------------------
	// �� ���ϥ����å�
	//-------------------------------------------------------------------------
	if( $_POST["strProcMode"] == "check")
	{
		// ���ٹԤ����
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $_POST );

			if( $strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}


		//-----------------------------------------------------------
		// �إå������ܥ����å�
		//-----------------------------------------------------------
		$aryData["renew"] = "true"; // ȯ��ͭ��������������å����ʤ�����Υե饰

		list( $aryData, $bytErrorFlag ) = fncCheckData_sc( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag == "TRUE" ) ? 1 : 0;


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


		//-----------------------------------------------------------
		// ���ٹԤΥ����å�
		//-----------------------------------------------------------
		for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			list( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_sc( $_POST["aryPoDitail"][$i], "detail", $objDB );
		}

		for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ )
		{
			// ���ٹԤΥ��顼�ؿ�
			if( $bytErrorFlag2[$i] == "true" )
			{
				$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
			}
		}


		// �桼���˱����Ƥζ������
		if( $_POST["lngReceiveNo"] == "")
		{
			$lngUserGroup = $objAuth->AuthorityGroupCode;

			// ���ܱߤ��Ѵ�
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];

			// 608 �������ʼ���NO����ꤷ�ʤ���Ͽ����ǽ���ɤ�����
			if ( !fncCheckAuthority( DEF_FUNCTION_SC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 607, "", "", FALSE, "sc/regist/renew.php?lngSalesNo=" . $lngSalesNo . "&strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// 609 �����ۤ���°ʾ�Ǥ�����Ͽ��ǽ�ʸ��¤���äƤ��ʤ��ʤ�
				if ( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_SC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/renew.php?lngSalesNo=" . $lngSalesNo . "&strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

/*
			if( $lngUserGroup == 5 )					// �桼��
			{
				$aryDetailErrorMessage[] = fncOutputError ( 9060, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// �ޥ͡����㡼
			{
				// 5���ޤ���Ͽ��ǽ
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// �ǥ��쥯����
			{
				// 20���ޤ���Ͽ��ǽ
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

			}
*/
		}

		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}

		if( strcmp( $_POST["strReceiveCode"],"") != 0 )
		{

			$aryData["lngReceiveNo"] = $_POST["lngReceiveNo"];

			if ( $_POST["strReceiveCode"] == "" )
			{
				//-----------------------------------------
				// DB -> SELECT : m_Receive
				//-----------------------------------------
				$strQuery = "SELECT strReceiveCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

				if ( isset( $lngResultID ) )
				{
					$objDB->freeResult( $lngResultID );
				}
				list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
				if ( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strReceiveCode = $objResult->strreceivecode;
				}
				else
				{
					// ȯ�����ɼ�������
					fncOutputError ( 9051, DEF_ERROR, "�������ɼ�������", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
				$objDB->freeResult( $lngResultID );
			}
			else
			{
				$strReceiveCode = $_POST["strReceiveCode"];
			}

			//-------------------------------------------------------
			// DB -> SELECT : m_Receive
			//-------------------------------------------------------
			$strQuery = "SELECT lngMonetaryUnitCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

			if ( isset( $lngResultID ) )
			{
				$objDB->freeResult( $lngResultID );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngReceiveMonetaryUnitCode = $objResult->lngmonetaryunitcode;
			}
			else
			{
				// �̲ߥ졼�ȼ�������
				fncOutputError ( 9051, DEF_ERROR, "�̲ߥ졼�ȼ�������", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );


			//-------------------------------------------------------
			// DB -> SELECT : t_receivedetail
			//-------------------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "lngreceivedetailno, ";							// ���������ֹ�
			$aryQuery[] = "lngrevisionno, ";								// ��ӥ�����ֹ�
			$aryQuery[] = "strproductcode, ";								// ���ʥ�����
			$aryQuery[] = "lngsalesclasscode, ";							// ����ʬ������
			$aryQuery[] = "dtmdeliverydate, ";								// Ǽ����
			$aryQuery[] = "lngconversionclasscode, ";						// ������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
			$aryQuery[] = "curproductprice, ";								// ���ʲ���
			$aryQuery[] = "lngproductquantity, ";							// ���ʿ���
			$aryQuery[] = "lngproductunitcode, ";							// ����ñ�̥�����
			$aryQuery[] = "lngtaxclasscode, ";								// �����Ƕ�ʬ������
			$aryQuery[] = "lngtaxcode, ";									// �����ǥ�����
			$aryQuery[] = "curtaxprice, ";									// �����Ƕ��
			$aryQuery[] = "cursubtotalprice, ";								// ���׶��
			$aryQuery[] = "strnote ";										// ����
			$aryQuery[] = "FROM t_receivedetail ";
			$aryQuery[] = "WHERE ";
			$aryQuery[] = "lngreceiveno = ".$aryData["lngReceiveNo"];
			$aryQuery[] = " ORDER BY lngSortKey ASC";						// ������ϥ����ȥ�����

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryReceiveDetail[] = $objDB->fetchArray( $lngResultID, $i );
				}
			}
			else
			{
				// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
				fncOutputError( 9051, DEF_ERROR, "���ٹԼ�������", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );

			// �̲ߤ��ͤ򥳡��ɤ��ѹ�
			if ( $_POST["lngMonetaryUnitCode"] != "" )
			{
				$_POST["lngMonetaryUnitCode"] = ( $_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
				$lngSalesMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// �̲ߥ졼�ȼ�������
				fncOutputError ( 9061, DEF_ERROR, "�̲�ñ�̤μ�������", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// $_POST["aryPoDitail"] �ξ�����ѹ����Ƥ��ޤ�ʤ������
			$aryDetailResult = $_POST["aryPoDitail"];


			$lngCalcCode = DEF_CALC_KIRISUTE;

			//---------------------------------------------
			// ����İʾ���������ꤷ�Ƥ��ʤ����ɤ����Υ����å�
			//---------------------------------------------
			$lngResult = fncGetStatusSalesRemains( $aryData["lngReceiveNo"], $aryDetailResult, $lngReceiveMonetaryUnitCode, $lngSalesMonetaryUnitCode, $lngSalesNo, $lngCalcCode, $objDB );
//var_dump( $_POST );exit;
			switch ( $lngResult )
			{
				// �������μ�������
				case 0:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "���ꤵ�줿����Ϻ������ޤ���", FALSE, "" . $_POST["strSessionID"], $objDB );
					break;

				// �������١�������پ���μ�������
				case 1:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "����������μ����˼��Ԥ��ޤ���", FALSE, "", $objDB );
					break;

				// ���Ĥΰʾ�λ��������ꤵ��Ƥ���
				case 99:
					$aryDetailErrorMessage[] = fncOutputError ( 604, DEF_ERROR, "", FALSE, "", $objDB );
					break;

				// ���ꤵ��Ƥ����������������
				case 50:
					break;
			}
		}




		//-----------------------------------------------------------
		// �ǿ��ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngSalesNo, lngSalesStatusCode FROM m_Sales s WHERE s.strSalesCode = '" . $aryData["strSalesCode"] . "'";
		$strCheckQuery .= " AND s.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND s.lngRevisionNo = ( " . "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode )\n";

		// �����å������꡼�μ¹�
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngSalesStatusCode = $objResult->lngsalesstatuscode;

			//---------------------------------------------
			// �����֤Υ����å�
			//---------------------------------------------
			// ������ξ��
			if( $lngSalesStatusCode == DEF_SALES_PREORDER )
			{
				fncOutputError( 606, DEF_WARNING, "", TRUE, "../sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			// ����Ѥξ��
			if( $lngSalesStatusCode == DEF_SALES_CLOSED )
			{
				fncOutputError( 606, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngCheckResultID );



		//-----------------------------------------------------------
		// ���ϥ��顼
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount == 1 )
		{
			if( is_array( $aryDetailErrorMessage ))
			{
				$aryData["strErrorMessage"] = implode( " : ", $aryDetailErrorMessage );
			}

			$aryData = fncChangeData3( $aryData , $objDB );

			// orderno�����Ϥ��줿�����̲ߤ�disabled�ˤ���
			if( is_array( $_POST["aryPoDitail"] ))
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
				// ���ٹԤ�hidden�ͤ��Ѵ�����
				$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert", $objDB);
			}

			//�إå����ͤ��ü�ʸ���Ѵ�
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			// �ץ�������˥塼������
			// �̲�
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// �졼�ȥ�����
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// ��ʧ���
			$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// ������ˡ
			$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
			// ����ñ��
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
			// �ٻ�ñ��
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
			// ����ʬ
			$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );



			// ����ե�ɽ������ɽ������
			$aryData["visibleWF"] = "hidden";
			// ��ǧ�롼��
			// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );



			//-------------------------------------------------------------------------
			// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
			//-------------------------------------------------------------------------
			$lngSalesStatusCode = fncCheckNullStatus( $lngSalesStatusCode );


			//---------------------------------------------------------------
			// �����֤μ���
			//---------------------------------------------------------------
			$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $lngSalesStatusCode,'', $objDB );
			$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
			$aryData["lngSalseStatusCode"]         = $lngSalesStatusCode;



			$aryData["strGetDataMode"]        = "none";
			$aryData["strProcMode"]           = "check";
			$aryData["lngOrderCode"]          = $_REQUEST["strOrderCode"];
			$aryData["ReceiveSubmit"]         = "";
			$aryData["ReceiveSubmit2"]        = "";
			$aryData["strCustomerReceiveDis"] = 'contenteditable="false"';
			$aryData["strProductCodeOpenDis"] = 'contenteditable="false"';
			//$aryData["strReceiveCode_Editable"] = 'contenteditable="false"';
			$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
			$aryData["RENEW"]                 = TRUE;
			$aryData["strPageCondition"]      = "renew";


			// CRC�ꥹ��
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����



			// ľ��Ͽ�ե饰
			$aryData["lngDirectRegistFlag"] = 0;


			// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
			if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
				$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
			}
			else
			{
				$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
				$aryData["blnBtnEditable"]        = '';			// �ܥ���������
			}


			$objDB->close();
			$objDB->freeResult( $lngResultID );

			echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;
		}

		//-----------------------------------------------------------
		// ��ǧ����ɽ��
		//-----------------------------------------------------------
		else
		{
			$aryData["strProcMode"] = "renew";
			$aryData["RENEW"] = TRUE;

			// ���������
			if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
			{
				$aryTytle = $aryTableTytleEng;
			}
			else
			{
				$aryTytle = $aryTableTytle;
			}

			// ���ٹ��Ѥ˾����ǥ����ɤ��������
			// �����ǥ�����
			// �׾�����ꤽ�λ�����Ψ���Ȥ��
			$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
				. "FROM m_tax "
				. "WHERE dtmapplystartdate <= '" . $aryData["dtmOrderAppDate"] . "' "
				. "AND dtmapplyenddate >= '" . $aryData["dtmOrderAppDate"] . "' "
				. "GROUP BY lngtaxcode, curtax "
				. "ORDER BY 3 ";

			// ��Ψ�ʤɤμ��������꡼�μ¹�
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum == 1 )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngTaxCode = $objResult->lngtaxcode;
				$curTax = $objResult->curtax;
			}
			else
			{
				$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
					. "FROM m_tax "
					. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
					. "GROUP BY lngtaxcode, curtax ";

				// ��Ψ�ʤɤμ��������꡼�μ¹�
				list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

				if ( $lngResultNum == 1 )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$lngTaxCode = $objResult->lngtaxcode;
					$curTax = $objResult->curtax;
				}
				else
				{
					fncOutputError ( 9051, DEF_ERROR, "�����Ǿ���μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
				}
			}

			$objDB->freeResult( $lngResultID );

			$lngCalcCode = DEF_CALC_KIRISUTE;


			// ���������̲�ñ�̥����ɤ������оݷ��������
			if ( $aryData["lngMonetaryUnitCode"] == "\\" or $aryData["lngMonetaryUnitCode"] == "\\\\" )
			{
				$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
			}
			else
			{
				$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
			}

			// hidden�������κݤ��ǳ۰۾��Ĵ����Ԥ�
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				// ���Ǥκݤ����ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤ�ʤ������ǳۤκƷ׻���Ԥ�
				if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_UCHIZEI ) 
				{
					// ���ʲ��� = ����ñ�� �� ����
					$curProductTotalPrice = $_POST["aryPoDitail"][$i]["curProductPrice"] * $_POST["aryPoDitail"][$i]["lngGoodsQuantity"];

					// ���ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤäƤ��ʤ����
					if ( $curProductTotalPrice != ( $_POST["aryPoDitail"][$i]["curTaxPrice"] + $_POST["aryPoDitail"][$i]["curTotalPrice"] ) )
					{
						// �����ǳ� �� ��ȴ��� �� ��Ψ
						$curTaxPrice = $_POST["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
						// ü��������Ԥ�
						$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );

						$_POST["aryPoDitail"][$i]["curTaxPrice"] = $curTaxPrice;
					}
				}
			}

			// ���ٹԤ�hidden�ͤ��Ѵ�����
			$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert", $objDB);

			// �����̾������
			$aryHeadColumnNames = fncSetSalesTabelName( $aryTableViewHead, $aryTytle );
			// �����̾������
			$aryDetailColumnNames = fncSetSalesTabelName( $aryTableViewDetail, $aryTytle );

			for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{

				$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;

				// ����ʬ
				$_POST["aryPoDitail"][$i]["strSalesClassName"] = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $_POST["aryPoDitail"][$i]["lngSalesClassCode"] ,'', $objDB );

				// ñ��
				$_POST["aryPoDitail"][$i]["strProductUnitName"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

				// �Ƕ�ʬ
				$_POST["aryPoDitail"][$i]["strTaxClassName"] = fncGetMasterValue( "m_taxclass", "lngtaxclasscode", "strtaxclassname", $_POST["aryPoDitail"][$i]["lngTaxClassCode"], '', $objDB );

				// ��Ψ
				$_POST["aryPoDitail"][$i]["strTaxName"] = ( $_POST["aryPoDitail"][$i]["lngTaxCode"] != "" ) ?  fncGetMasterValue( "m_tax", "lngtaxcode", "curtax", $_POST["aryPoDitail"][$i]["lngTaxCode"], '', $objDB ) : "";

				// �ܵ�����
				$_POST["aryPoDitail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );

				// ���ٹ����ͤ��ü�ʸ���Ѵ�
				$_POST["aryPoDitail"][$i]["strDetailNote"] = fncHTMLSpecialChars( $_POST["aryPoDitail"][$i]["strDetailNote"] );


				$strProductName = "";

				if( $strProductName = fncGetMasterValue( "m_product", "strproductcode", "strproductname",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB ) )
				{
					$_POST["aryPoDitail"][$i]["strproductname"] = $strProductName;
				}

				// ���Ǥκݤ����ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤ�ʤ������ǳۤκƷ׻���Ԥ�
				if ( $_POST["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_UCHIZEI ) 
				{
					// ���ʲ��� = ����ñ�� �� ����
					$curProductTotalPrice = $_POST["aryPoDitail"][$i]["curProductPrice"] * $_POST["aryPoDitail"][$i]["lngGoodsQuantity"];

					// ���ʲ��� �� ��ȴ��� �� �ǳ� �ˤʤäƤ��ʤ����
					if ( $curProductTotalPrice != ( $_POST["aryPoDitail"][$i]["curTaxPrice"] + $_POST["aryPoDitail"][$i]["curTotalPrice"] ) )
					{
						// �����ǳ� �� ��ȴ��� �� ��Ψ
						$curTaxPrice = $_POST["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
						// ü��������Ԥ�
						$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );

						$_POST["aryPoDitail"][$i]["curTaxPrice"] = $curTaxPrice;
					}
				}


				$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
				$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ( $_POST["aryPoDitail"][$i]["curProductPrice"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
				$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
				$_POST["aryPoDitail"][$i]["curtotalprice_DIS"] = ( $_POST["aryPoDitail"][$i]["curTotalPrice"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";
				$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] = ( $_POST["aryPoDitail"][$i]["curTaxPrice"] != "" ) ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";

				// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
				$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["lngsalesclasscode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["lngSalesClassCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["lngSalesClassCode"]."]" : "";


				$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;



				// �ƥ�ץ졼���ɤ߹���
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "sc/result/parts_detail2.tmpl" );

				// �ƥ�ץ졼������
				$objTemplate->replace( $aryDetailColumnNames );
				$objTemplate->replace( $_POST["aryPoDitail"][$i] );
				$objTemplate->complete();

				// HTML����
				$aryDetailTable[] = $objTemplate->strTemplate;
			}


			$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );

			$aryData["strMode"] = "regist";
			$aryData["strProcMode"] = "renew";

			// ��Ͽ��
			$aryData["dtminsertdate"] = date( 'Y/m/d', time());
			// ����
			$aryData["strAction"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];

			// ���ϼ� 
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserName"] = $objAuth->UserDisplayName;

			// ����
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
			// ô����
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
			// �̲�
			$_POST["strMonetaryUnitName"] = ($aryData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryData["lngMonetaryUnitCode"];
			$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );


			// �졼�ȥ�����
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryData["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;



/*
			//---------------------------------------------------------------
			// �����֤μ���
			//---------------------------------------------------------------
			if( strcmp( $aryData["lngsalesstatuscode"], "" ) != 0 )
			{
				$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData["lngSalesStatusCode"],'', $objDB );
				$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
				$aryData["lngSalseStatusCode"]         = $aryData["lngSalesStatusCode"];
			}
*/

			// ����ե�ɽ������ɽ������
			$aryData["visibleWF"] = "hidden";


			//---------------------------------------------
			// ��ǧ�롼��
			//---------------------------------------------
			if ( $_POST["lngWorkflowOrderCode"] != "" and $_POST["lngWorkflowOrderCode"] != 0 )
			{
				$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $_POST["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB );

				$aryData["strWorkflowMessage_visibility"] = "block;";
			}
			else
			{
				$aryData["strWorkflowOrderName"] = "��ǧ�ʤ�";

				$aryData["strWorkflowMessage_visibility"] = "none;";
			}



			//�إå����ͤ��ü�ʸ���Ѵ�
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );

			$aryData["lngRegistConfirm"] = 0;

			$aryData["RENEW"] = TRUE;

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];



			// ����
			$aryData["strAction"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];

			$aryData["strActionURL"] = "/sc/regist/index2.php?strSessionID=".$aryData["strSessionID"];

			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��

			// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
			$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
			$aryData["lngcustomercode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
			//$aryData["lngInchargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInchargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";

			//$aryData["strReceiveCode_Editable"] = "contenteditable=\"false\"";

			$aryData["lngCalcCode"] = DEF_CALC_KIRISUTE;



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



			// CRC�ꥹ��
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';



			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����



			// ľ��Ͽ�ե饰
			$aryData["lngDirectRegistFlag"] = 0;

			// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
			if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
				$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
			}
			else
			{
				$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
				$aryData["blnBtnEditable"]        = '';			// �ܥ���������
			}



			// �ƥ�ץ졼���ɤ߹���
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "sc/confirm/parts.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryHeadColumnNames );
			$objTemplate->replace( $aryData );
			$objTemplate->complete();

			// HTML����
			echo $objTemplate->strTemplate;

			$objDB->close();

			return true;

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





	//-------------------------------------------------------------------------
	// �� ���ɽ�� -> ���������
	//-------------------------------------------------------------------------
	// ����ֹ����
	$lngSalesNo = $_REQUEST["lngSalesNo"];


	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "s.lngSalesNo, ";															// 1:����ֹ�
	$aryQuery[] = "s.lngRevisionNo, ";														// 2:��ӥ�����ֹ� 
	$aryQuery[] = "s.strSalesCode, ";														// 3:��女����
	$aryQuery[] = "tsd.lngReceiveNo, ";														// 4:�����ֹ� 
	$aryQuery[] = "To_char( s.dtmAppropriationDate, 'YYYY/mm/dd') as dtmOrderAppDate, ";	// 5:�׾���
	$aryQuery[] = "s.lngCustomerCompanyCode as lngCustomerCode, ";							// 6:�ܵ�
	//$aryQuery[] = "s.lngGroupCode as lnginchargegroupcode, ";								// 7:����
	//$aryQuery[] = "s.lngUserCode as lnginchargeusercode, ";									// 8:ô����
	$aryQuery[] = "s.lngSalesStatusCode, ";													// 9:�����֥����� 
	$aryQuery[] = "s.lngMonetaryUnitCode, ";												// 10:�̲�ñ�̥�����
	$aryQuery[] = "s.lngMonetaryRateCode, ";												// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "s.curConversionRate, ";													// 12:�����졼��
	$aryQuery[] = "s.strSlipCode, ";														// 13:��ɼ������
	$aryQuery[] = "s.curTotalPrice, ";														// 14:��׶��
	$aryQuery[] = "s.strNote ";																// 15:����
	$aryQuery[] = "FROM m_sales s ";
	$aryQuery[] = "left join t_salesdetail tsd on tsd.lngsalesno = s.lngsalesno";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "s.lngSalesNo = $lngSalesNo";

	$strQuery = implode("\n", $aryQuery );


	// �����꡼�¹�
	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}


	// �ǡ�������
	$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );



/*
	//-------------------------------------------------------------------------
	// ������֤Υ����å�
	//-------------------------------------------------------------------------
	// ������ξ��
	if( $aryData["lngorderstatuscode"] == DEF_RECEIVE_APPLICATE )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// Ǽ�ʺѤξ��
	if( $aryData["lngorderstatuscode"] == DEF_RECEIVE_END )
	{
		fncOutputError( 405, DEF_WARNING, "", TRUE, "", $objDB );
	}
*/


	// �����ֹ����
	$lngReceiveNo = $aryData2["lngreceiveno"];


	if( $lngReceiveNo != "" )
	{ 
		$aryData["strReceiveCode"] = fncGetMasterValue( "m_receive", "lngreceiveno", "strreceivecode", $lngReceiveNo , '',$objDB );
		$aryData["strReviseCode"]  = fncGetMasterValue( "m_receive", "lngreceiveno", "strrevisecode", $lngReceiveNo , '',$objDB );
	}


	// �ץ�������˥塼������
	// �̲�
	$lngMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData2["lngmonetaryunitcode"], "", $objDB );
	$aryData["lngmonetaryunitcode"] = fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );

	// �졼�ȥ�����
	$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, $aryData2["lngmonetaryratecode"], '', $objDB );
	// ��ʧ���
	$aryData["lngpayconditioncode"] = fncPulldownMenu( 2, $aryData2["lngpayconditioncode"], '', $objDB );

	//�إå����ͤ��ü�ʸ���Ѵ�
	$aryData["strnote"] = fncHTMLSpecialChars( $aryData2["strnote"] );


	// �ǡ����Υޡ���
	$aryData = array_merge( $aryData2, $aryData );

	// �ؿ� fncChangeDisplayName��ɽ���ѥǡ������Ѵ�(HEADER��
	$aryData = fncChangeData2( $aryData , $objDB );



	// ���ٹ�
	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngSalesNo, ";									// 1:����ֹ�
	$aryQuery[] = "lngSalesDetailNo, ";								// 2:��������ֹ�
	$aryQuery[] = "lngSalesDetailNo as lngOrderDetailNo, ";			// �ݡݡ����ٹ��ֹ�
	$aryQuery[] = "lngRevisionNo, ";								// 3:��ӥ�����ֹ�
	$aryQuery[] = "strProductCode, ";								// 4:���ʥ�����
	$aryQuery[] = "lngSalesClassCode, ";							// 5:����ʬ������
	$aryQuery[] = "To_char(dtmDeliveryDate, 'YYYY/mm/dd') as dtmDeliveryDate,";	// 6:Ǽ����
	$aryQuery[] = "lngConversionClassCode, ";						// 7:������ʬ������  1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
	$aryQuery[] = "curProductPrice, ";								// 8:���ʲ���
	$aryQuery[] = "lngProductQuantity as lngGoodsQuantity, ";		// 9:���ʿ���
	$aryQuery[] = "lngProductUnitCode, ";							// 10:����ñ�̥�����
	$aryQuery[] = "lngTaxClassCode, ";								// 11:�����Ƕ�ʬ������
	$aryQuery[] = "lngTaxCode, ";									// 12:�����ǥ�����
	$aryQuery[] = "curTaxPrice, ";									// 13:�����Ƕ��
	$aryQuery[] = "curSubTotalPrice, ";								// 14:���׶��
	$aryQuery[] = "strnote as strDetailNote ";										// 15:����

	$aryQuery[] = ",lngreceiveno";
	$aryQuery[] = ",lngreceivedetailno";

	$aryQuery[] = "FROM t_salesdetail ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngSalesNo = $lngSalesNo";
	$aryQuery[] = " ORDER BY lngSortKey ASC";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );



	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}


	if( !$lngResultNum = pg_num_rows( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}
	else
	{
		for( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryQueryResult[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}



	// ���ٹԤ�hidden�ͤ��Ѵ�����
	$aryData["strDetailHidden"] = fncDetailHidden_sc( $aryQueryResult ,"detail", $objDB );


	// �ץ�������˥塼������
	// �̲�
	$lngMonetaryUnit = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData2["lngmonetaryunitcode"],'', $objDB );
	$aryData["lngmonetaryunitcode"] = fncPulldownMenu( 0, $lngMonetaryUnit, '', $objDB );





	//-------------------------------------------------------------------------
	// �����֤Υ����å�
	//-------------------------------------------------------------------------
	// ������ξ��
	if( $aryData2["lngsalesstatuscode"] == DEF_SALES_APPLICATE )
	{
		fncOutputError( 608, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// ����Ѥξ��
	if( $aryData2["lngsalesstatuscode"] == DEF_SALES_CLOSED )
	{
		fncOutputError( 9062, DEF_WARNING, "", TRUE, "", $objDB );
	}




	//-------------------------------------------------------------------------
	// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
	//-------------------------------------------------------------------------
	$aryData2["lngsalesstatuscode"] = fncCheckNullStatus( $aryData2["lngsalesstatuscode"] );




	//---------------------------------------------------------------
	// �����֤μ���
	//---------------------------------------------------------------
	$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData2["lngsalesstatuscode"],'', $objDB );
	$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
	$aryData["lngSalseStatusCode"]         = $aryData2["lngsalesstatuscode"];





	// ����ե�ɽ������ɽ������
	$aryData["visibleWF"] = "hidden";
	// ��ǧ�롼�Ȥμ���
	$lngWorkflowOrderCode = 0;
	//$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $lngReceiveNo, '', $objDB );
	// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );



	// �ܵҼ����ֹ�μ���
	if( $lngReceiveNo )
	{
		$strCustomerReceiveCode = fncGetMasterValue( "m_receive", "lngreceiveno", "strcustomerreceivecode", $lngReceiveNo, '', $objDB );
		$aryData["strCustomerReceiveCode"] = $strCustomerReceiveCode;
	}



	// �졼�ȥ�����
//	$aryData["lngmonetaryratecode"]   = fncPulldownMenu( 1, $aryData["lngmonetaryratecode"], '', $objDB );
	// ��ʧ���
	$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
	// ����ñ��
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// �ٻ�ñ��
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
	// ����ʬ
	$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );

	$aryData["MonetaryUnitDisabled"]  = " disabled";


	$aryData["strGetDataMode"]        = "none";
	$aryData["strProcMode"]           = "check";
	$aryData["strSessionID"]          = $aryData["strSessionID"];
	$aryData["ReceiveSubmit"]         = "";
	$aryData["ReceiveSubmit2"]        = "";
	$aryData["strCustomerReceiveDis"] = 'contenteditable="false"';
	$aryData["strProductCodeOpenDis"] = 'contenteditable="false"';
	//$aryData["strReceiveCode_Editable"] = 'contenteditable="false"';
	$aryData["RENEW"]                 = TRUE;
	$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]      = "renew";
	$aryData["lngFunctionCode"]       = DEF_FUNCTION_SC5;



	// CRC�ꥹ��
	$aryData["crcflag"] = '0';
	$aryData["crcview"] = 'hidden';

	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


	// ľ��Ͽ�ե饰
	$aryData["lngDirectRegistFlag"] = 0;

	// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
	if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
		$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
		$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
	}
	else
	{
		$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
		$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
		$aryData["blnBtnEditable"]        = '';			// �ܥ���������
	}


	$objDB->close();
	$objDB->freeResult( $lngResultID );

	echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>