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

		// 0:部門コード
		// 1:担当者
		// 2:顧客
		// 3:生産工場
		// 4:アッセンブリ工場
		// 5:納品場所
		
		// displaycode→codeに変換
		// displaycodeが入っていなくdisplaynameが入っている場合はdisplaynameからdisplaycodeを引いてくる
		$aryTrueCode[0] = fncGetCode( $aryData["lngInChargeGroupCode"], $aryData["strInChargeGroupName"],m_group, lnggroupcode, strgroupdisplay, ' and bytgroupdisplayflag = true',$objDB );
		$aryTrueCode[1] = fncGetCode( $aryData["lngInChargeUserCode"], $aryData["strInChargeUserName"], m_user, lngusercode, struserdisplay,'', $objDB);
		$aryTrueCode[2] = fncGetCode( $aryData["lngCustomerCompanyCode"], $aryData["strCustomerCompanyName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		$aryTrueCode[3] = fncGetCode( $aryData["lngFactoryCode"], $aryData["strFactoryName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		$aryTrueCode[4] = fncGetCode( $aryData["lngAssemblyFactoryCode"], $aryData["AssemblyFactoryName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);
		$aryTrueCode[5] = fncGetCode( $aryData["lngDeliveryPlaceCode"], $aryData["DeliveryPlaceName"], m_company, lngcompanycode, strcompanydisplay, '',$objDB);

		
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
						
							if( count( $aryTrueCode[$n] ) == 1)						// TrueCodeに値が複数入っているか？
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
							//ここにaryQueryWhereをついかすれば
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
								$aryQueryWhere[] = "g.dtmRevisionDate = To_Date('". $strValues2 ."', 'YYYY-MM-DD') AND";
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
	*       dispalaycode → displayNameへ変換関数
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
		$aryOptionCode[0] = "lngrevisionno";					// 0:証紙
		$aryOptionCode[1] = "lngcertificateclasscode";			// 1:版権元
		$aryOptionCode[2] = "lngcopyrightcode";					// 2:商品形態
		$aryOptionCode[3] = "lngpackingunitcode";				// 3:荷姿単位
		$aryOptionCode[4] = "lngproductunitcode";				// 4:製品単位
		$aryOptionCode[5] = "lngproductionunitcode";			// 5:生産予定数の単位
		$aryOptionCode[6] = "lngfirstdeliveryunitcode";			// 6:初回納品数の単位
		$aryOptionCode[7] = "lngtargetagecode";					// 7:対象年齢
		$aryOptionCode[8] = "lngcertificateclasscode";			// 8:証紙
		$aryOptionCode[9] = "lngcopyrightcode";					// 9:版権元
		$aryOptionCode[10] = "lngproductformcode";				// 10:商品形態
		
	
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
	function fncCreateHtml ( $aryNewResult ,$lngSortNumber, $strSession, $renew ,$del,$objDB)
	{
//echo "修正表示 : $renew<br>";
//echo "削除表示 : $del<br>";


		// dispalaycode → displayNameへ変換 =================================================
		// 0:部門コード
		// 1:担当者
		// 2:顧客
		// 3:生産工場
		// 4:アッセンブリ工場
		// 5:納品場所
		
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

				
		// 日本語コード
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
		$aryWebName_Product["strcustomerusername"]		= "顧客担当者";					// 顧客担当者
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
		$aryWebName_Product["strcopyrightdisplaystamp"]	= "版権表示（刻印）";			// 版権表示（刻印）
		$aryWebName_Product["strcopyrightdisplayprint"]	= "版権表示（印刷物）";			// 版権表示（印刷物）
		$aryWebName_Product["lngproductformcode"]		= "商品形態";					// 商品形態
		$aryWebName_Product["strproductcomposition"]	= "製品構成";					// 製品構成
		$aryWebName_Product["strassemblycontents"]		= "アッセンブリ内容";			// アッセンブリ内容
		$aryWebName_Product["strspecificationdetails"]	= "仕様詳細";					// 仕様詳細

		$aryWebName_Product["dtminsertdate"]			= "作成日";						// 作成日
		$aryWebName_Product["lngrevisionno"]			= "企画進行状況";				// 企画進行状況
		$aryWebName_Product["dtmrevisiondate"]			= "改訂日時";					// 改訂日時
		
		
		// 英語コード
		$aryWebName_Product_ENG["strproductcode"]			= "Products code";			// 製品コード
		$aryWebName_Product_ENG["strproductname"]			= "Products name(ja)";		// 製品名称(日本語)
		$aryWebName_Product_ENG["strproductenglishname"]	= "Products name(en)";		// 製品名称(英語)
		$aryWebName_Product_ENG["lnginchargegroupcode"]		= "Dept";					// 部門
		$aryWebName_Product_ENG["lnginchargeusercode"]		= "In charge name";			// 担当者
		$aryWebName_Product_ENG["strgoodscode"]				= "Goods code(Corresp)";	// 顧客品番
		$aryWebName_Product_ENG["strgoodsname"]				= "Goods name";				// 商品名称
		$aryWebName_Product_ENG["lngpackingunitcode"]		= "Packing unit";			// 荷姿単位
		$aryWebName_Product_ENG["lngboxquantity"]			= "Box Qty";				// 内箱（袋）入数
		$aryWebName_Product_ENG["lngcartonquantity"]		= "Carton Qty";				// カートン入数
		$aryWebName_Product_ENG["lngproductunitcode"]		= "Products Unit";			// 製品単位
		$aryWebName_Product_ENG["lngproductionquantity"]	= "Refound Qty";			// 生産予定数
		$aryWebName_Product_ENG["lngcustomercompanycode"]	= "Vendor";					// 顧客
		$aryWebName_Product_ENG["strcustomerusername"]		= "In charge name";			// 顧客担当者
		$aryWebName_Product_ENG["lngfactorycode"]			= "Creation fact";			// 生産工場
		$aryWebName_Product_ENG["lngfirstdeliveryquantity"]	= "Delivery Qty";			// 初回納品数
		$aryWebName_Product_ENG["lngassemblyfactorycode"]	= "Assembly fact";			// アッセンブリ工場
		$aryWebName_Product_ENG["lngdeliveryplacecode"]		= "Location";				// 納品場所
		$aryWebName_Product_ENG["dtmdeliverylimitdate"]		= "Deli date";				// 納期
		$aryWebName_Product_ENG["curproductprice"]			= "Wholesale price";		// 卸値
		$aryWebName_Product_ENG["curretailprice"]			= "Selling price";			// 売値
		$aryWebName_Product_ENG["lngtargetagecode"]			= "Age";					// 対象年齢
		$aryWebName_Product_ENG["lngcertificateclasscode"]	= "Inspection";				// 証紙
		$aryWebName_Product_ENG["lngroyalty"]				= "Loyalty";				// ロイヤリティ
		$aryWebName_Product_ENG["lngcopyrightcode"]			= "Copyright";				// 版権元
		$aryWebName_Product_ENG["strcopyrightdisplaystamp"]	= "Copyright(Stamp)";		// 版権表示（刻印）
		$aryWebName_Product_ENG["strcopyrightdisplayprint"]	= "Copyright(Print)";		// 版権表示（印刷物）
		$aryWebName_Product_ENG["lngproductformcode"]		= "Goods form";				// 商品形態
		$aryWebName_Product_ENG["strproductcomposition"]	= "Products Info";			// 製品構成
		$aryWebName_Product_ENG["strassemblycontents"]		= "Assembly Info";			// アッセンブリ内容
		$aryWebName_Product_ENG["strspecificationdetails"]	= "Details";				// 仕様詳細

		$aryWebName_Product_ENG["dtminsertdate"]			= "Creation date";				// 作成日
		$aryWebName_Product_ENG["lngrevisionno"]			= "Plan status";				// 企画進行状況
		$aryWebName_Product_ENG["dtmrevisiondate"]			= "Revise date";				// 改訂日時
		
		
		
		
		// 数値型（1000→1,000)
		$aryToNumber[0] = "lngboxquantity";				// 内箱（袋）入数
		$aryToNumber[1] = "lngproductionquantity";		// 生産予定数
		$aryToNumber[2] = "lngfirstdeliveryquantity";	// 初回納品数
		$aryToNumber[3] = "lngroyalty";					// ロイヤリティ


		$count1 = count($aryNewResult);
		//$count2 = 10;
		
		$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
		for ( $i = 0; $i < $count1; $i++ )
		{
			
			
			
			// <TH>の生成 ========================================================
			if($i == 0)
			{
			$aryHtml[] = "<tr id=\"SegTitle\">";
				$aryHtml[] = "<td>&nbsp;</td>";
				$aryHtml[] = "<td nowarp>詳細</td>";
				if(strcmp($renew, "") != 0 )
				{
					$aryHtml[] = "<td nowarp>修正</td>";
				}
				if(strcmp($del, "") != 0)
				{ 
					$aryHtml[] = "<td nowarp>削除</td>";
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
			$aryHtml[] = "<td><a class=\"cells\" href=\"javascript:fncSubwin('./index2.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession')\">●</a></td>";
			if(strcmp($renew, "") != 0 )
			{
				$aryHtml[] = "<td><a class=\"cells\" href=\"javascript:fncSubwin('../regist/renew.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession')\">▲</a></td>";
			}
			
			if(strcmp($del, "") != 0)
			{
				$aryHtml[] = "<td><a class=\"cells\" href=\"javascript:fncSubwin('./index3.php?strProductCode=" .$aryNewResult[$i]["strproductcode"]. "&strSessionID=$strSession')\">■</a></td>";
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