<?
/** 
*	ȯ���ܺ١������̵�����ؿ���
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
*	2004.03.17	�ܺ�ɽ������ñ����ʬ��ɽ�������򾮿����ʲ�������ѹ�
*	2004.03.29	�ܺ�ɽ���������ٹ��ֹ��ɽ����ʬ��ɽ���ѥ����ȥ�����ɽ������褦���ѹ�
*	2004.05.31	�ⷿ�ֹ�λ���ս���ɲ�
*
*/

/**
* ���ꤵ�줿ȯ���ֹ椫��ȯ��إå�������������ӣѣ�ʸ�����
*
*	����ȯ���ֹ�Υإå�����μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngOrderNo 			��������ȯ���ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetPurchaseHeadNoToInfoSQL ( $lngOrderNo )
{
	// SQLʸ�κ���
	$aryQuery[] = "SELECT distinct on (o.lngOrderNo) o.lngOrderNo as lngOrderNo, o.lngRevisionNo as lngRevisionNo";

	// ��Ͽ��
	$aryQuery[] = ", to_char( o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
	// �׾���
	$aryQuery[] = ", to_char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
	// ȯ��No
	$aryQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
	// ȯ������
	$aryQuery[] = ", o.strOrderCode as strRealOrderCode";
	// ���ϼ�
	$aryQuery[] = ", o.lngInputUserCode as lngInputUserCode";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
	// ������
	$aryQuery[] = ", o.lngCustomerCompanyCode as lngCustomerCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
	// ����
	$aryQuery[] = ", o.lngGroupCode as lngInChargeGroupCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
	// ô����
	$aryQuery[] = ", o.lngUserCode as lngInChargeUserCode";
	$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
	$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
	// Ǽ�ʾ��
	$aryQuery[] = ", o.lngDeliveryPlaceCode as lngDeliveryPlaceCode";
	$aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryDisplayCode";
	$aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryDisplayName";
	// �̲�
	$aryQuery[] = ", o.lngMonetaryUnitCode as lngMonetaryUnitCode";
	$aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
	$aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	// �졼�ȥ�����
	$aryQuery[] = ", o.lngMonetaryRateCode as lngMonetaryRateCode";
	$aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
	// �����졼��
	$aryQuery[] = ", o.curConversionRate as curConversionRate";
	// ����
	$aryQuery[] = ", o.lngOrderStatusCode as lngOrderStatusCode";
	$aryQuery[] = ", os.strOrderStatusName as strOrderStatusName";
	// ��ʧ���
	$aryQuery[] = ", o.lngPayConditionCode as lngPayConditionCode";
	$aryQuery[] = ", pc.strPayConditionName as strPayConditionName";
	// ȯ��ͭ��������
	$aryQuery[] = ", to_char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
	// ����
	$aryQuery[] = ", o.strNote as strNote";
	// ��׶��
	$aryQuery[] = ", To_char( o.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";

	$aryQuery[] = " FROM m_Order o LEFT JOIN t_OrderDetail ot USING (lngOrderNo)";
	$aryQuery[] = " LEFT JOIN m_Product p ON ot.strproductCode = p.strproductCode";
	$aryQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Company delv_c ON o.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
	$aryQuery[] = " LEFT JOIN m_PayCondition pc ON o.lngPayConditionCode = pc.lngPayConditionCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON o.lngMonetaryRateCode = mr.lngMonetaryRateCode";

	$aryQuery[] = " WHERE o.lngOrderNo = " . $lngOrderNo . "";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* ���ꤵ�줿ȯ���ֹ椫��ȯ�����پ�����������ӣѣ�ʸ�����
*
*	����ȯ���ֹ�����پ���μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngOrderNo 			��������ȯ���ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetPurchaseDetailNoToInfoSQL ( $lngOrderNo )
{
// 2004.03.29 suzukaze update start
	// SQLʸ�κ���
//	$aryQuery[] = "SELECT distinct on (od.lngOrderDetailNo) od.lngOrderDetailNo as lngRecordNo, ";
	$aryQuery[] = "SELECT distinct on (od.lngSortKey) od.lngSortKey as lngRecordNo, ";
// 2004.03.29 suzukaze update end
	$aryQuery[] = "od.lngOrderNo as lngOrderNo, od.lngRevisionNo as lngRevisionNo";

	// ���ʥ����ɡ�̾��
	$aryQuery[] = ", od.strProductCode as strProductCode";
	$aryQuery[] = ", p.strProductName as strProductName";
	// ��������
	$aryQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
	$aryQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
	// ��������
	$aryQuery[] = ", od.lngStockItemCode as lngStockItemCode";
	$aryQuery[] = ", si.strStockItemName as strStockItemName";
	// �ⷿ�ֹ�
	$aryQuery[] = ", od.strMoldNo as strMoldNo";
	// �ܵ�����
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode";
	// ������ˡ
	$aryQuery[] = ", od.lngDeliveryMethodCode as lngDeliveryMethodCode";
	$aryQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
	// Ǽ��
	$aryQuery[] = ", od.dtmDeliveryDate as dtmDeliveryDate";
// 2004.03.17 suzukaze update start
	// ñ��
	$aryQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
//	$aryQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.99' )  as curProductPrice";
// 2004.03.17 suzukaze update start
	// ñ��
	$aryQuery[] = ", od.lngProductUnitCode as lngProductUnitCode";
	$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
	// ����
	$aryQuery[] = ", To_char( od.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// ��ȴ���
	$aryQuery[] = ", To_char( od.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// ��������
	$aryQuery[] = ", od.strNote as strDetailNote";

	// ���ٹԤ�ɽ��������
	$aryQuery[] = " FROM t_OrderDetail od LEFT JOIN m_Product p USING (strProductCode)";
	$aryQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
//	$aryQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)";
	$aryQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
	$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
	$aryQuery[] = ", m_StockItem si ";
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " ";
	$aryQuery[] = "AND si.lngStockSubjectCode = ss.lngStockSubjectCode ";
	$aryQuery[] = "AND od.lngStockItemCode = si.lngStockItemCode ";

// 2004.03.29 suzukaze update start
	$aryQuery[] = " ORDER BY od.lngSortKey ASC ";
// 2004.03.29 suzukaze update end

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* �ܺ�ɽ���ؿ��ʥإå��ѡ�
*
*	�ơ��֥빽����ȯ��ǡ����ܺ٤���Ϥ���ؿ�
*	�إå��Ԥ�ɽ������
*
*	@param  Array 	$aryResult 				�إå��Ԥθ�����̤���Ǽ���줿����
*	@access public
*/
function fncSetPurchaseHeadTabelData ( $aryResult )
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
		else if ( $strColumnName == "dtmorderappdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmorderappdate"] );
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
		else if ( $strColumnName == "lngorderstatuscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strorderstatusname"];
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
			// ���ͤ��ü�ʸ���Ѵ�
			$aryResult["strNote"] = fncHTMLSpecialChars( $aryResult["strNote"] );

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
*	�ơ��֥빽����ȯ��ǡ����ܺ٤���Ϥ���ؿ�
*	���ٹԤ�ɽ������
*
*	@param  Array 	$aryDetailResult 	���ٹԤθ�����̤���Ǽ���줿����ʣ��ǡ���ʬ��
*	@param  Array 	$aryHeadResult 		�إå��Ԥθ�����̤���Ǽ���줿����ʻ����ѡ�
*	@access public
*/
function fncSetPurchaseDetailTabelData ( $aryDetailResult, $aryHeadResult )
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

		// ��������
		else if ( $strColumnName == "strdetailnote" )
		{
			// ���ͤ��ü�ʸ���Ѵ�
			$aryDetailResult[$strColumnName] = fncHTMLSpecialChars( $aryDetailResult[$strColumnName] );

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
function fncSetPurchaseTabelName ( $aryResult, $aryTytle )
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
*	@param	Integer		$lngMode		�����⡼��	1:ȯ�����ɤ�������ޥ���	�ʽ缡�ɲá�
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
		case 1:		// ȯ�����ɤ�������ޥ����θ�����
			$strQuery .= "s.strStockCode) s.strStockCode as lngSearchNo FROM m_Stock s, m_Order o ";
			$strQuery .= "WHERE s.lngOrderNo = o.lngOrderNo AND s.bytInvalidFlag = FALSE AND o.strOrderCode = '";
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






/**
* �����NO�Υǡ�����¾�Υޥ����ǻ��Ѥ��Ƥ��륳���ɼ���
*
*	����NO���Ф��ơ����ꤵ�줿�ޥ����θ����ؿ�
*
*	@param  Integer 	$lngNo 			�����о�No
*	@param	Integer		$lngMode		�����⡼��	1:ȯ�����ɤ�������ޥ���	�ʽ缡�ɲá�
*	@param  Object		$objDB			DB���֥�������
*	@return Array 		$aryCode		�����оݥ����ɤ����Ѥ���Ƥ���ޥ�����Υ����ɤ�����
*	@access public
*/
function fncGetDeleteNoToMaster ( $lngNo, $lngMode, $objDB )
{
	// SQLʸ�κ���
	$strQuery = "SELECT distinct on (";
	switch ( $lngMode )
	{
		case 1:		// ȯ��No��������ޥ����θ�����
			$strQuery .= "s.lngOrderNo) s.lngOrderNo as lngSearchNo FROM m_Stock s ";
			$strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngOrderNo = ";
			break;
		case 2:		// ����No�������ޥ����θ�����
			$strQuery .= "s.lngReceiveNo) s.lngReceiveNo as lngSearchNo FROM m_Sales s ";
			$strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngReceiveNo = ";
			break;
	}
	$strQuery .= $lngNo;

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






/**
* �����ȯ��ǡ����ˤĤ���̵�������뤳�ȤǤɤ��ʤ뤫�������櫓����
*
*	�����ȯ��ǡ����ξ��֤�Ĵ�������������櫓����ؿ�
*
*	@param  Array 		$aryOrderData 	ȯ��ǡ���
*	@param  Object		$objDB			DB���֥�������
*	@return Integer 	$lngCase		���֤Υ�����
*										1: �о�ȯ��ǡ�����̵�������Ƥ⡢�ǿ���ȯ��ǡ������ƶ������ʤ�
*										2: �о�ȯ��ǡ�����̵�������뤳�Ȥǡ��ǿ���ȯ��ǡ����������ؤ��
*										3: �о�ȯ��ǡ���������ǡ����ǡ�ȯ�����褹��
*										4: �о�ȯ��ǡ�����̵�������뤳�Ȥǡ��ǿ���ȯ��ǡ����ˤʤꤦ��ȯ��ǡ������ʤ�
*	@access public
*/
function fncGetInvalidCodeToMaster ( $aryOrderData, $objDB )
{
	// ȯ�����ɤμ���
	$strOrderCode = substr( $aryOrderData["strordercode"], 0, 8);

	// ����о�ȯ���Ʊ��ȯ�����ɤκǿ���ȯ��No��Ĵ�٤�
	$strQuery = "SELECT lngOrderNo FROM m_Order o WHERE o.strOrderCode = '" . $strOrderCode . "' AND o.bytInvalidFlag = FALSE ";
	$strQuery .= " AND o.lngRevisionNo >= 0";
	$strQuery .= " AND o.lngRevisionNo = ( "
		. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode )";
	$strQuery .= " AND o.strReviseCode = ( "
		. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode )";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
	}
	else
	{
		$lngCase = 4;
	}
	$objDB->freeResult( $lngResultID );

	// ����оݤ��ǿ����ɤ����Υ����å�
	if ( $lngCase != 4 )
	{
		if ( $lngNewOrderNo == $aryOrderData["lngorderno"] )
		{
			// �ǿ��ξ��
			// ����о�ȯ��ʳ��Ǥ�Ʊ��ȯ�����ɤκǿ���ȯ��No��Ĵ�٤�
			$strQuery = "SELECT lngOrderNo FROM m_Order o WHERE o.strOrderCode = '" . $strOrderCode . "' AND o.bytInvalidFlag = FALSE ";
			$strQuery .= " AND o.lngOrderNo <> " . $aryOrderData["lngorderno"] . " AND o.lngRevisionNo >= 0";

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
		// �о�ȯ������ǡ������ɤ����γ�ǧ
		else if ( $aryOrderData["lngrevisionno"] < 0 )
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






?>