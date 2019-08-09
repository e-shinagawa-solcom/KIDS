<?php


	// -----------------------------------------------------------------
	/**
	*	dispalaynameからcodeを取得
	*	codeがなしでdisplaynameが存在する時
	*	
	*	@param  Long	$lngDisplayCode		フォームに入力された値（コード）
	*	@param  String	$strDisplayName 	フォームに入力された値（名前）
	*	@param  String	$strTable			テーブル
	*	@param	String	$strCodeColumn		取得したカラム名（コード）
	*	@param	String	$strWhereColumn		$strDisplayNameで比較するカラム
	*	@param	String	$strQueryWhere		条件
	*	@param  Object	$objDB				DBオブジェクト
	*	@return $aryTrueCode[0]				DB上のcode
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
		
		// 値をとる =====================================
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
	*	SQLの生成( SELECT部 )
	*
	*
	*	@param Array	$aryQueryName				// 
	*	@param Array	$aryGoods_column			// 
	*	@return 		$aryNewQueryData			// DB上のcode
	*/
	// -----------------------------------------------------------------

	// strProductCode → p.strProductCode
	function fncChangeName( $aryQueryName ,$aryGoods_column) 
	{

		// 日付型を適用する項目
		$aryToDate[0] = "dtmInsertDate";
		
		// 数値型(000,000)を適用する項目
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
	/**				fncQueryWhere()関数
	*
	*		SQLのWHERE部を作成する
	*
	*
	*		@param Array	$arySearchColumn		「検索」チェックされたもの
	*		@param Array	$aryData				ポストで渡ってきた値
	*		@param Array	$aryDisplayCode			コードから名称を引く該当項目
	*		@param Object	$objDB					DBオブジェクト
	*		@param String	$flgP3					権限フラグ（削除済みデータを表示するか）
	*		@return	Array	$aryQueryWhere
	*/
	// -----------------------------------------------------------------
	
	// 関数：検索条件の生成 ==========================================================
	function fncQueryWhere( $arySearchColumn, $aryData , $aryDisplayCode, $flgP3, $objDB)
	{
//print_r($arySearchColumn);
		// 0:部門コード
		// 1:担当者
		// 2:顧客
		// 3:生産工場
		// 4:アッセンブリ工場
		// 5:納品場所
		// 6:顧客担当者
		// 7:入力者

		
		// displaycode→codeに変換
		// displaycodeが入っていなくdisplaynameが入っている場合はdisplaynameからdisplaycodeを引いてくる
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
			// codeが入っていればcodeで検索nameが入っている場合は既存関数が使えないので分けてます
			if(strcmp( $aryData["lngCustomerUserCode"],"") != 0)
			{ 
				$aryTrueCode[6] = fncGetCode( $aryData["lngCustomerUserCode"], '',m_user ,lngusercode, struserdisplay, '',$objDB);
			}
			// nameが入っている場合はリレーションするのでfncGetCodeに渡す引数が変わる
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
		//strProductCodeの場合は文字型なのでlngproductnoで検索。対象文字列を数値にする
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
			list ( $strKeys, $strValues ) = each ( $arySearchColumn );				//検索実行がチェックされた項目
			// echo "$strKeys ======= $strValues<br>";
			// fncChangeNameで変換された値をもう一度戻し$_POSTと比較
			// 例）p.strProductCode → strProductCode
			// dtmInsertDateだけ日付処理（To_char・・）されているので削除してから比較
			$strValues			= preg_replace ( "/,$/", "", $strValues );
			$strValues_replace	= preg_replace ( "/^[pg]+\./", "", $strValues );
			$strValues_replace = preg_replace ("/.+?dtmInsertDate$/", "dtmInsertDate", $strValues_replace );
			$strValues_replace = preg_replace ("/.+?dtmRevisionDate$/",dtmRevisionDate,$strValues_replace );
			
			//echo "value_replace : $strValues_replace<br>";
			reset( $aryData );
			for ( $j = 0; $j < count( $aryData ); $j++ )							//FORM全ての値
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
						//fncOutputError( 302, DEF_WARNING, "検索項目チェックに誤りがあります",TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
					}
					
					// displaycode → code
					reset($aryDisplayCode);
					for ( $n = 0; $n < count( $aryDisplayCode ); $n++ )				// dispalaycode → code
					{
						if( $strValues_replace == $aryDisplayCode[$n] )
						{
						
						//echo "count : $n :".count($aryTrueCode[$n])."<br>";
						//echo "tureCode".$aryTrueCode[$n];
							if( count( $aryTrueCode[$n] ) <= 1)						// TrueCodeに値が複数入っているか？
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
								$strValues2 = preg_replace("/^0/","", $strValues2);		//文字列→数値（0685→685)
								$aryQueryWhere[] =  $aryFromToValue[$h].$strValues2.$aryFromToValueEnd[$h] ;
								$aryQueryWhere[] = " AND";
							}
								break;
						}
					}
					
					
					
					if($h == count( $aryFromTo ) )
					{
						// 文字型
						if( preg_replace( "/^str/", $strValues_replace ) )
						{
							$aryQueryWhere[] = " $strValues ~* '$strValues2' AND";
						}
						// 日付型
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
						// その他
						elseif( $strLoopFlag == "true")
						{
						// 何もしない
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
	*       へ変換関数
	*
	*		SQL実行後の処理：コードから名称へ
	*		SQL実行後の処理：オプションコードからオプション値へ
	*		strspecificationdetailsに含まれている改行を<br>に変換
	*
	*		@param	Array	$aryResult			// SQLが実行された結果
	*		@param	Array	$aryDisplayCode		// コードから名称を引く該当項目
	*		@param	Object	$objDB				// DBオブジェクト
	*		@retuen	Array	$aryNewData
	*/
	// -----------------------------------------------------------------

	function fncChangeDisplayName ( $aryResult ,$aryDisplayCode, $objDB )
	{
	//print_r( $aryResult );
	//echo "<br><br>";
		// code → displaycodeへ変換 =================================================
		// 0:部門コード
		// 1:担当者
		// 2:顧客
		// 3:生産工場
		// 4:アッセンブリ工場
		// 5:納品場所
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


		// option code → option value ========================================
		$aryOptionCode[0] = "lngproductformcode";					// 0:商品形態
		$aryOptionCode[1] = "lngcertificateclasscode";			// 1:版権元
		$aryOptionCode[2] = "lngcopyrightcode";					// 2:商品形態
		$aryOptionCode[3] = "lngpackingunitcode";				// 3:荷姿単位
		$aryOptionCode[4] = "lngproductunitcode";				// 4:製品単位
		$aryOptionCode[5] = "lngproductionunitcode";			// 5:生産予定数の単位
		$aryOptionCode[6] = "lngfirstdeliveryunitcode";			// 6:初回納品数の単位
		$aryOptionCode[7] = "lngtargetagecode";					// 7:対象年齢
		$aryOptionCode[8] = "lngcertificateclasscode";			// 8:証紙
		$aryOptionCode[9] = "lngcopyrightcode";					// 9:版権元

		
	
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
			list ($strKeys, $strValues ) = each ( $aryResult );							// SQLが実行された結果をまわす
			
			// 文字列のみ比較
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
					// option code → option Name
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
	
	
	
	
	
	// HTML出力 （仮）=============================================
	function fncCreateHtml ( $aryNewResult ,$lngSortNumber, $strSession, $renew ,$del, $lngLanguageCode, $objDB, $objCache, $aryViewButton )
	{
//echo "修正表示 : $renew<br>";
//echo "削除表示 : $del<br>";
//echo "<br>languageCode : $lngLanguageCode<br>";
//print_r($aryNewResult);
		//print_r($aryNewResult);
		// dispalaycode → displayNameへ変換 =================================================
		// 0:部門コード
		// 1:担当者
		// 2:顧客
		// 3:生産工場
		// 4:アッセンブリ工場
		// 5:納品場所
		// 6:企画進行状況
		
		
		// WEB表記が '9002'→'[9002]渡辺'とするもの
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
						// 名称の取得
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
					// グループ名称の設定
					$strCodeToName = $aryInfo[1];
					break;
				case 1:
				case 7:
				case 8:
					$aryInfo = $objCache->GetValue("lngusercode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// 名称の取得
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
					// ユーザー名称の設定
					$strCodeToName = $aryInfo[1];
					break;
				case 2:
				case 3:
				case 4:
				case 5:
					$aryInfo = $objCache->GetValue("lngcompanycode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// 名称の取得
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
					// 会社名称の設定
					$strCodeToName = $aryInfo[1];
					break;
				case 6:
					$aryInfo = $objCache->GetValue("lnggoodsplanprogresscode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// 名称の取得
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
					// 商品化企画名称の設定
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
						// 名称の取得
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
					// グループ名称の設定
					$strCodeToName = $aryInfo[0];
					break;
				case 1:
				case 7:
				case 8:
					$aryInfo = $objCache->GetValue("lngusercode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// 名称の取得
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
					// ユーザー名称の設定
					$strCodeToName = $aryInfo[0];
					break;
				case 2:
				case 3:
				case 4:
				case 5:
					$aryInfo = $objCache->GetValue("lngcompanycode", $lngCodeValue);
					if( !is_array($aryInfo) )
					{
						// 名称の取得
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
					// 会社名称の設定
					$strCodeToName = $aryInfo[0];
					break;
			}
			return $strCodeToName;
		}

		if( $lngLanguageCode == 1 )
		{
			// 日本語コード
			$aryWebName_Product["bytinvalidflag"]			= "フラグ";
			$aryWebName_Product["groupcode"]				= "グループコード";
			$aryWebName_Product["strproductcode"]			= "製品コード";					// 製品コード
			$aryWebName_Product["strproductname"]			= "製品名称(日本語)";			// 製品名称(日本語)
			$aryWebName_Product["strproductenglishname"]	= "製品名称(英語)";				// 製品名称(英語)
			$aryWebName_Product["lnginchargegroupcode"]		= "営業部署";						// 部門
			$aryWebName_Product["lnginchargeusercode"]		= "開発担当者";						// 担当者
			$aryWebName_Product["strgoodscode"]				= "顧客品番";					// 顧客品番
			$aryWebName_Product["strgoodsname"]				= "商品名称";					// 商品名称
			$aryWebName_Product["lngpackingunitcode"]		= "荷姿単位";					// 荷姿単位
			$aryWebName_Product["lngboxquantity"]			= "内箱（袋）入数";				// 内箱（袋）入数
			$aryWebName_Product["lngcartonquantity"]		= "カートン入数";				// カートン入数
			$aryWebName_Product["lngproductunitcode"]		= "製品単位";					// 製品単位
			$aryWebName_Product["lngproductionquantity"]	= "生産予定数";					// 生産予定数
			$aryWebName_Product["lngcustomercompanycode"]	= "顧客";						// 顧客
			$aryWebName_Product["lngcustomerusercode"]		= "顧客担当者";					// 顧客担当者
			$aryWebName_Product["lngfactorycode"]			= "生産工場";					// 生産工場
			$aryWebName_Product["lngfirstdeliveryquantity"]	= "初回納品数";					// 初回納品数
			$aryWebName_Product["lngassemblyfactorycode"]	= "アッセンブリ工場";			// アッセンブリ工場
			$aryWebName_Product["lngdeliveryplacecode"]		= "納品場所";					// 納品場所
			$aryWebName_Product["dtmdeliverylimitdate"]		= "納期";						// 納期
			$aryWebName_Product["curproductprice"]			= "卸値";						// 卸値
			$aryWebName_Product["curretailprice"]			= "売値";						// 売値
			$aryWebName_Product["lngtargetagecode"]			= "対象年齢";					// 対象年齢
			$aryWebName_Product["lngcertificateclasscode"]	= "証紙";						// 証紙
			$aryWebName_Product["lngroyalty"]				= "ロイヤリティ";				// ロイヤリティ
			$aryWebName_Product["lngcopyrightcode"]			= "版権元";						// 版権元
			$aryWebName_Product["strcopyrightnote"]			= "版権元備考";					// 版権元備考
			$aryWebName_Product["strcopyrightdisplaystamp"]	= "版権表示（刻印）";			// 版権表示（刻印）
			$aryWebName_Product["strcopyrightdisplayprint"]	= "版権表示（印刷物）";			// 版権表示（印刷物）
			$aryWebName_Product["lngproductformcode"]		= "商品形態";					// 商品形態
			$aryWebName_Product["strproductcomposition"]	= "製品構成";					// 製品構成
			$aryWebName_Product["strassemblycontents"]		= "アッセンブリ内容";			// アッセンブリ内容
			$aryWebName_Product["strspecificationdetails"]	= "仕様詳細";					// 仕様詳細
			$aryWebName_Product["dtminsertdate"]			= "作成日";						// 作成日
			$aryWebName_Product["lnggoodsplanprogresscode"]	= "企画進行状況";				// 企画進行状況
			$aryWebName_Product["dtmrevisiondate"]			= "改訂日時";					// 改訂日時
			$aryWebName_Product["lnginputusercode"]			= "入力者";						// 入力者
			
			$aryWebName_Column["detail"]					= "詳細";
			$aryWebName_Column["fix"]						= "修正";
			$aryWebName_Column["delete"]					= "削除";
		}
		else
		{
		
			// 英語コード
			$aryWebName_Product["bytinvalidflag"]			= "flg";
			$aryWebName_Product["groupcode"]				= "Groupcode";
			$aryWebName_Product["strproductcode"]			= "Products code";				// 製品コード
			$aryWebName_Product["strproductname"]			= "Products name(ja)";			// 製品名称(日本語)
			$aryWebName_Product["strproductenglishname"]	= "Products name(en)";			// 製品名称(英語)
			$aryWebName_Product["lnginchargegroupcode"]		= "Dept";						// 部門
			$aryWebName_Product["lnginchargeusercode"]		= "In charge name";				// 担当者
			$aryWebName_Product["strgoodscode"]				= "Goods code(Corresp)";		// 顧客品番
			$aryWebName_Product["strgoodsname"]				= "Goods name";					// 商品名称
			$aryWebName_Product["lngpackingunitcode"]		= "Packing unit";				// 荷姿単位
			$aryWebName_Product["lngboxquantity"]			= "Box Qty";					// 内箱（袋）入数
			$aryWebName_Product["lngcartonquantity"]		= "Carton Qty";					// カートン入数
			$aryWebName_Product["lngproductunitcode"]		= "Products Unit";				// 製品単位
			$aryWebName_Product["lngproductionquantity"]	= "Refound Qty";				// 生産予定数
			$aryWebName_Product["lngcustomercompanycode"]	= "Vendor";						// 顧客
			$aryWebName_Product["lngcustomerusercode"]		= "In charge name";				// 顧客担当者
			$aryWebName_Product["lngfactorycode"]			= "Creation fact";				// 生産工場
			$aryWebName_Product["lngfirstdeliveryquantity"]	= "Delivery Qty";				// 初回納品数
			$aryWebName_Product["lngassemblyfactorycode"]	= "Assembly fact";				// アッセンブリ工場
			$aryWebName_Product["lngdeliveryplacecode"]		= "Location";					// 納品場所
			$aryWebName_Product["dtmdeliverylimitdate"]		= "Deli date";					// 納期
			$aryWebName_Product["curproductprice"]			= "Wholesale price";			// 卸値
			$aryWebName_Product["curretailprice"]			= "Selling price";				// 売値
			$aryWebName_Product["lngtargetagecode"]			= "Target Age";					// 対象年齢
			$aryWebName_Product["lngcertificateclasscode"]	= "Inspection";					// 証紙
			$aryWebName_Product["lngroyalty"]				= "Loyalty";					// ロイヤリティ
			$aryWebName_Product["lngcopyrightcode"]			= "Copyright";					// 版権元
			$aryWebName_Product["strcopyrightnote"]			= "Copyright remark";			// 版権元備考
			$aryWebName_Product["strcopyrightdisplaystamp"]	= "Copyright(Stamp)";			// 版権表示（刻印）
			$aryWebName_Product["strcopyrightdisplayprint"]	= "Copyright(Print)";			// 版権表示（印刷物）
			$aryWebName_Product["lngproductformcode"]		= "Goods form";					// 商品形態
			$aryWebName_Product["strproductcomposition"]	= "Products Info";				// 製品構成
			$aryWebName_Product["strassemblycontents"]		= "Assembly Info";				// アッセンブリ内容
			$aryWebName_Product["strspecificationdetails"]	= "Details";					// 仕様詳細

			$aryWebName_Product["dtminsertdate"]			= "Creation date";				// 作成日
			$aryWebName_Product["lnggoodsplanprogresscode"]	= "Plan status";				// 企画進行状況
			$aryWebName_Product["dtmrevisiondate"]			= "Revise date";				// 改訂日時
			$aryWebName_Product["lnginputusercode"]			= "Input person";				// 入力者
			
			$aryWebName_Column["detail"]					= "Detail";
			$aryWebName_Column["fix"]						= "Fix";
			$aryWebName_Column["delete"]					= "Delete";
			
		}

		$strQuery = "SELECT lnggroupcode, strgroupdisplaycolor FROM m_group";
		//echo "$strQuery";

		// クエリー実行 =====================================
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			//fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 値をとる =====================================
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

		
		// 数値型（1000→1,000)
		$aryToNumberOrDate[1] = "lngboxquantity";				// 内箱（袋）入数
		$aryToNumberOrDate[2] = "lngproductionquantity";		// 生産予定数
		$aryToNumberOrDate[3] = "lngfirstdeliveryquantity";		// 初回納品数
		$aryToNumberOrDate[4] = "lngcartonquantity";			// カートン入数
		$aryToNumberOrDate[5] = "lngroyalty";					// ロイヤリティ

		// 日付型（YYYY-MM-DD→YYYY/MM/DD)
		$aryToNumberOrDate[6] = "dtminsertdate";				// 作成日時
		$aryToNumberOrDate[7] = "dtmrevisiondate";				// 改訂日時
		$aryToNumberOrDate[8] = "dtmdeliverylimitdate";			// 納期

		$count1 = count($aryNewResult);

		$aryHtml[] = "<span id=\"COPYAREA1\">";
		$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
		for ( $i = 0; $i < $count1; $i++ )
		{
			// <TH>の生成 ========================================================
			if($i == 0)
			{
				$aryHtml[] = "<tr id=\"SegTitle\">";
				$aryHtml[] = "<td valign=\"top\" valign=\"center\"><a href=\"#\" onclick=\"fncDoCopy( copyhidden , document.getElementById('COPYAREA1') , document.getElementById('COPYAREA2') );return false;\"><img onmouseover=\"CopyOn(this);\" onmouseout=\"CopyOff(this);\" src=\"/img/type01/cmn/seg/copy_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"COPY\"></a></td>";

				// 詳細表示ボタンが表示設定されていれば
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

				$aryHtml[]  = "<tr class=\"Segs\" height=\"1\"><td bgcolor=\"#6f8180\"><img src=\"/img/_%lngLayoutCode%_/cmn/seg/dot.gif\" height=\"1\" width=\"1\"></td></tr>"; // ダミーTR
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

				// 表示変換するものを検索
				$lngArrayCount = array_search($strKeys, $aryToNumberOrDate);
				if ( $lngArrayCount )
				{
					// 表示文字列の形成
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
									$aryHtml[] = "全" . $aryNewResult[$i][$strKeys] . "種アッセンブリ";
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