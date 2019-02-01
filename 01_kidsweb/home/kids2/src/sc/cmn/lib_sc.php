<?php
/**
*       ���������ؿ���
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
*	2004.03.03	���ٹԤΥ����å��ؿ�����Ǽ�����ܤ�ɬ�ܤ�������å���Ϥ����褦�˽���
*
*/


	
	// -----------------------------------------------------------------
	/**		fncCheckData_sc()�ؿ�
	*
	*		submit���줿�ǡ���������å�����
	*		PO�ȤϹ��ܤ���̯�˰㤦
	*
	*		@param Array	$aryData			// submit���줿��
	*		@param Object	$objDB				// DB��³���֥�������
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------
	
	function fncCheckData_sc( $aryData, $strPart, $objDB )
	{
		if($strPart == "header")
		{
			$aryCheck["dtmOrderAppDate"]				= "null:date";					// �׾���
			//$aryCheck["strSalesCode"]					= "null";					// ���Σϡ�
			$aryCheck["lngCustomerCode"]				= "null";					// �ܵ�
			//$aryCheck["lngInChargeGroupCode"]			= "null";					// ���祳����
			//$aryCheck["lngInChargeUserCode"]			= "null";					// ô����

			//if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )		//�̲ߤ����ܰʳ�
			//{
			//	$aryCheck["lngMonetaryRateCode"]		= "number(0,99)";			// �졼�ȥ�����
			//	$aryCheck["curConversionRate"]			= "null";					// �����졼��
			//}
			$aryCheck["strSlipCode"]					= "null";					// Ǽ�ʽ�Σϡ�
		}
		else
		// ���ٹԤ�¾�Υѡ��Ȥ�Ʊ��header�ȥ��åȤˤ��Ƥ�������
		{
			$aryCheck["strProductCode"]					= "null";					// ����
			$aryCheck["lngConversionClassCode"]			= "null";					// ����ñ�̷׾�
			$aryCheck["lngProductUnitCode"]				= "null";					// �ٻ�ñ��
			$aryCheck["lngGoodsQuantity"]				= "null:number(-999999999,999999999)";// ���ʿ���
			$aryCheck["lngTaxClassCode"]				= "null";					// �Ƕ�ʬ
//			$aryCheck["dtmDeliveryDate"]				= "null";					// Ǽ��
			$aryCheck["curTotalPrice"]					= "null:money(-9999999999,9999999999)";					// ��ȴ���

		}
		
		// �����å��ؿ��ƤӽФ�
		$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
		
		list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
		
		return array ( $aryData, $bytErrorFlag );
	
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncDetailHidden_sc()�ؿ�
	*
	*		������Ͽ�����������ٹԤ�hidden�ͤ��Ѵ�����
	*
	*		@param Array	$aryData			// ���ٹԤΥǡ���
	*		@param String	$strMode			// ��Ͽ�Ƚ�����Ƚ��(��ʸ����ʸ���ΰ㤤��������Ͽ��������ʸ����DB����������Ͼ�ʸ��
	*		@return	Array	$aryJScript			//
	*/
	// -----------------------------------------------------------------

	function fncDetailHidden_sc( $aryData, $strMode, $objDB, &$lngPlusCnt=0)
	{
		//require_once( LIB_DEBUGFILE );

		if( $strMode == "insert" )
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				//fncDebug( 'sc_detail.txt', $aryData[$i]["org_lngGoodsQuantity"], __FILE__, __LINE__);

				// ���ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				// ������ʬ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";
				// ���ʲ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";
				// ����ñ�̥�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";

				// ���ʿ���
				if ( $aryData[$i]["lngProductQuantity"] == "" and $aryData[$i]["lngGoodsQuantity"] != "" )
				{
					$aryData[$i]["lngProductQuantity"] = $aryData[$i]["lngGoodsQuantity"];
				}

				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngProductQuantity"]."\">";

				// ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["org_lngGoodsQuantity"]."\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";
				// �Ƕ�ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxClassCode]\" value=\"".$aryData[$i]["lngTaxClassCode"]."\">";
				// ��Ψ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxCode]\" value=\"".$aryData[$i]["lngTaxCode"]."\">";
				// �ǳ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTaxPrice]\" value=\"".$aryData[$i]["curTaxPrice"]."\">";
				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTotalPrice]\" value=\"".$aryData[$i]["curTotalPrice"]."\">";
				
				// pcs�Ȥ���ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";
				// ñ���ꥹ�ȡ�ɽ���ѡ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPriceForList]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				
				// ����ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCode]\" value=\"".$aryData[$i]["lngSalesClassCode"]."\">";
				// ����ʬ��value + ̾��)
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCodeName]\" value=\"".$aryData[$i]["lngSalesClassCodeName"]."\">";
				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";

				// �����ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveNo]\" value=\"".$aryData[$i]["lngReceiveNo"]."\">";

				// ���ٹ��ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveDetailNo]\" value=\"".$aryData[$i]["lngReceiveDetailNo"]."\">";

				// �о�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngChkVal]\" value=\"".$aryData[$i]["lngChkVal"]."\">";
			}
		}
		else
		{
		// ������
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				//$lngconversionclasscode = ($aryData[$i]["lngconversionclasscode"] == 1) ? "gs" : "ps";

				// ñ��̾��
				$lngProductUnitName = fncGetMasterValue("m_productunit", "lngproductunitcode", "strproductunitname", $aryData[$i]["lngproductunitcode"],'', $objDB );
				// ����ʬ
				$lngSalesClassCodeName = fncGetMasterValue("m_salesclass", "lngsalesclasscode", "strsalesclassname", $aryData[$i]["lngsalesclasscode"], '', $objDB );

				$lngConversionClassCode = ( $aryData[$i]["lngconversionclasscode"] == 1 ) ? "gs" : "ps";

				// ���ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";
				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strProductCode]\" value=\"".$aryData[$i]["strproductcode"]."\">";
				// ������ʬ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngConversionClassCode]\" value=\"$lngConversionClassCode\">";
				// ���ʲ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPrice]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				// ����ñ�̥�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";

				// ���ʿ���
				if ( $aryData[$i]["lngproductquantity"] == "" and $aryData[$i]["lnggoodsquantity"] != "" )
				{
					$aryData[$i]["lngproductquantity"] = $aryData[$i]["lnggoodsquantity"];
				}

				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";

				// ������
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strdetailnote"])."\">";
				// �Ƕ�ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxClassCode]\" value=\"".$aryData[$i]["lngtaxclasscode"]."\">";
				// ��Ψ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxCode]\" value=\"".$aryData[$i]["lngtaxcode"]."\">";
				// �ǳ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTaxPrice]\" value=\"".$aryData[$i]["curtaxprice"]."\">";
				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTotalPrice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";
				
				// pcs�Ȥ���ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCodeName]\" value=\"$lngProductUnitName\">";
				// ñ���ꥹ�ȡ�ɽ���ѡ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPriceForList]\" value=\"\">";
				
				// ����ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCode]\" value=\"".$aryData[$i]["lngsalesclasscode"]."\">";
				// ����ʬ��value + ̾��)
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCodeName]\" value=\"$lngSalesClassCodeName\">";
				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";

				// �����ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveNo]\" value=\"".$aryData[$i]["lngreceiveno"]."\">";

				// ���ٹ��ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveDetailNo]\" value=\"".$aryData[$i]["lngreceivedetailno"]."\">";

				// �о�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngChkVal]\" value=\"".$aryData[$i]["lngchkval"]."\">";
			}
		
		}

		// �����󥿰��Ѥ���
		$lngPlusCnt = $i + $lngPlusCnt;
	
		$strDetailHidden = implode("\n", $aryDetailHidden);
		//echo htmlspecialchars( $strDetailHidden );
		
		return $strDetailHidden;
	}





	// -----------------------------------------------------------------
	/**		fncOutPut_sc()�ؿ�
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
	*		@param Array	$aryDataB				// �����Ȥ˥��󥵡��Ȥ��줿�ǡ����ʹ��ֹ桧ñ��������
	*		@return	Array	$aryOutPut				// 
	*/
	// -----------------------------------------------------------------

	function fncOutPut_sc( $aryDataA, $aryDataB, $objDB )
	{

		for( $i = 0; $i < count( $aryDataA ); $i++ )
		{
			for( $j = 0; $j < count( $aryDataB ); $j++ )
			{
			
				if( $aryDataA[$i]["lngorderdetailno"] == $aryDataB[$j]["lngstockdetailno"] )	// ���ֹ椬���������
				{
					//echo "�ԡ�$i ".$aryDataA[$i]["lngconversionclasscode"] ." != ". $aryDataB[$i]["lngconversionclasscode"]."<br>";

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


						$aryOutPut[$i]["lngorderdetailno"]			= $aryDataA[$i]["lngorderdetailno"];				// ���ֹ�
						$aryOutPut[$i]["strproductcode"]			= $aryDataA[$i]["strproductcode"];					// �����ֹ�
						$aryOutPut[$i]["lngrevisionno"]				= $aryDataA[$i]["lngrevisionno"];					// 
						$aryOutPut[$i]["lngsalesclasscode"]			= $aryDataA[$i]["lngsalesclasscode"];				// ����ʬ
						$aryOutPut[$i]["dtmdeliverydate"]			= $aryDataA[$i]["dtmdeliverydate"];					// Ǽ��
						$aryOutPut[$i]["lngconversionclasscode"]	= $aryDataB[$j]["lngconversionclasscode"];			// ������ʬ������
						$aryOutPut[$i]["curproductprice"]			= $curProductPrice;									// ���ʲ���
						$aryOutPut[$i]["lnggoodsquantity"]			= $lngCartonQuanty;									// ���ʿ���
						$aryOutPut[$i]["lngproductunitcode"]		= 2;												// ����ñ�̥�����
						$aryOutPut[$i]["lngtaxclasscode"]			= $aryDataA[$i]["lngtaxclasscode"];					
						$aryOutPut[$i]["lngtaxcode"]				= $aryDataA[$i]["lngtaxcode"];						
						$aryOutPut[$i]["curtaxprice"]				= $aryDataA[$i]["curtaxprice"];						
						$aryOutPut[$i]["cursubtotalprice"]			= $aryDataA[$i]["cursubtotalprice"];				// ��ȴ���
						$aryOutPut[$i]["strdetailnote"]				= $aryDataA[$i]["strdetailnote"];					// ����

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

?>