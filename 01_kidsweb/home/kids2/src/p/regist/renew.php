<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ��������
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
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );
	require( SRC_ROOT."p/cmn/lib_p3.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( "libsql.php" );
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

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	//$aryData["lngLanguageCode"] = $_REQUEST["lngLanguageCode"];



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


	// 300 ���ʴ���
	if( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 306 ���ʴ����ʾ��ʽ�����
	if( !fncCheckAuthority( DEF_FUNCTION_P6, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}




	//-------------------------------------------------------------------------
	// �� ���ϥ����å�
	//-------------------------------------------------------------------------
	if( strcmp ( $aryData["strProcess"], "" ) != 0)
	{
		$strSelectError = fncOutputError ( 9020, "", "", FALSE, "", $objDB );

		if($aryData["strProcess"] == "check" )
		{
			//���顼�����å����ܤ�����ǳ�Ǽ ====================================
			$aryCheck = array();
			$aryCheck["strProductName"]				= "null:length(1,100)";			// 3:����̾��
			$aryCheck["strProductEnglishName"]		= "null:length(1,100)"; 			// 4:����̾��(�Ѹ�)
			$aryCheck["lngInChargeGroupCode"]		= "null:length(1,2)";				// 5:����
			$aryCheck["lngInChargeUserCode"]		= "null:length(1,3)";				// 6:ô����
			$aryCheck["strGoodsCode"]				= "length(1,10)";			// 9:���ʥ����ɡ�null��Ͽ��ǽ��
/*ɬ�ܳ���		$aryCheck["lngCategoryCode"]			= "number(1,999999999):length(1,10)";		// xxxx:���ƥ��꡼������// added by k.saito*/
			$aryCheck["lngCategoryCode"]			= "length(1,10)";
			$aryCheck["strGoodsName"]				= "length(1,80)";			// 10:����̾��
			$aryCheck["lngCustomerCode"]			= "number(0,999999999)";			// 11:�ܵ�
			//$aryCheck["lngCustomerUserCode"]		= "";						// 13:�ܵ�ô���ԥ����� 
			// 13�����äƤ�����Τ�
			if( strcmp($aryCheck["lngCustomerUserCode"], "") == 0 )
			{
				$aryCheck["strCustomerUserName"]		= "length(1,50)";			// 14:�ܵ�ô����50byte
			}
			$aryCheck["lngPackingUnitCode"]			= "money(1,99)";				// 15:�ٻ�ñ��(int2)
			$aryCheck["lngProductUnitCode"]			= "money(1,99)";				// 16:����ñ��(int2)
			$aryCheck["lngBoxQuantity"]				= "number(0,2147483647)";		// 17:��Ȣ���ޡ�����10byte"   :length(1,10)
			$aryCheck["lngCartonQuantity"]			= "null:number(1,2147483647)";			// 18:�����ȥ�����10byte   :length(1,10)
			$aryCheck["lngProductionQuantity"]		= "null:number(1,2147483647)";			// 19:����ͽ���10byte     :length(1,10)
			$aryCheck["lngProductionUnitCode"]		= "number(1,99):length(1,4)";			// 20:����ͽ�����ñ��4byte
			$aryCheck["lngFirstDeliveryQuantity"]	= "null:number(1,2147483647)";				// 21:���Ǽ�ʿ�10byte   :length(1,10)
			$aryCheck["lngFirstDeliveryUnitCode"]	= "number(1,99):length(0,4)";				// 22:���Ǽ�ʿ���ñ��4byte
			$aryCheck["lngFactoryCode"]				= "length(1,4)";			// 23:��������4byte
			$aryCheck["lngAssemblyFactoryCode"]		= "length(1,4)";	 			// 24:���å���֥깩��4byte
			$aryCheck["lngDeliveryPlaceCode"]		= "length(1,4)";				// 25:Ǽ�ʾ��4byte
			$aryCheck["dtmDeliveryLimitDate"]		= "null:date"; 					// 26:Ǽ�ʴ�����:date
			$aryCheck["curProductPrice"]			= "null:money(0.0001,99999999999999.9999)";	// 27:����
			$aryCheck["curRetailPrice"]				= "null:money(0.0000,99999999999999.9999)";	// 28:����     :length(1,8)
/*			$aryCheck["lngRoyalty"]					= "null:number(0,999999)";		// 30:�����ƥ��� 
*/
 			$aryCheck["lngRoyalty"]					= "number(0,100)";

			$aryCheck["lngCertificateClassCode"]	= "null";		 				// 31:�ڻ�
/*ɬ�ܳ���			
			if( $aryData["lngCopyrightCode"] == 0 && strcmp( $aryData["strCopyrightNote"], "") == 0 )
			{
				$aryCheck["lngCopyrightCode"]		= "number(1,99)"; 				// 32:�Ǹ���
				$aryCheck["strCopyrightNote"]		= "null";					// :�Ǹ�������
			}
*/
			$aryCheck["lngCopyrightCode"]		= "length(1,50)"; 					// 32:�Ǹ���
			$aryCheck["strCopyrightNote"]		= "length(1,200)";					// :�Ǹ�������
			
			// ���������ؤ�Ǥ���
			$aryCheck["lngProductFormCode"]			= "number(1,99,The list has not been selected.):length(1,100)"; 	// 35:���ʷ���100
			if($_COOKIE["lngLanguageCode"])
			{
				$aryCheck["lngProductFormCode"]			= "number(1,99,�ꥹ�Ȥ����򤵤�Ƥ��ޤ���):length(1,100)"; 	// 35:���ʷ���100
			}
			$aryCheck["strCopyrightDisplayPrint"]	= "length(1,100)"; 						// 34:�Ǹ�ɽ��(����ʪ)100
			$aryCheck["strProductComposition"]		= "null:number(0,99)";  				// 36:���ʹ���100byte
			$aryCheck["strAssemblyContents"]		= "length(1,100)";  					// 37:���å���֥�����100byte
			$aryCheck["strSpecificationDetails"]	= "length(1,10000)";  						// 38:���;ܺ�10000
			// ���顼�ؿ��θƤӽФ�
			$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
			list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
			$errorCount = ($bytErrorFlag == "TRUE" ) ? 1 : 0;
			//-------------------------------------------------
			// �ǡ������ֿ�����פˤʤäƤ��ʤ����ɤ�����ǧ
			//-------------------------------------------------
			$strCheckQuery = "SELECT lngProductStatusCode FROM m_Product p WHERE p.strProductCode = '" . $aryData["strProductCode"] . "'";
			$strCheckQuery .= " AND p.bytInvalidFlag = FALSE\n";
			// �����å������꡼�μ¹�
			list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

			if ( $lngCheckResultNum == 1 )
			{
				$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
				$lngProductStatusCode = $objResult->lngproductstatuscode;

				if( $lngProductStatusCode == DEF_PRODUCT_APPLICATE )
				{
					fncOutputError( 307, DEF_WARNING, "", TRUE, "../p/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

			// ���ID�����
			$objDB->freeResult( $lngCheckResultID );




			/**
				���;ܺٲ����ե�����HIDDEN����
			*/
			if( $aryData["uploadimages"] )
			{
				for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
				{
					$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
				}

				// �Ƽ����Ѥ�����
				$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
				$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
			}
			else
			{
				$aryData["re_uploadimages"]	= "";
				$aryData["re_editordir"]	= "";
			}



			//��ǧ���� ==================================================================================
			if( $errorCount == 0 )
			{

				// �ѹ������ä����ܤΤ�$aryUpdate�˳�Ǽ
				$strProductCode = $aryData["strProductCode"];

				$aryQuery[] = "SELECT ";
				$aryQuery[] = "lngproductno, ";
				$aryQuery[] = "strProductCode, ";																		//2:���ʥ�����
				$aryQuery[] = "strProductName, ";																		//3:����̾��
				$aryQuery[] = "strProductEnglishName, ";	 															//4:����̾��(�Ѹ�)
				$aryQuery[] = "lngInChargeGroupCode, ";																	//5:����
				$aryQuery[] = "lngInChargeUserCode, ";																	//6:ô����
				$aryQuery[] = "lnginputusercode, ";																		//7:���ϼ�
																														//8:�ܵҼ��̥�����(ɽ���Τ�)
				$aryQuery[] = "strGoodsCode, ";																			//9:���ʥ�����
				$aryQuery[] = "strGoodsName, ";																			//10:����̾��
				// 2004/03/08 watanabe update start
				$aryQuery[] = "lngCustomerCompanyCode as lngCompanyCode, ";												//11:�ܵ�
				$aryQuery[] = "lngCustomerUserCode as strCustomerUserCode, ";											//13:�ܵ�ô���ԥ�����
				// 2004/03/08 watanabe update end
				$aryQuery[] = "strCustomerUserName, ";																	//14:�ܵ�ô����()
				$aryQuery[] = "lngPackingUnitCode, ";																	//15:�ٻ�ñ��(int2)
				$aryQuery[] = "lngProductUnitCode, ";																	//16:����ñ��(int2)
				$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, ";						//17:��Ȣ���ޡ�����(int4)
				$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, ";				//18:�����ȥ�����(int4)
				$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, ";		//19:����ͽ���()
				$aryQuery[] = "lngProductionUnitCode, ";																//20:����ͽ�����ñ��()
				$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, ";	//21:���Ǽ�ʿ�(int4)
				$aryQuery[] = "lngFirstDeliveryUnitCode, ";																//22:���Ǽ�ʿ���ñ��()
				$aryQuery[] = "lngFactoryCode, ";																		//23:��������()
				$aryQuery[] = "lngAssemblyFactoryCode, ";	 															//24:���å���֥깩��()
				$aryQuery[] = "lngDeliveryPlaceCode, ";																	//25:Ǽ�ʾ��(int2)
				$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, ";						//26:Ǽ�ʴ�����()
				$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, ";				//27:����()
				$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,";					//28:����()
				$aryQuery[] = "lngTargetAgeCode, ";																		//29:�о�ǯ��()
				$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,";										//30:�����ƥ���()
				$aryQuery[] = "lngCertificateClassCode, "; 																//31:�ڻ�()
				$aryQuery[] = "lngCopyrightCode, ";																		//32:�Ǹ���()
				$aryQuery[] = "strCopyrightDisplayStamp, ";																//33:�Ǹ�ɽ��(���)
				$aryQuery[] = "strCopyrightDisplayPrint, ";																//34:�Ǹ�ɽ��(����ʪ)
				$aryQuery[] = "lngProductFormCode, ";																	//35:���ʷ���()
				$aryQuery[] = "strProductComposition, ";																//36:���ʹ���()
				$aryQuery[] = "strAssemblyContents, "; 																	//37:���å���֥�����()
				$aryQuery[] = "strSpecificationDetails, "; 																//38:���;ܺ�()
				$aryQuery[] = "strNote, ";																				//39:����
				$aryQuery[] = "strCopyrightNote, ";																		//40:�Ǹ�������
				$aryQuery[] = "lngProductStatusCode,";																	// ���ʾ���
				$aryQuery[] = "lngCategoryCode";																		// ���ƥ��꡼������
				$aryQuery[] = "FROM m_product ";
				$aryQuery[] = "WHERE bytinvalidflag = false ";
				$aryQuery[] = "AND strProductCode = '$strProductCode'";

				$strQuery = "";
				$strQuery = implode("\n", $aryQuery);

				//echo "$strQuery<br><br>";

				$objDB->freeResult( $lngResultID );
				if ( !$lngResultID = $objDB->execute( $strQuery ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
					$objDB->close();
					return true;
				}


				if ( !$lngResultNum = pg_Num_Rows( $lngResultID ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
					$objDB->close();
					return true;
				}

				$aryResult = array();
				$aryResult = $objDB->fetchArray( $lngResultID, 0 );


				// ���ʹԾ��� ====================================================
				$lngproductno = $aryResult["lngproductno"];

				$aryQuery2[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
				$aryQuery2[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate "; 
				$aryQuery2[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
				$aryQuery2[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
				$aryQuery2[] = "$lngproductno )";

				$strQuery2 = "";
				$strQuery2 = implode("\n", $aryQuery2);

				//echo "$strQuery2<br><br>";

				$objDB->freeResult( $lngResultID2 );
				if ( !$lngResultID2 = $objDB->execute( $strQuery2 ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
				}

				if ( !$lngResultNum = pg_Num_Rows( $lngResultID2 ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
				}

				$aryResult2 = array();
				$aryResult2 = $objDB->fetchArray( $lngResultID2, 0 );

				$aryResult["lnggoodsplanprogresscode"] = $aryResult2["lnggoodsplanprogresscode"];


				$aryKeys = array_keys($aryResult);

				// DB�Υ����̾�˹�碌�����
				for( $i = 0; $i < count( $aryKeys ); $i++ )
				{
					for( $j = 0; $j < count( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME] ) ; $j++ )
					{
						if( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME][$j] === $aryKeys[$i] )
						{
							$strColmName = $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_COLMNAME][$j];					// �ѹ���Υ����̾
							$strColmName2 = strtolower($gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME][$j]);		// �Ѵ��оݤΥ����̾�ʾ�ʸ����
							$strColmName = strtolower($strColmName);

							$aryResult[$strColmName] = $aryResult[$strColmName2];

						}
					}
				}


				// ����Υ�����
				$aryData["lngInChargeGroupCode"]	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "lnggroupcode",  $aryData["lngInChargeGroupCode"] . ":str",'bytGroupDisplayFlag=true',$objDB);
				// ô����
				$aryData["lngInChargeUserCode"]		= fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
				// �ܵ�
				$aryData["lngCompanyCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCompanyCode"] . ":str", '',$objDB);
				// ��������
				$aryData["lngFactoryCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngFactoryCode"] . ":str", '',$objDB);
				// ���å���֥깩��
				$aryData["lngAssemblyFactoryCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngAssemblyFactoryCode"] . ":str", '',$objDB);
				// Ǽ�ʾ��
				$aryData["lngDeliveryPlaceCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngDeliveryPlaceCode"] . ":str", '',$objDB);
				// �ܵ�ô����

				if( strcmp( $aryData["strCustomerUserCode"], "" ) != 0)
				{
					$aryData["strCustomerUserCode"]	= fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $aryData["strCustomerUserCode"] . ":str", '',$objDB);
				}

				// --------------------------
				// ���塞�����⥵���С��Ǥϡ�POST���줿�ǡ����Υ��֥륯�����Ȥˡ�ޡ������դ��Ƥ��ޤ����ᡢ�����������
				// confirm/index.php �� regist/renew.php �ˤ��б���2006/10/08 K.Saito
				$aryData["strSpecificationDetails"] = StripSlashes($aryData["strSpecificationDetails"]);
				// --------------------------

				// http:// ���� https:// �Υۥ��Ȥ��ޤޤ�Ƥ����硢�������
				$aryData["strSpecificationDetails"] = preg_replace("/(http:\/\/?[^\/]+)|(https:\/\/?[^\/]+)/i", "" , $aryData["strSpecificationDetails"]);


				// ���;ܺ٤��ü�ʸ������
				//$aryData["strSpecificationDetails"] = fncHTMLSpecialChars( $_POST["strSpecificationDetails"] );
				// ���;ܺ�ɽ����
				$aryData["strSpecificationDetails_DIS"] = nl2br( $aryData["strSpecificationDetails"] );



				$aryUpdate = array();
				$aryResult_Keys = array_keys($aryResult);
				$aryData_Keys = array_keys($aryData);


				// �ѹ������ä����ܤΤߥ��åץǡ��Ȥ���
				for ( $i = 0; $i < count( $aryResult_Keys ); $i++ ) 
				{
					list ( $strKey_Result, $strValue_Result ) = each ( $aryResult_Keys );

					reset( $aryData_Keys );
					for($j = 0; $j < count( $aryData_Keys ); $j++)
					{
						list ($strKey_Data, $strValue_Data) = each ( $aryData_Keys );

						$strValue_Data_Low = strtolower( $strValue_Data );

						if( strcmp($strValue_Result, $strValue_Data_Low ) == 0 ) //ʸ���󷿤������
						{

							//�ܺٻ��ͤξ��:���ԥ����ɤ�����
							if($strValue_Result == "strspecificationdetails")
							{
								$aryResult[$strValue_Result] = preg_replace( '/\x0D\x0A|\x0A|\x0D/', "\x0A", htmlspecialchars($aryResult[$strValue_Result], ENT_COMPAT | ENT_HTML401, "ISO-8859-1") );
								$aryData[$strValue_Data] = preg_replace( '/\x0D\x0A|\x0A|\x0D/', "\x0A", $aryData[$strValue_Data] );
							}


							if( $aryResult[$strValue_Result] != $aryData[$strValue_Data] )
							{

								// ô���ԥ�����:��Ͽ���̤�ô���Ԥ�code����Ͽ������硢DB�ˤ�name�ͤ��Ϥ��äƤ��ʤ���
								// ���ϲ��̤Ǥ�code����name������Ƥ���
								// ���β��̤�POST�Υǡ�����DB��ȹ礹��Ȥ���name�������оݤˤʤ�
								//if( $strValue_Result == "strcustomerusername")
								//{
								//	if( strcmp( $aryData["lngInChargeUserCode"] ,"" ) == 0 )
								//	{
								//		 $aryUpdate[] = $strValue_Result;
								//	}
								//}
								//else
								//{
						  			$aryUpdate[] = $strValue_Result;
						  		//}
						  	}
						}
					}
				}

				if( count( $aryUpdate ) == 0)
				{

					fncOutputError ( 306, DEF_WARNING, "", TRUE, "../p/regist/renew.php?strProductCode=".$_GET['strProductCode']."&strSessionID=".$_GET["strSessionID"], $objDB );

				}


				// ô���ԥ����ɽ���
				$lngResult1 = array_search("lngcustomerusercode", $aryUpdate);
				$lngResult2 = array_search("strcustomerusername", $aryUpdate);

				if( strcmp( $lngResult1, "") != 0 && strcmp( $lngResult2, "") != 0 )
				{
					for( $i = 0; $i < count( $aryUpdate ) ; $i++ )
					{
						if( $aryUpdate[$i] != "strcustomerusername")
						{
							$aryUpdate2[] = $aryUpdate[$i];
						}
					}

					//�ѹ������ä�key�Τߡ�,�׶��ڤ�ǤĤʤ���
					if( $aryUpdate )
					{
						while ( list ($strkey, $strvalue) = each ( $aryUpdate2 ) )
						{
							$strUpdate .= $strvalue ;
							$strUpdate .= ",";
						}
					}

				}
				else
				{
					//�ѹ������ä�key�Τߡ�,�׶��ڤ�ǤĤʤ���
					if( $aryUpdate )
					{
						while ( list ($strkey, $strvalue) = each ( $aryUpdate ) )
						{
							$strUpdate .= $strvalue ;
							$strUpdate .= ",";
						}
					}
				}


				// �����Ρ�,�פ�Ȥ�
				$strUpdate = ereg_replace (",$", "", $strUpdate);
				$aryData["updatekey"] = $strUpdate;


				// ����̾�ʱѸ�ˤ��������(POST�Υǡ�����DB�Υǡ������������Τߡ�����������
				if( $aryResult["strproductenglishname"] != $_POST["strProductEnglishName"] )
				{
					$lngInChargeGroupCode = $aryData["lngInChargeGroupCode"];
// 2004.02.24 fncNearName �ؿ��ΥХ������б�
					$strOptionValue = fncNearName ( $aryData["strProductEnglishName"], $lngInChargeGroupCode, $strProductCode ,$objDB );
					$aryData["strOptionValue"] = $strOptionValue;
				}



				// ��ǧ����ɽ�� 

				if(strcmp($aryData["strNexturl"], "") == 0)
				{
					$aryData["strNextUrl"] = "renew3.php";
				}

				if(strcmp($aryData["strBackurl"], "") == 0)
				{
					$aryData["strBackurl"] = "renew.php";
				}
				//$aryData["strButton"] = "<input type=\"button\" onClick=\"fncPageback('')\" value=\"���\"><input type=\"button\" onClick=\"fncPagenext('renew3.php')\" value=\"��Ͽ\">";


				// �����̾��DIS�ϳ�ǧ���̤�ɽ����Τ��
				// ./tmp/p/confirm/parts.tmpl ��
				
				// ���ƥ��꡼
				$aryData["lngCategoryCode_DIS"] = fncGetMasterValue( "m_Category", "lngCategoryCode", "strCategoryName", $_POST["lngCategoryCode"], '', $objDB);
				// �ٻ�ñ��
				$aryData["lngPackingUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngPackingUnitCode"], '', $objDB);

				// ����ñ��
				$aryData["lngProductUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngProductUnitCode"], '', $objDB);

// 2004.05.27 suzukaze update start
				// ���ʷ���
				$aryData["lngProductFormCode_DIS"] = fncGetMasterValue( "m_ProductForm", "lngProductFormCode", "strProductFormName", $_POST["lngProductFormCode"], '', $objDB);
// 2004.05.27 suzukaze update end

				// �о�ǯ��
				$aryData["lngTargetAgeCode_DIS"] = fncGetMasterValue( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $_POST["lngTargetAgeCode"], '', $objDB);

				// �ڻ�
				$aryData["lngCertificateClassCode_DIS"] = fncGetMasterValue( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $_POST["lngCertificateClassCode"], '', $objDB);

				// �Ǹ���
				$aryData["lngCopyrightCode_DIS"] = fncGetMasterValue( "m_copyright", "lngcopyrightcode", "strcopyrightname", $_POST["lngCopyrightCode"], '', $objDB);

				// ����ͽ���
				$aryData["lngProductionUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngProductionUnitCode"], '', $objDB);

				// ���Ǽ�ʿ�
				$aryData["lngFirstDeliveryUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngFirstDeliveryUnitCode"], '', $objDB);

				// ���ʹԾ���
				$aryData["lngGoodsPlanProgressCode_DIS"] = fncGetMasterValue( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname" ,$_POST["lngGoodsPlanProgressCode"], '',  $objDB );

				// 2004/03/19 watanabe update �����ɢ�̾�Τ����ƽ������롣�����ɤ��ʤ�����[]��ɽ�����ʤ���ɬ�ܹ��ܤ����ơ�����������
				$aryData["strCustomerUserCode_DISCODE"] = ( $aryData["strCustomerUserCode"] != "" ) ? "[".$aryData["strCustomerUserCode"]."]" : "";
				$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
				$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
				$aryData["lngCompanyCode_DISCODE"] = ( $aryData["lngCompanyCode"] != "" ) ? "[".$aryData["lngCompanyCode"]."]" : "";
				$aryData["lngFactoryCode_DISCODE"] = ( $aryData["lngFactoryCode"] != "" ) ? "[".$aryData["lngFactoryCode"]."]" : "";
				$aryData["lngAssemblyFactoryCode_DISCODE"] = ( $aryData["lngAssemblyFactoryCode"] != "" ) ? "[".$aryData["lngAssemblyFactoryCode"]."]" : "";
				$aryData["lngDeliveryPlaceCode_DISCODE"] = ( $aryData["lngDeliveryPlaceCode"] != "" ) ? "[".$aryData["lngDeliveryPlaceCode"]."]" : "";
				// watanabe update end

				$aryData["strMonetaryrate"] = DEF_EN_MARK; //�̲ߥޡ���




				//---------------------------------------------
				// ��ǧ�롼��
				//---------------------------------------------
				if ( $aryData["lngWorkflowOrderCode"] != "" and $aryData["lngWorkflowOrderCode"] != 0 )
				{
					$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $_POST["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB );

					$aryData["strWorkflowMessage_visibility"] = "block;";
				}
				else
				{
					$aryData["strWorkflowOrderName"] = "��ǧ�ʤ�";

					$aryData["strWorkflowMessage_visibility"] = "none;";
				}





				$aryData["strActionURL"] = "/p/regist/renew3.php?strSessionID=".$aryData["strSessionID"];

				$aryData["RENEW"] = TRUE;
				// submit�ؿ�

				// ���;ܺ�HIDDEN�ѡ�HIDDEN�������ि���;ʬ�ʥ����ʤɤ��������
				if( strcmp( $aryData["strSpecificationDetails"], "") != 0 ) {
					$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
					$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
				}


				$objDB->close();
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "p/confirm/parts.tmpl" );
				// �ƥ�ץ졼������
				$objTemplate->replace( $aryData );
				$objTemplate->complete();

//fncDebug("renew2.txt", $objTemplate->strTemplate, __FILE__, __LINE__ );

				// HTML����
				echo $objTemplate->strTemplate;
				return true;

			}
			else
			// ���顼����ä����
			// ��ǧ���̤ǤϹ��ܤ�ɽ�����ʤ��Ȥλ����ä����ѹ��ˤʤ�ޤ���������displaycode��code���Ѵ���������ä��ΤǤ�����Ʊ������񤭤ޤ���
			{
				// ���¥��롼�ץ����ɤμ���
				$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

				// ��ǧ�롼�Ȥ�����
				// �֥ޥ͡����㡼�װʾ�ξ��
				if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
				{
					$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
				}
				else
				{
					$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngInputUserCode , $objDB , '');
				}


				// ���ץ�����ͤ�����
				// ���ƥ��꡼
				$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$objAuth->UserCode)), $aryData["lngCategoryCode"], $objDB);
				// �ٻ�ñ��
				$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], "WHERE bytpackingconversionflag=true", $objDB);
				// ����ñ��
				$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductunitCode"], "WHERE bytproductconversionflag=true", $objDB);
				// ����ͽ�����ñ��
				$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
				// ���Ǽ�ʿ���ñ��
				$aryData["lngFirstDeliveryUnitCode"]	= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryunitCode"], '', $objDB);
				// �о�ǯ��
				$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
				// �ڻ� �ơ��֥�ʤ�
				$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
				// �Ǹ���
				$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
				// ���ʷ��� �ơ��֥�ʤ�
				$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", $aryData["lngProductFormCode"], '', $objDB);
				// ���ʹԾ��� 
				$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lngGoodsPlanProgressCode"], '', $objDB);

				// ����
				if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
				{
					$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
					$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
				}



				//-------------------------------------------------------------------------
				// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
				//-------------------------------------------------------------------------
				$lngProductStatusCode = fncCheckNullStatus( $lngProductStatusCode );


				//---------------------------------------------
				// ���֤μ���
				//---------------------------------------------
				$aryData["strProductStatusCodeDisplay"] = fncGetMasterValue( "m_productstatus", "lngproductstatuscode", "strproductstatusname", $lngProductStatusCode, '', $objDB );




				$aryData["strProcess"] = "check";
				$aryData["RENEW"] = TRUE;


				// submit�ؿ�
				$aryData["lngRegistConfirm"] = 0;

				echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData, $objAuth );
				$objDB->close();
				return true;
			}
		}
	}


	//���顼�����ä����:�����פ���ä���� ==========================================================================
	if($_POST["back"] == "true" )
	{
		/**
			���;ܺٲ����ե�����HIDDEN����
		*/
		if( $aryData["uploadimages"] )
		{
			for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
			{
				$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
			}

			// �Ƽ����Ѥ�����
			$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
			$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
		}
		else
		{
			$aryData["re_uploadimages"]	= "";
			$aryData["re_editordir"]	= "";
		}



		// ���¥��롼�ץ����ɤμ���
		$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

		// ��ǧ�롼�Ȥ�����
		// �֥ޥ͡����㡼�װʾ�ξ��
		if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
		{
			$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
		}
		else
		{
			$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngInputUserCode , $objDB , '');
		}



		// ����Υ�����
		$aryData["lngInChargeGroupCode"]		= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode", $aryData["lngInChargeGroupCode"], 'bytGroupDisplayFlag=true', $objDB);
		// ô���ԤΥ�����
		$aryData["lngInChargeUserCode"]			= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $aryData["lngInChargeUserCode"],'', $objDB);
		// �ܵ�
		$aryData["lngCompanyCode"]				= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngCompanyCode"], '',$objDB);
		//�������쥳����
		$aryData["lngFactoryCode"]				= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngFactoryCode"],'',$objDB);
		//���å���֥깩�쥳����
		$aryData["lngAssemblyFactoryCode"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngAssemblyFactoryCode"],'',$objDB);
		//Ǽ�ʾ�ꥳ����
		$aryData["lngDeliveryPlaceCode"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode" ,$aryData["lngDeliveryPlaceCode"],'',$objDB);
		// �ܵ�ô����
		$lngCustomerUserCode = $aryData["lngCustomerUserCode"];

		if( strcmp( $aryData["lngCustomerUserCode"], "" ) != 0)
		{

			$aryData["strCustomerUserCode"]		= fncGetMasterValue(m_user ,lngusercode, struserdisplaycode, $aryData["strCustomerUserCode"],'',$objDB);

		}

		// ����
		if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
		{
			$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
			$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
		}

		// ���ץ�����ͤ�����
		// ���ƥ��꡼
		$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$objAuth->UserCode)), $aryData["lngcategorycode"], $objDB);
		// �ٻ�ñ��
		$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], "WHERE bytpackingconversionflag=true", $objDB);
		// ����ñ��
		$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductunitCode"], "WHERE bytproductconversionflag=true", $objDB);
		// ����ͽ�����ñ��
		$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
		// ���Ǽ�ʿ���ñ��
		$aryData["lngFirstDeliveryUnitCode"]	= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryunitCode"], '', $objDB);
		// �о�ǯ��
		$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
		// �ڻ� �ơ��֥�ʤ�
		$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
		// �Ǹ���
		$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
		// ���ʷ��� �ơ��֥�ʤ�
		$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", $aryData["lngProductFormCode"], '', $objDB);
		// ���ʹԾ��� 
		$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lngGoodsPlanProgressCode"], '', $objDB);




		//-------------------------------------------------------------------------
		// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
		//-------------------------------------------------------------------------
		$lngProductStatusCode = fncCheckNullStatus( $lngProductStatusCode );


		//---------------------------------------------
		// ���֤μ���
		//---------------------------------------------
		$aryData["strProductStatusCodeDisplay"] = fncGetMasterValue( "m_productstatus", "lngproductstatuscode", "strproductstatusname", $lngProductStatusCode, '', $objDB );



		// �ե�����URL
		if(strcmp($aryData["strurl"], "") == 0)
		{
			$aryResult["strurl"] = "renew2.php";
		}

		$aryData["strProcess"] = "check";
		$aryData["RENEW"] = TRUE;

		// submit�ؿ�
		$aryData["lngRegistConfirm"] = 0;

		echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData, $objAuth );
		$objDB->close();
		return true;
	}






	// ���¥��롼�ץ�����(�桼�����ʲ�)�����å�
	$blnAG = fncCheckUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

	// �֥桼�����װʲ��ξ��
	if( $blnAG )
	{
		// ��ǧ�롼��¸�ߥ����å�
		$blnWF = fncCheckWorkFlowRoot( $lngInputUserCode, $aryData["strSessionID"], $objDB );

		// ��ǧ�롼�Ȥ�¸�ߤ��ʤ����
		if( !$blnWF )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}
	}





	//�ǽ�β��� ========================================================================================================







	$strProductCode = $_GET['strProductCode']; 


	$aryQuery = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngproductno, ";
	$aryQuery[] = "strProductCode, ";																		//2:���ʥ�����
	$aryQuery[] = "strProductName, ";																		//3:����̾��
	$aryQuery[] = "strProductEnglishName, ";	 															//4:����̾��(�Ѹ�)
	$aryQuery[] = "lngInChargeGroupCode, ";																	//5:����
	$aryQuery[] = "lngInChargeUserCode, ";																	//6:ô����
	$aryQuery[] = "lnginputusercode, ";																		//7:���ϼ�
																											//8:�ܵҼ��̥�����(ɽ���Τ�)
	$aryQuery[] = "strGoodsCode, ";																			//9:���ʥ�����
	$aryQuery[] = "strGoodsName, ";																			//10:����̾��
	$aryQuery[] = "lngCustomerCompanyCode, ";																//11:�ܵ�
	//$aryQuery[] = "lngcustomergroupcode, ";																//12:�ܵ�����(NULL)
	$aryQuery[] = "lngCustomerUserCode, ";																	//13:�ܵ�ô���ԥ����� (NULL)
	$aryQuery[] = "strCustomerUserName, ";																	//14:�ܵ�ô����()
	$aryQuery[] = "lngPackingUnitCode, ";																	//15:�ٻ�ñ��(int2)
	$aryQuery[] = "lngProductUnitCode, ";																	//16:����ñ��(int2)
	$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, ";						//17:��Ȣ���ޡ�����(int4)
	$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, ";				//18:�����ȥ�����(int4)
	$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, ";		//19:����ͽ���()
	$aryQuery[] = "lngProductionUnitCode, ";																//20:����ͽ�����ñ��()
	$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, ";	//21:���Ǽ�ʿ�(int4)
	$aryQuery[] = "lngFirstDeliveryUnitCode, ";																//22:���Ǽ�ʿ���ñ��()
	$aryQuery[] = "lngFactoryCode, ";																		//23:��������()
	$aryQuery[] = "lngAssemblyFactoryCode, ";	 															//24:���å���֥깩��()
	$aryQuery[] = "lngDeliveryPlaceCode, ";																	//25:Ǽ�ʾ��(int2)
	$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, ";						//26:Ǽ�ʴ�����()
	$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, ";				//27:����()
	$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,";					//28:����()
	$aryQuery[] = "lngTargetAgeCode, ";																		//29:�о�ǯ��()
	$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,";										//30:�����ƥ���()
	$aryQuery[] = "lngCertificateClassCode, "; 																//31:�ڻ�()
	$aryQuery[] = "lngCopyrightCode, ";																		//32:�Ǹ���()
	$aryQuery[] = "strCopyrightDisplayStamp, ";																//33:�Ǹ�ɽ��(���)
	$aryQuery[] = "strCopyrightDisplayPrint, ";																//34:�Ǹ�ɽ��(����ʪ)
	$aryQuery[] = "lngProductFormCode, ";																	//35:���ʷ���()
	$aryQuery[] = "strProductComposition, ";																//36:���ʹ���()
	$aryQuery[] = "strAssemblyContents, "; 																	//37:���å���֥�����()
	$aryQuery[] = "strSpecificationDetails, "; 																//38:���;ܺ�()
	$aryQuery[] = "strNote, ";																				//39:����
	//$aryQuery[] = "bytinvalidflag, ";																		//40:̵���ե饰
	$aryQuery[] = "To_char(dtmInsertDate,'YYYY/MM/DD HH24:MI') as dtmInsertDate, ";							//41:��Ͽ��
	//$aryQuery[] = "dtmUpdateDate ";																		//42:������
	$aryQuery[] = "strcopyrightnote, ";																		//43:�Ǹ�������
	$aryQuery[] = "lngProductStatusCode, ";																	// ���ʾ���
	$aryQuery[] = "lngCategoryCode ";																		// ���ƥ��꡼

	$aryQuery[] = "FROM m_product ";
	$aryQuery[] = "WHERE  bytinvalidflag = false AND ";
	$aryQuery[] = "strProductCode = '$strProductCode'";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);

	//echo "$strQuery<br><br>";

	$objDB->freeResult( $lngResultID );
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;

	}

	if ( !$lngResultNum = pg_Num_Rows( $lngResultID ) )
	{
		fncOutputError ( 303, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;
	}

	$aryResult = array();
	$aryResult = $objDB->fetchArray( $lngResultID, 0 );





	//-------------------------------------------------------------------------
	// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
	//-------------------------------------------------------------------------
	$strFncFlag = "P";
	$blnCheck = fncCheckInChargeProduct( $aryResult["lngproductno"], $lngInputUserCode, $strFncFlag, $objDB );

	// �桼�������о����ʤ�°���Ƥ��ʤ����
	if( !$blnCheck )
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}





	//�����ɤ����ͤ򻲾�

	// ����Υ�����
	$lngInchargeGroupCode					= $aryResult["lnginchargegroupcode"];
	if ( $lngInchargeGroupCode )
	{
		$aryResult["lnginchargegroupcode"]		= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode", $lngInchargeGroupCode, 'bytGroupDisplayFlag=true', $objDB);
		// �����̾�� 
		$aryResult["strinchargegroupname"]		= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplayname", $lngInchargeGroupCode, "bytgroupdisplayflag=true", $objDB);
	}

	// ô���ԤΥ�����
	$lngUserCode = $aryResult["lnginchargeusercode"];

	if ( $lngUserCode )
	{
		$aryResult["lnginchargeusercode"]		= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $lngUserCode,'', $objDB);
		// ô���Ԥ�̾�� 
		$aryResult["strinchargeusername"]		= fncGetMasterValue( "m_user", "lngusercode", "struserdisplayname", $lngUserCode,'', $objDB);
	}
	// �ܵҤ�̾�Υ�����
	$lngCustomerCompanyCode					= $aryResult["lngcustomercompanycode"];
	if ( $lngCustomerCompanyCode ) 
	{
		$aryResult["lngCompanyCode"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngCustomerCompanyCode, '',$objDB);
		// �ܵҤ�̾��
		$aryResult["strCustomerName"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngCustomerCompanyCode, '',$objDB);
		// :�ܵҼ��̥�����
		$aryResult["strCustomerDistinctCode"]	= fncGetMasterValue( "m_company", "lngcompanycode", "strdistinctcode", $aryResult["lngcustomercompanycode"], '',$objDB);
	}

	//�������쥳����
	$lngFactoryCode							= $aryResult["lngfactorycode"];
	if ( $lngFactoryCode )
	{
		$aryResult["lngfactorycode"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngFactoryCode,'',$objDB);
		//Ǽ�ʾ���̾��
		$aryResult["strFactoryName"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngFactoryCode,'',$objDB);
	}

	//���å���֥깩�쥳����
	$lngAssemblyFactoryCode					= $aryResult["lngassemblyfactorycode"];
	if ( $lngAssemblyFactoryCode ) 
	{
		$aryResult["lngassemblyfactorycode"]	= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngAssemblyFactoryCode,'',$objDB);
		//���å���֥깩��
		$aryResult["strAssemblyFactoryName"]	= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngAssemblyFactoryCode,'',$objDB);	}

	//Ǽ�ʾ�ꥳ����
	$lngDeliveryPlaceCode					= $aryResult["lngdeliveryplacecode"];
	if ( $lngDeliveryPlaceCode )
	{
		$aryResult["lngdeliveryplacecode"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngDeliveryPlaceCode,'',$objDB);
		//Ǽ�ʾ��
		$aryResult["strDeliveryPlaceName"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngDeliveryPlaceCode,'',$objDB);
	}

	// �ܵ�ô����
	$lngCustomerUserCode = $aryResult["lngcustomerusercode"];

	if( strcmp( $aryResult["lngcustomerusercode"], "" ) != 0)
	{
		$aryResult["strcustomerusercode"]	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $lngCustomerUserCode, '', $objDB);
		$aryResult["strcustomerusername"]	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplayname", $lngCustomerUserCode,'',$objDB);
	}

	// ���;ܺ٤��ü�ʸ���Ѵ�
	$aryResult["strspecificationdetails"] = fncHTMLSpecialChars( $aryResult["strspecificationdetails"] );


	//���ץ�����ͤ����� ==============================================================
	// Ϣ������Υ���ǥå����ˤϡ���ʸ���ǻ��ꤷ�ʤ��Ȥ���
	
	// ���ƥ��꡼
	$aryResult["lngcategorycode"]			= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$objAuth->UserCode)), $aryResult["lngcategorycode"], $objDB);
	// �ٻ�ñ��
	$aryResult["lngpackingunitcode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngpackingunitcode"], "WHERE bytpackingconversionflag=true", $objDB);
	// ����ñ��
	$aryResult["lngproductunitcode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductunitcode"], "WHERE bytproductconversionflag=true", $objDB);
	// ����ͽ�����ñ��
	$aryResult["lngproductionunitcode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductionunitcode"], '', $objDB);
	// ���Ǽ�ʿ���ñ��
	$aryResult["lngfirstdeliveryunitcode"]	= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngfirstdeliveryunitcode"], '', $objDB);
	// �о�ǯ��
	$aryResult["lngtargetagecode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryResult["lngtargetagecode"], '', $objDB);
	// �ڻ� �ơ��֥�ʤ�
	$aryResult["lngcertificateclasscode"]	= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryResult["lngcertificateclasscode"], '', $objDB);
	// �Ǹ���
	$aryResult["lngcopyrightcode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryResult["lngcopyrightcode"], '', $objDB);
	// ���ʷ��� �ơ��֥�ʤ�
	$aryResult["lngproductformcode"]		= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", $aryResult["lngproductformcode"], '', $objDB);


//fncDebug("p_renew_category.txt", $aryResult["lngCategoryCode"], __FILE__, __LINE__ );

	// ���ʹԾ��� ===================================================================
	$lngproductno = $aryResult["lngproductno"];
	$aryQuery2[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
	$aryQuery2[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate "; 
	$aryQuery2[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
	$aryQuery2[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
	$aryQuery2[] = "$lngproductno )";


	$strQuery2 = "";
	$strQuery2 = implode("\n", $aryQuery2);


	//echo "$strQuery2<br><br>";
	$objDB->freeResult( $lngResultID2 );
	if ( !$lngResultID2 = $objDB->execute( $strQuery2 ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;

	}

	if ( !$lngResultNum = pg_Num_Rows( $lngResultID2 ) )
	{
		fncOutputError ( 303, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;
	}

	$aryResult2 = array();
	$aryResult2 = $objDB->fetchArray( $lngResultID2, 0 );



	// ���ʹԾ��� =============================================================
	$aryResult["lngGoodsPlanProgressCode"]	= fncGetPulldown(m_goodsplanprogress, lnggoodsplanprogresscode, strgoodsplanprogressname, $aryResult2["lnggoodsplanprogresscode"], '', $objDB);
	//�����ֹ�
	$aryResult["lngRevisionNo"]				= $aryResult2["lngrevisionno"];
	//��������
	$aryResult["dtmRevisionData"]			= $aryResult2["dtmrevisiondate"];
	//goodsplancode
	$aryResult["lnGgoodsPlanCode"]			= $aryResult2["lnggoodsplancode"];
	$aryResult["strProcess"]				= "check";



//var_dump( $aryResult["lngproductstatuscode"] );exit();

	//-------------------------------------------------------------------------
	// ���ʾ��֤Υ����å�
	//-------------------------------------------------------------------------
	// ������ξ��
	if( $aryResult["lngproductstatuscode"] == DEF_PRODUCT_APPLICATE )
	{
		fncOutputError( 307, DEF_WARNING, "", TRUE, "", $objDB );
	}




	// ��ǧ�롼�Ȥμ���
	$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryResult["lngproductno"].":str", '', $objDB );


	// ���¥��롼�ץ����ɤμ���
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

	// ��ǧ�롼�Ȥ�����
	// �֥ޥ͡����㡼�װʾ�ξ��
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryResult["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
	}
	else
	{
		$aryResult["lngWorkflowOrderCode"] = fncWorkFlow( $lngInputUserCode , $objDB , $lngWorkflowOrderCode );
	}

	//-------------------------------------------------------------------------
	// ���᡼���ե�����μ�������
	//-------------------------------------------------------------------------

	$objImageLo = new clsImageLo();
	$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");
	// ���������ɡ����ʥ����ɡˤ��ˤ��ơ����᡼���ե��������н����ʴ�Ϣ�������ƥ�ݥ��ǥ��쥯�ȥ�˽��Ϥ�����
	$objImageLo->getImageLo($objDB, $strProductCode, $strDestPath, $aryImageInfo);




	// �ե�����URL
	if( strcmp( $aryData["strurl"], "" ) == 0 )
	{
		$aryResult["strurl"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';
	}

	$aryResult["strActionURL"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';

	$aryResult["strSessionID"] = $aryData["strSessionID"];
	$aryResult["RENEW"] = TRUE;

	// submit�ؿ�
	$aryResult["lngRegistConfirm"] = 0;

	// �إ���б�
	$aryResult["lngFunctionCode"] = DEF_FUNCTION_P6;



/**
	debug

	���;ܺٲ����ե�����HIDDEN����
*/
// �Ƽ����Ѥ�����
$lngImageCnt	= count($aryImageInfo['strTempImageFile']);

if( $lngImageCnt )
{
	for( $i = 0; $i < $lngImageCnt; $i++ )
	{
		$aryUploadImagesHidden[]	= '<input type="hidden" name="uploadimages[]" value="' .$aryImageInfo['strTempImageFile'][$i]. '" />';
	}

	// �Ƽ����Ѥ�����
	$aryResult["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
	$aryResult["re_editordir"]		= '<input type="hidden" name="strTempImageDir" value="' .$aryImageInfo['strTempImageDir'][0]. '" />';
}

// debug file����
//fncDebug("p_renew.txt", fncGetReplacedHtml( "p/regist/parts.tmpl", $aryResult, $objAuth ), __FILE__, __LINE__ );



	echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryResult, $objAuth );

	$objDB->close();
	return true;

?>