<?php


	// -----------------------------------------------------------------
	/**
	*	dispalayname����code�����
	*	code���ʤ���displayname��¸�ߤ����
	*	
	*	@param  Long	$lngDisplayCode		�ե���������Ϥ��줿�͡ʥ����ɡ�
	*	@param  String	$strDisplayName 	�ե���������Ϥ��줿�͡�̾����
	*	@param  String	$strTable			�ơ��֥�
	*	@param	String	$strCodeColumn		�������������̾�ʥ����ɡ�
	*	@param	String	$strWhereColumn		$strDisplayName����Ӥ��륫���
	*	@param	String	$strQueryWhere		���
	*	@param  Object	$objDB				DB���֥�������
	*	@return $aryTrueCode[0]				DB���code
	*/
	// -----------------------------------------------------------------
	function fncGetCode( $lngDisplayCode , $strDisplayName ,$strTable, $strCodeColumn, $strWhereColumn, $strQueryWhere, $objDB )
	{
	// echo "DisplayCode : $lngDisplayCode<br>";
	// echo "DisplayName : $strDisplayName<br>";
		if( strcmp( $lngDisplayCode, "") == "" && strcmp( $strDisplayName, "") != "" )
		{
			if( $strCodeColumn == "u.lngusercode" )
			{
				$strQuery = "SELECT DISTINCT($strCodeColumn) FROM $strTable WHERE (u.lngusercode = p.lngcustomerusercode AND u.struserdisplayname ~* '$strDisplayName')  OR (p.strcustomerusername ~*  '$strDisplayName') AND p.lngcustomerusercode != null";
			}
			else
			{
				$strQuery = "SELECT $strCodeColumn FROM $strTable WHERE $strWhereColumn"."name"." ~* '$strDisplayName' $strQueryWhere";
			}
		}
		else
		{
			$strQuery = "SELECT $strCodeColumn FROM $strTable WHERE $strWhereColumn"."code"." = '$lngDisplayCode' $strQueryWhere";
		}
		// echo "strQuery : $strQuery<br>";
		
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		
		if ( !$lngResultNum )
		{
			return FALSE;
		}
		
		// �ͤ�Ȥ� =====================================
		if( $lngResultNum = pg_num_rows ( $lngResultID ) )
		{
			for( $i = 0; $i < $lngResultNum; $i++ )
			{
				$aryResut[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
			}
		}
		return $aryResut;
	}
	
	
	
	
	// -----------------------------------------------------------------
	/**
	*
	*	SQL������( SELECT�� )
	*
	*
	*	@param Array	$aryQueryName				// 
	*	@param Array	$aryGoods_column			// 
	*	@return 		$aryNewQueryData			// DB���code
	*/
	// -----------------------------------------------------------------

	// strProductCode �� p.strProductCode
	function fncChangeName( $aryQueryName ,$aryGoods_column) 
	{

		// ���շ���Ŭ�Ѥ������
		$aryToDate[0] = "dtmInsertDate";
		
		// ���ͷ�(000,000)��Ŭ�Ѥ������
		$aryToNumber[0] = "lngBoxQuantity";
		$aryToNumber[1] = "lngCartonQuantity";
		$aryToNumber[2] = "lngProductionQuantity";
		$aryToNumber[3] = "lngFirstDeliveryQuantity";
		$aryToNumber[4] = "curProductPrice";
		$aryToNumber[5] = "curRetailPrice";
		$aryToNumber[6] = "lngRoyalty";
		
		// 
		
		
		if( is_array( $aryQueryName ) )
		{
		$aryNewQueryData[] = "p.bytinvalidflag, ";
		$aryNewQueryData[] = "p.lnginchargegroupcode as groupcode, ";
		
			for( $i = 0; $i < count( $aryQueryName ); $i++ )
			{
				list ( $strKeys, $strValues ) = each ( $aryQueryName );
				
				//echo "$strKeys ::: $strValues<br>";
				reset( $aryGoods_column );
				for ( $j = 0; $j < count( $aryGoods_column ); $j++ )
				{	
					//echo "aryGoods_column === ".$aryGoods_column[$j]. "<br>";
					if ( $strValues == $aryGoods_column[$j] )
					{
						if( $strValues == "dtmRevisionDate" )
						{
							$aryNewQueryData[] = "To_Date(g.$strValues, 'YYYY/MM/DD') as $strValues,";
						}
						else
						{
							$aryNewQueryData[] = "g.".$strValues .",";
							
						}
						break;
					}
				}
				
				if( $j == count( $aryGoods_column ) )
				{
					for( $n = 0; $n < count( $aryToDate ); $n++ )
					{
						if( $strValues == $aryToDate[$n])
						{
							$aryNewQueryData[] = "To_Date(p.$strValues, 'YYYY/MM/DD') as $strValues,";
							break;
						}
					}
					reset ($aryToNumber);
					for( $h = 0; $h < count( $aryToNumber ); $h++ )
					{
						if( $strValues == $aryToNumber[$h])
						{
							if( $strValues == "curProductPrice" || $strValues == "curRetailPrice" )
							{
								$aryNewQueryData[] = "To_char(p.$strValues,'9,999,999,990.99') as $strValues," ;
								break;
							}
							elseif( $strValues == "lngRoyalty" )
							{
								$aryNewQueryData[] = "To_char(p.$strValues,'99,990.99') as $strValues," ;
								break;
							}
							else
							{
								$aryNewQueryData[] = "To_char(p.$strValues,'9,999,999,999') as $strValues," ;
								break;
							}
						}
					}
					
					if( $n == count( $aryToDate ) && $h == count( $aryToNumber ) )
					{
						$aryNewQueryData[] = "p.".$strValues.",";
					}
				}
				
			}
		}
		
			return $aryNewQueryData;
	}
	
	
	
	
	
	
	
	
	
	// -----------------------------------------------------------------
	/**				fncQueryWhere()�ؿ�
	*
	*		SQL��WHERE�����������
	*
	*
	*		@param Array	$arySearchColumn		�ָ����ץ����å����줿���
	*		@param Array	$aryData				�ݥ��Ȥ��ϤäƤ�����
	*		@param Array	$aryDisplayCode			�����ɤ���̾�Τ������������
	*		@param Object	$objDB					DB���֥�������
	*		@param String	$flgP3					���¥ե饰�ʺ���Ѥߥǡ�����ɽ�����뤫��
	*		@return	Array	$aryQueryWhere
	*/
	// -----------------------------------------------------------------
	
	// �ؿ��������������� ==========================================================
	function fncQueryWhere( $arySearchColumn, $aryData , $aryDisplayCode, $flgP3, $objDB)
	{
//print_r($arySearchColumn);
		// 0:���祳����
		// 1:ô����
		// 2:�ܵ�
		// 3:��������
		// 4:���å���֥깩��
		// 5:Ǽ�ʾ��
		// 6:�ܵ�ô����
		// 7:���ϼ�

		
		// displaycode��code���Ѵ�
		// displaycode�����äƤ��ʤ�displayname�����äƤ������displayname����displaycode������Ƥ���
		if( array_search( 'p.lngInChargeGroupCode,', $arySearchColumn ) )
		{
			$aryTrueCode[0] = fncGetCode( $aryData["lngInChargeGroupCode"], $aryData["strInChargeGroupName"],m_group, lnggroupcode, strgroupdisplay, ' and bytgroupdisplayflag = true',$objDB );
		}
		if( array_search( 'p.lngInChargeUserCode,', $arySearchColumn ) )
		{
			$aryTrueCode[1] = fncGetCode( $aryData["lngInChargeUserCode"], $aryData["strInChargeUserName"], m_user, lngusercode, struserdisplay,'', $objDB);
		}
		if( array_search( 'p.lngCustomerCompanyCode,', $arySearchColumn ) )
		{
			$aryTrueCode[2] = fncGetCode( $aryData["lngCustomerCompanyCode"], $aryData["strCustomerCompanyName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		}
		if( array_search( 'p.lngFactoryCode,', $arySearchColumn ) )
		{
			$aryTrueCode[3] = fncGetCode( $aryData["lngFactoryCode"], $aryData["strFactoryName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		}
		if( array_search( 'p.lngAssemblyFactoryCode,', $arySearchColumn ) )
		{
			$aryTrueCode[4] = fncGetCode( $aryData["lngAssemblyFactoryCode"], $aryData["AssemblyFactoryName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		}
		if( array_search( 'p.lngDeliveryPlaceCode,', $arySearchColumn ) )
		{
			$aryTrueCode[5] = fncGetCode( $aryData["lngDeliveryPlaceCode"], $aryData["DeliveryPlaceName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		}

		//$str = array_search("p.lngCustomerUserCode", $arySearchColumn);
		//echo "$str";
		if( array_search( 'p.lngCustomerUserCode,', $arySearchColumn ) )
		{ 
			// code�����äƤ����code�Ǹ���name�����äƤ�����ϴ�¸�ؿ����Ȥ��ʤ��Τ�ʬ���Ƥޤ�
			if(strcmp( $aryData["lngCustomerUserCode"],"") != 0)
			{ 
				$aryTrueCode[6] = fncGetCode( $aryData["lngCustomerUserCode"], '',m_user ,lngusercode, struserdisplay, '',$objDB);
			}
			// name�����äƤ�����ϥ�졼����󤹤�Τ�fncGetCode���Ϥ��������Ѥ��
			elseif( strcmp( $aryData["strCustomerUserName"],"") != 0)
			{
				$aryTrueCode[6] = fncGetCode( $aryData["lngCustomerUserCode"], $aryData["strCustomerUserName"], "m_user u, m_product p" ,"u.lngusercode", '','', $objDB );
			}
		}
		if( array_search( 'g.lngInputUserCode,', $arySearchColumn ) )
		{
			$aryTrueCode[1] = fncGetCode( $aryData["lngInputUserCode"], $aryData["strInputUserName"], m_user, lngusercode, struserdisplay,'', $objDB);
		}

		// from to 
		//strProductCode�ξ���ʸ�����ʤΤ�lngproductno�Ǹ������о�ʸ�������ͤˤ���
		$aryFromTo[0]		= "dtmInsertDateFrom";
		$aryFromTo[1]		= "dtmInsertDateTo";
		$aryFromTo[2]		= "strProductCodeFrom";
		$aryFromTo[3]		= "strProductCodeTo";
		
		$aryFromToValue[0]	= "p.dtmInsertDate >= To_Date('";
		$aryFromToValue[1]	= "p.dtmInsertDate <= To_Date('";
		$aryFromToValue[2]	= "p.lngproductno >= ";
		$aryFromToValue[3]	= "p.lngproductno <= ";
		
		$aryFromToValueEnd[0]	= "', 'YYYY-MM-DD')";
		$aryFromToValueEnd[1]	= "', 'YYYY-MM-DD')";
		$aryFromToValueEnd[2]	= "";
		$aryFromToValueEnd[3]	= "";


		for ( $i = 0 ; $i < count( $arySearchColumn ) ; $i++)
		{
			list ( $strKeys, $strValues ) = each ( $arySearchColumn );				//�����¹Ԥ������å����줿����
			// echo "$strKeys ======= $strValues<br>";
			// fncChangeName���Ѵ����줿�ͤ�⤦�����ᤷ$_POST�����
			// ���p.strProductCode �� strProductCode
			// dtmInsertDate�������ս�����To_char�����ˤ���Ƥ���ΤǺ�����Ƥ������
			$strValues			= preg_replace ( "/,$/", "", $strValues );
			$strValues_replace	= preg_replace ( "/^[pg]+\./", "", $strValues );
			$strValues_replace = preg_replace ("/.+?dtmInsertDate$/", "dtmInsertDate", $strValues_replace );
			$strValues_replace = preg_replace ("/.+?dtmRevisionDate$/",dtmRevisionDate,$strValues_replace );
			
			//echo "value_replace : $strValues_replace<br>";
			reset( $aryData );
			for ( $j = 0; $j < count( $aryData ); $j++ )							//FORM���Ƥ���
			{
				list ($strKeys2, $strValues2 ) = each ( $aryData );
				$strKeys2_replace = preg_replace ("/(From|To)$/","", $strKeys2);
				
				// echo "$strKeys2 ===  $strValues2<br>";
				//echo "strValues_replace : $strValues_replace<br>";
				//echo "strKey2_replace : $strKeys2_replace<br>";
				//echo "strKeys2 : $strKeys2<br><br>";

				if( $strValues_replace == $strKeys2_replace )
				{
					if( strcmp( $aryData[$strValues_replace],"" ) == 0)
					{
						//fncOutputError( 302, DEF_WARNING, "�������ܥ����å��˸�꤬����ޤ�",TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
					}
					
					// displaycode �� code
					reset($aryDisplayCode);
					for ( $n = 0; $n < count( $aryDisplayCode ); $n++ )				// dispalaycode �� code
					{
						if( $strValues_replace == $aryDisplayCode[$n] )
						{
						
						//echo "count : $n :".count($aryTrueCode[$n])."<br>";
						//echo "tureCode".$aryTrueCode[$n];
							if( count( $aryTrueCode[$n] ) <= 1)						// TrueCode���ͤ�ʣ�����äƤ��뤫��
							{
								list ($strKeys4, $strValues4 ) = each ($aryTrueCode[$n][0]);
								
								$aryQueryWhere[] = "$strValues = $strValues4 AND";
								$strLoopFlag = "true";
								//break;
							}
							else
							{
								//echo "count :".count( $aryTrueCode[$n]);
								for( $t = 0; $t < count( $aryTrueCode[$n] ); $t++ )
								{
									//echo "t: $t<br>";
									list($strKeys3, $strValues3) = each ($aryTrueCode[$n][$t]);
									$strInArray .=  $strValues3.",";
								}
								
								$strInArray = preg_replace("/,$/", "",$strInArray );
								$aryQueryWhere[] = "$strValues in ($strInArray) AND";
								$strLoopFlag = "true";
								//break;
							}
							
						}
					}
					
					// echo "strKeys2 : $strKeys2<br>";
					reset($aryFromTo);
					for( $h = 0; $h < count($aryFromTo); $h++ )						// From to
					{
						if( $strKeys2 == $aryFromTo[$h] )
						{
							if( strcmp( $strValues2, "") != 0 )
							{
								$strValues2 = preg_replace("/^0/","", $strValues2);		//ʸ���󢪿��͡�0685��685)
								$aryQueryWhere[] =  $aryFromToValue[$h].$strValues2.$aryFromToValueEnd[$h] ;
								$aryQueryWhere[] = " AND";
							}
								break;
						}
					}
					
					
					
					if($h == count( $aryFromTo ) )
					{
						// ʸ����
						if( preg_replace( "/^str/", $strValues_replace ) )
						{
							$aryQueryWhere[] = " $strValues ~* '$strValues2' AND";
						}
						// ���շ�
						elseif ( preg_replace( "/^dtm/", $strValues_replace ) )
						{
							if($strValues_replace == dtmRevisionDate)
							{
								$aryQueryWhere[] = "to_char(g.dtmRevisionDate, 'YYYY/MM/DD') = '". $strValues2 ."' AND";
							}
							else
							{
								$aryQueryWhere[] = " $strValues = To_Date('". $strValues2 ."', 'YYYY-MM-DD') AND";
							}
						}
						// ����¾
						elseif( $strLoopFlag == "true")
						{
						// ���⤷�ʤ�
						}
						else
						{
						
							$aryQueryWhere[] = "$strValues = $strValues2 AND";
						}
					}
				}
			}
		}
		if( $flgP3 != TRUE )
		{
			$aryQueryWhere[] = "bytinvalidflag = false";
		}
		
		return $aryQueryWhere;
	}
	
	
	
	
	
	
	
	
	// -----------------------------------------------------------------
	/**
	*       ���Ѵ��ؿ�
	*
	*		SQL�¹Ը�ν����������ɤ���̾�Τ�
	*		SQL�¹Ը�ν��������ץ���󥳡��ɤ��饪�ץ�����ͤ�
	*		strspecificationdetails�˴ޤޤ�Ƥ�����Ԥ�<br>���Ѵ�
	*
	*		@param	Array	$aryResult			// SQL���¹Ԥ��줿���
	*		@param	Array	$aryDisplayCode		// �����ɤ���̾�Τ������������
	*		@param	Object	$objDB				// DB���֥�������
	*		@retuen	Array	$aryNewData
	*/
	// -----------------------------------------------------------------

	function fncChangeDisplayName ( $aryResult ,$aryDisplayCode, $objDB )
	{
	//print_r( $aryResult );
	//echo "<br><br>";
		// code �� displaycode���Ѵ� =================================================
		// 0:���祳����
		// 1:ô����
		// 2:�ܵ�
		// 3:��������
		// 4:���å���֥깩��
		// 5:Ǽ�ʾ��
		/*
		$aryCodeName[0] = "lnginchargegroupcode";

		if(strcmp ($aryResult["lnginchargegroupcode"], "") != 0 )
		{
			$aryDisplayName[0] = fncGetMasterValue(m_group, lnggroupcode, strgroupdisplaycode, $aryResult["lnginchargegroupcode"], "bytgroupdisplayflag=true", $objDB);
		}
		if(strcmp ($aryResult["lnginchargeusercode"], "") != 0 )
		{
			$aryDisplayName[1] = fncGetMasterValue(m_user, lngusercode, struserdisplaycode, $aryResult["lnginchargeusercode"],'', $objDB);
		}
		if(strcmp( $aryResult["lngcustomercompanycode"], "") != 0 )
		{
			$aryDisplayName[2] = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode, $aryResult["lngcustomercompanycode"], '',$objDB);
		}
		if(strcmp ($aryResult["lngfactorycode"], "") != 0)
		{
			$aryDisplayName[3] = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode ,$aryResult["lngfactorycode"],'',$objDB);
		}
		if(strcmp ($aryResult["lngassemblyfactorycode"], "") != 0)
		{
			$aryDisplayName[4] = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode ,$aryResult["lngassemblyfactorycode"],'',$objDB);
		}
		if(strcmp ($aryResult["lngdeliveryplacecode"], "") != 0)
		{
			$aryDisplayName[5] = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode ,$aryResult["lngdeliveryplacecode"],'',$objDB);
		}
		*/


		// option code �� option value ========================================
		$aryOptionCode[0] = "lngproductformcode";					// 0:���ʷ���
		$aryOptionCode[1] = "lngcertificateclasscode";			// 1:�Ǹ���
		$aryOptionCode[2] = "lngcopyrightcode";					// 2:���ʷ���
		$aryOptionCode[3] = "lngpackingunitcode";				// 3:�ٻ�ñ��
		$aryOptionCode[4] = "lngproductunitcode";				// 4:����ñ��
		$aryOptionCode[5] = "lngproductionunitcode";			// 5:����ͽ�����ñ��
		$aryOptionCode[6] = "lngfirstdeliveryunitcode";			// 6:���Ǽ�ʿ���ñ��
		$aryOptionCode[7] = "lngtargetagecode";					// 7:�о�ǯ��
		$aryOptionCode[8] = "lngcertificateclasscode";			// 8:�ڻ�
		$aryOptionCode[9] = "lngcopyrightcode";					// 9:�Ǹ���

		
	
		if( strcmp($aryResult["lngproductformcode"], "") != 0)
		{
			$aryOptionName[0] = fncGetMasterValue(m_productform,lngproductformcode,strproductformname,$aryResult["lngproductformcode"], '', $objDB);
		}

		if( strcmp( $aryResult["lngcertificateclasscode"], "" ) != 0)
		{
			$aryOptionName[1] = fncGetMasterValue( m_certificateclass, lngcertificateclasscode, strcertificateclassname, $aryResult["lngcertificateclasscode"], '', $objDB);
		}
		
		if( strcmp( $aryResult["lngcopyrightcode"], "" ) != 0)
		{
			$aryOptionName[2] = fncGetMasterValue( m_copyright, lngcopyrightcode, strcopyrightname, $aryResult["lngcopyrightcode"], '', $objDB);
		}
		if( strcmp( $aryResult["lngpackingunitcode"], "" ) != 0)
		{
			$aryOptionName[3] = fncGetMasterValue( m_productunit, lngProductUnitCode, strProductUnitName, $aryResult["lngpackingunitcode"], 'bytpackingconversionflag=true ', $objDB);
		}
		if( strcmp( $aryResult["lngproductunitcode"], "" ) != 0)
		{
			$aryOptionName[4] = fncGetMasterValue(m_productunit, lngProductUnitCode, strProductUnitName, $aryResult["lngproductunitcode"], 'bytproductconversionflag=true', $objDB);
		}
		if( strcmp( $aryResult["lngproductionunitcode"], "" ) != 0)
		{
			$aryOptionName[5] = fncGetMasterValue(m_productunit, lngProductUnitCode, strProductUnitName, $aryResult["lngproductionunitcode"], '', $objDB);
		}
		if( strcmp( $aryResult["lngfirstdeliveryunitcode"], "" ) != 0)
		{
			$aryOptionName[6] = fncGetMasterValue(m_productunit, lngProductUnitCode, strProductUnitName, $aryResult["lngfirstdeliveryunitcode"], '', $objDB);
		}
		if( strcmp( $aryResult["lngtargetagecode"], "" ) != 0)
		{
			$aryOptionName[7] = fncGetMasterValue(m_targetage, lngTargetAgeCode, strTargetAgeName, $aryResult["lngtargetagecode"], '', $objDB);
		}
		if( strcmp( $aryResult["lngcertificateclasscode"], "" ) != 0)
		{
			$aryOptionName[8] = fncGetMasterValue(m_CertificateClass, lngcertificateclasscode, strcertificateclassname, $aryResult["lngcertificateclasscode"], '', $objDB);
		}
		if( strcmp( $aryResult["lngcopyrightcode"], "" ) != 0)
		{
			$aryOptionName[9] = fncGetMasterValue(m_copyright, lngcopyrightcode, strcopyrightname, $aryResult["lngcopyrightcode"], '', $objDB);
		}

		


		for ( $i = 0; $i <count( $aryResult ); $i++)
		{
			list ($strKeys, $strValues ) = each ( $aryResult );							// SQL���¹Ԥ��줿��̤�ޤ魯
			
			// ʸ����Τ����
			if( strcmp ($strKeys, "") != 0)
			{
				
				//reset( $aryDisplayCode );
				//for($j = 0; $j < count( $aryDisplayCode ); $j++ )
				//{
					
					// if( $strKeys == strtolower( $aryDisplayCode[$j] ) )
					 //{
					 //	$aryNewData[$strKeys] = strtolower($aryDisplayName[$j]);
					 //	break;
					 //}
				//}
				
				//if($j == count( $aryDisplayCode ) )
				//{
					// option code �� option Name
					reset( $aryOptionCode );
					for( $n = 0; $n < count( $aryOptionCode ); $n++ )
					{
						if( $strKeys == $aryOptionCode[$n] )
						{
							$aryNewData[$strKeys] = $aryOptionName[$n];
							break;
						}
					}
					
					if( $n == count( $aryOptionCode ) )
					{
						if($strKeys == "strspecificationdetails")
						{
							$aryNewData[$strKeys] = nl2br( $strValues );
						}
						else
						{
							$aryNewData[$strKeys] = $strValues;
						}
					}
				//}
				
			}
		}
		return $aryNewData;
	}
	
	
	
	
	
	// HTML���� �ʲ���=============================================
	function fncCreateHtml ( $aryNewResult ,$lngSortNumber, $strSession, $renew ,$del, $lngLanguageCode, $objDB, $objCache, $aryViewButton )
	{
//echo "����ɽ�� : $renew<br>";
//echo "���ɽ�� : $del<br>";
//echo "<br>languageCode : $lngLanguageCode<br>";
//print_r($aryNewResult);
		//print_r($aryNewResult);
		// dispalaycode �� displayName���Ѵ� =================================================
		// 0:���祳����
		// 1:ô����
		// 2:�ܵ�
		// 3:��������
		// 4:���å���֥깩��
		// 5:Ǽ�ʾ��
		// 6:���ʹԾ���
		
		
		// WEBɽ���� '9002'��'[9002]����'�Ȥ�����
		$aryCodeToName[0] = "lnginchargegroupcode";
		$aryCodeToName[1] = "lnginchargeusercode";
		$aryCodeToName[2] = "lngcustomercompanycode";
		$aryCodeToName[3] = "lngfactorycode";
		$aryCodeToName[4] = "lngassemblyfactorycode";
		$aryCodeToName[5] = "lngdeliveryplacecode";
		$aryCodeToName[6] = "lnggoodsplanprogresscode";
		$aryCodeToName[7] = "lngcustomerusercode";
		$aryCodeToName[8] = "lnginputusercode";
				
		function fncCodeToDisplayName ( $lngNumber, $lngCodeValue, &$objDB, &$objCache )
		{

/////// 2003.12.18 Suzukaze Modified 
			switch ( $lngNumber )
			{
				case 0:
					$aryInfo = $objCache->GetValue("lnggroupcode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strGroupDisplayCode, strGroupDisplayName FROM m_Group WHERE lngGroupCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
							$aryInfo[1] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->strgroupdisplaycode;
							$aryInfo[1] = $objResult->strgroupdisplayname;
							$objCache->SetValue("lnggroupcode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// ���롼��̾�Τ�����
					$strCodeToName = $aryInfo[1];
					break;
				case 1:
				case 7:
				case 8:
					$aryInfo = $objCache->GetValue("lngusercode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strUserDisplayCode, strUserDisplayName FROM m_User WHERE lngUserCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
							$aryInfo[1] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->struserdisplaycode;
							$aryInfo[1] = $objResult->struserdisplayname;
							$objCache->SetValue("lngusercode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// �桼����̾�Τ�����
					$strCodeToName = $aryInfo[1];
					break;
				case 2:
				case 3:
				case 4:
				case 5:
					$aryInfo = $objCache->GetValue("lngcompanycode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strCompanyDisplayCode, strCompanyDisplayName FROM m_Company WHERE lngCompanyCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
							$aryInfo[1] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->strcompanydisplaycode;
							$aryInfo[1] = $objResult->strcompanydisplayname;
							$objCache->SetValue("lngcompanycode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// ���̾�Τ�����
					$strCodeToName = $aryInfo[1];
					break;
				case 6:
					$aryInfo = $objCache->GetValue("lnggoodsplanprogresscode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strGoodsPlanProgressName FROM m_GoodsPlanProgress WHERE lngGoodsPlanProgressCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->strgoodsplanprogressname;
							$objCache->SetValue("lnggoodsplanprogresscode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// ���ʲ����̾�Τ�����
					$strCodeToName = $aryInfo[0];
					break;
			}
			return $strCodeToName;
		}

		function fncCodeToDisplayCode ( $lngNumber, $lngCodeValue, $objDB, $objCache )
		{
			switch ( $lngNumber )
			{
				case 0:
					$aryInfo = $objCache->GetValue("lnggroupcode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strGroupDisplayCode, strGroupDisplayName FROM m_Group WHERE lngGroupCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
							$aryInfo[1] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->strgroupdisplaycode;
							$aryInfo[1] = $objResult->strgroupdisplayname;
							$objCache->SetValue("lnggroupcode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// ���롼��̾�Τ�����
					$strCodeToName = $aryInfo[0];
					break;
				case 1:
				case 7:
				case 8:
					$aryInfo = $objCache->GetValue("lngusercode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strUserDisplayCode, strUserDisplayName FROM m_User WHERE lngUserCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
							$aryInfo[1] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->struserdisplaycode;
							$aryInfo[1] = $objResult->struserdisplayname;
							$objCache->SetValue("lngusercode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// �桼����̾�Τ�����
					$strCodeToName = $aryInfo[0];
					break;
				case 2:
				case 3:
				case 4:
				case 5:
					$aryInfo = $objCache->GetValue("lngcompanycode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// ̾�Τμ���
						$strQuery = "SELECT strCompanyDisplayCode, strCompanyDisplayName FROM m_Company WHERE lngCompanyCode = " . $lngCodeValue;
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
						if ( !$lngResultNum )
						{
							$aryInfo[0] = "";
							$aryInfo[1] = "";
						}
						else
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$aryInfo[0] = $objResult->strcompanydisplaycode;
							$aryInfo[1] = $objResult->strcompanydisplayname;
							$objCache->SetValue("lngcompanycode", $lngCodeValue, $aryInfo);
						}
						$objDB->freeResult( $lngResultID );
					}
					// ���̾�Τ�����
					$strCodeToName = $aryInfo[0];
					break;
			}
			return $strCodeToName;
		}

		if( $lngLanguageCode == 1 )
		{
			// ���ܸ쥳����
			$aryWebName_Product["bytinvalidflag"]			= "�ե饰";
			$aryWebName_Product["groupcode"]				= "���롼�ץ�����";
			$aryWebName_Product["strproductcode"]			= "���ʥ�����";					// ���ʥ�����
			$aryWebName_Product["strproductname"]			= "����̾��(���ܸ�)";			// ����̾��(���ܸ�)
			$aryWebName_Product["strproductenglishname"]	= "����̾��(�Ѹ�)";				// ����̾��(�Ѹ�)
			$aryWebName_Product["lnginchargegroupcode"]		= "�Ķ�����";						// ����
			$aryWebName_Product["lnginchargeusercode"]		= "��ȯô����";						// ô����
			$aryWebName_Product["strgoodscode"]				= "�ܵ�����";					// �ܵ�����
			$aryWebName_Product["strgoodsname"]				= "����̾��";					// ����̾��
			$aryWebName_Product["lngpackingunitcode"]		= "�ٻ�ñ��";					// �ٻ�ñ��
			$aryWebName_Product["lngboxquantity"]			= "��Ȣ���ޡ�����";				// ��Ȣ���ޡ�����
			$aryWebName_Product["lngcartonquantity"]		= "�����ȥ�����";				// �����ȥ�����
			$aryWebName_Product["lngproductunitcode"]		= "����ñ��";					// ����ñ��
			$aryWebName_Product["lngproductionquantity"]	= "����ͽ���";					// ����ͽ���
			$aryWebName_Product["lngcustomercompanycode"]	= "�ܵ�";						// �ܵ�
			$aryWebName_Product["lngcustomerusercode"]		= "�ܵ�ô����";					// �ܵ�ô����
			$aryWebName_Product["lngfactorycode"]			= "��������";					// ��������
			$aryWebName_Product["lngfirstdeliveryquantity"]	= "���Ǽ�ʿ�";					// ���Ǽ�ʿ�
			$aryWebName_Product["lngassemblyfactorycode"]	= "���å���֥깩��";			// ���å���֥깩��
			$aryWebName_Product["lngdeliveryplacecode"]		= "Ǽ�ʾ��";					// Ǽ�ʾ��
			$aryWebName_Product["dtmdeliverylimitdate"]		= "Ǽ��";						// Ǽ��
			$aryWebName_Product["curproductprice"]			= "����";						// ����
			$aryWebName_Product["curretailprice"]			= "����";						// ����
			$aryWebName_Product["lngtargetagecode"]			= "�о�ǯ��";					// �о�ǯ��
			$aryWebName_Product["lngcertificateclasscode"]	= "�ڻ�";						// �ڻ�
			$aryWebName_Product["lngroyalty"]				= "�����ƥ�";				// �����ƥ�
			$aryWebName_Product["lngcopyrightcode"]			= "�Ǹ���";						// �Ǹ���
			$aryWebName_Product["strcopyrightnote"]			= "�Ǹ�������";					// �Ǹ�������
			$aryWebName_Product["strcopyrightdisplaystamp"]	= "�Ǹ�ɽ���ʹ����";			// �Ǹ�ɽ���ʹ����
			$aryWebName_Product["strcopyrightdisplayprint"]	= "�Ǹ�ɽ���ʰ���ʪ��";			// �Ǹ�ɽ���ʰ���ʪ��
			$aryWebName_Product["lngproductformcode"]		= "���ʷ���";					// ���ʷ���
			$aryWebName_Product["strproductcomposition"]	= "���ʹ���";					// ���ʹ���
			$aryWebName_Product["strassemblycontents"]		= "���å���֥�����";			// ���å���֥�����
			$aryWebName_Product["strspecificationdetails"]	= "���;ܺ�";					// ���;ܺ�
			$aryWebName_Product["dtminsertdate"]			= "������";						// ������
			$aryWebName_Product["lnggoodsplanprogresscode"]	= "���ʹԾ���";				// ���ʹԾ���
			$aryWebName_Product["dtmrevisiondate"]			= "��������";					// ��������
			$aryWebName_Product["lnginputusercode"]			= "���ϼ�";						// ���ϼ�
			
			$aryWebName_Column["detail"]					= "�ܺ�";
			$aryWebName_Column["fix"]						= "����";
			$aryWebName_Column["delete"]					= "���";
		}
		else
		{
		
			// �Ѹ쥳����
			$aryWebName_Product["bytinvalidflag"]			= "flg";
			$aryWebName_Product["groupcode"]				= "Groupcode";
			$aryWebName_Product["strproductcode"]			= "Products code";				// ���ʥ�����
			$aryWebName_Product["strproductname"]			= "Products name(ja)";			// ����̾��(���ܸ�)
			$aryWebName_Product["strproductenglishname"]	= "Products name(en)";			// ����̾��(�Ѹ�)
			$aryWebName_Product["lnginchargegroupcode"]		= "Dept";						// ����
			$aryWebName_Product["lnginchargeusercode"]		= "In charge name";				// ô����
			$aryWebName_Product["strgoodscode"]				= "Goods code(Corresp)";		// �ܵ�����
			$aryWebName_Product["strgoodsname"]				= "Goods name";					// ����̾��
			$aryWebName_Product["lngpackingunitcode"]		= "Packing unit";				// �ٻ�ñ��
			$aryWebName_Product["lngboxquantity"]			= "Box Qty";					// ��Ȣ���ޡ�����
			$aryWebName_Product["lngcartonquantity"]		= "Carton Qty";					// �����ȥ�����
			$aryWebName_Product["lngproductunitcode"]		= "Products Unit";				// ����ñ��
			$aryWebName_Product["lngproductionquantity"]	= "Refound Qty";				// ����ͽ���
			$aryWebName_Product["lngcustomercompanycode"]	= "Vendor";						// �ܵ�
			$aryWebName_Product["lngcustomerusercode"]		= "In charge name";				// �ܵ�ô����
			$aryWebName_Product["lngfactorycode"]			= "Creation fact";				// ��������
			$aryWebName_Product["lngfirstdeliveryquantity"]	= "Delivery Qty";				// ���Ǽ�ʿ�
			$aryWebName_Product["lngassemblyfactorycode"]	= "Assembly fact";				// ���å���֥깩��
			$aryWebName_Product["lngdeliveryplacecode"]		= "Location";					// Ǽ�ʾ��
			$aryWebName_Product["dtmdeliverylimitdate"]		= "Deli date";					// Ǽ��
			$aryWebName_Product["curproductprice"]			= "Wholesale price";			// ����
			$aryWebName_Product["curretailprice"]			= "Selling price";				// ����
			$aryWebName_Product["lngtargetagecode"]			= "Target Age";					// �о�ǯ��
			$aryWebName_Product["lngcertificateclasscode"]	= "Inspection";					// �ڻ�
			$aryWebName_Product["lngroyalty"]				= "Loyalty";					// �����ƥ�
			$aryWebName_Product["lngcopyrightcode"]			= "Copyright";					// �Ǹ���
			$aryWebName_Product["strcopyrightnote"]			= "Copyright remark";			// �Ǹ�������
			$aryWebName_Product["strcopyrightdisplaystamp"]	= "Copyright(Stamp)";			// �Ǹ�ɽ���ʹ����
			$aryWebName_Product["strcopyrightdisplayprint"]	= "Copyright(Print)";			// �Ǹ�ɽ���ʰ���ʪ��
			$aryWebName_Product["lngproductformcode"]		= "Goods form";					// ���ʷ���
			$aryWebName_Product["strproductcomposition"]	= "Products Info";				// ���ʹ���
			$aryWebName_Product["strassemblycontents"]		= "Assembly Info";				// ���å���֥�����
			$aryWebName_Product["strspecificationdetails"]	= "Details";					// ���;ܺ�

			$aryWebName_Product["dtminsertdate"]			= "Creation date";				// ������
			$aryWebName_Product["lnggoodsplanprogresscode"]	= "Plan status";				// ���ʹԾ���
			$aryWebName_Product["dtmrevisiondate"]			= "Revise date";				// ��������
			$aryWebName_Product["lnginputusercode"]			= "Input person";				// ���ϼ�
			
			$aryWebName_Column["detail"]					= "Detail";
			$aryWebName_Column["fix"]						= "Fix";
			$aryWebName_Column["delete"]					= "Delete";
			
		}

		$strQuery = "SELECT lnggroupcode, strgroupdisplaycolor FROM m_group";
		//echo "$strQuery";

		// �����꡼�¹� =====================================
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			//fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// �ͤ�Ȥ� =====================================
		if( $lngResultNum = pg_num_rows( $lngResultID ) )
		{
			for( $i = 0; $i < $lngResultNum; $i++ )
			{
				$aryResut[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
			}
		}

		for( $j=0; $j<count($aryResut); $j++)
		{
			$lngCode = $aryResut[$j]["lnggroupcode"];
			$strColor = $aryResut[$j]["strgroupdisplaycolor"];
			
			if( $strColor != "" )
			{
				$aryColor[$lngCode] = $strColor;
			}
			else
			{
				$aryColor[$lngCode] = "#ffffff";
			}
			//echo "color : $lngCode :".$aryColor[$lngCode]."<br>";
		}
		//print_r($aryColor);

		
		// ���ͷ���1000��1,000)
		$aryToNumberOrDate[1] = "lngboxquantity";				// ��Ȣ���ޡ�����
		$aryToNumberOrDate[2] = "lngproductionquantity";		// ����ͽ���
		$aryToNumberOrDate[3] = "lngfirstdeliveryquantity";		// ���Ǽ�ʿ�
		$aryToNumberOrDate[4] = "lngcartonquantity";			// �����ȥ�����
		$aryToNumberOrDate[5] = "lngroyalty";					// �����ƥ�

		// ���շ���YYYY-MM-DD��YYYY/MM/DD)
		$aryToNumberOrDate[6] = "dtminsertdate";				// ��������
		$aryToNumberOrDate[7] = "dtmrevisiondate";				// ��������
		$aryToNumberOrDate[8] = "dtmdeliverylimitdate";			// Ǽ��

		$count1 = count($aryNewResult);

		$aryHtml[] = "<span id=\"COPYAREA1\">";
		$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
		for ( $i = 0; $i < $count1; $i++ )
		{
			// <TH>������ ========================================================
			if($i == 0)
			{
				$aryHtml[] = "<tr id=\"SegTitle\">";
				$aryHtml[] = "<td valign=\"top\" valign=\"center\"><a href=\"#\" onclick=\"fncDoCopy( copyhidden , document.getElementById('COPYAREA1') , document.getElementById('COPYAREA2') );return false;\"><img onmouseover=\"CopyOn(this);\" onmouseout=\"CopyOff(this);\" src=\"/img/type01/cmn/seg/copy_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"COPY\"></a></td>";

				// �ܺ�ɽ���ܥ���ɽ�����ꤵ��Ƥ����
				if ( $aryViewButton["curDetail"] )
				{
					$aryHtml[] = "<td nowrap>".$aryWebName_Column["detail"]."</td>";
				}

				for( $n = 0; $n < count( $aryNewResult[0] ); $n++ )
				{
					list ( $strKeys,$strValues ) = each ( $aryNewResult[0] );

					reset ( $aryWebName_Product );
					
					for ( $h = 0; $h < count( $aryWebName_Product ); $h++ )
					{
						list ( $strKeys2, $strValues2 ) = each ( $aryWebName_Product );
						if( $strKeys == $strKeys2 )
						{
							//reset( $aryCodeToName );
							//$strColspan = "";
							//for( $y = 0; $y < count( $aryCodeToName ); $y++ )
							//{
							//	if($strKeys == $aryCodeToName[$y])
							//	{
							//		$strColspan = " colspan='2'";
							//		break;
							//	}
							//}
							
							if($strKeys != "bytinvalidflag" && $strKeys != "groupcode")
							{
								if( strcmp( $lngSortNumber, "") != 0 )
								{
									$aryHtml[] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"fncSort('$strKeys',$lngSortNumber);\"><a href=\"#\">$strValues2</a></td>";
								}
								else
								{
									$aryHtml[] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"fncSort('$strKeys',1 );\"><a href=\"#\">$strValues2</a></td>";
								}
							}
						}
					}
				}

				if ( strcmp($renew, "") != 0 and $aryViewButton["curFix"] )
				{
					$aryHtml[] = "<td nowrap>".$aryWebName_Column["fix"]."</td>";
				}

				if ( strcmp($del, "") != 0 and $aryViewButton["curDelete"] )
				{ 
					$aryHtml[] = "<td nowrap>".$aryWebName_Column["delete"]."</td>";
				}

				if($strKeys != "bytinvalidflag" || $strKeys != groupcode)
				{
					$aryHtml[] = "</tr>";
				}
				//$aryHtml[] = "</tr><tr class=\"Lists01\">";
				$aryHtml[] = "</span>";

				$aryHtml[]  = "<tr class=\"Segs\" height=\"1\"><td bgcolor=\"#6f8180\"><img src=\"/img/_%lngLayoutCode%_/cmn/seg/dot.gif\" height=\"1\" width=\"1\"></td></tr>"; // ���ߡ�TR
			}
			$strInputColor = $aryNewResult[$i]["groupcode"];

			$aryHtml[] = "<span id=\"COPYAREA2\">";
			$aryHtml[] = "<tr class=\"Segs\" name=\"strTrName$i\" style=\"background:$aryColor[$strInputColor]\" onclick=\"fncSelectTrColor( this );\">";

			$strInputColor2 = $aryNewResult[$i]["groupcode"];

			reset( $aryNewResult[$i] );
			$lngGyou = $i+1;
			$aryHtml[] =  "<td nowrap>$lngGyou</td>";
			if ( $aryViewButton["curDetail"] )
			{
				$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/p/result/index2.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession' , window.form1 , 'ResultIframeCommon' , 'YES' , $lngLanguageCode , 'detail' )\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
			}

			for ( $j = 0; $j < count($aryNewResult[$i]); $j++ )
			{
				list ($strKeys, $strValues ) = each ( $aryNewResult[$i] );

				// ɽ���Ѵ������Τ򸡺�
				$lngArrayCount = array_search($strKeys, $aryToNumberOrDate);
				if ( $lngArrayCount )
				{
					// ɽ��ʸ����η���
					if ( $lngArrayCount > 5 )
					{
						$strNewDate = str_replace("-", "/", $aryNewResult[$i][$strKeys]);
						if ( $strKeys == "dtmdeliverylimitdate" )
						{
							$strNewDate = substr( $strNewDate, 0, 7 );
						}
						$aryHtml[] = "<td nowrap>". $strNewDate . "</td>";
					}
					else
					{
						if ( $strKeys == "lngroyalty" )
						{
							$aryHtml[] = "<td nowrap align=\"right\">". $aryNewResult[$i][$strKeys] . "</td>";
						}
						else
						{
							$aryHtml[] = "<td nowrap align=\"right\">". number_format( $aryNewResult[$i][$strKeys] ). "</td>";
						}
					}
				}

				else
				{
					if( strcmp( $aryNewResult[$i][$strKeys], "" ) != 0 ) 
					{
						if($strKeys != "bytinvalidflag" && $strKeys != "groupcode")
						{
							if ( $strKeys == "curproductprice" or $strKeys == "curretailprice" )
							{
								$aryHtml[] = "<td nowrap align=\"right\"> \\";
							}
							else
							{
								$aryHtml[] = "<td nowrap>";
							}
						}
						//reset( $aryCodeToName );
						for ( $h = 0; $h < count($aryCodeToName); $h++ )
						{
							if ( $strKeys == $aryCodeToName[$h] )
							{
								if ( $aryNewResult[$i][$strKeys] )
								{
									unset( $strDisplayCode );
									unset( $strDisplayName );
									if ( $strKeys == "lnggoodsplanprogresscode" )
									{
										$strDisplayCode = fncCodeToDisplayName ( $h, $aryNewResult[$i][$strKeys], $objDB, $objCache );
										$aryHtml[] = $strDisplayCode;
									}
									else if ( $strKeys == "lngcustomerusercode" )
									{
										unset( $strText );
										$strDisplayCode = fncCodeToDisplayCode ( $h, $aryNewResult[$i][$strKeys], $objDB, $objCache );
										$strDisplayName = fncCodeToDisplayName ( $h, $aryNewResult[$i][$strKeys], $objDB, $objCache );
										if ( $strDisplayCode )
										{
											$strText = "[" . $strDisplayCode . "] ";
										}
										if ( $strDisplayName )
										{
											$strText .= $strDisplayName;
										}
										else
										{
											$strText .= $aryNewResult[$i][$strKeys];
										}
										$aryHtml[] = $strText;
									}
									else
									{
										unset( $strText );
										$strDisplayCode = fncCodeToDisplayCode ( $h, $aryNewResult[$i][$strKeys], $objDB, $objCache );
										$strDisplayName = fncCodeToDisplayName ( $h, $aryNewResult[$i][$strKeys], $objDB, $objCache );
										if ( $strDisplayCode )
										{
											$strText = "[" . $strDisplayCode . "] ";
										}
										if ( $strDisplayName )
										{
											$strText .= $strDisplayName;
										}
										$aryHtml[] = $strText;
									}
								}
								break;
							}
						}

						if($h == count( $aryCodeToName ))
						{
							if($strKeys != "bytinvalidflag" && $strKeys != "groupcode")
							{
								if ( $strKeys == "strproductcomposition" and $aryNewResult[$i][$strKeys] )
								{
									$aryHtml[] = "��" . $aryNewResult[$i][$strKeys] . "�異�å���֥�";
								}
								else
								{
									$aryHtml[] = $aryNewResult[$i][$strKeys];
								}
							}
						}

						//reset( $aryCodeToName );
						//for($h=0; $h<count($aryCodeToName); $h++)
						//{
						//	if($strKeys == $aryCodeToName[$h])
						//	{
							//echo "$h ====  $strKeys<br>";
							//$test = fncCodeToName( $h, $aryNewResult[$i][$strKeys], $objDB );
							//echo "test : $test<br>";
							
							//echo "$strKeys : $h<br><br>";
						//		$aryHtml[] = "Name";
						//		break;
						//	}
						//}
						if($strKeys != "bytinvalidflag" && $strKeys != "groupcode")
						{
							$aryHtml[] = "</td>";
						}

					}
					/*
					if( strcmp( $aryNewResult[$i][$strKeys], "" ) != 0 ) 
					{
						$aryHtml[] = "<td nowrap>". $aryNewResult[$i][$strKeys]. "</td>";
						
						reset( $aryCodeToName );
						for($h=0; $h<count($aryCodeToName); $h++)
						{
							if($strKeys == $aryCodeToName[$h])
							{
							//echo "$h ====  $strKeys<br>";
							//$test = fncCodeToName( $h, $aryNewResult[$i][$strKeys], $objDB );
							//echo "test : $test<br>";
							
							//echo "$strKeys : $h<br><br>";
								$aryHtml[] = "<td nowrap>Name</td>";
								break;
							}
						}

					}
					*/
					else
					{
							$aryHtml[] = "<td nowrap>&nbsp;</td>";
					}
					// =====
					

					
				}
			}
			if ( strcmp($renew, "") != 0 and $aryViewButton["curFix"] )
			{
				$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/p/regist/renew.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession' , window.form1 , 'ResultIframeRenew' , 'NO' , $lngLanguageCode )\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
			}
			
			if ( strcmp($del, "") != 0 && $aryNewResult[$i]["bytinvalidflag"] == f and $aryViewButton["curDelete"] )
			{
				$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon( '/p/result/index3.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession' , window.form1 , 'ResultIframeCommon' , 'YES' , $lngLanguageCode , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
			}
			else
			{
				if ( strcmp($del, "") != 0 and $aryViewButton["curDelete"] )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\">&nbsp;</td>";
				}
			}
			$aryHtml[] = "</tr>";
		}
		
		$aryHtml[] = "</table>";
		$aryHtml[] = "</span>";

		$strhtml = implode( "\n", $aryHtml );
		
		return $strhtml;
	}
	
	
	
	

?>