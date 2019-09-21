<?php

// ----------------------------------------------------------------------------
/**
*       ������  ��Ͽ
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
*         ����Ͽ����
*         �����顼�����å�
*         ����Ͽ������λ�塢��Ͽ��λ���̤�
*
*       ��������
*       2013.05.31����������Ψ�������������ʴ��������Ψ�����Ǥ��ʤ���硢�ǿ����֤���Ψ��������� ��
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php");
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."sc/cmn/lib_scp.php" );
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

	$lngInputUserCode = $objAuth->UserCode;

	// 600 ������
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 601 �������������Ͽ��
	if( fncCheckAuthority( DEF_FUNCTION_SCO1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// ���ٹԤ����
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );

		if( $strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}


	// displayCode��code���Ѵ�����
	// fncChangeData�ǿ������������
	$aryNewData = fncChangeData( $aryData, $objDB );


	// ���ٹԤ����������
	for($i=0; $i<count($_POST[aryPoDitail]); $i++ )
	{
		while( list( $strKeys, $strValues ) = each( $_POST[aryPoDitail][$i] ) )
		{
			$aryNewData["aryPoDitail"][$i][$strKeys] = ( $strValues == "" ) ? "null" : $strValues ;
		}
	}


	//-------------------------------------------------------------------------
	// �� �ȥ�󥶥�����󳫻�
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();



	//-------------------------------------------------------------------------
	// �� DB -> SELECT : m_productprice
	//-------------------------------------------------------------------------
	// Ʊ���ֻ������ܡסֻ������ʡפξ���Ʊ��ñ�������뤫
	// m_productPrice��Ʊ���ͤ����뤫���ߤä���������˹��ֹ�򵭲�����
	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

		$lngmonetaryunitcode = "";
		$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
		$strProductCode = "";
		$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "lngproductno", $aryNewData["aryPoDitail"][$i]["strProductCode"]. ":str", '', $objDB );

		$arySelect = array();
		$arySelect[] = "SELECT ";
		$arySelect[] = "lngproductpricecode ";
		$arySelect[] = "FROM ";
		$arySelect[] = "m_productprice ";
		$arySelect[] = "WHERE ";
		$arySelect[] = "lngproductno = $strProductCode AND ";
		$arySelect[] = "lngsalesclasscode = ".$aryNewData["aryPoDitail"][$i]["lngSalesClassCode"]." AND ";
		$arySelect[] = "lngmonetaryunitcode = $lngmonetaryunitcode AND ";
		$arySelect[] = "curproductprice = ".$aryNewData["aryPoDitail"][$i]["curProductPrice"];

		$strSelect = implode("\n", $arySelect );

		if ( $lngResultID = $objDB->execute( $strSelect ) )
		{
			// Ʊ�����ʲ��ʤ����Ĥ���ʤ���硢�⤷����ñ�̷׾夬����ñ�̷׾�ξ��Τ߹��ֹ�򵭲�����
			if( pg_num_rows( $lngResultID ) == 0 and $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" )
			{
				$aryM_ProductPrice[] = $i;		//���ֹ�򵭲�
			}
		}
		$objDB->freeResult( $lngResultID );

	}



	//-------------------------------------------------------------------------
	// �� m_Sales �Υ��������ֹ�����
	//-------------------------------------------------------------------------
	$sequence_m_sales = fncGetSequence( 'm_sales.lngSalesNo', $objDB );


	//-------------------------------------------------------------------------
	// �� DB -> SELECT : m_Receive
	//-------------------------------------------------------------------------
	// �����ֹ�
	$strReceiveCode = $aryNewData["strReceiveCode"];

	if( $strReceiveCode != "null" )
	{
		$aryQuery = array();
		$aryQuery[] = "SELECT "; 
		$aryQuery[] = "r.lngReceiveNo, ";										// 1:�����ֹ�
		$aryQuery[] = "r.lngReceiveStatusCode as lngSalesStatusCode ";			// 9:������֥�����
		$aryQuery[] = "FROM m_Receive r ";
		$aryQuery[] = "WHERE r.strReceiveCode = '". $strReceiveCode . "' ";
		$aryQuery[] = "AND r.bytInvalidFlag = FALSE ";
		$aryQuery[] = "AND r.lngRevisionNo >= 0 ";
		$aryQuery[] = "AND r.lngRevisionNo = ( ";
		$aryQuery[] = "SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode  ";
		$aryQuery[] = "AND r2.strReviseCode = ( ";
		$aryQuery[] = "SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) ) ";
		$aryQuery[] = "AND 0 <= ( ";
		$aryQuery[] = "SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum == 1 )
		{
			$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
		}
		// ���ꤵ�줿ȯ��¸�ߤ��ʤ����
		else
		{
			fncOutputError ( 403, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		$lngReceiveCode = $aryReceiveResult["lngreceiveno"];
	}
	else
	{
		$lngReceiveCode = "null";
	}



	// �̲ߤ򥳡��ɤ��Ѵ�
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];
	$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );

	// ���ϼԥ����ɤ����
	$lngUserCode = $objAuth->UserCode;

	// ��ɼ�ֹ�
	$strSlipCode = ( $aryNewData["strSlipCode"] != "null" ) ? "'".$aryNewData["strSlipCode"]."'" : "null";
	// ����
	$strNote = ( $aryNewData["strNote"] != "null" ) ? "'".$aryNewData["strNote"]."'" : "null";



	// �ܵҥ����ɤ����
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );

	//var_dump( $aryNewData["lngCustomerCode"] ); exit();


	//-------------------------------------------------------------------------
	// �� �����⡼�ɤ��ֽ����פξ��
	//-------------------------------------------------------------------------
	// ��ӥ�����ֹ� // ��女����
	if( $aryNewData["strProcMode"] == "renew" )
	{
		//-----------------------------------------------------------
		// �ǿ���Х����ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngSalesNo, lngSalesStatusCode FROM m_Sales s WHERE s.strSalesCode = '" . $aryNewData["strSalesCode"] . "'";
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



		//-------------------------------------------------------------------------
		// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
		//-------------------------------------------------------------------------
		$lngSalesStatusCode = fncCheckNullStatus( $lngSalesStatusCode );

		//-------------------------------------------------------------------------
		// ���֥����ɤ���0�פξ�硢��1�פ������
		//-------------------------------------------------------------------------
		$lngSalesStatusCode = fncCheckZeroStatus( $lngSalesStatusCode );




		$strsalsecode = $aryNewData["strSalesCode"];

		// �����ξ��Ʊ���������Ф��ƥ�ӥ�����ֹ�κ����ͤ��������
		// ��ӥ�����ֹ�򸽺ߤκ����ͤ�Ȥ�褦�˽�������
		// ���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ���������Ф��ƥ�å����֤ˤ���
		$strLockQuery = "SELECT lngRevisionNo FROM m_Sales WHERE strSalesCode = '" . $strsalsecode . "' FOR UPDATE";

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

			// ��ӥ�����ֹ�򥤥󥯥����
			$lngrevisionno = $lngMaxRevision + 1;
		}
		else
		{
			$lngrevisionno = $lngMaxRevision;
		}

		// ���ID�����
		$objDB->freeResult( $lngLockResultID );
	}
	//-------------------------------------------------------------------------
	// �� �����⡼�ɤ�����Ͽ�פξ��
	//-------------------------------------------------------------------------
	else
	{
		// ��ӥ�����ֹ������
		$lngrevisionno = 0;

		// ����ֹ�μ���
		$strsalsecode = fncGetDateSequence( date( 'Y', strtotime( $aryNewData["dtmOrderAppDate"] ) ), date( 'm',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_sales.lngSalesNo", $objDB );

		// �����֥����ɤμ���
		$lngSalesStatusCode = ( $aryNewData["lngWorkflowOrderCode"] == 0 ) ? DEF_SALES_ORDER : DEF_SALES_APPLICATE;
	}

	//-------------------------------------------------------------------------
	// �� DB -> INSERT : m_sales
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_sales ( ";
	$aryQuery[] = "lngsalesno, ";											// 1:����ֹ�
	$aryQuery[] = "lngrevisionno, ";										// 2:��ӥ�����ֹ�
	$aryQuery[] = "strsalescode, ";											// 3:��女����(yymmxxx ǯ��Ϣ�֤ǹ������줿7����ֹ�)
	$aryQuery[] = "lngreceiveno, ";											// 4:�����ֹ�
	$aryQuery[] = "dtmappropriationdate, ";									// 5:�׾���
	$aryQuery[] = "lngcustomercompanycode, ";								// 6:�ܵ�
	//$aryQuery[] = "lnggroupcode, ";											// 7:����
	//$aryQuery[] = "lngusercode, ";											// 8:ô����
	$aryQuery[] = "lngsalesstatuscode, ";									// 9:�����֥�����
	$aryQuery[] = "lngmonetaryunitcode, ";									// 10:�̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode, ";									// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";									// 12:�����졼��
	$aryQuery[] = "strslipcode, ";											// 13:��ɼ������ 
	$aryQuery[] = "curtotalprice, ";										// 14:��׶��
	$aryQuery[] = "strnote, ";												// 15:����
	$aryQuery[] = "lnginputusercode, ";										// 16:���ϼԥ�����
	$aryQuery[] = "bytinvalidflag, ";										// 17:̵���ե饰
	$aryQuery[] = "dtminsertdate";											// 18:��Ͽ��
	$aryQuery[] = " ) values ( ";
	$aryQuery[] = "$sequence_m_sales,";										// 1:����ֹ�
	$aryQuery[] = "$lngrevisionno, ";										// 2:��ӥ�����ֹ�
	$aryQuery[] = "'$strsalsecode', ";										// 3:��女����
	$aryQuery[] = "null, ";													// 4:�����ֹ�
	//$aryQuery[] = "$lngReceiveCode, ";										// 4:�����ֹ�
	$aryQuery[] = "'".$aryNewData["dtmOrderAppDate"]."',";					// 5:�׾���
	$aryQuery[] = $aryNewData["lngCustomerCode"].", ";						// 6:�ܵ�
	//$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";					// 7:����
	//$aryQuery[] = $aryNewData["lngInChargeUserCode"].", ";					// 8:ô����

	$aryQuery[] = $lngSalesStatusCode . ", ";								// 9:�����֥�����
/*
	if( $strReceiveCode == "null" )// 9:�����֥�����
	{
		$aryQuery[] = DEF_SALES_END . ", ";
	}
	else
	{
		$aryQuery[] = $lngSalesStatusCode . ", ";
	}
*/

	$aryQuery[] = "$lngmonetaryunitcode, ";									// 10:�̲�ñ�̥�����
	$aryQuery[] = $aryNewData["lngMonetaryRateCode"].", ";					// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "'".$aryNewData["curConversionRate"]."', ";				// 12:�����졼��
	$aryQuery[] = "$strSlipCode, ";											// 13:��ɼ������
	$aryQuery[] = "'".$aryNewData["curAllTotalPrice"]."', ";				// 14:��׶��
	$aryQuery[] = "$strNote, ";												// 15:����
	$aryQuery[] = "$lngUserCode, ";											// 16:���ϼԥ�����
	$aryQuery[] = "false, ";												// 17:̵���ե饰
	$aryQuery[] = "now() ";													// 18:��Ͽ��
	$aryQuery[] = ")";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );




	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$objDB->freeResult( $lngResultID );


/*
	////////////////////////////////////
	//// ���ٹ��ֹ椬�����ʹԤ��н� ////
	////////////////////////////////////
	$lngMaxDetailNo = 0;

	if ( $lngReceiveCode != "null" )
	{
		// ���ꤵ��Ƥ������Ǥκ����ͤ����
		$strQuery = "SELECT MAX(lngReceiveDetailNo) as maxDetailNo FROM t_ReceiveDetail WHERE lngReceiveNo = " . $lngReceiveCode;
		// ���������꡼�μ¹�
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$lngMaxDetailNo = $objResult->maxdetailno;
		}
		else
		{
			fncOutputError ( 9051, DEF_ERROR, "����μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngResultID );
	}
	else
	// ����No����ꤷ�ʤ������ξ��
	{
		// ���ٹԤ���ǻ��ꤵ��Ƥ�������ͤ����
		for ( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
		{
			if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "null" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "undefined" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] > $lngMaxDetailNo )
			{
				$lngMaxDetailNo = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
			}
		}
	}
*/

	// ��ǧ���̤����Ψ�Υ����ɤ��Ϥ���ʤ����Ϥ��λ��η׾��������ټ������ʤ���
	// ���ٹ��Ѥ˾����ǥ����ɤ��������
	// �����ǥ�����
	// �׾�����ꤽ�λ�����Ψ���Ȥ��
	$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
		. "FROM m_tax "
		. "WHERE dtmapplystartdate <= '" . $aryNewData["dtmOrderAppDate"] . "' "
		. "AND dtmapplyenddate >= '" . $aryNewData["dtmOrderAppDate"] . "' "
		. "GROUP BY lngtaxcode, curtax "
		. "ORDER BY 3 ";
fncDebug( 'pc_regist_index2.txt', $strQuery, __FILE__, __LINE__);
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
		// �ǿ�����Ψ������������ 20130531 add
		$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
			. "FROM m_tax "
			. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
			. "GROUP BY lngtaxcode, curtax ";
fncDebug( 'pc_regist_index2.txt', $strQuery, __FILE__, __LINE__);
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



	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
/*
		// ���������ֹ�
		// ���ٹ��ֹ椬�ʤ����ʻ������ɲä��줿���ٹԤξ���
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}
*/

		// ����
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" )  ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

		// ������ʬ������
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2 ;


		// �ǳۤ��⤷NULL�ʤ�С��Ƕ�ʬ����ȴ��ۤ��Ƚ�Ǥ����Ʒ׻�����
		$lngCalcCode = DEF_CALC_KIRISUTE;

		// �������̲�ñ�̥����ɤ������оݷ��������
		if ( $lngmonetaryunitcode == DEF_MONETARY_YEN )
		{
			$lngDigitNumber = 0; // ���ܱߤξ��ϣ���
		}
		else
		{
			$lngDigitNumber = 2; // ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
		}

		// �ǳ�
		if ( $aryNewData["aryPoDitail"][$i]["curTaxPrice"] == "null" )
		{
			// �Ƕ�ʬ������ǰʳ��ξ��
			if ( $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"] != DEF_TAXCLASS_HIKAZEI )
			{
				$curTaxPrice = $aryNewData["aryPoDitail"][$i]["curTotalPrice"] * $curTax;
				// ü��������Ԥ�
				$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );
			}
			else
			{
				$curTaxPrice = 0;
			}
		}
		else
		{
			$curTaxPrice = $aryNewData["aryPoDitail"][$i]["curTaxPrice"];
		}

		// �����ǥ�����
		// ����Ǥξ���NULL�ͤ�����
		if ( $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"] == DEF_TAXCLASS_HIKAZEI )
		{
			$lngTaxCode_Detail = "null";
		}
		// ����ǰʳ��ξ��϶��̤��ǥ����ɤ�����
		else
		{
			$lngTaxCode_Detail = $lngTaxCode;
		}

		// �������ֹ�
		$lngSortKey = $i + 1;

		//-----------------------------------------------------------
		// DB -> INSERT : t_salesdetail
		//-----------------------------------------------------------
		$aryQuery	= array();
		$aryQuery[] = "INSERT INTO t_salesdetail ( ";
		$aryQuery[] = "lngreceiveno,";													// �����ֹ�
		$aryQuery[] = "lngreceivedetailno,";											// ���ٹ��ֹ�

		$aryQuery[] = "lngsalesno, ";													// 1:����ֹ�
		$aryQuery[] = "lngsalesdetailno, ";												// 2:��������ֹ�
		$aryQuery[] = "lngrevisionno, ";												// 3:��ӥ�����ֹ�
		$aryQuery[] = "strproductcode, ";												// 4:���ʥ�����
		$aryQuery[] = "lngsalesclasscode, ";											// 5:����ʬ������
		$aryQuery[] = "dtmdeliverydate, ";												// 6:Ǽ����
		$aryQuery[] = "lngconversionclasscode, ";										// 7:������ʬ������
		$aryQuery[] = "curproductprice, ";												// 8:���ʲ���
		$aryQuery[] = "lngproductquantity, ";											// 9:���ʿ���
		$aryQuery[] = "lngproductunitcode, ";											// 10:����ñ�̥�����
		$aryQuery[] = "lngtaxclasscode, ";												// 11:�����Ƕ�ʬ������
		$aryQuery[] = "lngtaxcode, ";													// 12:�����ǥ�����
		$aryQuery[] = "curtaxprice, ";													// 13:�����Ƕ��
		$aryQuery[] = "cursubtotalprice, ";												// 14:���׶��
		$aryQuery[] = "strnote, ";														// 15:����
		$aryQuery[] = "lngSortKey ";													// 16:ɽ���ѥ����ȥ���

		$aryQuery[] = " ) values ( ";

		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngReceiveNo"] . ",";				// �����ֹ�
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngReceiveDetailNo"] . ",";		// ���ٹ��ֹ�


		$aryQuery[] = "$sequence_m_sales, ";											// 1:����ֹ�
		$aryQuery[] = $i + 1 . ", ";													// 2:��������ֹ� �Ԥ��Ȥ�����ȯ��ϻ��äƤ���
		//$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ", ";		// 2:��������ֹ� �Ԥ��Ȥ�����ȯ��ϻ��äƤ���
		$aryQuery[] = "$lngrevisionno, ";												// 3:��ӥ�����ֹ�
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";		// 4:���ʥ�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngSalesClassCode"].", ";			// 5:����ʬ������

		if ( $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] == "" or $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] == "null" )
		{
			$aryQuery[] = $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] . ", ";
		}
		else
		{
			$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";		// 6:Ǽ����
		}

		$aryQuery[] = "$lngConversionClassCode, ";										// 7:������ʬ������
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."', ";		// 8:���ʲ���
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";			// 9:���ʿ���
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";		// 10:����ñ�̥�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"].", ";			// 11:�����Ƕ�ʬ������

		$aryQuery[] = "$lngTaxCode_Detail, ";											// 12:�����ǥ�����

		$aryQuery[] = $curTaxPrice . ", ";			// 13:�����Ƕ��

		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curTotalPrice"]."', ";		// 14:���׶��
		$aryQuery[] = $strDetailNote. ", ";												// 15:����
		$aryQuery[] = $lngSortKey. " ";													// 16:ɽ���ѥ����ȥ���
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			$objDB->close();
		}
		$objDB->freeResult( $lngResultID );

	}


	//require( LIB_DEBUGFILE );
	//fncDebug( 'lib_sc.txt', $aryNewData["aryPoDitail"], __FILE__, __LINE__);
	//exit();


	$lngCalcCode = DEF_CALC_KIRISUTE;		// ��������ü���������ڼΤ�


	////////////////////////////////////////////////////
	//// ����������Ͽ�ˤ�������Ф��Ƥξ������� ////
	////////////////////////////////////////////////////
	$lngReceiveNoBuff = null;

	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$lngReceiveNo = $aryNewData["aryPoDitail"][$i]["lngReceiveNo"];

		if( $lngReceiveNoBuff == $lngReceiveNo )
		{
			continue;
		}
		else
		{
			$lngReceiveNoBuff = null;
		}

		if( $lngReceiveNo != "" and $lngReceiveNo != "null" )
		{
			$lngResult = fncSalesSetStatus( $lngReceiveNo, $lngCalcCode, $objDB );

			if( $lngResult == 1 )
			{
				fncOutputError( 403, DEF_ERROR, "", TRUE, "", $objDB );
			}
			else if( $lngResult == 2 )
			{
				fncOutputError( 9061, DEF_ERROR, "", TRUE, "", $objDB );
			}
		}

		$lngReceiveNoBuff = $lngReceiveNo;
	}


	//-------------------------------------------------------------------------
	// �� DB -> INSERT : m_productprice
	//-------------------------------------------------------------------------
	// m_productPrice Ʊ���ͤ����äƤ��ʤ����
	if( count($aryM_ProductPrice) != 0)
	{
		for( $i = 0; $i < count( $aryM_ProductPrice ); $i++ )
		{
			// m_order�Υ������󥹤����
			$sequence_m_productprice = fncGetSequence( 'm_ProductPrice.lngProductPriceCode', $objDB );

			list ( $strKeys, $strValues ) = each( $aryM_ProductPrice );
			$curProductPrice = sprintf("%d", $aryNewData["aryPoDitail"][$strValues]["curProductPrice"]);

			$aryQuery = array();
			$aryQuery[] = "INSERT INTO m_productprice (";
			$aryQuery[] = "lngproductpricecode, ";												// ���ʲ��ʥ����� 
			$aryQuery[] = "lngproductno,";														// �����ֹ�
			$aryQuery[] = "lngsalesclasscode, ";												// ����ʬ������
			$aryQuery[] = "lngmonetaryunitcode,";												// �̲�ñ�̥�����
			$aryQuery[] = "curproductprice ";													// ���ʲ��� 
			$aryQuery[] = ") VALUES (";
			$aryQuery[] = "$sequence_m_productprice, ";
			$aryQuery[] = "'".$aryNewData["aryPoDitail"][$strValues]["strProductCode"]."', ";
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["lngSalesClassCode"].",";
			$aryQuery[] = "$lngmonetaryunitcode,";
			$aryQuery[] = "'".$curProductPrice."'";
			$aryQuery[] = ")";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			// echo "<br>$strQuery<br>";

			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
				fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}
	}




/*
	//-------------------------------------------------------------------------
	// �� ��ǧ����
	//
	//   ��ǧ�롼��
	//     ��0 : ��ǧ�롼�Ȥʤ�
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryNewData["lngWorkflowOrderCode"];	// ��ǧ�롼��

	$strWFName   = "��� [No:" . $strsalsecode . "]";
	$lngSequence = $sequence_m_sales;
	$strDefFnc   = DEF_FUNCTION_SC1;

	$strProductCode       = $aryNewData["aryPoDitail"][0]["strProductCode"];
	$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );


	// ��ǧ�롼�Ȥ����򤵤줿���
	if( $lngWorkflowOrderCode != 0 )
	{
		//---------------------------------------------------------------
		// DB -> INSERT : m_workflow
		//---------------------------------------------------------------
		// m_workflow �Υ������󥹤����
		$lngworkflowcode = fncGetSequence( 'm_Workflow.lngworkflowcode', $objDB );
		$strworkflowname = $strWFName;

		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO m_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// 1  : ����ե�������
		$aryQuery[] = "lngworkflowordercode, ";						// 2  : ����ե����������
		$aryQuery[] = "strworkflowname, ";							// 3  : ����ե�̾��
		$aryQuery[] = "lngfunctioncode, ";							// 4  : ��ǽ������
		$aryQuery[] = "strworkflowkeycode, ";						// 5  : ����ե����������� 
		$aryQuery[] = "dtmstartdate, ";								// 6  : �Ʒ�ȯ����
		$aryQuery[] = "dtmenddate, ";								// 7  : �Ʒｪλ��
		$aryQuery[] = "lngapplicantusercode, ";						// 8  : �Ʒ����ԥ�����
		$aryQuery[] = "lnginputusercode, ";							// 9  : �Ʒ����ϼԥ�����
		$aryQuery[] = "bytinvalidflag, ";							// 10 : ̵���ե饰
		$aryQuery[] = "strnote";									// 11 : ����

		$aryQuery[] = " ) values (";
		$aryQuery[] = "$lngworkflowcode, ";							// 1  : ����ե�������
		$aryQuery[] = ( $lngWorkflowOrderCode != "" ) ? $lngWorkflowOrderCode . ", " : "null, ";	// 2  : ����ե����������
		$aryQuery[] = "'$strworkflowname', ";						// 3  : ����ե�̾��
		$aryQuery[] = $strDefFnc . ", ";								// 4  : ��ǽ������
		$aryQuery[] = $lngSequence . ", ";							// 5  : ����ե����������� 
		$aryQuery[] = "now(), ";									// 6  : �Ʒ�ȯ����
		$aryQuery[] = "null, ";										// 7  : �Ʒｪλ��
		$aryQuery[] = $lngApplicantUserCode . ", ";					// 8  : �Ʒ����ԥ�����
		$aryQuery[] = "$lngUserCode, ";								// 9  : �Ʒ����ϼԥ�����
		$aryQuery[] = "false, ";									// 10 : ̵���ե饰
		$aryQuery[] = "null";										// 11 : ����
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// ������¹�
		$lngResultID = $objDB->execute( $strQuery );


		// ������¹Լ��Ԥξ��
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		// ͭ���������μ���
		$lngLimitDate = fncGetMasterValue( "m_workfloworder" ,"lngworkflowordercode", "lnglimitdays", $lngWorkflowOrderCode ,"lngworkfloworderno = 1", $objDB );

		//echo "��������$lngLimitDate<br>";



		//---------------------------------------------------------------
		// DB -> INSERT : t_workflow
		//---------------------------------------------------------------
		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO t_workflow (";
		$aryQuery[] = "lngworkflowcode, ";								// ����ե�������
		$aryQuery[] = "lngworkflowsubcode, ";							// ����ե����֥�����
		$aryQuery[] = "lngworkfloworderno, ";							// ����ե�����ֹ�
		$aryQuery[] = "lngworkflowstatuscode, ";						// ����ե����֥�����
		$aryQuery[] = "strnote, ";										// ����
		$aryQuery[] = "dtminsertdate, ";								// ��Ͽ��
		$aryQuery[] = "dtmlimitdate ";									// ������

		$aryQuery[] = ") values (";
		$aryQuery[] = "$lngworkflowcode, ";								// ����ե�������
		$aryQuery[] = DEF_T_WORKFLOW_SUBCODE.", ";						// ����ե����֥�����
		$aryQuery[] = DEF_T_WORKFLOW_ORDERNO.", ";						// ����ե�����ֹ�
		$aryQuery[] = DEF_T_WORKFLOW_STATUS.", ";						// ����ե����֥�����
		$aryQuery[] = "'" . $aryNewData["strWorkflowMessage"] . "',";	// 11:����
		$aryQuery[] = "now(), ";										// ��Ͽ��
		$aryQuery[] = "now() + (interval '$lngLimitDate day' )";		// ������
		$aryQuery[] = ")";

		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );


		// ������¹�
		$lngResultID = $objDB->execute( $strQuery );


		// ������¹Լ��Ԥξ��
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_workfloworder, m_user, m_authoritygroup
		//---------------------------------------------------------------
		// ��ǧ�Ԥ˥᡼�������
		$arySelect = array();
		$arySelect[] = "SELECT u.strmailaddress, ";									// �᡼�륢�ɥ쥹
		$arySelect[] = "u.bytMailTransmitFlag, ";									// �᡼���ۿ����ĥե饰
		$arySelect[] = "w.strworkflowordername, ";									// ����ե�̾
		$arySelect[] = "u.struserdisplayname ";										// ��ǧ��
		$arySelect[] = "FROM m_workfloworder w, m_user u, m_authoritygroup a ";
		$arySelect[]= "WHERE w.lngworkflowordercode = ";
		$arySelect[] = $lngWorkflowOrderCode." AND ";
		$arySelect[] = "u.lngusercode = w.lnginchargecode AND ";
		$arySelect[] = "u.lngauthoritygroupcode = a.lngauthoritygroupcode ";
		$arySelect[] = "ORDER BY a.lngauthoritylevel DESC";

		$strSelect = "";
		$strSelect = implode("\n", $arySelect );

		// echo "$strSelect";


		// ������¹�
		$lngResultID = $objDB->execute( $strSelect );


		// ������¹������ξ��
		if( $lngResultID )
		{
			$aryResult[] = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_User
		//---------------------------------------------------------------
		// ���ϼԥ᡼�륢�ɥ쥹�μ���
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;

		list( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );

		// ������¹������ξ��
		if( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag = $objResult->bytmailtransmitflag;
			$strInputUserMailAddress      = $objResult->strmailaddress;
		}
		// ������¹Լ��Ԥξ��
		else
		{
			fncOutputError( 9051, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "po/regist/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// ���ID�����
		$objDB->freeResult( $lngUserMailResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// �᡼������
		//---------------------------------------------------------------
		// �᡼��ʸ�̤�ɬ�פʥǡ��������� $aryMailData �˳�Ǽ
		//$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];	// ��ǧ�ԥ᡼�륢�ɥ쥹

		// �᡼���ۿ����ĥե饰�� TRUE �����ꤵ��Ƥ��ʤ���礫�ġ�
		// ���ϼԡʿ����ԡˤΥ᡼�륢�ɥ쥹�����ꤵ��Ƥ��ʤ����ϡ��᡼���������ʤ�
		if( $aryResult[0]["bytmailtransmitflag"] == "t" and $aryMailData["strmailaddress"] != "" and $strInputUserMailAddress != "" )
		{
			$aryMailData                       = array();
			//$strMailAddress                    = $aryResult[0]["strmailaddress"];			// ��ǧ�ԥ᡼�륢�ɥ쥹
			$aryMailData["strmailaddress"]     = $aryResult[0]["strmailaddress"];			// ��ǧ�ԥ᡼�륢�ɥ쥹
			$aryMailData["strWorkflowName"]    = $strworkflowname;							// �Ʒ�̾
			//$aryMailData["strUserDisplayName"] = $aryResult[0]["struserdisplayname"];		// ��ǧ�����
			$aryMailData["strUserDisplayName"] = $objAuth->UserDisplayName;					// ���ϼԡʿ����ԡ�ɽ��̾
			$aryMailData["strURL"]             = LOGIN_URL;									// URL

			// ��ǧ���̾�Υ�å�������᡼�����������Ȥ�������
			$aryMailData["strNote"] = $aryNewData["strWorkflowMessage"];


			// �᡼���å���������
			list( $strSubject, $strTemplate ) = fncGetMailMessage( 807, $aryMailData, $objDB );

			// �����ԥ᡼�륢�ɥ쥹����
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

			// �᡼������
			mail( $strMailAddress, $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}

		// Ģɼ����ɽ������
		$aryData["PreviewVisible"] = "hidden";
		//---------------------------------------------------------------
	}



	//-------------------------------------------------------------------------
	// �� ¨��ǧ�ξ��
	//-------------------------------------------------------------------------
	// �ץ�ӥ塼�ܥ����ɽ������
	else
	{
		// Ģɼ�����б�
		// ���¤���äƤʤ����ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
		if( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $lngSequence . "&bytCopyFlag=TRUE";

			// Ģɼ����ɽ������
			$aryData["PreviewVisible"] = "visible";
		}
		else
		{
			// Ģɼ����ɽ������
			$aryData["PreviewVisible"] = "hidden";
		}
	}
*/



	//-------------------------------------------------------------------------
	// �� �ȥ�󥶥������λ
	//-------------------------------------------------------------------------
	$objDB->transactionCommit();



	//-------------------------------------------------------------------------
	// �� ����
	//-------------------------------------------------------------------------
	// �ƥ�ץ졼�Ȥ�ȿ�Ǥ���ʸ����
	$aryData["lngPONo"] = $strsalsecode;

	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/sc/regist/index.php?strSessionID=";

	$objDB->close();

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	return true;

?>
