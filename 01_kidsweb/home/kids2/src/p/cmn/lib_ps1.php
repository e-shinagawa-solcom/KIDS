<?
/**
* ���ꤵ�줿�����ֹ椫�龦�ʥإå�������������ӣѣ�ʸ�����
*
*	���꾦�ʾ���μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngProductNo 			�������뾦���ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetProductNoToInfoSQL ( $lngProductNo )
{
	// SQLʸ�κ���
	$aryQuery[] = "SELECT distinct on (p.lngProductNo) p.lngProductNo as lngProductNo, \n";
	$aryQuery[] = " p.lngInChargeGroupCode as lngGroupCode, p.bytInvalidFlag as bytInvalidFlag\n";

	// ��������
	$aryQuery[] = ", to_char( p.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate\n";
	// ���ʹԾ���
	$aryQuery[] = ", t_gp.lngGoodsPlanProgressCode as lngGoodsPlanProgressCode\n";
	$aryQuery[] = ", t_gp.strgoodsplanprogressname as strgoodsplanprogressname\n";
	// �����ֹ�
	$aryQuery[] = ", t_gp.lngRevisionNo as lngRevisionNo\n";
	// ��������
	$aryQuery[] = ", to_char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmRevisionDate\n";
	// ���ʥ�����
	$aryQuery[] = ", p.strProductCode as strProductCode\n";
	// ����̾��
	$aryQuery[] = ", p.strProductName as strProductName\n";
	// ����̾�ΡʱѸ��
	$aryQuery[] = ", p.strProductEnglishName as strProductEnglishName\n";
	// ���ϼ�
	$aryQuery[] = ", p.lngInputUserCode as lngInputUserCode\n";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode\n";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName\n";
	// ����
	$aryQuery[] = ", p.lngInChargeGroupCode as lngInChargeGroupCode\n";
	$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode\n";
	$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName\n";
	// ô����
	$aryQuery[] = ", p.lngInChargeUserCode as lngInChargeUserCode\n";
	$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode\n";
	$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName\n";
	// ��ȯô����
	$aryQuery[] = ", p.lngdevelopusercode as lngdevelopusercode\n";
	$aryQuery[] = ", devp_u.strUserDisplayCode as strDevelopUserDisplayCode\n";
	$aryQuery[] = ", devp_u.strUserDisplayName as strDevelopUserDisplayName\n";
	$aryQuery[] = ", mc.strCategoryName as lngCategoryCode\n";	// ���ƥ��꡼
		
	// �ܵ�����
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode\n";
	$aryQuery[] = ", cust_c.strDistinctCode as strDistinctCode\n";
	// ����̾��
	$aryQuery[] = ", p.strGoodsName as strGoodsName\n";
	// �ܵ�
	$aryQuery[] = ", p.lngCustomerCompanyCode as lngCustomerCompanyCode\n";
	$aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerCompanyDisplayCode\n";
	$aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerCompanyDisplayName\n";
	// �ܵ�ô����
	$aryQuery[] = ", p.lngCustomerUserCode as lngCustomerUserCode\n";
	$aryQuery[] = ", cust_u.strUserDisplayCode as strCustomerUserDisplayCode\n";
	$aryQuery[] = ", cust_u.strUserDisplayName as strCustomerUserDisplayName\n";
	$aryQuery[] = ", p.strCustomerUserName as strCustomerUserName\n";
	// �ٻ�ñ��
	$aryQuery[] = ", p.lngPackingUnitCode as lngPackingUnitCode\n";
	$aryQuery[] = ", packingunit.strProductUnitName as strPackingUnitName\n";
	// ����ñ��
	$aryQuery[] = ", p.lngProductUnitCode as lngProductUnitCode\n";
	$aryQuery[] = ", productunit.strProductUnitName as strProductUnitName\n";
	// ���ʷ���
	$aryQuery[] = ", p.lngProductFormCode as lngProductFormCode\n";
	$aryQuery[] = ", productform.strProductFormName as strProductFormName\n";
	// ��Ȣ���ޡ�����
	$aryQuery[] = ", To_char( p.lngBoxQuantity, '9,999,999,990' ) as lngBoxQuantity\n";
	// �����ȥ�����
	$aryQuery[] = ", To_char( p.lngCartonQuantity, '9,999,999,990' ) as lngCartonQuantity\n";
	// ����ͽ���
	$aryQuery[] = ", To_char( p.lngProductionQuantity, '9,999,999,990' ) as lngProductionQuantity\n";
	$aryQuery[] = ", p.lngProductionUnitCode as lngProductionUnitCode\n";
	
	// ���Ǽ�ʿ�
	$aryQuery[] = ", To_char( p.lngFirstDeliveryQuantity, '9,999,999,990' ) as lngFirstDeliveryQuantity\n";
	$aryQuery[] = ", p.lngFirstDeliveryUnitCode as lngFirstDeliveryUnitCode\n";
	$aryQuery[] = ", fstdelyunit.strProductUnitName as strfirstdeliveryunitname\n";
	// ��������
	$aryQuery[] = ", p.lngFactoryCode as lngFactoryCode\n";
	$aryQuery[] = ", fact_c.strCompanyDisplayCode as strFactoryDisplayCode\n";
	$aryQuery[] = ", fact_c.strCompanyDisplayName as strFactoryDisplayName\n";
	// ���å���֥깩��
	$aryQuery[] = ", p.lngAssemblyFactoryCode as lngAssemblyFactoryCode\n";
	$aryQuery[] = ", assemfact_c.strCompanyDisplayCode as strAssemblyFactoryDisplayCode\n";
	$aryQuery[] = ", assemfact_c.strCompanyDisplayName as strAssemblyFactoryDisplayName\n";
	// Ǽ�ʾ��
	$aryQuery[] = ", p.lngDeliveryPlaceCode as lngDeliveryPlaceCode\n";
	$aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryPlaceDisplayCode\n";
	$aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryPlaceDisplayName\n";
	// Ǽ��
	$aryQuery[] = ", to_char( p.dtmDeliveryLimitDate, 'YYYY/MM' ) as dtmDeliveryLimitDate";
	// Ǽ��
	$aryQuery[] = ", To_char( p.curProductPrice, '9,999,999,990.99' )  as curProductPrice\n";
	// ����
	$aryQuery[] = ", To_char( p.curRetailPrice, '9,999,999,990.99' )  as curRetailPrice\n";
	// �о�ǯ��
	$aryQuery[] = ", p.lngTargetAgeCode as lngTargetAgeCode\n";
	$aryQuery[] = ", targetage.strTargetAgeName as strTargetAgeName\n";
	// �����ƥ�
	$aryQuery[] = ", To_char( p.lngRoyalty, '9,999,999,990.99' )  as lngRoyalty\n";
	// �ڻ�
	$aryQuery[] = ", p.lngCertificateClassCode as lngCertificateClassCode\n";
	$aryQuery[] = ", certificate.strCertificateClassName as strCertificateClassName\n";
	// �Ǹ���
	$aryQuery[] = ", p.lngCopyrightCode as lngCopyrightCode\n";
	$aryQuery[] = ", copyright.strCopyrightName as strCopyrightName\n";
	// �Ǹ�������
	$aryQuery[] = ", p.strCopyrightNote as strCopyrightNote\n";
	// �Ǹ�ɽ���ʹ����
	$aryQuery[] = ", p.strCopyrightDisplayStamp as strCopyrightDisplayStamp\n";
	// �Ǹ�ɽ���ʰ���ʪ��
	$aryQuery[] = ", p.strCopyrightDisplayPrint as strCopyrightDisplayPrint\n";
	// ���ʹ���
	$aryQuery[] = ", p.strProductComposition as strProductComposition\n";
	// ���å���֥�����
	$aryQuery[] = ", p.strAssemblyContents as strAssemblyContents\n";
	// ���;ܺ�
	$aryQuery[] = ", p.strSpecificationDetails as strSpecificationDetails\n";


	// ���ʾ���
	$aryQuery[] = ", p.lngproductstatuscode as lngproductstatuscode\n";
	// �꡼�Х���������
	$aryQuery[] = ", p.strrevisecode as lngproductstatuscode\n";


	$aryQuery[] = " FROM m_Product p\n";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	$aryQuery[] = " LEFT JOIN m_User input_u ON p.lngInputUserCode = input_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lngInChargeGroupCode = inchg_g.lngGroupCode\n";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lngInChargeUserCode = inchg_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_User devp_u ON p.lngInChargeUserCode = devp_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_Category mc ON mc.lngCategoryCode = p.lngCategoryCode\n";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_User cust_u ON p.lngCustomerUserCode = cust_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_ProductUnit packingunit ON p.lngPackingUnitCode = packingunit.lngProductUnitCode\n";
	$aryQuery[] = " LEFT JOIN m_ProductUnit productunit ON p.lngProductUnitCode = productunit.lngProductUnitCode\n";
	$aryQuery[] = " LEFT JOIN m_ProductUnit fstdelyunit ON p.lngFirstDeliveryUnitCode = fstdelyunit.lngProductUnitCode\n";

	$aryQuery[] = " LEFT JOIN m_ProductForm productform ON p.lngProductFormCode = productform.lngProductFormCode\n";
	$aryQuery[] = " LEFT JOIN m_Company fact_c ON p.lngFactoryCode = fact_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_Company assemfact_c ON p.lngAssemblyFactoryCode = assemfact_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_Company delv_c ON p.lngDeliveryPlaceCode = delv_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_TargetAge targetage ON p.lngTargetAgeCode = targetage.lngTargetAgeCode\n";
	$aryQuery[] = " LEFT JOIN m_CertificateClass certificate ON p.lngCertificateClassCode = certificate.lngCertificateClassCode\n";
	$aryQuery[] = " LEFT JOIN m_Copyright copyright ON p.lngCopyrightCode = copyright.lngCopyrightCode\n";

	$aryQuery[] = ", (select tt_gp.*, m_gp.strgoodsplanprogressname from t_GoodsPlan tt_gp \n";
	$aryQuery[] = " LEFT JOIN  m_goodsplanprogress m_gp on m_gp.lnggoodsplanprogresscode = tt_gp.lnggoodsplanprogresscode) t_gp\n";

	$aryQuery[] = " WHERE p.lngProductNo = " . $lngProductNo . "";

	$aryQuery[] = " AND t_gp.lngProductNo = p.lngProductNo\n";
	$aryQuery[] = " AND t_gp.lngRevisionNo = ( SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo )\n";

	$strQuery = implode( "\n", $aryQuery );
	return $strQuery;
}






/**
* �ܺ�ɽ���ؿ�
*
*	�ơ��֥빽���Ǿ��ʥǡ����ܺ٤���Ϥ���ؿ�
*
*	@param  Array 	$aryResult 	������̤���Ǽ���줿����
* 	@param	Object	$objDB		�ģ¥��֥�������
*	@access public
*/
function fncSetProductTableData ( $aryResult, $objDB )
{
	$aryColumnNames = array_keys($aryResult);

	unset( $aryNewResult );

	// ɽ���оݥ������������̤ν���
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		unset( $strText );

		///////////////////////////////////
		////// ɽ���оݤ����դξ�� ///////
		///////////////////////////////////
		// ������������������
		if ( $strColumnName == "dtminsertdate" or $strColumnName == "dtmrevisiondate" )
		{
			if ( $aryResult[$strColumnName] )
			{
				$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult[$strColumnName] );
			}
		}
		// Ǽ��
		else if ( $strColumnName == "dtmdeliverylimitdate" )
		{
			if ( $aryResult["dtmdeliverylimitdate"] )
			{
				$dtmNewDate = substr( $aryResult["dtmdeliverylimitdate"], 0, 7 );
				$aryNewResult[$strColumnName] = str_replace( "-", "/", $dtmNewDate );
			}
		}

		/////////////////////////////////////////////////
		////// ɽ���оݤ������ɤ���̾�λ��Ȥξ�� ///////
		/////////////////////////////////////////////////
		// ���ʹԾ���
		else if ( $strColumnName == "lnggoodsplanprogresscode" )
		{
			if ( $aryResult["lnggoodsplanprogresscode"] )
			{
				$aryNewResult[$strColumnName] = $aryResult["strgoodsplanprogressname"];
			}
		}
		// ���ϼ�
		else if ( $strColumnName == "lnginputusercode" )
		{
			if ( $aryResult["strinputuserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$strText = "     ";
			}
			$strText .= " " . $aryResult["strinputuserdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// ����
		else if ( $strColumnName == "lnginchargegroupcode" )
		{
			if ( $aryResult["strinchargegroupdisplaycode"] )
			{
				$strText = "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$strText = "    ";
			}
			$strText .= " " . $aryResult["strinchargegroupdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// ô����
		else if ( $strColumnName == "lnginchargeusercode" )
		{
			if ( $aryResult["strinchargeuserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$strText = "     ";
			}
			$strText .= " " . $aryResult["strinchargeuserdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// �ܵ�
		else if ( $strColumnName == "lngcustomercompanycode" )
		{
			if ( $aryResult["strcustomercompanydisplaycode"] )
			{
				$strText = "[" . $aryResult["strcustomercompanydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strcustomercompanydisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// �ܵ�ô����
		else if ( $strColumnName == "lngcustomerusercode" )
		{
			if ( $aryResult["strcustomeruserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strcustomeruserdisplaycode"] ."]";
				$strText .= " " . $aryResult["strcustomeruserdisplayname"];
			}
			else
			{
				$strText = "      ";
				$strText .= " " . $aryResult["strcustomerusername"];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
		// �ٻ�ñ��
		else if ( $strColumnName == "lngpackingunitcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strpackingunitname"];
		}
		// ����ñ��
		else if ( $strColumnName == "lngproductunitcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strproductunitname"];
		}
		// ���ʷ���
		else if ( $strColumnName == "lngproductformcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strproductformname"];
		}
		// ��������
		else if ( $strColumnName == "lngfactorycode" )
		{
			if ( $aryResult["strfactorydisplaycode"] )
			{
				$strText = "[" . $aryResult["strfactorydisplaycode"] ."]";
			}
			else
			{
				$strText = "      ";
			}
			$strText .= " " . $aryResult["strfactorydisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// ���å���֥깩��
		else if ( $strColumnName == "lngassemblyfactorycode" )
		{
			if ( $aryResult["strassemblyfactorydisplaycode"] )
			{
				$strText = "[" . $aryResult["strassemblyfactorydisplaycode"] ."]";
			}
			else
			{
				$strText = "      ";
			}
			$strText .= " " . $aryResult["strassemblyfactorydisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// Ǽ�ʾ��
		else if ( $strColumnName == "lngdeliveryplacecode" )
		{
			if ( $aryResult["strdeliveryplacedisplaycode"] )
			{
				$strText = "[" . $aryResult["strdeliveryplacedisplaycode"] ."]";
			}
			else
			{
				$strText = "      ";
			}
			$strText .= " " . $aryResult["strdeliveryplacedisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// �о�ǯ��
		else if ( $strColumnName == "lngtargetagecode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strtargetagename"];
		}
		// �ڻ�
		else if ( $strColumnName == "lngcertificateclasscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strcertificateclassname"];
		}
		// �Ǹ���
		else if ( $strColumnName == "lngcopyrightcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strcopyrightname"];
		}

		///////////////////////////////////
		////// ɽ���оݤ����̤ξ�� ///////
		///////////////////////////////////
		// ��Ȣ���ޡ������������ȥ�����
		else if ( $strColumnName == "lngboxquantity" or $strColumnName == "lngcartonquantity" )
		{
			if ( !$aryResult[$strColumnName] )
			{
				$strText = "0";
			}
			else
			{
				$strText = $aryResult[$strColumnName];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
		// ����ͽ���
		else if ( $strColumnName == "lngproductionquantity" )
		{
			if ( !$aryResult["lngproductionquantity"] )
			{
				$strText = "0";
			}
			else
			{
				$strText = $aryResult["lngproductionquantity"];
			}
			// ñ�̤�����
			if ( $aryResult["strproductunitname"] )
			{
				$strText .= " " . $aryResult["strproductunitname"];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
		// ���Ǽ�ʿ�
		else if ( $strColumnName == "lngfirstdeliveryquantity" )
		{
			if ( !$aryResult["lngfirstdeliveryquantity"] )
			{
				$strText = "0";
			}
			else
			{
				$strText = $aryResult["lngfirstdeliveryquantity"];
			}
			// ñ�̤�����
			if ( $aryResult["strfirstdeliveryunitname"] )
			{
				$strText .= " " . $aryResult["strfirstdeliveryunitname"];
			}
			$aryNewResult[$strColumnName] = $strText;
		}

		///////////////////////////////////
		////// ɽ���оݤ����ʤξ�� ///////
		///////////////////////////////////
		// Ǽ��������
		else if ( $strColumnName == "curproductprice" or $strColumnName == "curretailprice" )
		{
			$strText = DEF_PRODUCT_MONETARYSIGN . " ";
			if ( !$aryResult[$strColumnName] )
			{
				$strText .= "0.00";
			}
			else
			{
				$strText .= $aryResult[$strColumnName];
			}
			$aryNewResult[$strColumnName] = $strText;
		}

		///////////////////////////////////
		////// ɽ���оݤ����ͤξ�� ///////
		///////////////////////////////////
		// �����ƥ�
		else if ( $strColumnName == "lngroyalty" )
		{
			$aryNewResult[$strColumnName] = $aryResult["lngroyalty"];
		}

		/////////////////////////////////////////
		////// ɽ���оݤ�ʸ������ܤξ�� ///////
		/////////////////////////////////////////
		// ����¾�ι��ܤϤ��Τޤ޽���
		else
		{
			// ���;ܺ٤ϲ�������
			if ( $strColumnName == "strspecificationdetails" )
			{
				$strText = $aryResult[$strColumnName];
			}
			// ���ʹ�����ʸ�����ɲ�
			else if ( $strColumnName == "strproductcomposition" )
			{
				if ( $aryResult[$strColumnName] )
				{
					$strText = "��" . $aryResult[$strColumnName] . "�異�å���֥�";
				}
				else
				{
					$strText = $aryResult[$strColumnName];
				}
			}
			// �ܵ����֤ϼ��̥����ɤ��ɲ�
			else if ( $strColumnName == "strGoodsCode" )
			{
				$strText = $aryResult["strdistinctcode"] . " " . $aryResult[$strColumnName];
			}
			else
			{
				$strText = $aryResult[$strColumnName];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
	}

	return $aryNewResult;
}






/**
* �ܺ�ɽ���ѥ����̾���åȴؿ�
*
*	�ܺ�ɽ�����Υ����̾�����ܸ졢�Ѹ�ˤǤ�����ؿ�
*
*	@param  Array 	$aryResult 		������̤���Ǽ���줿����
*	@param  Array 	$aryTytle 		�����̾����Ǽ���줿����
*	@access public
*/
function fncSetProductTabelName ( $aryResult, $aryTytle )
{
	$aryColumnNames = array_values($aryResult);

	// ɽ���оݥ������������̤ν���
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		if ( $aryTytle[$strColumnName] )
		{
			$strNewColumnName = "CN" . $strColumnName;
			$aryNames[$strNewColumnName] = $aryTytle[$strColumnName];
		}
	}

	return $aryNames;
}






/**
* ����Υ����ɤΥǡ�����¾�Υޥ����ǻ��Ѥ��Ƥ��륳���ɼ���
*
*	���ꥳ���ɤ��Ф��ơ����ꤵ�줿�ޥ����θ����ؿ�
*
*	@param  String 		$strCode 		�����оݥ�����
*	@param	Integer		$lngMode		�����⡼��	1:���ʥ����ɤ������ޥ����ʼ���ܺ٥ơ��֥��
*													2:���ʥ����ɤ���ȯ��ޥ�����ȯ��ܺ٥ơ��֥��
*													3:���ʥ����ɤ������ޥ��������ܺ٥ơ��֥��
*													4:���ʥ����ɤ�������ޥ����ʻ����ܺ٥ơ��֥��
*	@param  Object		$objDB			DB���֥�������
*	@return Array 		$aryCode		�����оݥ����ɤ����Ѥ���Ƥ���ޥ�����Υ����ɤ�����
*	@access public
*/
function fncGetDeleteCodeToMaster ( $strCode, $lngMode, $objDB )
{
	// SQLʸ�κ���
	$strQuery = "SELECT distinct on (";
	switch ( $lngMode )
	{
		case 1:		// ���ʥ����ɤ������ޥ����θ�����
			$strQuery .= "r.strReceiveCode) r.strReceiveCode as lngSearchNo ";
			$strQuery .= "FROM m_Receive r LEFT JOIN t_ReceiveDetail tr ON r.lngReceiveNo = tr.lngReceiveNo, m_Product p ";
			$strQuery .= "WHERE tr.strProductCode = p.strProductCode AND r.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
		case 2:		// ���ʥ����ɤ���ȯ��ޥ����θ�����
			$strQuery .= "o.strOrderCode) o.strOrderCode as lngSearchNo ";
			$strQuery .= "FROM m_Order o LEFT JOIN t_OrderDetail tod ON o.lngOrderNo = tod.lngOrderNo, m_Product p ";
			$strQuery .= "WHERE tod.strProductCode = p.strProductCode AND o.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
		case 3:		// ���ʥ����ɤ������ޥ����θ�����
			$strQuery .= "s.strSalesCode) s.strSalesCode as lngSearchNo ";
			$strQuery .= "FROM m_Sales s LEFT JOIN t_SalesDetail ts ON s.lngSalesNo = ts.lngSalesNo, m_Product p ";
			$strQuery .= "WHERE ts.strProductCode = p.strProductCode AND s.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
		case 4:		// ���ʥ����ɤ�������ޥ����θ�����
			$strQuery .= "s.strStockCode) s.strStockCode as lngSearchNo ";
			$strQuery .= "FROM m_Stock s LEFT JOIN t_StockDetail ts ON s.lngStockNo = ts.lngStockNo, m_Product p ";
			$strQuery .= "WHERE ts.strProductCode = p.strProductCode AND s.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
	}
	$strQuery .= $strCode . "'";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryCode[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryCode = FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return $aryCode;
}






?>
