<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ��Ͽ
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  Kuwagata
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
*	  ����Ψ�������������ʴ��������Ψ�����Ǥ��ʤ���硢�ǿ����֤���Ψ��������� 20130531��
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."pc/cmn/lib_pcp.php" );
	require( SRC_ROOT."po/cmn/lib_pop.php" );



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

	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 700 ��������
	if ( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}


	// ���ٹԤ����
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each ( $_POST );
		if($strKeys != "aryPoDitail")
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
	// �� m_Sales �Υ��������ֹ�����
	//-------------------------------------------------------------------------
	$sequence_m_stock = fncGetSequence( 'm_stock.lngStockNo', $objDB );



	// �̲ߤ򥳡��ɤ��Ѵ�
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

	$lngmonetaryunitcode = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );

	// ���ϼԥ����ɤ����
	$lngUserCode = $objAuth->UserCode;


	// �������ֹ�
	if( $aryNewData["strOrderCode"] != "null" )
	{
		$lngOrderCode = $aryData["lngOrderNo"];
	}
	else
	{
		$lngOrderCode = "null";
	}


	// ��ɼ�ֹ�
	$strSlipCode = ( $aryNewData["strSlipCode"] != "null" ) ? "'".$aryNewData["strSlipCode"]."'" : "null";



	// ����������
	if( $aryNewData["strProcMode"] == "regist" )
	{
		$strstockcode = fncGetDateSequence( date('Y',strtotime( $aryNewData["dtmOrderAppDate"] ) ), date('m',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_stock.strStockCode", $objDB );
	}
	else
	{
		$strstockcode = $aryNewData["lngStockCode"];
	}

	$strstockcode2 = ( $strstockcode != "" ) ? "'" . $strstockcode . "'" : "null";



	//-------------------------------------------------------------------------
	// �� �����⡼�ɤ�����Ͽ�פξ��
	//-------------------------------------------------------------------------
	//if( $aryNewData["strProcMode"] == "regist" && $aryNewData["lngRevisionNo"] == "null" )
	if( $aryNewData["strProcMode"] == "regist" )
	{
		$lngrevisionno = 0;

		// �������֥����ɤμ���
		$lngStockStatusCode = ( $aryNewData["lngWorkflowOrderCode"] == 0 ) ? DEF_STOCK_ORDER : DEF_STOCK_APPLICATE;
	}
	//-------------------------------------------------------------------------
	// �� �����⡼�ɤ��ֽ����פξ��
	//-------------------------------------------------------------------------
	else
	{
		//-----------------------------------------------------------
		// �ǿ���Х����ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngStockNo, lngStockStatusCode FROM m_Stock s WHERE s.strStockCode = '" . $strstockcode . "'";
		$strCheckQuery .= " AND s.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND s.lngRevisionNo = ( " . "SELECT MAX( s1.lngRevisionNo ) FROM m_Stock s1 WHERE s1.strStockCode = s.strStockCode )\n";


		// �����å������꡼�μ¹�
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if( $lngCheckResultNum == 1 )
		{
			$objResult          = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngStockStatusCode = $objResult->lngstockstatuscode;

			//---------------------------------------------
			// �������֤Υ����å�
			//---------------------------------------------
			// ������ξ��
			if( $lngStockStatusCode == DEF_STOCK_APPLICATE )
			{
				fncOutputError( 711, DEF_WARNING, "", TRUE, "../pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			// ����Ѥξ��
			if( $lngSalesStatusCode == DEF_STOCK_CLOSED )
			{
				fncOutputError( 711, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngCheckResultID );




		//-------------------------------------------------------------------------
		// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
		//-------------------------------------------------------------------------
		$lngStockStatusCode = fncCheckNullStatus( $lngStockStatusCode );

		//-------------------------------------------------------------------------
		// ���֥����ɤ���0�פξ�硢��1�פ������
		//-------------------------------------------------------------------------
		$lngStockStatusCode = fncCheckZeroStatus( $lngStockStatusCode );




		// �����ξ��Ʊ���������Ф��ƥ�ӥ�����ֹ�κ����ͤ��������
		//��ӥ�����ֹ�򸽺ߤκ����ͤ�Ȥ�褦�˽������롡���κݤ�SELECT FOR UPDATE����Ѥ��ơ�Ʊ���������Ф��ƥ�å����֤ˤ���
		$strLockQuery = "SELECT lngRevisionNo FROM m_Stock WHERE strStockCode = " . $strstockcode2 . " FOR UPDATE";

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

			$lngrevisionno = $lngMaxRevision + 1;
		}
		else
		{
			$lngrevisionno = $lngMaxRevision;
		}

		$objDB->freeResult( $lngLockResultID );
	}




	// �����襳���ɤ����
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );

	// Ǽ�ʾ�ꥳ���ɤ����
	$aryNewData["lngLocationCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngLocationCode"] . ":str", '', $objDB );



	//-------------------------------------------------------------------------
	// �� DB -> INSERT : m_stock
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_stock( ";
	$aryQuery[] = "lngstockno, ";														// 1:�����ֹ�
	$aryQuery[] = "lngrevisionno, ";													// 2:��ӥ�����ֹ�
	$aryQuery[] = "strstockcode, ";														// 3:���������� / yymmxxx ǯ��Ϣ�֤ǹ������줿7����ֹ�
	$aryQuery[] = "lngorderno, ";														// 4:ȯ���ֹ� 
	$aryQuery[] = "dtmappropriationdate, ";												// 5:������
	$aryQuery[] = "lngcustomercompanycode, ";											// 6:�����襳���� 
	//$aryQuery[] = "lnggroupcode, ";														// 7:���祳����
	//$aryQuery[] = "lngusercode, ";														// 8:ô���ԥ����� 
	$aryQuery[] = "lngstockstatuscode, ";												// 9:�������֥�����
	$aryQuery[] = "lngmonetaryunitcode, ";												// 10:�̲�ñ�̥�����
	$aryQuery[] = "lngmonetaryratecode, ";												// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "curconversionrate, ";												// 12:Ŭ�ѥ졼�� 
	$aryQuery[] = "lngpayconditioncode, ";												// 13:��ʧ�����
	$aryQuery[] = "strslipcode, ";														// 14:��ɼ������ 
	$aryQuery[] = "curtotalprice, ";													// 15:��׶��
	$aryQuery[] = "lngdeliveryplacecode, ";												// 16:Ǽ�ʾ��
	$aryQuery[] = "dtmexpirationdate, ";												// 17:����������
	$aryQuery[] = "strnote, ";															// 18:����
	$aryQuery[] = "lnginputusercode, ";													// 19:���ϼԥ����� 
	$aryQuery[] = "bytinvalidflag, ";													// 20:̵���ե饰 
	$aryQuery[] = "dtminsertdate ";														// 21:��Ͽ��
	$aryQuery[] = " ) VALUES ( ";
	$aryQuery[] = "$sequence_m_stock, ";												// 1:�����ֹ�
	$aryQuery[] = "$lngrevisionno,";													// 2:��ӥ�����ֹ�
	$aryQuery[] = "$strstockcode2, ";													// 3:���������� 
	$aryQuery[] = "$lngOrderCode, ";													// 4:ȯ���ֹ�
	$aryQuery[] = "'".$aryNewData["dtmOrderAppDate"]."', ";								// 5:�׾���

	if ( $aryNewData["lngCustomerCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngCustomerCode"] . ", ";									// 6:�����襳����
	}
	else
	{
		$aryQuery[] = "null, ";
	}
/*
	if ( $aryNewData["lngInChargeGroupCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";								// 7:���祳����
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["lngInChargeUserCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngInChargeUserCode"].", ";								// 8:ô���ԥ����� 
	}
	else
	{
		$aryQuery[] = "null, ";
	}
*/

	$aryQuery[] = $lngStockStatusCode . ", ";												// 9:�������֥�����
/*
	if ( $aryNewData["lngOrderStatusCode"] != "null" )
	{
		$aryQuery[] = $aryNewData["lngOrderStatusCode"].", ";								// 9:�������֥�����
	}
	else
	{
		if( $lngOrderCode == "null" )
		{
			$aryQuery[] = DEF_STOCK_END.", ";
		}
		else
		{
			$aryQuery[] = "null, ";
		}
	}
*/

	if ( $lngmonetaryunitcode != "" )
	{
		$aryQuery[] = "$lngmonetaryunitcode, ";												// 10:�̲�ñ�̥�����
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["lngMonetaryRateCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngMonetaryRateCode"].", ";								// 11:�̲ߥ졼�ȥ�����
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["curConversionRate"] != "" )
	{
		$aryQuery[] = $aryNewData["curConversionRate"].", ";								// 12:Ŭ�ѥ졼�� 
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	if ( $aryNewData["lngPayConditionCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngPayConditionCode"].", ";								// 13:��ʧ�����
	}
	else
	{
		$aryQuery[] = "null, ";
	}
	$aryQuery[] = "$strSlipCode, ";														// 14:��ɼ������ 
	$aryQuery[] = $aryNewData["curAllTotalPrice"].", ";									// 15:��׶��
	if ( $aryNewData["lngLocationCode"] != "" )
	{
		$aryQuery[] = $aryNewData["lngLocationCode"].", ";									// 16:Ǽ�ʾ��
	}
	else
	{
		$aryQuery[] = "null, ";
	}

	if( $aryNewData["dtmExpirationDate"] != "" and $aryNewData["dtmExpirationDate"] != "null" )	// 17:����������
	{
		$aryQuery[] = "'".$aryNewData["dtmExpirationDate"]."', ";
	}
	else
	{
		$aryQuery[] = "null, ";
	}

	// �����ξ��֤ˤĤ��ƤϤ��λ��ϥ����å���Ԥ鷺��ȯ���ֹ����ꤷ�ʤ������ξ��Τߡ���Ǽ�ʺѡפȤ���
	if( $aryNewData["strNote"] != "null" )
	{
		$aryQuery[] = "'".$aryNewData["strNote"]."', ";									// 18:����
	}
	else
	{
		$aryQuery[] = "null, ";
	}

	$aryQuery[] = "$lngUserCode, ";														// 19:���ϼԥ�����
	$aryQuery[] = "false, ";															// 20:̵���ե饰 
	$aryQuery[] = "now()";																// 21:��Ͽ��
	$aryQuery[] = " )";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );


	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$objDB->freeResult( $lngResultID );



	// ��ǧ���̤����Ψ�Υ����ɤ��Ϥ���ʤ����Ϥ��λ��λ����������ټ������ʤ���
	// ���ٹ��Ѥ˾����ǥ����ɤ��������
	// �����ǥ�����
	// ��������ꤽ�λ�����Ψ���Ȥ��
	$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
		. "FROM m_tax "
		. "WHERE dtmapplystartdate <= '" . $aryNewData["dtmOrderAppDate"] . "' "
		. "AND dtmapplyenddate >= '" . $aryNewData["dtmOrderAppDate"] . "' "
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
		// �ǿ����֤���Ψ������������
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


	// ���ٹ��ֹ椬�����ʹԤ��н�
	$lngMaxDetailNo = 0;
	if ( $lngOrderCode != "null" )
	{
		// ���ꤵ��Ƥ���ȯ��Ǥκ����ͤ����
		$strQuery = "SELECT MAX(lngOrderDetailNo) as maxDetailNo FROM t_OrderDetail WHERE lngOrderNo = " . $lngOrderCode;
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
	// ȯ��No����ꤷ�ʤ������ξ��
	{
		// ���ٹԤ���ǻ��ꤵ��Ƥ�������ͤ����
		for ( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
		{
			if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "null" and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
				and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] > $lngMaxDetailNo )
			{
				$lngMaxDetailNo = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
			}
		}
	}

	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		// ���������ֹ�
		// ���ٹ��ֹ椬�ʤ����ʻ������ɲä��줿���ٹԤξ���
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}

		// ����
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" )  ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

		// ������ʬ������
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2 ;


		// �ǳۤ��⤷NULL�ʤ�С��Ƕ�ʬ����ȴ��ۤ��Ƚ�Ǥ����Ʒ׻�����
		$lngCalcCode = DEF_CALC_KIRISUTE;

		// ���������̲�ñ�̥����ɤ������оݷ��������
		if ( $lngmonetaryunitcode == DEF_MONETARY_YEN )
		{
			$lngDigitNumber = 0;		// ���ܱߤξ��ϣ���
		}
		else
		{
			$lngDigitNumber = 2;		// ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
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

		// �ⷿ�ֹ���������Ƥ���������꤬�ʤ����Ƕⷿ������ä����Ͽ����˶ⷿ�ֹ�������
		if( $aryNewData["aryPoDitail"][$i]["strSerialNo"] == "null" or $aryNewData["aryPoDitail"][$i]["strSerialNo"] == "" )
		{
			$strSerialNo = "";
			// �������ܤ��������ʶⷿ�������ѡˡ��������ʤ�����Injection Mold�ˤξ��
			// �������ܤ��������ʶⷿ���ѹ�ˡ����������ʤ����ʶⷿ�ˤξ��
			if ( ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM )
				or ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT_ADD 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM_ADD ) )
			{
				$strSerialNo = fncGetMoldNo( $aryNewData["aryPoDitail"][$i]["strProductCode"], $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"], $aryNewData["aryPoDitail"][$i]["strStockItemCode"], $objDB );
			}
			else
			// ���ꤵ��Ƥ���������ܡ��������ʤ��ⷿ�ֹ���ѤǤʤ����϶ⷿ�ֹ�ս�ˤ�NULL����
			{
				$strSerialNo = "";
			}
		}
		else
		{
			// �������ܤ��������ʶⷿ�������ѡˡ��������ʤ�����Injection Mold�ˤξ��
			// �������ܤ��������ʶⷿ�������ѡˡ��������ʤ����ʶⷿ�ˤξ��
			if ( ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM )
				or ( $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"] == DEF_MOLD_STOCK_SUBJECT_ADD 
				and $aryNewData["aryPoDitail"][$i]["strStockItemCode"] == DEF_MOLD_STOCK_ITEM_ADD ) )
			{
				// ���ꤵ�줿�ⷿ�ֹ椬�������ǻ��ꤵ��Ƥ������Ϥ��Τޤޤζⷿ�ֹ�����ꤹ��
				$strSerialNo = "";
				$strSerialNo = $aryNewData["aryPoDitail"][$i]["strSerialNo"];
			}
			else
			{
				$strSerialNo = "";
			}
		}
		// SQLʸ�б�
		$strSerialNo = ( $strSerialNo != "" ) ? "'$strSerialNo'" : "null";

		// �������ֹ�
		$lngSortKey = $i + 1;



		//-----------------------------------------------------------
		// DB -> INSERT : t_stockdetail
		//-----------------------------------------------------------
		$aryQuery	= array();
		$aryQuery[] = "INSERT INTO t_stockdetail ( ";
		$aryQuery[] = "lngstockno, ";													// 1:�����ֹ�
		$aryQuery[] = "lngstockdetailno, ";												// 2:���������ֹ�
		$aryQuery[] = "lngrevisionno, ";												// 3:��ӥ�����ֹ� 
		$aryQuery[] = "strproductcode, ";												// 4:���ʥ�����
		$aryQuery[] = "lngstocksubjectcode, ";											// 5:�������ܥ�����
		$aryQuery[] = "lngstockitemcode, ";												// 6:�������ʥ�����
		$aryQuery[] = "dtmdeliverydate, ";												// 7:Ǽ����
		$aryQuery[] = "lngdeliverymethodcode, ";										// ������ˡ
		$aryQuery[] = "lngconversionclasscode, ";										// 8:������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
		$aryQuery[] = "curproductprice, ";												// 9:���ʲ���
		$aryQuery[] = "lngproductquantity, ";											// 10:���ʿ���
		$aryQuery[] = "lngproductunitcode, ";											// 11:����ñ�̥�����
		$aryQuery[] = "lngtaxclasscode, ";												// 12:�����Ƕ�ʬ������
		$aryQuery[] = "lngtaxcode, ";													// 13:�����ǥ�����
		$aryQuery[] = "curtaxprice, ";													// 14:�ǳ�
		$aryQuery[] = "cursubtotalprice, ";												// 15:���׶�� / ��ȴ���׶��
		$aryQuery[] = "strnote, ";														// 16:����
		$aryQuery[] = "strmoldno, ";													// 17:�ⷿ�ֹ�
		$aryQuery[] = "lngSortKey ";													// 18:ɽ���ѥ����ȥ���
		$aryQuery[] = " ) VALUES ( ";
		$aryQuery[] = "$sequence_m_stock, ";											// 1:�����ֹ�
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ", ";		// 2:���������ֹ� �Ԥ��Ȥ�����ȯ��ϻ��äƤ���
		$aryQuery[] = "$lngrevisionno, ";												// 3:��ӥ�����ֹ�
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";		// 4:���ʥ�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockSubjectCode"].", ";		// 5:�������ܥ�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["strStockItemCode"].", ";			// 6:�������ʥ�����
		if( $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] != "" and $aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"] != "null" )
																						// 7:Ǽ��
		{
			$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";
		}
		else
		{
			$aryQuery[] = "null, ";
		}
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngCarrierCode"].", ";
		$aryQuery[] = "$lngConversionClassCode, ";										// 8:������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."', ";		// 9:���ʲ���
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";			// 10:���ʿ���
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";		// 11:����ñ�̥�����
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngTaxClassCode"].", ";			// 12:�����Ƕ�ʬ������
		$aryQuery[] = "$lngTaxCode_Detail, ";											// 13:�����ǥ�����
		$aryQuery[] = "$curTaxPrice, ";													// 14:�ǳ�
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curTotalPrice"]."', ";		// 15:���׶�� / ��ȴ���׶��
		$aryQuery[] = "$strDetailNote, ";												// 16:����
		$aryQuery[] = $strSerialNo . ", ";												// 17:�ⷿ�ֹ�
		$aryQuery[] = $lngSortKey . " ";												// 18:ɽ���ѥ����ȥ���
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		}

		$objDB->freeResult( $lngResultID );
	}


	// ��������ü���������ڼΤ�
	$lngCalcCode = DEF_CALC_KIRISUTE;

	////////////////////////////////////////////////////////
	//// ���λ�������Ͽ�ˤ��ȯ����Ф��Ƥξ��֥����å� ////
	////////////////////////////////////////////////////////
	if ( $lngOrderCode != "" and $lngOrderCode != "null" )
	{
		$lngResult = fncStockSetStatus ( $lngOrderCode, $lngCalcCode, $objDB );
		if ( $lngResult == 1 )
		{
			fncOutputError ( 707, DEF_ERROR, "", TRUE, "", $objDB );
		}
		else if ( $lngResult == 2 )
		{
			fncOutputError ( 9061, DEF_ERROR, "", TRUE, "", $objDB );
		}
	}


	if( !fncCheckSetProduct ( $aryNewData["aryPoDitail"], $lngmonetaryunitcode, $objDB ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}




/*
	//-------------------------------------------------------------------------
	// �� ��ǧ����
	//
	//   ��ǧ�롼��
	//     ��0 : ��ǧ�롼�Ȥʤ�
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryNewData["lngWorkflowOrderCode"];	// ��ǧ�롼��

	$strWFName   = "���� [No:" . $strstockcode . "]";
	$lngSequence = $sequence_m_stock;
	$strDefFnc   = DEF_FUNCTION_PC1;

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
		$aryQuery[] = $strDefFnc . ", ";							// 4  : ��ǽ������
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
	// �������ֹ��ȯ��
	$aryData["lngPONo"] = $strstockcode;

	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/pc/regist/index.php?strSessionID=";

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	return true;

?>
