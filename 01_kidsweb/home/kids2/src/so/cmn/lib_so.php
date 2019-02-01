<?php

// ----------------------------------------------------------------------------
/**
*       �������  �ؿ��饤�֥��
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
*         �������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ����ɽ��
*
*       ��������
*         V1
*         ��2004.03.02  ���ٹԤΥ����å��ؿ�����ñ������ȴ����ۤ� 0�� �׾塢�ޥ��ʥ��ͷ׾��ǧ���褦�˽���
*         ��2004.03.29  fncDetailHidden�ؿ������ٹ��ֹ�ˤĤ��Ƥ��Ϥ��褦�˽���
*         V2
*         ��2005.10.14  
*/
// ----------------------------------------------------------------------------



	// ------------------------------------------------------------------------
	/**
	*   fncDetailHidden_pc() �ؿ�
	*
	*   ��������
	*     ��������Ͽ�����������ٹԤ�hidden�ͤ��Ѵ�����
	*
	*   @param   $aryData     [Array]   ���ٹԤΥǡ���
	*   @param   $strMode     [String]  ��Ͽ�Ƚ�����Ƚ��(��ʸ����ʸ���ΰ㤤��������Ͽ��������ʸ����DB���黲�Ȼ��Ͼ�ʸ��
	*   @return  $aryJScript  [Array]
	*/
	// ------------------------------------------------------------------------
	function fncDetailHidden_so( $aryData, $strMode, $objDB)
	{
		//-------------------------------------------------
		// ������Ͽ��
		//-------------------------------------------------
		if( $strMode == "insert" )
		{
			for($i = 0; $i < count( $aryData ); $i++ )
			{
				// ���ٹ��ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				
				//���֥����å�
//				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strGoodsCode]\" value=\"".$aryData[$i]["strGoodsCode"]."\">";

				// ñ���ꥹ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";

				// ������ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";

				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";

				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";

				// ñ�̡�̾�Ρ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngGoodsQuantity"]."\">";

				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTotalPrice]\" value=\"".$aryData[$i]["curTotalPrice"]."\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";

				// ñ���ꥹ���ɲåǡ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";

				// ����ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCode]\" value=\"".$aryData[$i]["lngSalesClassCode"]."\">";

				// ����ʬ��value + ̾�Ρ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCodeName]\" value=\"".$aryData[$i]["lngSalesClassCodeName"]."\">";

				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";
			}
		}
		//-------------------------------------------------
		// DB���Ȼ�
		//-------------------------------------------------
		else
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// ���ٹ��ֹ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngorderdetailno]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";

				// ñ�̡�̾�Ρ�
				$strProductUnitCodeName = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname", $aryData[$i]["lngproductunitcode"],'', $objDB );
				// ����ʬ̾
				$strSalesClassCodeName = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $aryData[$i]["lngsalesclasscode"], '', $objDB );

				// ���ʥ�����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strproductcode"]."\">";

				//���֥����å�
//				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strGoodsCode]\" value=\"".$aryData[$i]["strGoodsCode"]."\">";

				// ñ���ꥹ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";

				// ������ʬ
				$lngconversionclasscode = ( $aryData[$i]["lngconversionclasscode"] == 1) ? "gs" : "ps";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"$lngconversionclasscode\">";

				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curproductprice"]."\">";

				// ñ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";

				// ñ�̡�̾�Ρ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"$strProductUnitCodeName\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";

				// ��ȴ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTotalPrice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";

				// ����
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strnote"])."\">";

				// ñ���ꥹ���ɲåǡ���
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";

				// ����ʬ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCode]\" value=\"".$aryData[$i]["lngsalesclasscode"]."\">";

				// ����ʬ̾��value + ̾�Ρ�
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCodeName]\" value=\"$strSalesClassCodeName\">";

				// Ǽ��
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";
			}
		}


		$strDetailHidden = implode( "\n", $aryDetailHidden );

		return $strDetailHidden;
	}





	// ------------------------------------------------------------------------
	/**
	*   fncCheckData_so() �ؿ�
	*
	*   ��������
	*     ��submit���줿�ǡ���������å�����
	*
	*   @param   $aryData     [Array]     submit���줿��
	*   @param   $objDB       [Object]    DB��³���֥�������
	*   @return  $aryJScript  [aryError]
	*/
	// ------------------------------------------------------------------------
	function fncCheckData_so( $aryData, $strPart, $objDB )
	{
		if( $strPart == "header")
		{
			$aryCheck["dtmOrderAppDate"]				= "null:date";			// �׾���
			//$aryCheck["strReceiveCode"]					= "null";				// ����No
			$aryCheck["lngCustomerCode"]				= "null";				// �ܵ�
			$aryCheck["lngOrderStatusCode"]				= "";					// ����(���ץ������)
			$aryCheck["lngMonetaryUnitCode"]			= "null";				// �̲�
			$aryCheck["curAllTotalPrice"]				= "null";				// ���׶�ۡ���ȴ����

			if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )		// �̲ߤ����ܰʳ�
			{
				$aryCheck["lngMonetaryRateCode"]		= "number(0,99)";		// �졼�ȥ�����
				$aryCheck["curConversionRate"]			= "null";				// �����졼��
				//$aryCheck["lngPayConditionCode"]		= "number(1,99)";		// ��ʧ���
			}
		}
		else
		{
			$aryCheck["strProductCode"]					= "null";				// ����
//39���б�
//			$aryCheck["strGoodsCode"]					= "null";				// ����
//�ܵ�����
			$aryCheck["lngSalesClassCode"]				= "number(1,99)";		// ����ʬ
			$aryCheck["dtmDeliveryDate"]				= "null";				// Ǽ��
			$aryCheck["lngConversionClassCode"]			= "null";				// ����ñ�̷׾�
			$aryCheck["curProductPrice"]				= "null";				// ñ��
			$aryCheck["lngProductUnitCode"]				= "null";				// ñ��
			$aryCheck["lngGoodsQuantity"]				= "null";				// ����
			$aryCheck["curTotalPrice"]					= "null:money(0,9999999999)";	// ��ȴ���
		}


		// �����å��ؿ��ƤӽФ�
		$aryCheckResult = fncAllCheck( $aryData, $aryCheck );

		list( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );

		return array( $aryData, $bytErrorFlag );
	}

?>