<?php
/**
*       �����������ؿ���
*
*       @package   kuwagata
*       @license   http://www.wiseknot.co.jp/
*       @copyright Copyright &copy; 2003, Wiseknot
*       @author    Hiroki Watanabe <h-watanabe@wiseknot.co.jp>
*       @access    public
*       @version   1.00
*
*       ��������
*       
*	��������
*
*	2004.03.02	���ٹԤΥ����å��ؿ�����ñ������ȴ����ۤ� 0�� �׾塢�ޥ��ʥ��ͷ׾��ǧ���褦�˽���
*	2004.03.02	������������ɬ�ܥ����å���Ϥ���
*	2004.03.12	������ɽ�������Ƕ�ʬ�������оݤȰ�äƤ���Х��ν���
*
*/

	// fncDetailHidden_pc	$lngtaxcode = "0.05" ���ͤ���ޤäƤʤ��ΤǸ����ͤ�����ʸ��ľ����
	
	// -----------------------------------------------------------------
	/**		fncCheckData_pc()�ؿ�
	*
	*		submit���줿�ǡ���������å�����
	*		PO�ȤϹ��ܤ���̯�˰㤦
	*
	*		@param Array	$aryData			// submit���줿��
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------
	function fncCheckData_pc( $aryData, $strPart, $objDB )
	{
		// �إå���
		if($strPart == "header")
		{
			$aryCheck["dtmOrderAppDate"]		= "null:date";	// �׾���
			$aryCheck["lngStockCode"]			= "";			// �����Σϡ�
			$aryCheck["lngCustomerCode"]		= "null";		// ������
			//$aryCheck["lngInChargeGroupCode"]	= "null";		// ���祳����
			//$aryCheck["lngInChargeUserCode"]	= "null";		// ô����
			$aryCheck["lngLocationCode"]		= "null";		// Ǽ�ʾ��
			$aryCheck["dtmExpirationDate"] 		= "";			// ����������
			$aryCheck["lngOrderStatusCode"]		= "";			// ����(���ץ������)
			$aryCheck["lngMonetaryUnitCode"]	= "null";		// �̲�
			$aryCheck["strSlipCode"] 			= "null";		// Ǽ�ʽ�Σϡ�


			// �̲ߤ����ܱ߰ʳ��ξ��
			if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )
			{
				//$aryCheck["lngMonetaryRateCode"] = "number(0,99)";	// �졼�ȥ�����
				//$aryCheck["curConversionRate"]   = "null";			// �����졼��

				$aryCheck["lngPayConditionCode"] = "number(1,99,The list has not been selected.)";

				// ��ʧ���
				if( $_COOKIE["lngLanguageCode"] )
				{
					$aryCheck["lngPayConditionCode"] = "number(1,99,�ꥹ�Ȥ����򤵤�Ƥ��ޤ���)";
				}
			}
		}

		// ����
		else
		{
			$aryCheck["strProductCode"]			= "null";								// ����
			$aryCheck["strStockSubjectCode"]	= "number(1,999999999)";				// ��������
			$aryCheck["strStockItemCode"]		= "number(1,999999999)";				// ��������
			$aryCheck["lngConversionClassCode"]	= "null";								// ����ñ�̷׾�
			$aryCheck["lngProductUnitCode"]		= "null"; //:money(1,999999999999)";	// �ٻ�ñ��
			$aryCheck["lngGoodsQuantity"]		= "null:number(-999999999,999999999)";	// ���ʿ���
			$aryCheck["strDetailNote"]			= "";									// ����
			$aryCheck["lngTaxClassCode"]		= "null";								// �Ƕ�ʬ
			$aryCheck["curTotalPrice"]			= "null:money(-9999999999,9999999999)";	// ��ȴ���
		}


		// �����å��ؿ��ƤӽФ�
		$aryCheckResult  = fncAllCheck( $aryData, $aryCheck );

		list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );

		return array ( $aryData, $bytErrorFlag );
	}





	// -----------------------------------------------------------------
	/**		fncDetailHidden_pc()�ؿ�
	*
	*		������Ͽ�����������ٹԤ�hidden�ͤ��Ѵ�����
	*
	*		@param Array	$aryData			// ���ٹԤΥǡ���
	*		@param String	$strMode			// ��Ͽ�Ƚ�����Ƚ��(��ʸ����ʸ���ΰ㤤��������Ͽ��������ʸ����DB����������Ͼ�ʸ��
	*		@return	Array	$aryJScript			//
	*/
	// -----------------------------------------------------------------

	function fncDetailHidden_pc( $aryData, $strMode, $objDB)
	{
		if( $strMode == "insert" )
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// ���ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				// �������ܥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockSubjectCode]\" value=\"".$aryData[$i]["strStockSubjectCode"]."\">";
				// �������ʥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCode]\" value=\"".$aryData[$i]["strStockItemCode"]."\">";
				// ������ʬ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";
				// ���ʲ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";
				// ����ñ�̥�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";
				// ���ʿ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngGoodsQuantity"]."\">";


				// ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["org_lngGoodsQuantity"]."\">";


				// ������ˡ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngCarrierCode]\" value=\"".$aryData[$i]["lngCarrierCode"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";
				// �Ƕ�ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngTaxClassCode]\" value=\"".$aryData[$i]["lngTaxClassCode"]."\">";
				// ��Ψ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngTaxCode]\" value=\"".$aryData[$i]["lngTaxCode"]."\">";
				// �ǳ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTaxPrice]\" value=\"".$aryData[$i]["curTaxPrice"]."\">";
				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTotalPrice]\" value=\"".$aryData[$i]["curTotalPrice"]."\">";
				// �������ܤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockSubjectCodeName]\" value=\"".$aryData[$i]["strStockSubjectCodeName"]."\">";
				// �������ʤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCodeName]\" value=\"".$aryData[$i]["strStockItemCodeName"]."\">";
				// pcs�Ȥ���ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";
				// 
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";
				// 
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";
				// ���ꥢ��NO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strSerialNo]\" value=\"".$aryData[$i]["strSerialNo"]."\">";
				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";

				// �о�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngChkVal]\" value=\"".$aryData[$i]["lngChkVal"]."\">";
			}
		}
		else
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// 
				$lngconversionclasscode = ($aryData[$i]["lngconversionclasscode"] == 1) ? "gs" : "ps";
				// �Ƕ�ʬ��ɽ���ѡ�1:̵�� 2:����
// 2004.03.12 suzukaze update start
//				$lngtaxclasscode = ( $aryData[$i]["lngstocksubjectcode"] == "402" ||  $aryData[$i]["lngstocksubjectcode"] == 433 ) ? 1 : 2;
// 2004.03.12 suzukaze update end
				
				// ñ��̾��
				$lngProductUnitName = fncGetMasterValue("m_productunit", "lngproductunitcode", "strproductunitname", $aryData[$i]["lngproductunitcode"],'', $objDB );
				
				// ���ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngorderdetailno]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";
				// ���ʥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strproductcode]\" value=\"".$aryData[$i]["strproductcode"]."\">";
				// �������ܥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcode]\" value=\"".$aryData[$i]["lngstocksubjectcode"]."\">";
				// �������ʥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcode]\" value=\"".$aryData[$i]["lngstockitemcode"]."\">";
				// ������ʬ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngconversionclasscode]\" value=\"$lngconversionclasscode\">";
				// ���ʲ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductprice]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				// ����ñ�̥�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";
				// ���ʿ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodsquantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";


				// ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";


				// ������ˡ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngcarriercode]\" value=\"".$aryData[$i]["lngcarriercode"]."\">";
				// ����
				if ( $aryData[$i]["strnote"] == "" and $aryData[$i]["strdetailnote"] != "" )
				{
					$aryData[$i]["strnote"] = $aryData[$i]["strdetailnote"];
				}
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strdetailnote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strnote"])."\">";
				// �ǳ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curtaxprice]\" value=\"".$aryData[$i]["curtaxprice"]."\">";
				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curtotalprice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";
				
				$strStockSubjectName = "";
				$strStockSubjectName = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $aryData[$i]["lngstocksubjectcode"],'', $objDB );
				
				$strStockItemName = "";
				$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryData[$i]["lngstockitemcode"], "lngstocksubjectcode = ".$aryData[$i]["lngstocksubjectcode"],$objDB );
				
				// �������ܤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcodename]\" value=\"".$aryData[$i]["lngstocksubjectcode"]."  $strStockSubjectName\">";
				
				// �������ʤ�ɽ����value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcodename]\" value=\"".$aryData[$i]["lngstockitemcode"]."  $strStockItemName\">";
				// pcs�Ȥ���ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcodename]\" value=\"$lngProductUnitName\">";
// 2004.03.12 suzukaze update start
				// �Ƕ�ʬ��ɽ���ѡ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngtaxclasscode]\" value=\"" . $aryData[$i]["lngtaxclasscode"] . "\">";
				// ��Ψ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngtaxcode]\" value=\"\">";
				// ñ���ꥹ�ȡ�ɽ���ѡ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodspricecode]\" value=\"\">";
				// 
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductpriceforlist]\" value=\"\">";
				// ���ꥢ��NO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strserialno]\" value=\"".$aryData[$i]["strserialno"]."\">";
				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmdeliverydate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";

				// �о�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngChkVal]\" value=\"".$aryData[$i]["lngchkval"]."\">";
			}
		}


		$strDetailHidden = implode("\n", $aryDetailHidden);

		return $strDetailHidden;
	}
	


	// -----------------------------------------------------------------
	/**		fncChangeNumber()�ؿ�
	*
	*		t_StockDetail��t_OrderDetail�ǹ��ֹ椬��������Τ��Ф���
	*		���̤��Ѳ����Ƥ��뤫��Ĵ�٤롣�ѹ�����Ƥ�����ϡ���ȴ��ۡפ�׻�����
	*
	*		@param Array	$aryStock			// t_StockDetail�Υǡ���
	*		@param String	$aryOrder			// t_OrderDetail�Υǡ���
	*		@return	Array	$aryChangeNumber	// ¿����������֤�fncDetailHidden��¿���������Ѥ˺�äƤ��뤿��
	*/
	// -----------------------------------------------------------------

	function fncChangeNumber(  $aryOrder, $aryStock, $objDB )
	{
/*
for($i=0; $i<count($aryOrder); $i++)
{
	while(list($strKeys, $strValues ) = each($aryOrder[$i]))
	{
		echo "$i : $strKeys ������ $strValues<br>";
	}
	echo "<br><br>";
}
exit();

for($i=0; $i<count($aryStock); $i++)
{
	while(list($strKeys, $strValues ) = each($aryStock[$i]))
	{
		echo "$i : $strKeys +++++ $strValues<br>";
	}
	echo "<br><br>";
}
*/

		$number = 0;
		for ( $h = 0; $h < count( $aryOrder ); $h++ )
		{
			
			$lngProductQuantity = "";
			$curTotalPrice = "";
			
			for( $i = 0; $i < count( $aryOrder[$h] ); $i++ )
			{
				list ( $strKeys, $strValues ) = each( $aryOrder[$h] );
				
				
				
				if( $strKeys == "lngorderdetailno")									// ���ֹ�Υ����
				{
					
					reset($aryStock);
					for( $j = 0; $j < count( $aryStock ); $j++ )
					{
					
						if( $strValues == $aryStock[$j]["lngstockdetailno"])		// ���ֹ椬���������
						{
							
							// ñ�̤��������ʤ���С�����
							if( $aryOrder[$h][lngconversionclasscode] != $aryStock[$j][lngconversionclasscode] )
							{
							
								// ���ʤ��Ф��륫���ȥ�����������
								$lngCarton = fncGetMasterValue( "m_product", "strproductcode","lngcartonquantity", $aryOrder[$h]["strproductcode"].":str", '',$objDB );
								// echo "�����ȥ������ : $lngCarton<br>";
								// ���̤����
								// echo "����".$aryOrder[$h]["lngproductquantity"]."<br>";
								// echo "���̤����".$aryOrder[$h]["lngproductquantity"] * $lngCarton."<br>";
								
								
								$lngProductQuantity = $aryOrder[$h]["lngproductquantity"] * $lngCarton  - $aryStock[$j]["lngproductquantity"];
								// echo "��Ͽ�Ѥ߸Ŀ���".$aryStock[$j]["lngproductquantity"]."<br>";
								// echo "������ : $lngProductQuantity<br>";
								
								// ����ñ�������  �ٻ�ñ��(t_orderdetail)�५���ȥ�����
								$lngProductPrice = fncGetMasterValue("t_orderdetail", "strproductcode","curproductprice", $aryOrder[$h]["strproductcode"].":str", "lngorderdetailno=$strValues",$objDB );
								
								$curProductPrice = $lngProductPrice / $lngCarton;
								
								
								// ����ͤ���� �� (���ʿ��̡�����ñ��)
								// echo "���ʿ��̡� ����ñ��:$lngProductQuantity *** $curProductPrice<br>";
								$curTotalPrice = $lngProductQuantity * $curProductPrice ;
								$curTotalPrice = sprintf("%0.4f", $curTotalPrice);
								
								// echo "����ͤ���� : $curTotalPrice<br>";
								
								//$aryChangeNumber[$number][$strKeys]				= $strValues;						// ���ֹ�
								$ConversionClassFlg = "true";
								
							}
							else
							{
							
								// ���δؿ�����塦������Ƕ��̤���WEBɽ����̾�����㤦�ΤǢ�
								$lngQuantity = "";
								$lngQuantity = ( $aryOrder[$h]["lngproductquantity"] != "" ) ? $aryOrder[$h]["lngproductquantity"] : $aryOrder[$h]["lnggoodsquantity"];
								
								// echo "lngQuantity : $lngQuantity  - ".$aryStock[$j]["lngproductquantity"]."<br>";
								
								$lngProductQuantity = ( $lngQuantity ) - ( $aryStock[$j]["lngproductquantity"]);
								// echo "lngProductQuantity : $lngProductQuantity<br>";
								
								// ��׶��
								$curProductPrice = $aryOrder[$number]["curproductprice"];
								$curTotalPrice = $aryOrder[$number]["curproductprice"] * $lngProductQuantity;
								
								$curTotalPrice = sprintf("%0.4f", $curTotalPrice);
								// echo "��׶�� : $curTotalPrice<br>";
								
								//$aryChangeNumber[$number][$strKeys]				= $strValues;						// ���ֹ�
								
							}
							
							// ȯ��Ĺ�
							$lngZandaka = $aryOrder[$h][cursubtotalprice] - ($aryOrder[$h][cursubtotalprice] - $curTotalPrice);
							
							// echo "�Ŀ���$lngProductQuantity<br>";
							// echo "ȯ��Ĺ� : $lngZandaka<br>";
							
							if( $lngProductQuantity <= 0 || $lngZandaka <= 0 )
							{
								// ��0�פξ�硢�����촰λ
								$totalflg = "true";
								break;
							}
							else
							{
								//$aryChangeNumber[$number][$strKeys]				= $strValues;						// ���ֹ�
							}
						}

					}
					
					if( $j == count( $aryStock ) )
					{
						$aryChangeNumber[$number][$strKeys] = $strValues;
					}
				}
				else
				{
				
					if( $totalflg != "true" )		// ȯ��Ŀ���������Ŀ���������ͤ���0�פǤʤ���С���
					{
						if( $strKeys == "cursubtotalprice" )					// ��׶��
						{
							$aryChangeNumber[$number]["cursubtotalprice"] = ( $curTotalPrice != "") ? $curTotalPrice : $strValues ; 
						}
						elseif( $strKeys == "lngproductquantity" )				// ���ʿ���
						{
						
							$aryChangeNumber[$number]["lngproductquantity"] = ( $lngProductQuantity != "" ) ? $lngProductQuantity : $strValues ;
							
							// ���ʿ���(���ǻ��ѡ��������̾�����㤦�Τǡ�����
							$aryChangeNumber[$number]["lnggoodsquantity"] = ( $lngProductQuantity != "" ) ? $lngProductQuantity : $strValues ;
						}
						elseif( $strKeys == "lngconversionclasscode" )			// ����ñ�̷׾塧�ٻѷ׾�
						{
							$aryChangeNumber[$number]["lngconversionclasscode"] = ( $ConversionClassFlg != "" ) ? 1 : $strValues;
						}
						elseif( $strKeys == "curproductprice")
						{
							$aryChangeNumber[$number]["curproductprice"] = ( $curProductPrice != "" ) ? $curProductPrice : $strValues;
						}
						elseif( $strKeys == "lngproductunitcode")
						{
							$aryChangeNumber[$number]["lngproductunitcode"] = ( $ConversionClassFlg != "" ) ? 1 : $strValues;
						}
						else
						{
							// lngreceiveno�����ǻ��ѡ��㳰��
							if( $strKeys != "lngreceiveno" )
							{
								$aryChangeNumber[$number][$strKeys] = $strValues;
							}
						}
					}
				}
				
			}
			
			if( $totalflg != "true")
			{
				$number++;
			}
			
			$totalflg = "";
			$ConversionClassFlg = "";
			$curTotalPrice = "";
			$lngProductQuantity = "";
			$curProductPrice = "";
		}
		
		$number = 0;
		
		return $aryChangeNumber;

	}

	// -----------------------------------------------------------------
	/**		fncOutPut()�ؿ�
	*
	*		fncChangeNumber�Ƿ׻����줿�ͤ����ơ�����ñ���פǽ��Ϥ���Ƥ��롣
	*		���δؿ��ǤϺǸ�˥��󥵡��Ȥ��줿ñ�̤�Ĵ�١�����ñ�̤ˤ��碌�ƽ��Ϥ���
	*
	*
	*		lngstockdetailno		// ���ֹ�Υ����̾
	*		lngproductquantity		// ���̤Υ����̾
	*		lngconversionclasscode	// ����ñ����1��:: �ٻѡ�2��
	*
	*		@param Array	$aryDataA				// fncChangeNumber���Ѵ����줿��
	*		@param Array	$aryDataB				// ���󥵡��Ȥ��줿�ǡ����ʹ��ֹ桧ñ��������
	*		@return	Array	$aryOutPut				// 
	*/
	// -----------------------------------------------------------------
	
	function fncOutPut( $aryDataA, $aryDataB, $objDB )
	{
	
		for( $i = 0; $i < count( $aryDataA ); $i++ )
		{
			for( $j = 0; $j < count( $aryDataB ); $j++ )
			{
			
				if( $aryDataA[$i]["lngorderdetailno"] == $aryDataB[$j]["lngstockdetailno"] )	// ���ֹ椬���������
				{
					if( $aryDataA[$i]["lngconversionclasscode"] != $aryDataB[$i]["lngconversionclasscode"] )
					{
						//��껻
						$lngCarton = fncGetMasterValue( "m_product", "strproductcode", "lngcartonquantity", $aryDataB[$j]["strproductcode"].":str", '',$objDB );
						//echo "carton : $lngCarton<br>";
						// �ٻѿ���  ���ʿ��̡५���ȥ�����
						$lngCartonQuanty = $aryDataA[$i]["lngproductquantity"] / $lngCarton;
						//echo "�ٻѿ��� : $lngCartonQuanty<br>"; 
						
						// �ٻ�ñ��  ����ñ���ߥ����ȥ�����
						$curProductPrice = $aryDataA[$i]["curproductprice"] * $lngCarton;
						//echo "�ٻ�ñ�� : $curProductPrice<br>";
						
						
						
						$aryOutPut[$i]["lngorderdetailno"]			= $aryDataA[$i]["lngorderdetailno"];
						$aryOutPut[$i]["lngrevisionno"]				= $aryDataA[$i]["lngrevisionno"];
						$aryOutPut[$i]["strproductcode"]			= $aryDataA[$i]["strproductcode"];
						$aryOutPut[$i]["lngstocksubjectcode"]		= $aryDataA[$i]["lngstocksubjectcode"];
						$aryOutPut[$i]["lngstockitemcode"]			= $aryDataA[$i]["lngstockitemcode"];
						$aryOutPut[$i]["dtmdeliverydate"]			= $aryDataA[$i]["dtmdeliverydate"];
						$aryOutPut[$i]["lngcarriercode"]			= $aryDataA[$i]["lngcarriercode"];
						$aryOutPut[$i]["lngconversionclasscode"]	= $aryDataB[$j]["lngconversionclasscode"];
						$aryOutPut[$i]["curproductprice"]			= $curProductPrice;
						$aryOutPut[$i]["lngproductquantity"]		= $lngCartonQuanty;
						$aryOutPut[$i]["lngproductunitcode"]		= 2;
						$aryOutPut[$i]["lngtaxclasscode"]			= $aryDataA[$i]["lngtaxclasscode"];
						$aryOutPut[$i]["lngtaxcode"]				= $aryDataA[$i]["lngtaxcode"];
						$aryOutPut[$i]["curtaxprice"]				= $aryDataA[$i]["curtaxprice"];
						$aryOutPut[$i]["cursubtotalprice"]			= $aryDataA[$i]["cursubtotalprice"];
						$aryOutPut[$i]["strnote"]					= $aryDataA[$i]["strnote"];
						
						$aryOutPut[$i]["lnggoodsquantity"]			= $lngCartonQuanty;							// ���ǻ���
						$aryOutPut[$i]["lngsalesclasscode"]			= $aryDataA[$i]["lngsalesclasscode"];		// ���ǻ���
						
						$flg = "true";
					}
				}
			}
			
			if($flg == "")
			{
				$aryOutPut[$i] = $aryDataA[$i];
			}
			$flg = "";
		}
		
		return $aryOutPut;
	}

	
	
	
	
	
	
	
	// -----------------------------------------------------------------
	/**		fncNewDetail()�ؿ�
	*
	*		�ٻ�ñ�̤���Ͽ���줿��Τ����椫������ñ�����ѹ����줿���ν�����Ԥ�
	*		����ñ����A���ٻ�ñ�̡�B�Ȥ�����粼���Υѥ�����¸�ߤ���
	*
	*		    |  A  |  B
	*		----+-----+-----
	*		  A |  1  |  2
	*		  B |  2  |  1
	*
	*		1:���Τޤ�
	*		2:A��B��Ʊ��ιԿ��ֹ����äƤ��뢪����ñ�����Ѵ�����
	*
	*		lngstockdetailno		// ���ֹ�Υ����̾
	*		lngproductquantity		// ���̤Υ����̾
	*		lngconversionclasscode	// ����ñ����1��:: �ٻѡ�2��
	*
	*		@param Array	$aryDataA				// ����ñ�������ٹ�
	*		@param Array	$aryDataB				// �ٻ�ñ�������ٹ�
	*		@return	Array	$aryChangeNumber		// ���ֹ椬���������ߤ���ñ�����㤦��Τ������֤�(����ñ���ˤ����֤���
	*/
	// -----------------------------------------------------------------

	function fncNewDetail(  $aryDataA, $aryDataB ,$objDB )
	{	
	
		// A������ñ�̷׾�ˤ�B�ʲٻѡˤ�ξ����������ͤ����äƤ�����(B��A���Ѵ������
		if(is_array($aryDataA) && is_array($aryDataB) )
		{
			// B�ʲٻѡˤι��ֹ�����
			for( $i = 0; $i < count( $aryDataB ); $i++ )
			{	
				while( list ($strKeys, $strValues ) = each($aryDataB[$i]) )
				{
					if($strKeys == "lngstockdetailno")
					{
						$aryCheckGyou[] = $strValues.":".$i; // ���ֹ�������ֹ���ݻ��������ֹ�Ϲ�פǻȤ���
					}

				}
			}
			// print_r( $aryCheckGyou );
		
			// A������ñ�̷׾�ˤ�Ʊ�����ֹ椬���뤫������
			for($i = 0; $i < count( $aryDataA ); $i++ )
			{
				$lnggyou  = $aryDataA[$i][lngstockdetailno];

				reset($aryCheckGyou);
				while( list( $strKeys, $strValues ) = each( $aryCheckGyou ) )
				{
					list( $strKeys2, $strValues2 ) = explode( ":", $strValues );
					
					if( $lnggyou == $strKeys2 )
					{
						$aryNewData[] = $strKeys2.":".$strValues2; // ���ֹ�������ֹ���ݻ��������ֹ�Ϲ�פǻȤ���
					}
				}

			}
			
			
			// print_r($aryNewData);
			// A������ñ�̷׾�ˤ�B�ʲٻѡˤ��礹���$aryNewData�������Կ��ʳ����͡�
			$aryNewData2 = array_merge($aryDataA, $aryDataB);
			
			if(is_array( $aryNewData ) )
			{
				for( $i = 0; $i < count( $aryNewData2 ); $i++ )
				{
					reset($aryNewData);
					while (list ( $strKeys2, $strValues2 ) = each( $aryNewData ))
					{
						list( $strKeys2, $strValues2 ) = explode(":", $strValues2 );
						
						if( $aryNewData2[$i]["lngstockdetailno"] == $strKeys2 )
						{
							$flg = "true";
						}
					}
					
					if($flg != "true" )
					{
						$aryNewDataA[] =  $aryNewData2[$i];
					}
					$flg = "";
				}
			}

		
		
			// A������ñ�̷׾�ˤ�B�ʲٻѡˤ�Ʊ�����ֹ椬¸�ߤ�����:���������Τ���������˳�Ǽ
			
			if(is_array( $aryNewData ) )
			{
				for( $i = 0; $i < count( $aryDataA ); $i++ )
				{
					reset( $aryNewData );
					while( list ( $strKyes, $strValues ) = each( $aryNewData ) )
					{
					
						list( $strKeys2, $strValues2 ) = explode(":", $strValues );
						// �ֲٻѡפ�ñ�̤�����ʡפ�ñ���ˤ���
						
						if($aryDataA[$i][lngstockdetailno] == $strKeys2 )
						{
							// ���ʤ��Ф��륫���ȥ�����������
							$lngCarton = fncGetMasterValue( "m_product", "strproductcode","lngcartonquantity" , $aryDataB[$strValues2]["strproductcode"].":str", '',$objDB );
							//echo "�Ѵ�����ñ�̡�".$aryDataB[$strValues2][lngproductquantity] * $lngCarton."<br>";
							//echo "���̡�".$aryDataB[$strValues2][lngproductquantity]."<br>";
							//echo "�����ȥ�$lngCarton<br>";
							$lngQuanty = ( $aryDataB[$strValues2][lngproductquantity] * $lngCarton ) + $aryDataA[$i][lngproductquantity];
							
							$aryNewDataB[$i][lngstockdetailno]			= "$strKeys2";									// ���ֹ�
							$aryNewDataB[$i][lngproductquantity]		= "$lngQuanty";									// ���̡�����ñ�����Ѵ������͡�
							$aryNewDataB[$i][lngconversionclasscode]	= 1;											// ����ñ���ˤ˥ե饰���ѹ�����
							$aryNewDataB[$i][strproductcode]			= $aryDataB[$strValues2]["strproductcode"]; 	// ɬ�פʤ������Τ�ʤ����ɰ��¾�˹�碌��
							
						}
					}
				}

				$aryNewData2 = array_merge( $aryNewDataA, $aryNewDataB );
				//print_r($aryNewData);

			}
			// (A=�� B=����Ʊ�����ֹ椬¸�ߤ��ʤ����)
			else
			{
				$aryNewData2 = array_merge($aryDataA, $aryDataB);
				
			}
			
		}
		else
		{
			$aryNewData2 = array_merge($aryDataA, $aryDataB);
		}
		
		
		
		return $aryNewData2;
	}

?>
