<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ��Ͽ
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
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );
	require( SRC_ROOT . "po/cmn/lib_po.php" );
	require_once(LIB_DEBUGFILE);
	require_once(CLS_IMAGELO_FILE);


	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");



	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]         = $_REQUEST["strSessionID"];          // ���å����ID
	$aryData["lngLanguageCode"]      = $_COOKIE["lngLanguageCode"];        // ���쥳����


	//-------------------------------------------------------------------------
	// �� ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;



	// 300 ���ʴ���
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 301 ���ʴ����ʾ�����Ͽ��
	if ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// ���ϼԥ����ɤμ���
	$lngInputUserCode = $objAuth->UserCode;
	if( !$lngInputUserCode )
	{
		fncOutputError ( 9061, DEF_ERROR, "", TRUE, "", $objDB );
	}

	if(strcmp($aryData["strSpecificationDetails"], "") != 0)
	{
		$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
	}



	// insert�ѤΥǡ���(displaycode����code�ء�
	// ���祳����
	$aryData["lngInChargeGroupCode"]	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "lnggroupcode",  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
	// ô����(���ϻ������祳���ɤ򻲾Ȥ��Ƥ���ΤǤ����Ǥ����祳���ɤϻ��Ȥ��ʤ�
	$aryData["lngInChargeUserCode"]		= fncGetMasterValue( "m_user", "struserdisplaycode" ,"lngusercode" , $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
	// �ܵ�
	$aryData["lngCompanyCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCompanyCode"] . ":str", '',$objDB);
	// ��������
	$aryData["lngFactoryCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngFactoryCode"] . ":str", '',$objDB);
	// ���å���֥깩��
	$aryData["lngAssemblyFactoryCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngAssemblyFactoryCode"] . ":str", '',$objDB);
	// Ǽ�ʾ��
	$aryData["lngDeliveryPlaceCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngDeliveryPlaceCode"] . ":str", '',$objDB);


	// �ܵ�ô���ԥ�����
	if( strcmp( $aryData["lngCompanyCode"], "" ) != 0 )
	{
		$aryData["strCustomerUserCode"]	= fncGetMasterValue( "m_user", "struserdisplaycode" ,"lngusercode" , $aryData["strCustomerUserCode"] . ":str","lngCompanycode=".$aryData["lngCompanyCode"],$objDB);
	}




	//-------------------------------------------------------------------------
	// �� �ȥ�󥶥�����󳫻�
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();





	//-------------------------------------------------------------------------
	// �� m_Product�Υ��������ֹ�����
	//-------------------------------------------------------------------------
	// �������󥹴ؿ��ƤӽФ�
	$sequence_m_product = fncGetSequence( "m_product.lngproductno", $objDB );

	// ���������ֹ��4�������
	$sequence_code = $sequence_m_product;

	$fig = strlen( $sequence_code );
	$sequence_code = sprintf( "%05d" , $sequence_code );
	// echo "sequence_code : $sequence_code<br>";




	// ���֥����ɤμ���
	$lngProductStatusCode = ( $aryData["lngWorkflowOrderCode"] == 0 ) ? DEF_PRODUCT_NORMAL : DEF_PRODUCT_APPLICATE;




	$strCopyrightNote = ( $aryData["strCopyrightNote"] == "null" ) ? "null" : "'".$aryData["strCopyrightNote"]."'" ;

	$aryQuery = array();

	$aryQuery[] = "INSERT INTO m_product (";
	$aryQuery[] = "lngproductno, ";															// 1:�����ֹ�
	$aryQuery[] = "strProductCode, ";														// 2:���ʥ�����
	$aryQuery[] = "strProductName, ";														// 3:����̾��
	$aryQuery[] = "strProductEnglishName, ";	 											// 4:����̾��(�Ѹ�)
	$aryQuery[] = "lngInChargeGroupCode, ";													// 5:����
	$aryQuery[] = "lngInChargeUserCode, ";													// 6:ô����
	$aryQuery[] = "lnginputusercode, ";														// 7:���ϼ�
																							// 8:�ܵҼ��̥�����(ɽ���Τ�)
	$aryQuery[] = "strGoodsCode, ";															// 9:���ʥ�����
	$aryQuery[] = "strGoodsName, ";															// 10:����̾��
	$aryQuery[] = "lngCustomerCompanyCode, ";												// 11:�ܵ�
	$aryQuery[] = "lngcustomergroupcode, ";													// 12:�ܵ�����(NULL)
	$aryQuery[] = "lngCustomerUserCode, ";													// 13:�ܵ�ô���ԥ�����
	$aryQuery[] = "strCustomerUserName, ";													// 14:�ܵ�ô����()
	$aryQuery[] = "lngPackingUnitCode, ";													// 15:�ٻ�ñ��(int2)
	$aryQuery[] = "lngProductUnitCode, ";													// 16:����ñ��(int2)
	$aryQuery[] = "lngBoxQuantity, ";														// 17:��Ȣ���ޡ�����(int4)
	$aryQuery[] = "lngCartonQuantity, ";													// 18:�����ȥ�����(int4)
	$aryQuery[] = "lngProductionQuantity, ";												// 19:����ͽ���()
	$aryQuery[] = "lngProductionUnitCode, ";												// 20:����ͽ�����ñ��()
	$aryQuery[] = "lngFirstDeliveryQuantity, ";												// 21:���Ǽ�ʿ�(int4)
	$aryQuery[] = "lngFirstDeliveryUnitCode, ";												// 22:���Ǽ�ʿ���ñ��()
	$aryQuery[] = "lngFactoryCode, ";														// 23:��������()
	$aryQuery[] = "lngAssemblyFactoryCode, ";		 										// 24:���å���֥깩��()
	$aryQuery[] = "lngDeliveryPlaceCode, ";													// 25:Ǽ�ʾ��(int2)
	$aryQuery[] = "dtmDeliveryLimitDate, ";													// 26:Ǽ�ʴ�����()
	$aryQuery[] = "curProductPrice, ";		 												// 27:����()
	$aryQuery[] = "curRetailPrice, ";														// 28:����()
	$aryQuery[] = "lngTargetAgeCode, ";														// 29:�о�ǯ��()
	$aryQuery[] = "lngRoyalty, ";															// 30:�����ƥ���()
	$aryQuery[] = "lngCertificateClassCode, "; 												// 31:�ڻ�()
	$aryQuery[] = "lngCopyrightCode, ";														// 32:�Ǹ���()
	$aryQuery[] = "strCopyrightDisplayStamp, ";												// 33:�Ǹ�ɽ��(���)
	$aryQuery[] = "strCopyrightDisplayPrint, ";												// 34:�Ǹ�ɽ��(����ʪ)
	$aryQuery[] = "lngProductFormCode, ";													// 35:���ʷ���()
	$aryQuery[] = "strProductComposition, ";												// 36:���ʹ���()
	$aryQuery[] = "strAssemblyContents, "; 													// 37:���å���֥�����()
	$aryQuery[] = "strSpecificationDetails, "; 												// 38:���;ܺ�()
	$aryQuery[] = "strNote, ";																// 39:����
	$aryQuery[] = "bytinvalidflag, ";														// 40:̵���ե饰
	$aryQuery[] = "dtmInsertDate, ";														// 41:��Ͽ��
	$aryQuery[] = "dtmUpdateDate, ";														// 42:������
	$aryQuery[] = "strcopyrightnote ,";														// 43:�Ǹ�������
	$aryQuery[] = "lngproductstatuscode,";													// ���ʾ���
	$aryQuery[] = "lngCategoryCode";														// ���ƥ��꡼������


	$aryQuery[] = " ) values ( ";

	$aryQuery[] = "$sequence_m_product," ;													// 1:�����ֹ�
	$aryQuery[] = "'$sequence_code',";														// 2:���ʥ�����()
	$aryQuery[] = "'".$aryData["strProductName"] ."', ";									// 3:����̾��()
	$aryQuery[] = "'".$aryData["strProductEnglishName"]."', ";	 							// 4:����̾��(�Ѹ�)()
	$aryQuery[] = $aryData["lngInChargeGroupCode"].",";										// 5:����()
	$aryQuery[] = $aryData["lngInChargeUserCode"].",";										// 6:ô����(int2)
	$aryQuery[] = "$lngInputUserCode,";														// 7:���ϼ�()
																							// 8:�ܵҼ��̥�����()
	$aryQuery[] = "'".$aryData["strGoodsCode"]."', ";										// 9:���ʥ�����()
	$aryQuery[] = "'".$aryData["strGoodsName"]."', ";										// 10:����̾��()
	if ( $aryData["lngCompanyCode"] and $aryData["lngCompanyCode"] != "" )
	{
		$aryQuery[] = $aryData["lngCompanyCode"].",";										// 11:�ܵ�()
	}
	else
	{
		$aryQuery[] = "null, ";																// 11:�ܵ�()
	}
	$aryQuery[] = "null, ";																	// 12:�ܵ�����(NULL)

	if( strcmp( $aryData["strCustomerUserCode"], "" ) != 0)
	{
		$aryQuery[] = "'".$aryData["strCustomerUserCode"]."', ";							// 13:�ܵ�ô���ԥ�����
		$aryQuery[] = "null, ";																// 14:�ܵ�ô����()
	}
	elseif( strcmp( $aryData["strCustomerUserCode"], "" ) == 0 && strcmp( $aryData["strCustomerUserName"], "") != 0)
	{
		$aryQuery[] = "null, ";																// 13:�ܵ�ô���ԥ�����
		$aryQuery[] = "'".$aryData["strCustomerUserName"]."', ";							// 14:�ܵ�ô����()
	}
	else
	{
		$aryQuery[] = "null, ";																// 13:�ܵ�ô���ԥ�����
		$aryQuery[] = "null, ";																// 14:�ܵ�ô����()
	}

	$aryQuery[] = $aryData["lngPackingUnitCode"].",";										// 15:�ٻ�ñ��(int2)
	$aryQuery[] = $aryData["lngProductUnitCode"].",";										// 16:����ñ��(int2)
	if ( $aryData["lngBoxQuantity"] and $aryData["lngBoxQuantity"] != "" )
	{
		$aryQuery[] = "to_number('" .$aryData["lngBoxQuantity"]."','9999999999.9999'),";	// 17:��Ȣ���ޡ�����(int4)
	}
	else
	{
		$aryQuery[] = "null, ";																// 17:��Ȣ����
	}
	$aryQuery[] = "to_number('" .$aryData["lngCartonQuantity"]."','9999999999.9999'),";		// 18:�����ȥ�����(int4)
	$aryQuery[] = "to_number('" .$aryData["lngProductionQuantity"]."','9999999999.9999'),";	// 19:����ͽ���()
	$aryQuery[] = $aryData["lngProductionUnitCode"].",";									// 20:����ͽ�����ñ��()
	$aryQuery[] = "to_number('" .$aryData["lngFirstDeliveryQuantity"]."','9999999999.9999'),";			// 21:���Ǽ�ʿ�(int4)
	$aryQuery[] = $aryData["lngFirstDeliveryUnitCode"].",";									// 22:���Ǽ�ʿ���ñ��()
	if ( $aryData["lngFactoryCode"] and $aryData["lngFactoryCode"] != "" )
	{
		$aryQuery[] = $aryData["lngFactoryCode"].",";										// 23:��������()
	}
	else
	{
		$aryQuery[] = "null, ";																// 23:��������()
	}
	if ( $aryData["lngAssemblyFactoryCode"] and $aryData["lngAssemblyFactoryCode"] != "" )
	{
		$aryQuery[] = $aryData["lngAssemblyFactoryCode"].",";		 						// 24:���å���֥깩��()
	}
	else
	{
		$aryQuery[] = "null, ";		 														// 24:���å���֥깩��()
	}
	if ( $aryData["lngDeliveryPlaceCode"] and $aryData["lngDeliveryPlaceCode"] != "" )
	{
		$aryQuery[] = $aryData["lngDeliveryPlaceCode"].",";									// 25:Ǽ�ʾ��(int2)
	}
	else
	{
		$aryQuery[] = "null, ";																// 25:Ǽ�ʾ��(int2)
	}
	$aryQuery[] = "To_timestamp('". $aryData["dtmDeliveryLimitDate"] ."', 'YYYY/mm'),";		// 26:Ǽ�ʴ�����()
	$aryQuery[] = "to_number('" .$aryData["curProductPrice"]."','9999999999.9999'),";		// 27:����()
	$aryQuery[] = "to_number('" .$aryData["curRetailPrice"]. "','9999999999.9999'),";		// 28:����()
	$aryQuery[] = $aryData["lngTargetAgeCode"].",";											// 29:�о�ǯ��()
// 2004.06.17 suzukaze update start

//	$aryQuery[] = "to_number('" .$aryData["lngRoyalty"]."','999.99'),";						// 30:�����ƥ���()

	if ( $aryData["lngRoyalty"] and $aryData["lngRoyalty"] != "" )
	{
		$aryQuery[] = "to_number('" .$aryData["lngRoyalty"]."','999.99'),";	                                // 30:�����ƥ���()
	}
	else
	{
		$aryQuery[] = "null, ";	
	}


// 2004.06.17 suzukaze update end
	$aryQuery[] = $aryData["lngCertificateClassCode"].","; 									// 31:�ڻ�()
	$aryQuery[] = $aryData["lngCopyrightCode"].",";											// 32:�Ǹ���()
	$aryQuery[] = "'".$aryData["strCopyrightDisplayStamp"]."', ";							// 33:�Ǹ�ɽ��(���)
	$aryQuery[] = "'".$aryData["strCopyrightDisplayPrint"]."', ";							// 34:�Ǹ�ɽ��(����ʪ)
	$aryQuery[] = $aryData["lngProductFormCode"].",";										// 35:���ʷ���()
	$aryQuery[] = "'".$aryData["strProductComposition"]."', ";								// 36:���ʹ���()
	$aryQuery[] = "'".$aryData["strAssemblyContents"]."', "; 								// 37:���å���֥�����()
//	$aryQuery[] = "'".addslashes( $aryData["strSpecificationDetails"] )."', "; 				// 38:���;ܺ�()
	$aryQuery[] = "'". stripslashes( $aryData["strSpecificationDetails"] )."', "; 				// 38:���;ܺ�()
	$aryQuery[] = "null, ";																	// 39:����
	$aryQuery[] = "false, ";																// 40:̵���ե饰
	$aryQuery[] = "'now()',";																// 41:��Ͽ��
	$aryQuery[] = "'now()',";																// 42:������
	$aryQuery[] = $strCopyrightNote . ", ";													// 43:�Ǹ�������
	$aryQuery[] = $lngProductStatusCode. ", ";												// ���ʾ���
	$aryQuery[] = $aryData["lngCategoryCode"];												// ���ƥ��꡼������
	$aryQuery[] = ")" ;


	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);

// 2004.06.17 suzukaze update start

	//�ȥ�󥶥�����󳫻�
//	$objDB->transactionBegin();

// 2004.06.17 suzukaze update start

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		$objDB->close();
		return true;
	}



	//GOODS_PLANE����Ͽ
	// �������󥹴ؿ��ƤӽФ�
	$sequence_t_goodsplan = fncGetSequence( 't_goodsplan.lnggoodsplancode', $objDB );

	$aryQueryGoods = array();
	$aryQueryGoods[] = "INSERT INTO t_goodsplan ( ";
	$aryQueryGoods[] = "lnggoodsplancode, ";
	$aryQueryGoods[] = "lngrevisionno, ";
	$aryQueryGoods[] = "lngproductno, ";
	$aryQueryGoods[] = "dtmcreationdate, ";
	$aryQueryGoods[] = "dtmrevisiondate, ";
	$aryQueryGoods[] = "lnggoodsplanprogresscode, ";
	$aryQueryGoods[] = "lnginputusercode ";
	$aryQueryGoods[] = " ) values ( ";
	$aryQueryGoods[] = "$sequence_t_goodsplan, ";
	$aryQueryGoods[] = "0, ";
	$aryQueryGoods[] = "$sequence_m_product, ";
	$aryQueryGoods[] = "'now()',";
	$aryQueryGoods[] = "'now()',";
	$aryQueryGoods[] = $aryData["lngGoodsPlanProgressCode"].", ";
	$aryQueryGoods[] = "$lngInputUserCode ";
	$aryQueryGoods[] = ")" ;

	$strQueryGoods = "";
	$strQueryGoods = implode("\n", $aryQueryGoods);


	if ( !$lngResultID = $objDB->execute( $strQueryGoods ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		$objDB->close();

		return true;
	}

	//-------------------------------------------------------------------------
	// ���᡼���ե��������Ͽ����
	//-------------------------------------------------------------------------
/*
	// ���åץ��ɲ�����¸�ߤ��뤫���ǧ����
	if(!empty($aryData["uploadimages"]))
	{
		// ���᡼���������֥�����������
		$objImageLo = new clsImageLo();
		$lngUploadImageCount = count($aryData["uploadimages"]);

		// ������ѥ�������
		$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");

		// ���åץ��ɤ��줿�оݤβ����ѥ�������ˡ��顼�����֥����������֥������Ȥ��Ѥ��ƥǡ����١�������Ͽ
		for($i = 0; $i < $lngUploadImageCount; $i++)
		{
			$aryImageInfo = array();
			$aryImageInfo['type'] = "";
			$aryImageInfo['size'] = 0;
			$blnRet = $objImageLo->addImageLo($objDB, $sequence_code, $aryImageInfo, $strDestPath, $aryData["strTempImageDir"], $aryData["uploadimages"][$i]);
			if(!$blnRet)
			{
				// DB�ز�������Ͽ������ޤ���Ǥ���
			}
		}
	}
*/


//fncDebug( 'lib_so.txt', $aryData, __FILE__, __LINE__);

	//-------------------------------------------------------------------------
	// �� ��ǧ����
	//
	//   ��ǧ�롼��
	//     ��0 : ��ǧ�롼�Ȥʤ�
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryData["lngWorkflowOrderCode"];	// ��ǧ�롼��

	$strWFName   = "���� [No:" . $sequence_code . "]";
	$lngSequence = $sequence_m_product;
	$strDefFnc   = DEF_FUNCTION_P1;

	//$strProductCode       = $aryData["aryPoDitail"][0]["strProductCode"];
	//$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );
	$lngApplicantUserCode = $aryData["lngInChargeUserCode"];


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
		$aryQuery[] = ( $lngWorkflowOrderCode != "" ) ? $lngWorkflowOrderCode . ", " : "null, "; // 2  : ����ե����������
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
		$aryQuery[] = "'" . $aryData["strWorkflowMessage"] . "',";		// 11:����
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
		$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];	// ��ǧ�ԥ᡼�륢�ɥ쥹

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
			fncSendMail( $aryMailData["strmailaddress"], $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}

		// Ģɼ����ɽ������
		$aryData["PreviewVisible"] = "hidden";
		//---------------------------------------------------------------
	}




	// �ȥ�󥶥������λ
	$objDB->transactionCommit();

	$aryData["strBodyOnload"] = "";

	//��������
	$aryData["dtNowDate"] = date('Y/m/d', time() );
	$aryData["lngProductNumber"] = $sequence_m_product;

	// ���ʥ�����
	$aryData["strProductCode"] = $sequence_code;


	// �����������Υ��ɥ쥹����
	$aryData["strAction"] = "/p/regist/index.php?strSessionID=";

	$objDB->close();


	// Ģɼ�����б�
	// ���¤���äƤʤ�����ץ�ӥ塼�ܥ����ɽ�����ʤ�
	if( fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) && $lngProductStatusCode != DEF_PRODUCT_APPLICATE )
	{
		$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $sequence_m_product . "&bytCopyFlag=TRUE";

		$aryData["listview"] = 'visible';
	}
	else
	{
		$aryData["listview"] = 'hidden';
	}

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");


	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;
	return true;

?>