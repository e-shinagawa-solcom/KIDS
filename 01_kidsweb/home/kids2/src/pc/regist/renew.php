<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ��������
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
	include( 'conf.inc' );
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."pc/cmn/lib_pcp.php" );
	require( SRC_ROOT."pc/cmn/lib_pcs1.php" );
	require( SRC_ROOT."pc/cmn/column.php" );


	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	// $aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];



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

	// 700 ��������
	if( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 705 ���������� ����������
	if( fncCheckAuthority( DEF_FUNCTION_PC5, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}



	// 710 ���������ʹ��ɲá��Ժ����
	if( !fncCheckAuthority( DEF_FUNCTION_PC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}



	//-------------------------------------------------------------------------
	// �� �����ֹ����
	//-------------------------------------------------------------------------
	$lngstockno = $_REQUEST["lngStockNo"];



	//-------------------------------------------------------------------------
	// �� ���ϥ����å�
	//-------------------------------------------------------------------------
	if( $_POST["strMode"] == "check")
	{
		// ���ٹԤ����
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each ( $_POST );

			if($strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}


		//-----------------------------------------------------------
		// �إå������ܥ����å�
		//-----------------------------------------------------------
		$aryData["renew"] = "true"; // ȯ��ͭ��������������å����ʤ�����Υե饰

		list ( $aryData, $bytErrorFlag ) = fncCheckData_pc( $aryData,"header", $objDB );

		$errorCount = ( $bytErrorFlag == "TRUE" ) ? 1 : 0;


		//-----------------------------------------------------------
		// ���ٹԤΥ����å�
		//-----------------------------------------------------------
		$aryQueryResult2 = $_POST["aryPoDitail"];
		for( $i = 0; $i < count( $aryQueryResult2 ); $i++ )
		{
			list ( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_pc( $_POST["aryPoDitail"][$i], "detail", $objDB );
		}

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



		// ���ٹԤΥ��顼�ؿ�
		for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ )
		{
			if( $bytErrorFlag2[$i] == "true")
			{
				$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
			}
		}

		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		$_POST["lngOrderNo"] = trim( $_POST["lngOrderNo"] );

		// �桼���˱����Ƥζ������
		if( $_POST["lngOrderNo"] == "" )
		{
			$lngUserGroup = $objAuth->AuthorityGroupCode;

			// ���ܱߤ��Ѵ�
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];

			// 708 ����������ȯ��NO����ꤷ�ʤ���Ͽ����ǽ���ɤ�����
			if ( !fncCheckAuthority( DEF_FUNCTION_PC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 710, "", "", FALSE, "pc/regist/renew.php?lngStockNo=" . $lngstockno . "&strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// 709 �����ۤ���°ʾ�Ǥ�����Ͽ��ǽ�ʸ��¤���äƤ��ʤ��ʤ�
				if ( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_PC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/renew.php?lngStockNo=" . $lngstockno . "&strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

/*
			if( $lngUserGroup == 5 )					// �桼��
			{
				$aryDetailErrorMessage[] = fncOutputError ( 710, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// �ޥ͡����㡼
			{
				// 5���ޤ���Ͽ��ǽ
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// �ǥ��쥯����
			{
				// 20���ޤ���Ͽ��ǽ
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

			}
*/
		}


		if( strcmp( $_POST["strOrderCode"],"") != 0 )
		{
			$aryData["lngOrderNo"] = $_POST["lngOrderNo"];

			if ( $_POST["strOrderCode"] == "" )
			{
				//-----------------------------------------
				// DB -> SELECT : m_Order
				//-----------------------------------------
				$strQuery = "SELECT strOrderCode FROM m_Order WHERE lngOrderNo = " . $aryData["lngOrderNo"];

				if ( isset( $lngResultID ) )
				{
					$objDB->freeResult( $lngResultID );
				}

				list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

				if ( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strOrderCode = $objResult->strordercode;
				}
				else
				{
					// ȯ�����ɼ�������
					fncOutputError ( 9051, DEF_ERROR, "ȯ�����ɼ�������", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

				$objDB->freeResult( $lngResultID );
			}
			else
			{
				$strOrderCode = $_POST["strOrderCode"];
			}

			//-------------------------------------------------------
			// DB -> SELECT : m_Order
			//-------------------------------------------------------
			$strQuery = "SELECT lngMonetaryUnitCode FROM m_Order WHERE lngOrderNo = " . $aryData["lngOrderNo"];

			if ( isset( $lngResultID ) )
			{
				$objDB->freeResult( $lngResultID );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngOrderMonetaryUnitCode = $objResult->lngmonetaryunitcode;
			}
			else
			{
				// �̲ߥ졼�ȼ�������
				fncOutputError ( 9051, DEF_ERROR, "�̲ߥ졼�ȥ����ɼ�������", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );


			//-------------------------------------------------------
			// DB -> SELECT : t_orderdetail
			//-------------------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "lngorderdetailno, ";								// ȯ�������ֹ�
			$aryQuery[] = "lngrevisionno, ";								// ��ӥ�����ֹ�
			$aryQuery[] = "strproductcode, ";								// ���ʥ�����
			$aryQuery[] = "lngstocksubjectcode, ";							// �������ܥ�����
			$aryQuery[] = "lngstockitemcode, ";								// �������ʥ�����
			$aryQuery[] = "dtmdeliverydate, ";								// Ǽ����
			$aryQuery[] = "lngdeliverymethodcode as lngCarrierCode, ";		// ������ˡ������
			$aryQuery[] = "lngconversionclasscode, ";						// ������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
			$aryQuery[] = "curproductprice, ";								// ���ʲ���
			$aryQuery[] = "lngproductquantity, ";							// ���ʿ���
			$aryQuery[] = "lngproductunitcode, ";							// ����ñ�̥�����
			$aryQuery[] = "lngtaxclasscode, ";								// �����Ƕ�ʬ������
			$aryQuery[] = "lngtaxcode, ";									// �����ǥ�����
			$aryQuery[] = "curtaxprice, ";									// �����Ƕ��
			$aryQuery[] = "cursubtotalprice, ";								// ���׶��
			$aryQuery[] = "strnote ";										// ����
			$aryQuery[] = "FROM t_orderdetail ";
			$aryQuery[] = "WHERE ";
			$aryQuery[] = "lngorderno = ".$aryData["lngOrderNo"];
			$aryQuery[] = " ORDER BY lngSortKey ASC";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryOrderDetail[] = $objDB->fetchArray( $lngResultID, $i );
				}
			}
			else
			{
				// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
				fncOutputError ( 9051, DEF_ERROR, "���ٹԼ�������", TRUE, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			$objDB->freeResult( $lngResultID );


			// �̲ߤ��ͤ򥳡��ɤ��ѹ�
			if ( $_POST["lngMonetaryUnitCode"] != "" )
			{
				$_POST["lngMonetaryUnitCode"] = ( $_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
				$lngStockMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// ȯ�����ɼ�������
				fncOutputError ( 9061, DEF_ERROR, "�̲�ñ�̤μ�������", TRUE, "pc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			$aryDetailResult = $_POST["aryPoDitail"];

			$lngCalcCode = DEF_CALC_KIRISUTE;

			/////////////////////////////////////////////////////////////////////////
			///// ȯ��İʾ�˻��������ꤷ�Ƥ��ʤ����ɤ����Υ����å��ؿ��ƤӽФ� ////
			/////////////////////////////////////////////////////////////////////////
			$lngResult = fncGetStatusStockRemains ( $aryData["lngOrderNo"], $aryDetailResult, $lngOrderMonetaryUnitCode, $lngStockMonetaryUnitCode, $lngstockno, $lngCalcCode, $objDB );

			switch ( $lngResult )
			{
				// ȯ�����μ�������
				case 0:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "���ꤵ�줿ȯ��Ϻ������ޤ���", FALSE, "" . $_POST["strSessionID"], $objDB );
					break;

				// ȯ�����١��������پ���μ�������
				case 1:
					$aryDetailErrorMessage[] = fncOutputError ( 9061, DEF_ERROR, "ȯ����������μ����˼��Ԥ��ޤ���", FALSE, "", $objDB );
					break;

				// ȯ��İʾ�λ��������ꤵ��Ƥ���
				case 99:
					$aryDetailErrorMessage[] = fncOutputError ( 705, DEF_ERROR, "", FALSE, "", $objDB );
					break;

				// ���ꤵ��Ƥ�����������ȯ�����
				case 50:
					break;
			}
		}




		//-----------------------------------------------------------
		// �ǿ��ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
		//-----------------------------------------------------------
		$strCheckQuery = "SELECT lngStockNo, lngStockStatusCode FROM m_Stock s WHERE s.strStockCode = '" . $aryData["strStockCode"] . "'";
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
				fncOutputError( 711, DEF_WARNING, "", TRUE, "../sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			// ����Ѥξ��
			if( $lngStockStatusCode == DEF_STOCK_CLOSED )
			{
				fncOutputError( 711, DEF_WARNING, "", TRUE, "", $objDB );
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngCheckResultID );




		//-----------------------------------------------------------
		// ���ϥ��顼
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount == 1 )
		{
			// �ؿ� fncChangeDisplayName��ɽ���ѥǡ������Ѵ�(HEADER��
			$aryData = fncChangeData3( $aryData , $objDB );

			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage);
			}

			// ���ٹԤ�hidden�ͤ��Ѵ�����
			if( is_array( $_POST["aryPoDitail"] ))
			{
				$aryData["strDetailHidden"] = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert", $objDB);
				$aryData["MonetaryUnitDisabled"] = "disabled";
			}

			//�إå����ͤ��ü�ʸ���Ѵ�
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );




			//-------------------------------------------------------------------------
			// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
			//-------------------------------------------------------------------------
			$lngStockStatusCode = fncCheckNullStatus( $lngStockStatusCode );


			//---------------------------------------------------------------
			// �������֤μ���
			//---------------------------------------------------------------
			$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $lngStockStatusCode, '', $objDB );

			$aryData["lngStockStatusCode"] = $lngStockStatusCode;




			// �ץ�������˥塼������
			// �̲�
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// �졼�ȥ�����
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// ��ʧ���
			$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// ��������
			$aryData["strStockSubjectCode"]   = fncPulldownMenu( 3, $aryData["strStockSubjectCode"], '', $objDB );
			// ������ˡ
			$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
			// ����ñ��
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
			// �ٻ�ñ��
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



			// ����ե�ɽ������ɽ������
			$aryData["visibleWF"] = "hidden";
			// ��ǧ�롼��
			// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );



			$aryData["strMode"]               = "check";
			$aryData["strOrderCode_Editable"] = "contenteditable=\"false\"";
			$aryData["OrderSubmit"]           = "";
			$aryData["lngOrderCode"]          = $_GET["strOrderCode"];
			$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
			$aryData["RENEW"]                 = TRUE;
			$aryData["strPageCondition"]      = "renew";

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


			echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;

		}

		//-----------------------------------------------------------
		// ��ǧ����ɽ��
		//-----------------------------------------------------------
		else
		{
			// ���������
			if( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
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
				// �ǿ�����Ψ������������ 20130531 add
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
				$lngDigitNumber = 0; // ���ܱߤξ��ϣ���
			}
			else
			{
				$lngDigitNumber = 2; // ���ܱ߰ʳ��ξ��Ͼ������ʲ�����
			}

			// hidden�������κݤ��ǳ۰۾��Ĵ����Ԥ�
			for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
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
			$aryData["strDetailHidden"] = fncDetailHidden_pc( $_POST["aryPoDitail"] ,"insert", $objDB);


			// �����̾������
			$aryHeadColumnNames = fncSetStockTabelName( $aryTableViewHead, $aryTytle );
			// �����̾������
			$aryDetailColumnNames = fncSetStockTabelName( $aryTableViewDetail, $aryTytle );


			for ( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				$_POST["aryPoDitail"][$i]["lngrecordno"] = $i + 1;

				// ��������
				$_POST["aryPoDitail"][$i]["strStockSubjectName"] = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname",  $_POST["aryPoDitail"][$i]["strStockSubjectCode"], '', $objDB );

				// �������� 
				$_POST["aryPoDitail"][$i]["strStockItemName"] = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname", $_POST["aryPoDitail"][$i]["strStockItemCode"], "lngstocksubjectcode = ".$_POST["aryPoDitail"][$i]["strStockSubjectCode"], $objDB );

				// ������ˡ
				$_POST["aryPoDitail"][$i]["strCarrierName"] = fncGetMasterValue( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $_POST["aryPoDitail"][$i]["lngCarrierCode"],'', $objDB );

				// �ܵ�����
				$_POST["aryPoDitail"][$i]["strGoodsName"] = fncGetMasterValue( "m_product", "strproductcode", "strGoodsCode", $_POST["aryPoDitail"][$i]["strProductCode"].":str", "bytinvalidflag = false", $objDB );

				// ñ��
				$_POST["aryPoDitail"][$i]["strProductUnitName"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["aryPoDitail"][$i]["lngProductUnitCode"], '', $objDB );

				// �Ƕ�ʬ
				$_POST["aryPoDitail"][$i]["strTaxClassName"] = fncGetMasterValue( "m_taxclass", "lngtaxclasscode", "strtaxclassname", $_POST["aryPoDitail"][$i]["lngTaxClassCode"], '', $objDB );


				// ��Ψ
				if ( $_POST["aryPoDitail"][$i]["lngTaxCode"] != "" )
				{
					$_POST["aryPoDitail"][$i]["curTax"] = $curTax;
				}


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


				// number_format
				$_POST["aryPoDitail"][$i]["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
				$_POST["aryPoDitail"][$i]["curproductprice_DIS"] = ($_POST["aryPoDitail"][$i]["curProductPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curProductPrice"] ,4 ) : "";
				$_POST["aryPoDitail"][$i]["lnggoodsquantity_DIS"] = ($_POST["aryPoDitail"][$i]["lngGoodsQuantity"] != "") ? number_format( $_POST["aryPoDitail"][$i]["lngGoodsQuantity"] ) : "";
				$_POST["aryPoDitail"][$i]["curtaxprice_DIS"] = ($_POST["aryPoDitail"][$i]["curTaxPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTaxPrice"] ,2 ) : "";
				$_POST["aryPoDitail"][$i]["curTotalPrice_DIS"] = ($_POST["aryPoDitail"][$i]["curTotalPrice"] != "") ? number_format( $_POST["aryPoDitail"][$i]["curTotalPrice"] ,2 ) : "";


				// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
				$_POST["aryPoDitail"][$i]["strproductcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strProductCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strProductCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["strstockitemcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockItemCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockItemCode"]."]" : "";
				$_POST["aryPoDitail"][$i]["strstocksubjectcode_DISCODE"] = ( $_POST["aryPoDitail"][$i]["strStockSubjectCode"] != "" ) ? "[".$_POST["aryPoDitail"][$i]["strStockSubjectCode"]."]" : "";


				// �ƥ�ץ졼���ɤ߹���
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "pc/result/parts_detail2.tmpl" );


				// �ƥ�ץ졼������
				$objTemplate->replace( $aryDetailColumnNames );
				$objTemplate->replace( $_POST["aryPoDitail"][$i] );
				$objTemplate->complete();

				// HTML����
				$aryDetailTable[] = $objTemplate->strTemplate;
			}


			$aryData["strDetailTable"] = implode ("\n", $aryDetailTable );


			// ��Ͽ��
			$aryData["dtminsertdate"] = date( 'Y/m/d', time());
			// ���ϼ�
			$aryData["lngInputUserCode"] = $objAuth->UserID;
			$aryData["strInputUserName"] = $objAuth->UserDisplayName;

			// ����
			$aryData["strInChargeGroupName"] = fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  $_POST['lngInChargeGroupCode'] . ":str",'',$objDB);
			// ô����
			$aryData["strInChargeUserName"] = fncGetMasterValue( "m_user", "struserdisplaycode" ,"struserdisplayname" , $_POST["lngInChargeUserCode"] . ":str",'',$objDB);
			// �̲�
			$_POST["strMonetaryUnitName"] = ($_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];
			$aryData["strMonetaryUnitName"] = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $_POST["strMonetaryUnitName"] . ":str", '', $objDB );
			// �졼�ȥ�����
			$strMonetaryrateName = fncGetMasterValue( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $_POST["lngMonetaryRateCode"], '', $objDB);
			$aryData["strMonetaryrateName"] = ( $strMonetaryrateName == "��" ) ? "" : $strMonetaryrateName;

			// ��ʧ���
			$strPayConditionName = fncGetMasterValue( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $_POST["lngPayConditionCode"], '', $objDB );
			$aryData["strPayConditionName"] = ( $strPayConditionName == "��" ) ? "" : $strPayConditionName;

			//�إå����ͤ��ü�ʸ���Ѵ�
			$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );





			//---------------------------------------------------------------
			// �������֤μ���
			//---------------------------------------------------------------
/*
			if( $lngStockStatusCode != "" )
			{
				$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $lngStockStatusCode, '', $objDB );

				$aryData["lngStockStatusCode"] = $lngStockStatusCode;
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



			// number_format
			$aryData["curConversionRate_DIS"] = number_format( $aryData["curConversionRate"],6 );	// �����졼��
			$aryData["strMonetaryrate"] = $aryData["lngMonetaryUnitCode"];
			$aryData["curAllTotalPrice_DIS"] = number_format( $aryData["curAllTotalPrice"],2 );		// ��׶��


			// �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
			$aryData["lngInputUserCode_DISCODE"] = ( $aryData["lngInputUserCode"] != "" ) ? "[".$aryData["lngInputUserCode"]."]" : "";
			$aryData["lngCustomerCode_DISCODE"] = ( $aryData["lngCustomerCode"] != "" ) ? "[".$aryData["lngCustomerCode"]."]" : "";
			//$aryData["lngInchargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
			//$aryData["lngInchargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
			$aryData["lngLocationCode_DISCODE"] = ( $aryData["lngLocationCode"] != "" ) ? "[".$aryData["lngLocationCode"]."]" : "";



			$aryData["RENEW"]            = TRUE;
			$aryData["strMode"]          = "regist";
			$aryData["strProcMode"]      = "renew";
			$aryData["lngLanguageCode"]  = $_COOKIE["lngLanguageCode"];
			$aryData["lngCalcCode"]      = DEF_CALC_KIRISUTE;
			$aryData["lngRegistConfirm"] = 0;


			$aryData["strAction"]    = "/pc/regist/index2.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "/pc/regist/index2.php?strSessionID=".$aryData["strSessionID"];




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
			$objTemplate->getTemplate( "pc/confirm/parts.tmpl" );

			// �ƥ�ץ졼������
			$objTemplate->replace( $aryHeadColumnNames );
			$objTemplate->replace( $aryData );
			$objTemplate->complete();


			$objDB->close();


			// HTML����
			echo $objTemplate->strTemplate;
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
	$aryQuery   = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "o.strrevisecode as strReviseCode, ";					// 2:��Х����ֹ�
	$aryQuery[] = "o.strordercode as strOrderCode, ";					// 1:�����ֹ�
	$aryQuery[] = "s.strstockcode as lngStockCode, ";					// 3:���������� 
	$aryQuery[] = "s.lngorderno, ";
	$aryQuery[] = "To_char( s.dtmAppropriationDate, 'YYYY/mm/dd' ) as dtmOrderAppDate,";	// 4:�׾���
	$aryQuery[] = "s.lngcustomercompanycode, ";							// 6:������
	//$aryQuery[] = "s.lnggroupcode as lngInChargeGroupCode, ";			// 7:����
	//$aryQuery[] = "s.lngusercode as lngInChargeUserCode, ";				// 8:ô����
	$aryQuery[] = "s.lngstockstatuscode, ";								// 9:�������֥�����
	$aryQuery[] = "s.lngmonetaryunitcode, ";							// 10:�̲�ñ�̥�����
	$aryQuery[] = "s.lngmonetaryratecode, ";							// 11:�̲ߥ졼�ȥ�����
	$aryQuery[] = "s.curconversionrate, ";								// 12:�����졼��
	$aryQuery[] = "s.lngpayconditioncode, ";							// 13:��ʧ�����
	$aryQuery[] = "s.strslipcode, ";									// 14:��ɼ������
	$aryQuery[] = "s.lngdeliveryplacecode, ";							// 15:Ǽ�ʾ��
	$aryQuery[] = "s.curtotalprice, ";									// 16:��׶�� 
	$aryQuery[] = "To_char( s.dtmexpirationdate, 'YYYY/mm/dd') as dtmexpirationdate,";// 17:ȯ��ͭ��������
	$aryQuery[] = "s.strnote, ";											// 18:̵���ե饰
	$aryQuery[] = "o.lngorderstatuscode, ";								// ����
	$aryQuery[] = "s.lngrevisionno ";									// ��ӥ�����ֹ�
	$aryQuery[] = "FROM m_stock s LEFT JOIN m_order o ON o.lngorderno = s.lngorderno ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "s.lngstockno = $lngstockno AND ";					// ������������
	$aryQuery[] = "s.bytinvalidflag = false";							// ̵���ե饰

	$strQuery = implode("\n", $aryQuery );


	// �����꡼�¹�
	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );

	}


	$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );



	// ȯ���ֹ�μ���
	$lngorderno = $aryData2["lngorderno"];

	if( $lngorderno != "" )
	{
		$aryData["strReceiveCode"] = fncGetMasterValue( "m_order", "lngorderno", "strordercode", $lngorderno , '',$objDB );
		$aryData["strReviseCode"]  = fncGetMasterValue( "m_order", "lngorderno", "strrevisecode", $lngorderno , '',$objDB );
	}

	// �ؿ� fncChangeDisplayName��ɽ���ѥǡ������Ѵ�(HEADER��
	$aryData2 = fncChangeData2( $aryData2 , $objDB );






	// �ץ�������˥塼������
	// �̲�
	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData2["lngmonetaryunitcode"], "", $objDB );


	$aryData["lngmonetaryunitcode"] 		= fncPulldownMenu( 0, $lngMonetaryUnitCode, '', $objDB );

	// �졼�ȥ�����
	$aryData["lngmonetaryratecode"]			= fncPulldownMenu( 1, $aryData2["lngmonetaryratecode"], '', $objDB );
	// ��ʧ���
	$aryData["lngpayconditioncode"]			= fncPulldownMenu( 2, $aryData2["lngpayconditioncode"], '', $objDB );


	// �ǡ����Υޡ���
	$aryData = array_merge( $aryData2, $aryData);

	//�إå����ͤ��ü�ʸ���Ѵ�
	$aryData["strnote"] = fncHTMLSpecialChars( $aryData["strnote"] );


	// ���ٹ�
	$aryQuery = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngstockno, ";									// �����ֹ�
	$aryQuery[] = "lngstockdetailno as lngOrderDetailNo, ";			// ���������ֹ�
	$aryQuery[] = "lngrevisionno, ";								// ��ӥ�����ֹ�
	$aryQuery[] = "strproductcode, ";								// ���ʥ�����
	$aryQuery[] = "lngstocksubjectcode, ";							// �������ܥ�����
	$aryQuery[] = "lngstockitemcode, ";								// �������ʥ�����
	$aryQuery[] = "To_char(dtmdeliverydate, 'YYYY/mm/dd') as dtmdeliverydate, ";// Ǽ����
	$aryQuery[] = "lngdeliverymethodcode as lngcarriercode, ";			// ������ˡ
	$aryQuery[] = "lngdeliverymethodcode as lngdeliverymethodcode, ";	// ������ˡ
	$aryQuery[] = "lngconversionclasscode, ";						// ������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
	$aryQuery[] = "curproductprice, ";								// ���ʲ���
	$aryQuery[] = "lngproductquantity, ";							// ���ʿ���
	$aryQuery[] = "lngproductunitcode, ";							// ����ñ�̥�����
	$aryQuery[] = "lngtaxclasscode, ";								// �����Ƕ�ʬ������
	$aryQuery[] = "lngtaxcode, ";									// �����ǥ�����
	$aryQuery[] = "curtaxprice, ";									// �����Ƕ��
	$aryQuery[] = "cursubtotalprice, ";								// ���׶��
	$aryQuery[] = "strnote, ";										// ����
	$aryQuery[] = "strmoldno as strserialno ";						// ���ꥢ���ֹ�
	$aryQuery[] = "FROM t_stockdetail ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "lngstockno = $lngstockno";
	$aryQuery[] = " ORDER BY lngSortKey ASC";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );


	$objDB->freeResult( $lngResultID );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}


	if( $lngResultNum = pg_num_rows( $lngResultID ) )
	{
		for( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryQueryResult[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		}
	}


	$aryData["lngStockNo"] = $lngstockno;



	// ���ٹԤ�hidden�ͤ��Ѵ�����
	$aryData["strDetailHidden"] = fncDetailHidden_pc( $aryQueryResult ,"", $objDB);


	// ��������
	$strStockSubjectCode = ( $aryQueryResult["strStockSubjectCode"] != "" ) ? $aryQueryResult["strStockSubjectCode"] : 0;

	$aryData["strStockSubjectCode"]   = fncPulldownMenu( 3, $strStockSubjectCode, '', $objDB );
	// ������ˡ
	$aryData["lngCarrierCode"]        = fncPulldownMenu( 6, 0, '', $objDB );
	// ����ñ��
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// �ٻ�ñ��
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );

	$aryData["MonetaryUnitDisabled"]  = " disabled";






	//-------------------------------------------------------------------------
	// �������֤Υ����å�
	//-------------------------------------------------------------------------
	// ������ξ��
	if( $aryData["lngstockstatuscode"] == DEF_STOCK_APPLICATE )
	{
		fncOutputError( 712, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// ����Ѥξ��
	if( $aryData["lngstockstatuscode"] == DEF_STOCK_CLOSED )
	{
		fncOutputError( 9062, DEF_WARNING, "", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
	//-------------------------------------------------------------------------
	$aryData["lngstockstatuscode"] = fncCheckNullStatus( $aryData["lngstockstatuscode"] );


	//---------------------------------------------------------------
	// �������֤μ���
	//---------------------------------------------------------------
	$aryData["lngOrderStatusCode_Display"] = fncGetMasterValue( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", $aryData["lngstockstatuscode"], '', $objDB );




	// ����ե�ɽ������ɽ������
	$aryData["visibleWF"] = "hidden";
	// ��ǧ�롼�Ȥμ���
	if( $lngorderno != "" )
	{
		$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $lngorderno.":str", '', $objDB );
	}
	// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );




	// �����襳����
	$aryData["lngCustomerCode"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData2["lngcustomercompanycode"], '', $objDB );

	// ������̾��
	$aryData["strCustomerName"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $aryData2["lngcustomercompanycode"], '', $objDB );

	// Ǽ���襳����
	$aryData["lngLocationCode"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData2["lngdeliveryplacecode"], '', $objDB );

	// Ǽ����̾��
	$aryData["strLocationName"] = fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $aryData2["lngdeliveryplacecode"], '', $objDB );




	$aryData["RENEW"]                 = TRUE;
	$aryData["strMode"]               = "check";
	$aryData["strSessionID"]          = $aryData["strSessionID"];
	$aryData["strOrderCode_Editable"] = 'contenteditable="false"';
	$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]      = "renew";
	$aryData["lngFunctionCode"]       = DEF_FUNCTION_PC5;


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

	echo fncGetReplacedHtml( "pc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>