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
	
		if( strcmp( $lngDisplayCode, "") == "" && strcmp( $strDisplayName, "") != "" )
		{
			$strQuery = "SELECT $strCodeColumn FROM $strTable WHERE $strWhereColumn"."name"." ~* '$strDisplayName' $strQueryWhere";
		}
		else
		{
			$strQuery = "SELECT $strCodeColumn FROM $strTable WHERE $strWhereColumn"."code"." = '$lngDisplayCode' $strQueryWhere";
		}
		//echo "strQuery : $strQuery<br>";
		
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
		
		// 
		
		
		if( is_array( $aryQueryName ) )
		{
		
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
							//if( $strValues == "curProductPrice" || $strValues == "curRetailPrice" )
							//{
							//	$aryNewQueryData[] = "To_char(p.$strValues,'9,999,999,999') as $strValues," ;
							//	break;
							//}
							
							$aryNewQueryData[] = "To_char(p.$strValues,'9,999,999,999') as $strValues," ;
							break;
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

		// 0:���祳����
		// 1:ô����
		// 2:�ܵ�
		// 3:��������
		// 4:���å���֥깩��
		// 5:Ǽ�ʾ��
		
		// displaycode��code���Ѵ�
		// displaycode�����äƤ��ʤ�displayname�����äƤ������displayname����displaycode������Ƥ���
		$aryTrueCode[0] = fncGetCode( $aryData["lngInChargeGroupCode"], $aryData["strInChargeGroupName"],m_group, lnggroupcode, strgroupdisplay, ' and bytgroupdisplayflag = true',$objDB );
		$aryTrueCode[1] = fncGetCode( $aryData["lngInChargeUserCode"], $aryData["strInChargeUserName"], m_user, lngusercode, struserdisplay,'', $objDB);
		$aryTrueCode[2] = fncGetCode( $aryData["lngCustomerCompanyCode"], $aryData["strCustomerCompanyName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		$aryTrueCode[3] = fncGetCode( $aryData["lngFactoryCode"], $aryData["strFactoryName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		$aryTrueCode[4] = fncGetCode( $aryData["lngAssemblyFactoryCode"], $aryData["AssemblyFactoryName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		$aryTrueCode[5] = fncGetCode( $aryData["lngDeliveryPlaceCode"], $aryData["DeliveryPlaceName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);

		
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
						
							if( count( $aryTrueCode[$n] ) == 1)						// TrueCode���ͤ�ʣ�����äƤ��뤫��
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
							//������aryQueryWhere��Ĥ��������
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
								$aryQueryWhere[] = "g.dtmRevisionDate = To_Date('". $strValues2 ."', 'YYYY-MM-DD') AND";
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
	*       dispalaycode �� displayName���Ѵ��ؿ�
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
		$aryOptionCode[0] = "lngrevisionno";					// 0:�ڻ�
		$aryOptionCode[1] = "lngcertificateclasscode";			// 1:�Ǹ���
		$aryOptionCode[2] = "lngcopyrightcode";					// 2:���ʷ���
		$aryOptionCode[3] = "lngpackingunitcode";				// 3:�ٻ�ñ��
		$aryOptionCode[4] = "lngproductunitcode";				// 4:����ñ��
		$aryOptionCode[5] = "lngproductionunitcode";			// 5:����ͽ�����ñ��
		$aryOptionCode[6] = "lngfirstdeliveryunitcode";			// 6:���Ǽ�ʿ���ñ��
		$aryOptionCode[7] = "lngtargetagecode";					// 7:�о�ǯ��
		$aryOptionCode[8] = "lngcertificateclasscode";			// 8:�ڻ�
		$aryOptionCode[9] = "lngcopyrightcode";					// 9:�Ǹ���
		$aryOptionCode[10] = "lngproductformcode";				// 10:���ʷ���
		
	
		if( strcmp($aryResult["lngrevisionno"], "") != 0)
		{
			$aryOptionName[0] = fncGetMasterValue( m_goodsplanprogress, lnggoodsplanprogresscode, strgoodsplanprogressname, $aryResult["lngrevisionno"],'' ,$objDB);
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
		if( strcmp( $aryResult["lngproductformcode"], "" ) != 0)
		{
			$aryOptionName[10] = fncGetMasterValue(m_productform,lngproductformcode,strproductformname,$aryResult["lngproductformcode"], '', $objDB);
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
	function fncCreateHtml ( $aryNewResult ,$lngSortNumber, $strSession, $renew ,$del,$objDB)
	{
//echo "����ɽ�� : $renew<br>";
//echo "���ɽ�� : $del<br>";


		// dispalaycode �� displayName���Ѵ� =================================================
		// 0:���祳����
		// 1:ô����
		// 2:�ܵ�
		// 3:��������
		// 4:���å���֥깩��
		// 5:Ǽ�ʾ��
		
		$aryCodeToName[0] = "lnginchargegroupcode";
		$aryCodeToName[1] = "lnginchargeusercode";
		$aryCodeToName[2] = "lngcustomercompanycode";
		$aryCodeToName[3] = "lngfactorycode";
		$aryCodeToName[4] = "lngassemblyfactorycode";
		$aryCodeToName[5] = "lngdeliveryplacecode";
		
		function fncCodeToDisplayName ( $lngNumber,$lngCodeValue ,&$objDB )
		{
			switch ( $lngNumber )
			{
			
				case 0:
				$strCodeToName = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplayname", $lngCodeValue, '', $objDB );
				//echo "strCodeToName : $strCodeToName<br>";
				break;
				case 1:
				$strCodeToName = fncGetMasterValue(m_user, lngusercode, struserdisplayname, $lngCodeValue, '',  $objDB );
				break;
				case 2:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplayname,$lngCodeValue, '',  $objDB );
				break;
				case 3:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplayname, $lngCodeValue, '',  $objDB );
				break;
				case 4:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplayname ,$lngCodeValue, '',  $objDB );
				break;
				case 5:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplayname ,$lngCodeValue, '',  $objDB );
				break;
			}
			return $strCodeToName;
		}
		
		
		function fncCodeToDisplayCode ( $lngNumber,$lngCodeValue ,$objDB )
		{
			switch ( $lngNumber )
			{
			
				case 0:  // 
				$strCodeToName = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplaycode", $lngCodeValue, '', $objDB );
				break;
				case 1:
				$strCodeToName = fncGetMasterValue(m_user, lngusercode, struserdisplaycode, $lngCodeValue, '',  $objDB );
				break;
				case 2:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode,$lngCodeValue, '',  $objDB );
				break;
				case 3:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode, $lngCodeValue, '',  $objDB );
				break;
				case 4:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode ,$lngCodeValue, '',  $objDB );
				break;
				case 5:
				$strCodeToName = fncGetMasterValue(m_company, lngcompanycode, strcompanydisplaycode ,$lngCodeValue, '',  $objDB );
				break;
			}
			return $strCodeToName;
		}

				
		// ���ܸ쥳����
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
		$aryWebName_Product["strcustomerusername"]		= "�ܵ�ô����";					// �ܵ�ô����
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
		$aryWebName_Product["strcopyrightdisplaystamp"]	= "�Ǹ�ɽ���ʹ����";			// �Ǹ�ɽ���ʹ����
		$aryWebName_Product["strcopyrightdisplayprint"]	= "�Ǹ�ɽ���ʰ���ʪ��";			// �Ǹ�ɽ���ʰ���ʪ��
		$aryWebName_Product["lngproductformcode"]		= "���ʷ���";					// ���ʷ���
		$aryWebName_Product["strproductcomposition"]	= "���ʹ���";					// ���ʹ���
		$aryWebName_Product["strassemblycontents"]		= "���å���֥�����";			// ���å���֥�����
		$aryWebName_Product["strspecificationdetails"]	= "���;ܺ�";					// ���;ܺ�

		$aryWebName_Product["dtminsertdate"]			= "������";						// ������
		$aryWebName_Product["lngrevisionno"]			= "���ʹԾ���";				// ���ʹԾ���
		$aryWebName_Product["dtmrevisiondate"]			= "��������";					// ��������
		
		
		// �Ѹ쥳����
		$aryWebName_Product_ENG["strproductcode"]			= "Products code";			// ���ʥ�����
		$aryWebName_Product_ENG["strproductname"]			= "Products name(ja)";		// ����̾��(���ܸ�)
		$aryWebName_Product_ENG["strproductenglishname"]	= "Products name(en)";		// ����̾��(�Ѹ�)
		$aryWebName_Product_ENG["lnginchargegroupcode"]		= "Dept";					// ����
		$aryWebName_Product_ENG["lnginchargeusercode"]		= "In charge name";			// ô����
		$aryWebName_Product_ENG["strgoodscode"]				= "Goods code(Corresp)";	// �ܵ�����
		$aryWebName_Product_ENG["strgoodsname"]				= "Goods name";				// ����̾��
		$aryWebName_Product_ENG["lngpackingunitcode"]		= "Packing unit";			// �ٻ�ñ��
		$aryWebName_Product_ENG["lngboxquantity"]			= "Box Qty";				// ��Ȣ���ޡ�����
		$aryWebName_Product_ENG["lngcartonquantity"]		= "Carton Qty";				// �����ȥ�����
		$aryWebName_Product_ENG["lngproductunitcode"]		= "Products Unit";			// ����ñ��
		$aryWebName_Product_ENG["lngproductionquantity"]	= "Refound Qty";			// ����ͽ���
		$aryWebName_Product_ENG["lngcustomercompanycode"]	= "Vendor";					// �ܵ�
		$aryWebName_Product_ENG["strcustomerusername"]		= "In charge name";			// �ܵ�ô����
		$aryWebName_Product_ENG["lngfactorycode"]			= "Creation fact";			// ��������
		$aryWebName_Product_ENG["lngfirstdeliveryquantity"]	= "Delivery Qty";			// ���Ǽ�ʿ�
		$aryWebName_Product_ENG["lngassemblyfactorycode"]	= "Assembly fact";			// ���å���֥깩��
		$aryWebName_Product_ENG["lngdeliveryplacecode"]		= "Location";				// Ǽ�ʾ��
		$aryWebName_Product_ENG["dtmdeliverylimitdate"]		= "Deli date";				// Ǽ��
		$aryWebName_Product_ENG["curproductprice"]			= "Wholesale price";		// ����
		$aryWebName_Product_ENG["curretailprice"]			= "Selling price";			// ����
		$aryWebName_Product_ENG["lngtargetagecode"]			= "Age";					// �о�ǯ��
		$aryWebName_Product_ENG["lngcertificateclasscode"]	= "Inspection";				// �ڻ�
		$aryWebName_Product_ENG["lngroyalty"]				= "Loyalty";				// �����ƥ�
		$aryWebName_Product_ENG["lngcopyrightcode"]			= "Copyright";				// �Ǹ���
		$aryWebName_Product_ENG["strcopyrightdisplaystamp"]	= "Copyright(Stamp)";		// �Ǹ�ɽ���ʹ����
		$aryWebName_Product_ENG["strcopyrightdisplayprint"]	= "Copyright(Print)";		// �Ǹ�ɽ���ʰ���ʪ��
		$aryWebName_Product_ENG["lngproductformcode"]		= "Goods form";				// ���ʷ���
		$aryWebName_Product_ENG["strproductcomposition"]	= "Products Info";			// ���ʹ���
		$aryWebName_Product_ENG["strassemblycontents"]		= "Assembly Info";			// ���å���֥�����
		$aryWebName_Product_ENG["strspecificationdetails"]	= "Details";				// ���;ܺ�

		$aryWebName_Product_ENG["dtminsertdate"]			= "Creation date";				// ������
		$aryWebName_Product_ENG["lngrevisionno"]			= "Plan status";				// ���ʹԾ���
		$aryWebName_Product_ENG["dtmrevisiondate"]			= "Revise date";				// ��������
		
		
		
		
		// ���ͷ���1000��1,000)
		$aryToNumber[0] = "lngboxquantity";				// ��Ȣ���ޡ�����
		$aryToNumber[1] = "lngproductionquantity";		// ����ͽ���
		$aryToNumber[2] = "lngfirstdeliveryquantity";	// ���Ǽ�ʿ�
		$aryToNumber[3] = "lngroyalty";					// �����ƥ�


		$count1 = count($aryNewResult);
		//$count2 = 10;
		
		$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
		for ( $i = 0; $i < $count1; $i++ )
		{
			
			
			
			// <TH>������ ========================================================
			if($i == 0)
			{
			$aryHtml[] = "<tr id=\"SegTitle\">";
				$aryHtml[] = "<td>&nbsp;</td>";
				$aryHtml[] = "<td nowarp>�ܺ�</td>";
				if(strcmp($renew, "") != 0 )
				{
					$aryHtml[] = "<td nowarp>����</td>";
				}
				if(strcmp($del, "") != 0)
				{ 
					$aryHtml[] = "<td nowarp>���</td>";
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
				$aryHtml[] = "</tr>";
				//$aryHtml[] = "</tr><tr class=\"Lists01\">";
			}
			
			$aryHtml[] = "<tr id=\"Segs\">";
			
			reset( $aryNewResult[$i] );
			$lngGyou = $i+1;
			$aryHtml[] = "<td nowrap>$lngGyou</td>";
			$aryHtml[] = "<td><a class=\"cells\" href=\"javascript:fncSubwin('./index2.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession')\">��</a></td>";
			if(strcmp($renew, "") != 0 )
			{
				$aryHtml[] = "<td><a class=\"cells\" href=\"javascript:fncSubwin('../regist/renew.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession')\">��</a></td>";
			}
			
			if(strcmp($del, "") != 0)
			{
				$aryHtml[] = "<td><a class=\"cells\" href=\"javascript:fncSubwin('./index3.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession')\">��</a></td>";
			}
			
			for ( $j = 0; $j < count($aryNewResult[$i]); $j++ )
			{
				list ($strKeys, $strValues ) = each ( $aryNewResult[$i] );
				for ($n=0; $n<count($aryToNumber); $n++)
				{
					if($strKeys == $aryToNumber[$n])
					{
						$aryHtml[] = "<td nowrap>". number_format( $aryNewResult[$i][$strKeys] ). "</td>";
						break;
					}
				}
//number_format

				if($n == count($aryToNumber))
				{
					if( strcmp( $aryNewResult[$i][$strKeys], "" ) != 0 ) 
					{
						$aryHtml[] = "<td nowrap>";
						//reset( $aryCodeToName );
						for($h=0; $h<count($aryCodeToName); $h++)
						{
							if( $strKeys == $aryCodeToName[$h]  )
							{
								$strDisplayCode = fncCodeToDisplayName ( $h, $aryNewResult[$i][$strKeys], $objDB );
								$strDsiplyaName = fncCodeToDisplayCode ( $h, $aryNewResult[$i][$strKeys], $objDB );
								$aryHtml[] = "[$strDsiplyaName] $strDisplayCode";
								break;
							}
						}
						
						if($h == count( $aryCodeToName ))
						{
							$aryHtml[] = $aryNewResult[$i][$strKeys];
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
						
						$aryHtml[] = "</td>";
						
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
				}
			}
			
			$aryHtml[] = "</tr>";
		}
		
		$aryHtml[] = "</table>";
		
		$strhtml = implode( "\n", $aryHtml );
		
		return $strhtml;
	}
	
	
	
	

?>