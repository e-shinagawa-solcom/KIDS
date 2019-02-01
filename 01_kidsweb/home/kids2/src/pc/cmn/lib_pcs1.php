<?
/** 
*	�������ܺ١������̵�����ؿ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	��������
*	������̴�Ϣ�δؿ�
*
*	��������
*
*	2004.03.04	�����κ�����ˤ��λ����ξ��֤ˤ�ꡢ�б�����ȯ�������ξ��֤��ѹ�����ؿ����ɲ�����
*	2004.03.09	������ʬ��define�����ɲ�
*	2004.03.09	�ƺǿ��ǡ����������ν����˺���ǡ���Ƚ�̤��ɲ�
*	2004.03.17	�ܺ�ɽ������ñ����ʬ��ɽ�������򾮿����ʲ�������ѹ�
*	2004.03.29	�ܺ�ɽ������ɽ��������ٹ��ֹ�礫��ɽ���ѥ����ȥ�������ѹ�
*	2004.03.30	�������ܡ��������ʤ��ѹ����줿���Ǥ⡢���ٹ��ֹ�Τߤ�Ƚ�Ǵ��Ȥ����껦����褦���ѹ�����
*
*/

/**
* ���ꤵ�줿�����ֹ椫������إå�������������ӣѣ�ʸ�����
*
*	��������ֹ�Υإå�����μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngStockNo 			������������ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetStockHeadNoToInfoSQL ( $lngStockNo )
{
	// SQLʸ�κ���
	$aryQuery[] = "SELECT distinct on (s.lngStockNo) s.lngStockNo as lngStockNo, s.lngRevisionNo as lngRevisionNo";

	// ��Ͽ��
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
	// �׾���
	$aryQuery[] = ", to_char( s.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmStockAppDate";
	// ����No
	$aryQuery[] = ", s.strStockCode as strStockCode";
	// ȯ��No
	$aryQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
	// ȯ������
	$aryQuery[] = ", o.lngOrderNo as lngOrderNo";
	// ȯ������
	$aryQuery[] = ", o.strOrderCode as strRealOrderCode";
	// ��ɼ������
	$aryQuery[] = ", s.strSlipCode as strSlipCode";
	// ���ϼ�
	$aryQuery[] = ", s.lngInputUserCode as lngInputUserCode";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
	// ������
	$aryQuery[] = ", s.lngCustomerCompanyCode as lngCustomerCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
	// ����
	$aryQuery[] = ", s.lngGroupCode as lngInChargeGroupCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
	// ô����
	$aryQuery[] = ", s.lngUserCode as lngInChargeUserCode";
	$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
	$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
	// Ǽ�ʾ��
	$aryQuery[] = ", s.lngDeliveryPlaceCode as lngDeliveryPlaceCode";
	$aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryDisplayCode";
	$aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryDisplayName";
	// �̲�
	$aryQuery[] = ", s.lngMonetaryUnitCode as lngMonetaryUnitCode";
	$aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
	$aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	// �졼�ȥ�����
	$aryQuery[] = ", s.lngMonetaryRateCode as lngMonetaryRateCode";
	$aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
	// �����졼��
	$aryQuery[] = ", s.curConversionRate as curConversionRate";
	// ����
	$aryQuery[] = ", s.lngStockStatusCode as lngStockStatusCode";
	$aryQuery[] = ", ss.strStockStatusName as strStockStatusName";
	// ��ʧ���
	$aryQuery[] = ", s.lngPayConditionCode as lngPayConditionCode";
	$aryQuery[] = ", pc.strPayConditionName as strPayConditionName";
	// ����ͭ��������
	$aryQuery[] = ", to_char( s.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
	// ����
	$aryQuery[] = ", s.strNote as strNote";
	// ��׶��
	$aryQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";

	$aryQuery[] = " FROM m_Stock s LEFT JOIN m_Order o ON s.lngOrderNo = o.lngOrderNo";
	$aryQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON s.lngGroupCode = inchg_g.lngGroupCode";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON s.lngUserCode = inchg_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_StockStatus ss USING (lngStockStatusCode)";
	$aryQuery[] = " LEFT JOIN m_PayCondition pc ON s.lngPayConditionCode = pc.lngPayConditionCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON s.lngMonetaryRateCode = mr.lngMonetaryRateCode";

	$aryQuery[] = " WHERE s.lngStockNo = " . $lngStockNo . "";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* ���ꤵ�줿�����ֹ椫��������پ�����������ӣѣ�ʸ�����
*
*	��������ֹ�����پ���μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngStockNo 			������������ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetStockDetailNoToInfoSQL ( $lngStockNo )
{
// 2004.03.29 suzukaze update start
	// SQLʸ�κ���
//	$aryQuery[] = "SELECT distinct on (sd.lngStockDetailNo) sd.lngStockDetailNo as lngRecordNo, ";
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngRecordNo, ";
// 2004.03.29 suzukaze update end
	$aryQuery[] = "sd.lngStockNo as lngStockNo, sd.lngRevisionNo as lngRevisionNo";

	// ���ʥ����ɡ�̾��
	$aryQuery[] = ", sd.strProductCode as strProductCode";
	$aryQuery[] = ", p.strProductName as strProductName";
	// ��������
	$aryQuery[] = ", sd.lngStockSubjectCode as lngStockSubjectCode";
	$aryQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
	// ��������
	$aryQuery[] = ", sd.lngStockItemCode as lngStockItemCode";
	$aryQuery[] = ", si.strStockItemName as strStockItemName";
	// �ⷿ�ֹ�
	$aryQuery[] = ", sd.strMoldNo as strMoldNo";
	// �ܵ�����
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode";
	// ������ˡ
	$aryQuery[] = ", sd.lngDeliveryMethodCode as lngDeliveryMethodCode";
	$aryQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
	// Ǽ��
	$aryQuery[] = ", sd.dtmDeliveryDate as dtmDeliveryDate";
// 2004.03.17 suzukaze update start
	// ñ��
	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
//	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.99' )  as curProductPrice";
// 2004.03.17 suzukaze update end
	// ñ��
	$aryQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
	$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
	// ����
	$aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// ��ȴ���
	$aryQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// �Ƕ�ʬ
	$aryQuery[] = ", sd.lngTaxClassCode as lngTaxClassCode";
	$aryQuery[] = ", tc.strTaxClassName as strTaxClassName";
	// ��Ψ
	$aryQuery[] = ", sd.lngTaxCode as lngTaxCode";
	$aryQuery[] = ", To_char( t.curTax, '9,999,999,990.9999' ) as curTax";
	// �ǳ�
	$aryQuery[] = ", To_char( sd.curTaxPrice, '9,999,999,990.99' )  as curTaxPrice";
	// ��������
	$aryQuery[] = ", sd.strNote as strDetailNote";

	// ���ٹԤ�ɽ��������
	$aryQuery[] = " FROM t_StockDetail sd LEFT JOIN m_Product p USING (strProductCode)";
	$aryQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
//	$aryQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)";
	$aryQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
	$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	$aryQuery[] = " LEFT JOIN m_TaxClass tc USING (lngTaxClassCode)";
	$aryQuery[] = " LEFT JOIN m_Tax t USING (lngTaxCode)";
	$aryQuery[] = ", m_StockItem si ";

	$aryQuery[] = " WHERE sd.lngStockNo = " . $lngStockNo . "";
	$aryQuery[] = "AND si.lngStockSubjectCode = ss.lngStockSubjectCode ";
	$aryQuery[] = "AND sd.lngStockItemCode = si.lngStockItemCode ";

// 2004.03.30 suzukaze update start
	$aryQuery[] = " ORDER BY sd.lngSortKey ASC ";
// 2004.03.30 suzukaze update end

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* �ܺ�ɽ���ؿ��ʥإå��ѡ�
*
*	�ơ��֥빽���ǻ����ǡ����ܺ٤���Ϥ���ؿ�
*	�إå��Ԥ�ɽ������
*
*	@param  Array 	$aryResult 				�إå��Ԥθ�����̤���Ǽ���줿����
*	@access public
*/
function fncSetStockHeadTabelData ( $aryResult )
{
	$aryColumnNames = array_keys($aryResult);

	// ɽ���оݥ������������̤ν���
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		// ��Ͽ��
		if ( $strColumnName == "dtminsertdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", substr( $aryResult["dtminsertdate"], 0, 19 ) );
		}

		// �׾���
		else if ( $strColumnName == "dtmstockappdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmstockappdate"] );
		}

		// ���ϼ�
		else if ( $strColumnName == "lnginputusercode" )
		{
			if ( $aryResult["strinputuserdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "     ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strinputuserdisplayname"];
		}

		// ������
		else if ( $strColumnName == "lngcustomercode" )
		{
			if ( $aryResult["strcustomerdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strcustomerdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "      ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strcustomerdisplayname"];
		}

		// ����
		else if ( $strColumnName == "lnginchargegroupcode" )
		{
			if ( $aryResult["strinchargegroupdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "    ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strinchargegroupdisplayname"];
		}

		// ô����
		else if ( $strColumnName == "lnginchargeusercode" )
		{
			if ( $aryResult["strinchargeuserdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "     ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strinchargeuserdisplayname"];
		}

		// Ǽ�ʾ��
		else if ( $strColumnName == "lngdeliveryplacecode" )
		{
			if ( $aryResult["strdeliverydisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strdeliverydisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "      ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strdeliverydisplayname"];
		}

		// ��׶��
		else if ( $strColumnName == "curtotalprice" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strmonetaryunitsign"] . " ";
			if ( !$aryResult["curtotalprice"] )
			{
				$aryNewResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewResult[$strColumnName] .= $aryResult["curtotalprice"];
			}
		}

		// ����
		else if ( $strColumnName == "lngstockstatuscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strstockstatusname"];
		}

		// ��ʧ���
		else if ( $strColumnName == "lngpayconditioncode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strpayconditionname"];
		}

		// �̲�
		else if ( $strColumnName == "lngmonetaryunitcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strmonetaryunitname"];
		}

		// �졼�ȥ�����
		else if ( $strColumnName == "lngmonetaryratecode" )
		{
			if ( $aryResult["lngmonetaryratecode"] and $aryResult["lngmonetaryunitcode"] != DEF_MONETARY_YEN )
			{
				$aryNewResult[$strColumnName] = $aryResult["strmonetaryratename"];
			}
			else
			{
				$aryNewResult[$strColumnName] = "";
			}
		}

		// ȯ��ͭ��������
		else if ( $strColumnName == "dtmexpirationdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmexpirationdate"] );
		}

		// ����
		else if ( $strColumnName == "strnote" )
		{
			$aryNewResult[$strColumnName] = nl2br($aryResult["strnote"]);
		}

		// ����¾�ι��ܤϤ��Τޤ޽���
		else
		{
			$aryNewResult[$strColumnName] = $aryResult[$strColumnName];
		}
	}

	return $aryNewResult;
}






/**
* �ܺ�ɽ���ؿ��������ѡ�
*
*	�ơ��֥빽���ǻ����ǡ����ܺ٤���Ϥ���ؿ�
*	���ٹԤ�ɽ������
*
*	@param  Array 	$aryDetailResult 	���ٹԤθ�����̤���Ǽ���줿����ʣ��ǡ���ʬ��
*	@param  Array 	$aryHeadResult 		�إå��Ԥθ�����̤���Ǽ���줿����ʻ����ѡ�
*	@access public
*/
function fncSetStockDetailTabelData ( $aryDetailResult, $aryHeadResult )
{
	$aryColumnNames = array_keys($aryDetailResult);

	// ɽ���оݥ������������̤ν���
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		// ���ʥ�����̾��
		if ( $strColumnName == "strproductcode" )
		{
			if ( $aryDetailResult["strproductcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["strproductcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strproductname"];
		}

		// ��������
		else if ( $strColumnName == "lngstocksubjectcode" )
		{
			if ( $aryDetailResult["lngstocksubjectcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngstocksubjectcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strstocksubjectname"];
		}

		// ��������
		else if ( $strColumnName == "lngstockitemcode" )
		{
			if ( $aryDetailResult["lngstockitemcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngstockitemcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strstockitemname"];
		}

		// �ⷿ�ֹ�
		else if ( $strColumnName == "strmoldno" )
		{
// 2004.05.31 suzukaze update start
			// �������ܤ����������ⷿ�������ѡ��������ʤ��� Injection Mold�ξ��
			// �������ܤ����������ⷿ���ѹ⡡���������ʤ��� �ⷿ�ξ��
			if ( $aryDetailResult["strmoldno"] 
				and ( $aryDetailResult["lngstocksubjectcode"] = 433 and $aryDetailResult["lngstockitemcode"] = 1 )
				or  ( $aryDetailResult["lngstocksubjectcode"] = 431 and $aryDetailResult["lngstockitemcode"] = 8 ) )
			{
				$aryNewDetailResult[$strColumnName] = $aryDetailResult["strmoldno"];
			}
// 2004.05.31 suzukaze update end
		}

		// �ܵ�����
		else if ( $strColumnName == "strgoodscode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
		}

		// ������ˡ
		else if ( $strColumnName == "lngdeliverymethodcode" )
		{
			if ( $aryDetailResult["strdeliverymethodname"] == "" )
			{
				$aryDetailResult["strdeliverymethodname"] = "̤��";
			}
			$aryNewDetailResult[$strColumnName] .= $aryDetailResult["strdeliverymethodname"];
		}

		// Ǽ��
		else if ( $strColumnName == "dtmdeliverydate" )
		{
			$aryNewDetailResult[$strColumnName] = str_replace( "-", "/", $aryDetailResult["dtmdeliverydate"] );
		}

		// ñ��
		else if ( $strColumnName == "curproductprice" )
		{
			$aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryDetailResult["curproductprice"] )
			{
				$aryNewDetailResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] .= $aryDetailResult["curproductprice"];
			}
		}

		// ñ��
		else if ( $strColumnName == "lngproductunitcode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult["strproductunitname"];
		}

		// ��ȴ���
		else if ( $strColumnName == "cursubtotalprice" )
		{
			$aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryDetailResult["cursubtotalprice"] )
			{
				$aryNewDetailResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] .= $aryDetailResult["cursubtotalprice"];
			}
		}

		// �Ƕ�ʬ
		else if ( $strColumnName == "lngtaxclasscode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult["strtaxclassname"];
		}

		// ��Ψ
		else if ( $strColumnName == "curtax" )
		{
			if ( !$aryDetailResult["curtax"] )
			{
				$aryNewDetailResult[$strColumnName] = "";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = $aryDetailResult["curtax"];
			}
		}

		// �ǳ�
		else if ( $strColumnName == "curtaxprice" )
		{
			$aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryDetailResult["curtaxprice"] )
			{
				$aryNewDetailResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] .= $aryDetailResult["curtaxprice"];
			}
		}

		// ��������
		else if ( $strColumnName == "strdetailnote" )
		{
			$aryNewDetailResult[$strColumnName] = nl2br($aryDetailResult[$strColumnName]);
		}

		// ����¾�ι��ܤϤ��Τޤ޽���
		else
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
		}
	}

	return $aryNewDetailResult;
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
function fncSetStockTabelName ( $aryResult, $aryTytle )
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
* ����λ����ǡ����ˤĤ���̵�������뤳�ȤǤɤ��ʤ뤫�������櫓����
*
*	����λ����ǡ����ξ��֤�Ĵ�������������櫓����ؿ�
*
*	@param  Array 		$aryStockData 	�����ǡ���
*	@param  Object		$objDB			DB���֥�������
*	@return Integer 	$lngCase		���֤Υ�����
*										1: �оݻ����ǡ�����̵�������Ƥ⡢�ǿ��λ����ǡ������ƶ������ʤ�
*										2: �оݻ����ǡ�����̵�������뤳�Ȥǡ��ǿ��λ����ǡ����������ؤ��
*										3: �оݻ����ǡ���������ǡ����ǡ����������褹��
*										4: �оݻ����ǡ�����̵�������뤳�Ȥǡ��ǿ��λ����ǡ����ˤʤꤦ������ǡ������ʤ�
*	@access public
*/
function fncGetInvalidCodeToMaster ( $aryStockData, $objDB )
{
	// ����оݻ�����Ʊ�����������ɤκǿ��λ���No��Ĵ�٤�
	$strQuery = "SELECT lngStockNo FROM m_Stock s WHERE s.strStockCode = '" . $aryStockData["strstockcode"] . "' AND s.bytInvalidFlag = FALSE "
		. " AND s.lngRevisionNo >= 0"
		. " AND s.lngRevisionNo = ( "
		. "SELECT MAX( s1.lngRevisionNo ) FROM m_Stock s1 WHERE s1.strStockCode = s.strStockCode )";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewStockNo = $objResult->lngstockno;
	}
	else
	{
		$lngCase = 4;
	}
	$objDB->freeResult( $lngResultID );

	// ����оݤ��ǿ����ɤ����Υ����å�
	if ( $lngCase != 4 )
	{
		if ( $lngNewStockNo == $aryStockData["lngstockno"] )
		{
			// �ǿ��ξ��
			// ����оݻ����ʳ��Ǥ�Ʊ�����������ɤκǿ��λ���No��Ĵ�٤�
			$strQuery = "SELECT lngStockNo FROM m_Stock s WHERE s.strStockCode = '" . $strStockCode . "' AND s.bytInvalidFlag = FALSE ";
			$strQuery .= " AND s.lngStockNo <> " . $aryStockData["lngstockno"] . " AND s.lngRevisionNo >= 0";

			// ���������꡼�μ¹�
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum >= 1 )
			{
				$lngCase = 2;
			}
			else
			{
				$lngCase = 4;
			}
			$objDB->freeResult( $lngResultID );
		}
		// �оݻ���������ǡ������ɤ����γ�ǧ
		else if ( $aryStockData["lngrevisionno"] < 0 )
		{
			$lngCase = 3;
		}
		else
		{
			$lngCase = 1;
		}
	}

	return $lngCase;
}





// 2004.03.09 suzukaze update start
/**
* ����λ����ǡ����κ���˴ؤ��ơ����λ����ǡ����������뤳�ȤǤξ����ѹ��ؿ�
*
*	�����ξ��֤���Ǽ�ʺѡפξ�硢ȯ��No����ꤷ�Ƥ�����硢ʬǼ�Ǥ��ä����ʤ�
*	�ƾ��֤��Ȥˤ��λ����˴ؤ���ǡ����ξ��֤��ѹ�����
*
*	@param  Array 		$aryStockData 	�����ǡ���
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*/
function fncStockDeleteSetStatus ( $aryStockData, $objDB )
{
	// ����оݻ����ϡ�ȯ��No����ꤷ�ʤ������Ǥ���
	if ( $aryStockData["lngorderno"] == "" or $aryStockData["lngorderno"] == 0 )
	{
		return 0;
	}

	// ȯ��No����ꤷ�Ƥ�������ξ��ϡ����ꤷ�Ƥ���ǿ���ȯ��Υǡ������������
	$strQuery = "SELECT o.lngOrderNo as lngOrderNo, o.strOrderCode as strOrderCode, "
		. "o.lngOrderStatusCode as lngOrderStatusCode, o.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Order o "
		. "WHERE o.strOrderCode = ( "
		. "SELECT o1.strOrderCode FROM m_Order o1 WHERE o1.lngOrderNo = " . $aryStockData["lngorderno"] . " ) "
		. "AND o.bytInvalidFlag = FALSE "
		. "AND o.lngRevisionNo >= 0 "
		. "AND o.lngRevisionNo = ( "
		. "SELECT MAX( o2.lngRevisionNo ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ) ";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
		$strNewOrderCode = $objResult->strordercode;
		$lngNewOrderStatusCode = $objResult->lngorderstatuscode;
		$OrderlngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// ȯ��No�ϻ��ꤷ�Ƥ��뤬����ͭ���ʺǿ�ȯ��¸�ߤ��ʤ����Ϥ��Τޤ޺����ǽ�Ȥ���
		return 0;
	}
	$objDB->freeResult( $lngResultID );

	// �ǿ�ȯ������پ�����������
	$strQuery = "SELECT od.lngOrderDetailNo as lngOrderDetailNo, od.strProductCode as strProductCode, "
		. "od.lngStockSubjectCode as lngStockSubjectCode, od.lngStockItemCode as lngStockItemCode, "
		. "od.lngConversionClassCode as lngConversionClassCode, od.curProductPrice as curProductPrice, "
		. "od.lngProductQuantity as lngProductQuantity, od.lngProductUnitCode as lngProductUnitCode, "
		. "od.curSubTotalPrice as curSubTotalPrice, p.lngCartonQuantity as lngCartonQuantity "
		. "FROM t_OrderDetail od, m_Product p "
		. "WHERE lngOrderNo = " . $lngNewOrderNo . " AND od.strProductCode = p.strProductCode "
		. "ORDER BY lngSortKey";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
		return 1;
	}
	$objDB->freeResult( $lngResultID );

	// ����оݻ�����Ʊ��ȯ��No����ꤷ�Ƥ���ǿ������򸡺�
	$strQuery = "SELECT s.lngStockNo as lngStockNo, s.lngStockStatusCode as lngStockStatusCode, "
		. "s.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Stock s, m_Order o "
		. "WHERE o.strOrderCode = '" . $strNewOrderCode . "' AND o.lngOrderNo = s.lngOrderNo "
		. "AND s.bytInvalidFlag = FALSE "
		. "AND s.lngRevisionNo >= 0 "
		. "AND s.lngRevisionNo = ( "
		. "SELECT MAX( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.strStockCode = s.strStockCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s3.bytInvalidFlag = false AND s3.strStockCode = s.strStockCode ) "
		// �嵭��狼�ĺ���оݤλ����ǤϤʤ�
		. "AND s.strStockCode <> '" . $aryStockData["strstockcode"] . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// ����оݰʳ��λ����ǡ�����¸�ߤ�����
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			// ���پ�����������
			$strStockDetailQuery = "SELECT lngStockDetailNo, strProductCode, lngStockSubjectCode, lngStockItemCode, lngConversionClassCode, "
				. "curProductPrice, lngProductQuantity, lngProductUnitCode, curSubTotalPrice "
				. "FROM t_StockDetail "
				. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " "
				. "ORDER BY lngSortKey";

			list ( $lngStockDetailResultID, $lngStockDetailResultNum ) = fncQuery( $strStockDetailQuery, $objDB );

			if ( $lngStockDetailResultNum )
			{
				for ( $j = 0; $j < $lngStockDetailResultNum; $j++ )
				{
					$aryStockDetailResult[$i][] = $objDB->fetchArray( $lngStockDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngStockDetailResultID );
		}

		// ���ȸ�ȯ���������˼������������ˤƤɤΤ褦�ʾ��֤ˤʤäƤ���Τ�Ĵ��
		for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
		{
			// ���ȸ�ȯ������ٹ��ֹ������������������ٹ��ֹ�ˤҤ�Ť��ƻ������ä����ߤ���뤿��
			$lngOrderDetailNo 		= $aryOrderDetailResult[$i]["lngorderdetalno"];				// ���ٹ��ֹ�

			$strProductCode 		= $aryOrderDetailResult[$i]["strproductcode"];				// ���ʥ�����
			$lngStockSubjectCode 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// �������ܥ�����
			$lngStockItemCode 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// �������ʥ�����
			$lngConversionClassCode = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// ������ʬ������
			$curProductPrice		= $aryOrderDetailResult[$i]["curproductprice"];				// ����ñ���ʲٻ�ñ����
			$lngProductQuantity		= $aryOrderDetailResult[$i]["lngproductquantity"];			// ���ʿ��̡ʲٻѿ��̡�
			$lngProductUnitCode		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// ����ñ�̡ʲٻ�ñ�̡�
			$curSubTotalPrice		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// ��ȴ���
			$lngCartonQuantity		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// �����ȥ�����

			// ������ʬ���ٻ�ñ�̷׾�ξ�硢����ñ���ط׻�
			if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
			{
				// 0 ����к�
				if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
				{
					// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
					$lngCartonQuantity = 1;
				}

				// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
				$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

				// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
				$curProductPrice = $curProductPrice / $lngCartonQuantity;
			}

			$bytEndFlag = 0;
			$lngStockProductQuantity = 0;
			$curStockSubTotalPrice = 0;
			
			for ( $j = 0; $j < count($aryStockResult); $j++ )
			{
				$StocklngMonetaryUnitCode = $aryStockResult[$j]["lngmonetaryunitcode"];
				for ( $k = 0; $k < count($aryStockDetailResult[$j]); $k++ )
				{
// 2004.03.30 suzukaze update start
					// ȯ�����ٹ��ֹ���Ф��ƻ������ٹ��ֹ椬Ʊ�����������ʥ����ɤ�Ʊ�����٤����Ĥ��ä����
					// ����˲ä����̲ߤ�Ʊ�����
					if ( $lngOrderDetailNo == $aryStockDetailResult[$j][$k]["lngstockdetailno"] 
						and $strProductCode == $aryStockDetailResult[$j][$k]["strproductcode"] 
//						and $lngStockSubjectCode == $aryStockDetailResult[$j][$k]["lngstocksubjectcode"] 
//						and $lngStockItemCode == $aryStockDetailResult[$j][$k]["lngstockitemcode"] 
						and $OrderlngMonetaryUnitCode == $StocklngMonetaryUnitCode )
// 2004.03.30 suzukaze update end
					{
						// ������ʬ���ٻ�ñ�̷׾�ξ�硢����ñ���ط׻�
						if ( $aryStockDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
						{
							// 0 ����к�
							if ( $aryStockDetailResult[$j][$k]["lngCartonQuantity"] == 0 or $aryStockDetailResult[$j][$k]["lngCartonQuantity"] == "" )
							{
								// �����ȥ�������� �� ���ä����� �������ʤ��Ф���Ǽ�ʺѤߤ��ɤ�����Ƚ�Ǥ��Ǥ��ʤ����� ����Ū�� �� �ˤƴ���
								$aryStockDetailResult[$j][$k]["lngCartonQuantity"] = 1;
							}

							// ���ʿ��̤ϲٻѿ��� * �����ȥ�����
							$aryStockDetailResult[$j][$k]["lngProductQuantity"] 
								= $aryStockDetailResult[$j][$k]["lngProductQuantity"] * $aryStockDetailResult[$j][$k]["lngCartonQuantity"];

							// ���ʲ��ʤϲٻ�ñ�� / �����ȥ�����
							$aryStockDetailResult[$j][$k]["curProductPrice"] 
								= $aryStockDetailResult[$j][$k]["curProductPrice"] / $aryStockDetailResult[$j][$k]["lngCartonQuantity"];
						}

						// �������
						if ( $lngProductQuauntity > $aryStockDetailResult[$j][$k]["lngproductquantity"] )
						{
							$lngStockProductQuantity += $aryStockDetailResult[$j][$k]["lngproductquantity"];
							// ʣ����������ι绻�Ǥο������
							if ( $lngProductQuauntity <= $lngStockProductQuantity )
							{
								$bytEndFlag = 99;
								break;
							}
						}
						else
						{
							$bytEndFlag = 99;
							break;
						}
						
						// ��ȴ������
						if ( $curSubTotalPrice > $aryStockDetailResult[$j]["cursubtotalprice"] )
						{
							$curStockSubTotalPrice += $aryStockDetailResult[$j]["cursubtotalprice"];
							// ʣ����������ι绻�Ǥ���ȴ������
							if ( $curSubTotalPrice <= $curStockSubTotalPrice )
							{
								$bytEndFlag = 99;
								break;
							}
						}
						else
						{
							$bytEndFlag = 99;
							break;
						}

						// Ʊ�����ٹԤξ���ȯ��Ȼ����Ǹ��Ĥ��ä��ݤˤϡ�Ǽ����פȤʤ뤿��ʲ�����
						$bytEndFlag = 1;
					}
				}
				// �������٤�ȯ�����٤�Ʊ���Ƥ����Ĥ��ä����ϡ�for ʸȴ��
				if ( $bytEndFlag == 99 )
				{
					break;
				}
			}
			// ȯ�����ٹ���λ������ٹԤ����Ĥ��ä����֤򵭲�
			$aryStatus[] = $bytEndFlag;
		}
		
		// ���٥����å���$aryStatus�����٤��Ȥξ��֡ˤˤ��ȯ�����ΤȤ��Ƥξ��֤�Ƚ��
		$flagZERO = 0;
		$flagALL  = 0;
		for ( $i = 0; $i < count($aryStatus); $i++ )
		{
			if ( $aryStatus[$i] == 0 )
			{
				$flagZERO++;
			}
			if ( $aryStatus[$i] == 99 )
			{
				$flagALL++;
			}
		}
		// ȯ�����٤��Ф��ư��������ȯ�����Ƥ��ʤ���硢�ޤ��ϴ�Ǽ�ǤϤʤ����
		// ��flagZERO��ȯ�����ٿ����Ф��ƥ�������ξ��ºݤϽ�����֤Ǥ��뤬�������ˤ�
		//   ȯ��No�����ꤵ��Ƥ���ΤǤ����Ǥξ��֤ϡ�Ǽ����פȤ����
		if ( $flagALL != count($aryStatus) )
		{
			// ��������ȯ��ξ��֤ξ��֤��Ǽ����פȤ���
		
			// �����о�ȯ��ǡ������å�����
			$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";

			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// ��Ǽ����׾��֤ؤι�������
			$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_DELIVER . " WHERE lngOrderNo = " . $lngNewOrderNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// Ʊ��ȯ��NO����ꤷ�Ƥ�������ξ��֤��Ф��Ƥ��Ǽ����פȤ���
			for ( $i = 0; $i < count($aryStockResult); $i++ )
			{
				// �����оݻ����ǡ������å�����
				$strLockQuery = "SELECT lngStockNo FROM m_Stock " 
					. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";

				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// ��Ǽ����׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_DELIVER 
					. " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			
			return 0;
		}
		else
		// ����оݻ����������Ƥ��о�ȯ��ϴ�Ǽ���֤Ǥ��ä���
		{
			// ��������ȯ��ξ��֤ξ��֤��Ǽ�ʺѡפȤ���
		
			// �����о�ȯ��ǡ������å�����
			$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// ��Ǽ����׾��֤ؤι�������
			$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_END . " WHERE lngOrderNo = " . $lngNewOrderNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// Ʊ��ȯ��NO����ꤷ�Ƥ�������ξ��֤��Ф��Ƥ��Ǽ����פȤ���
			for ( $i = 0; $i < count($aryStockResult); $i++ )
			{
				// �����оݻ����ǡ������å�����
				$strLockQuery = "SELECT lngStockNo FROM m_Stock " 
					. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// ��Ǽ����׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_END 
					. " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			return 0;
		}
	}
	else
	{
		// ����оݰʳ��λ����ǡ�����¸�ߤ��ʤ����
		// �����λ��ȸ��ǿ�ȯ��ξ��֤��ȯ��פ��᤹
		
		// �����о�ȯ��ǡ������å�����
		$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if ( !$lngLockResultNum )
		{
			fncOutputError ( 9051, DEF_ERROR, "̵�����������顼", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngLockResultID );

		// ��ȯ��׾��֤ؤι�������
		$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_ORDER . " WHERE lngOrderNo = " . $lngNewOrderNo;

		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		$objDB->freeResult( $lngUpdateResultID );

		return 0;
	}

	$objDB->freeResult( $lngResultID );

	return 0;
}

// 2004.03.09 suzukaze update end





?>