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
function fncGetPurchaseHeadNoToInfo ( $lngOrderNo, $lngRevisionNo, $objDB )
{
	// SQLʸ�κ���
	$aryQuery[] = "SELECT o.lngOrderNo as lngOrderNo, o.lngRevisionNo as lngRevisionNo";

	// ��Ͽ��
	$aryQuery[] = ", to_char( o.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate";
	// �׾���
	$aryQuery[] = ", to_char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
	// ȯ��No
	$aryQuery[] = ", o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode";
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
	$aryQuery[] = ", to_char( mp.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
	// // ����
	// $aryQuery[] = ", o.strNote as strNote";
	// ��׶��
	$aryQuery[] = ", To_char( ot.curSubTotalPrice, '9,999,999,990.99' ) as curSubTotalPrice";
	// ���ʥ����ɡ�����̾
	$aryQuery[] = ", ot.strProductCode as strProductCode";
	$aryQuery[] = ", p.strProductName as strProductName";

	$aryQuery[] = " FROM m_Order o INNER JOIN t_OrderDetail ot on ot.lngOrderNo = o.lngorderno and ot.lngrevisionno = o.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_Product p ON ot.strproductCode = p.strproductCode and ot.strrevisecode = p.strrevisecode and ot.lngrevisionno = p.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Company delv_c ON o.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON o.lngMonetaryRateCode = mr.lngMonetaryRateCode";
	$aryQuery[] = " LEFT JOIN t_purchaseorderdetail tp ON ot.lngorderno = tp.lngorderno AND ot.lngorderdetailno = tp.lngorderdetailno and ot.lngrevisionno = tp.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_purchaseorder mp on  tp.lngpurchaseorderno = mp.lngpurchaseorderno and tp.lngrevisionno = mp.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_PayCondition pc ON pc.lngPayConditionCode = mp.lngPayConditionCode";

	$aryQuery[] = " WHERE o.lngOrderNo = " . $lngOrderNo;
	$aryQuery[] = " AND   o.lngRevisionNo = " . $lngRevisionNo;

	$strQuery = implode( "\n", $aryQuery );
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum == 1 )
	{
		$aryOrderResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	else
	{
		fncOutputError( 503, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
	$objDB->freeResult( $lngResultID );

	return $aryOrderResult;
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
function fncGetPurchaseDetailNoToInfo ( $lngOrderNo, $lngRevisionNo, $objDB )
{
	// 2004.03.29 suzukaze update start
	// SQLʸ�κ���
	//	$aryQuery[] = "SELECT distinct on (od.lngOrderDetailNo) od.lngOrderDetailNo as lngRecordNo, ";
	$aryQuery[] = "SELECT od.lngOrderDetailNo as lngRecordNo, ";
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
	$aryQuery[] = ", to_char(od.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
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
	$aryQuery[] = ", od.lngOrderDetailNo as lngOrderDetailNo";

	// ���ٹԤ�ɽ��������
	$aryQuery[] = " FROM t_OrderDetail od INNER JOIN m_Product p ON od.strproductCode = p.strproductCode and od.strrevisecode = p.strrevisecode and od.lngrevisionno = p.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
	//	$aryQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)";
	$aryQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
	$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
	$aryQuery[] = ", m_StockItem si ";
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " ";
	$aryQuery[] = "AND od.lngRevisionNo = " . $lngRevisionNo . " ";
	$aryQuery[] = "AND si.lngStockSubjectCode = ss.lngStockSubjectCode ";
	$aryQuery[] = "AND od.lngStockItemCode = si.lngStockItemCode ";

	// 2004.03.29 suzukaze update start
	$aryQuery[] = " ORDER BY od.lngSortKey ASC ";
	// 2004.03.29 suzukaze update end

	$strQuery = implode( "\n", $aryQuery );
	// ���٥ǡ����μ���
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		$aryDetailResult = $objDB->fetchArray( $lngResultID, 0 );
	}

	$objDB->freeResult( $lngResultID );

	return $aryDetailResult;
}

/**
 * ��ä�Ԥä�ȯ��ǡ�����HTML�����
 * 
 * @param	Array	$aryOrder		ȯ��ǡ���
 * @param	Array	$aryOrderDetail	ȯ�����٥ǡ���
 * @access	public
 * 
 */
function fncDeletePurchaseOrderHtml($aryOrder, $aryOrderDetail){
	$aryHtml[] = "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">��Ͽ��</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["dtminsertdate"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">ȯ��ͭ��������</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["dtmexpirationdate"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">ȯ��NO.</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["strordercode"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">���ʥ�����</td>";
	$aryHtml[] = "    <td class=\"Segs\">[" . $aryOrder["strproductcode"] . "]</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">����̾</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["strproductname"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">�Ķ�����</td>";
	$aryHtml[] = "    <td class=\"Segs\">[" . $aryOrder["strinchargegroupdisplaycode"] . "] " . $aryOrder["strinchargegroupdisplayname"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">��ȯô����</td>";
	$aryHtml[] = "    <td class=\"Segs\">[" . $aryOrder["strinchargeuserdisplaycode"] . "] " . $aryOrder["strinchargeUserdisplayname"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">��������</td>";
	$aryHtml[] = "    <td class=\"Segs\">[" . $aryOrderDetail["lngstocksubjectcode"] . "] " . $aryOrderDetail["strstocksubjectname"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">������</td>";
	$aryHtml[] = "    <td class=\"Segs\">[" . $aryOrder["strcustomerdisplaycode"] . "] " . $aryOrder["strcustomerdisplayname"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">Ǽ��</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrderDetail["dtmdeliverddate"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">ñ��</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["strmonetaryunitsign"] . $aryOrderDetail["curproductprice"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">����</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrderDetail["lngproductquantity"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">��ȴ���</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["strmonetaryunitsign"] . $aryOrderDetail["cursubtotalprice"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "  <tr>";
	$aryHtml[] = "    <td class=\"SegColumn\">��������</td>";
	$aryHtml[] = "    <td class=\"Segs\">" . $aryOrderDetail["strdetailnote"] . "</td>";
	$aryHtml[] = "  </tr>";
	$aryHtml[] = "</table>";
	$aryHtml[] = "<br>";

	$strHtml = implode("\n", $aryHtml);
	return $strHtml;
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
			$strQuery .= "WHERE s.lngStockNo = o.lngOrderNo AND s.bytInvalidFlag = FALSE AND o.strOrderCode = '";
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

/**
 * ȯ����󥻥�
 * 
 * @param	Integer		$lngOrderNo		ȯ��NO
 * @param	Integer		$lngRevisionNo	��ӥ�����ֹ�
 * @param	Object		$objDB			DB���֥�������
 * @access	public
 * 
 */
function fncGetCancelOrder($lngOrderNo, $lngRevisionNo, $objDB){
	$arySql[] = "UPDATE m_order SET lngorderstatuscode = 1 ";
	$arySql[] = "WHERE lngorderno = " . intval($lngOrderNo) . " ";
	$arySql[] = "AND   lngrevisionno = " . intval($lngRevisionNo) . " ";

	$strQuery = implode("\n", $arySql);
	if ( !$lngResultID = $objDB->execute( $strSql ) )
	{
		//fncOutputError ( 9051, DEF_ERROR, "�ǡ����١����ι����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		return FALSE;
	}
	$objDB->freeResult( $lngResultID );
	return true;
}

/**
 * ȯ���ޥ�������(ȯ�����ɤǸ���)
 * 
 * @param	String		$strOrderCode	ȯ������
 * @param	Integer		$lngRevisionNo	��ӥ�����ֹ�
 * @param	Object		$objDB			DB���֥�������
 * @access	public
 * 
 */
function fncGetPurchaseOrder($strOrderCode, $lngRevisionNo, $objDB){
	$arySql[] = "SELECT";
	$arySql[] = "   lngpurchaseorderno";
	$arySql[] = "  ,lngrevisionno";
	$arySql[] = "  ,strordercode"; 
	$arySql[] = "  ,lngcustomercode"; 
	$arySql[] = "  ,strcustomername"; 
	$arySql[] = "  ,strcustomercompanyaddreess"; 
	$arySql[] = "  ,strcustomercompanytel"; 
	$arySql[] = "  ,strcustomercompanyfax"; 
	$arySql[] = "  ,strproductcode"; 
	$arySql[] = "  ,strrevisecode"; 
	$arySql[] = "  ,strproductname"; 
	$arySql[] = "  ,strproductenglishname"; 
	$arySql[] = "  ,TO_CHAR(dtmexpirationdate, 'YYYY/MM/DD')"; 
	$arySql[] = "  ,lngmonetaryunitcode"; 
	$arySql[] = "  ,strmonetaryunitsign"; 
	$arySql[] = "  ,lngmonetaryratecode"; 
	$arySql[] = "  ,strmonetaryratename";
	$arySql[] = "  ,lngpayconditioncode";
	$arySql[] = "  ,strpayconditionname";
	$arySql[] = "  ,lnggroupcode";
	$arySql[] = "  ,strgroupname";
	$arySql[] = "  ,txtsignaturefilename";
	$arySql[] = "  ,lngusercode";
	$arySql[] = "  ,strusername";
	$arySql[] = "  ,lngdeliveryplacecode";
	$arySql[] = "  ,strdeliveryplacename";
	$arySql[] = "  ,curtotalprice";
	$arySql[] = "  ,TO_CHAR(dtminsertdate, 'YYYY/MM/DD')";
	$arySql[] = "  ,lnginsertusercode";
	$arySql[] = "  ,strinsertusername"; 
	$arySql[] = "  ,strnote"; 
	$arySql[] = "  ,lngprintcount"; 
	$arySql[] = "FROM m_purchaseorder";
	$arySql[] = "WHERE strordercode = '" . $strOrderCode . "'";
	$arySql[] = "AND   lngrevisionno = " . intval($lngRevisionNo);

	$strQuery = implode("\n", $arySql);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum == 1 )
	{
		$aryPurchaseOrder = $objDB->fetchArray( $lngResultID, 0 );
	}
	$objDB->freeResult( $lngResultID );

	return $aryPurchaseOrder;
}

/**
 * ȯ���ޥ�������(ȯ����ֹ�Ǹ���)
 * 
 * @param	Integer		$lngPurchaseOrderCode	ȯ����ֹ�
 * @param	Integer		$lngRevisionNo			��ӥ�����ֹ�
 * @param	Object		$objDB					DB���֥�������
 * @access	public
 * 
 */
function fncGetPurchaseOrder2($lngPurchaseOrderCode, $lngRevisionNo, $objDB){
	$arySql[] = "SELECT";
	$arySql[] = "   mp.lngpurchaseorderno";
	$arySql[] = "  ,mp.lngrevisionno";
	$arySql[] = "  ,mp.strordercode"; 
	$arySql[] = "  ,mp.lngcustomercode"; 
	$arySql[] = "  ,mp.strcustomername"; 
	$arySql[] = "  ,mp.strcustomercompanyaddreess"; 
	$arySql[] = "  ,mp.strcustomercompanytel"; 
	$arySql[] = "  ,mp.strcustomercompanyfax"; 
	$arySql[] = "  ,mp.strproductcode"; 
	$arySql[] = "  ,mp.strrevisecode"; 
	$arySql[] = "  ,mp.strproductname"; 
	$arySql[] = "  ,mp.strproductenglishname"; 
	$arySql[] = "  ,TO_CHAR(mp.dtmexpirationdate, 'YYYY/MM/DD') AS dtmexpirationdate"; 
	$arySql[] = "  ,mp.lngmonetaryunitcode"; 
	$arySql[] = "  ,mp.strmonetaryunitsign"; 
	$arySql[] = "  ,mp.lngmonetaryratecode"; 
	$arySql[] = "  ,mp.strmonetaryratename";
	$arySql[] = "  ,mp.lngpayconditioncode";
	$arySql[] = "  ,mp.strpayconditionname";
	$arySql[] = "  ,mp.lnggroupcode";
	$arySql[] = "  ,mp.strgroupname";
	$arySql[] = "  ,mp.txtsignaturefilename";
	$arySql[] = "  ,mp.lngusercode";
	$arySql[] = "  ,mp.strusername";
	$arySql[] = "  ,mp.lngdeliveryplacecode";
	$arySql[] = "  ,mp.strdeliveryplacename";
	$arySql[] = "  ,mp.curtotalprice";
	$arySql[] = "  ,TO_CHAR(mp.dtminsertdate, 'YYYY/MM/DD') AS dtminsertdate";
	$arySql[] = "  ,mp.lnginsertusercode";
	$arySql[] = "  ,mp.strinsertusername"; 
	$arySql[] = "  ,mp.strnote"; 
	$arySql[] = "  ,mp.lngprintcount"; 
	$arySql[] = "  ,mc.strcompanydisplaycode";
	$arySql[] = "FROM m_purchaseorder mp";
	$arySql[] = "LEFT JOIN m_company mc ON mp.lngcustomercode = mc.lngcompanycode";
	$arySql[] = "WHERE lngpurchaseorderno = " . intval($lngPurchaseOrderCode);
	$arySql[] = "AND   lngrevisionno = " . intval($lngRevisionNo);

	$strQuery = implode("\n", $arySql);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum == 1 )
	{
		$aryPurchaseOrder = $objDB->fetchArray( $lngResultID, 0 );
	}
	$objDB->freeResult( $lngResultID );

	return $aryPurchaseOrder;
}

/**
 * ȯ���ޥ���������SQL����
 * 
 * @param	Integer		$lngOrderNo			ȯ���ֹ�
 * @param	Integer		$lngRevisionNo		��ӥ�����ֹ�
 * @access	public
 * 
 */
function fncGetPurchaseOrderDetailSQL($lngOrderNo, $lngRevisionNo){
	$arySql[] = "SELECT";
	$arySql[] = "   lngpurchaseorderno";
	$arySql[] = "  ,lngpurchaseorderdetailno";
	$arySql[] = "  ,lngrevisionno";
	$arySql[] = "  ,lngorderno";
	$arySql[] = "  ,lngorderdetailno";
	$arySql[] = "  ,lngorderrevisionno";
	$arySql[] = "  ,lngstockitemcode";
	$arySql[] = "  ,strstockitemname";
	$arySql[] = "  ,lngdeliverymethodcode";
	$arySql[] = "  ,strdeliverymethodname";
	$arySql[] = "  ,curproductprice";
	$arySql[] = "  ,lngproductquantity";
	$arySql[] = "  ,lngproductunitcode";
	$arySql[] = "  ,strproductunitname";
	$arySql[] = "  ,cursubtotalprice";
	$arySql[] = "  ,to_char(dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
	$arySql[] = "  ,strnote";
	$arySql[] = "  ,lngsortkey";
	$arySql[] = "FROM t_purchaseorderdetail";
	$arySql[] = "WHERE lngpurchaseorderno = " . intval($lngOrderNo);
	$arySql[] = "AND   lngrevisionno = " . intval($lngRevisionNo);

	return implode("\n", $arySql);
}

/**
 * ȯ������ټ�����SQL����
 * 
 * @param	Array		$aryPurchaseOrderDetail		ȯ�������
 * @access	public
 * 
 */
function fncInsertPurchaseOrderDetailSQL($aryPurchaseOrderDetail){
	$arySql[] = "INSERT INTO t_purchaseorderdetail (";
	$arySql[] = "   lngpurchaseorderno";
	$arySql[] = "  ,lngpurchaseorderdetailno";
	$arySql[] = "  ,lngrevisionno";
	$arySql[] = "  ,lngorderno";
	$arySql[] = "  ,lngorderdetailno";
	$arySql[] = "  ,lngorderrevisionno";
	$arySql[] = "  ,lngstockitemcode";
	$arySql[] = "  ,strstockitemname";
	$arySql[] = "  ,lngdeliverymethodcode";
	$arySql[] = "  ,strdeliverymethodname";
	$arySql[] = "  ,curproductprice";
	$arySql[] = "  ,lngproductquantity";
	$arySql[] = "  ,lngproductunitcode";
	$arySql[] = "  ,strproductunitname";
	$arySql[] = "  ,cursubtotalprice";
	$arySql[] = "  ,dtmdeliverydate";
	$arySql[] = "  ,strnote";
	$arySql[] = "  ,lngsortkey";
	$arySql[] = ") VALUES (";
	$arySql[] = "   "  . intval($aryPurchaseOrderDetail["lngpurchaseorderno"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngpurchaseorderdetailno"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngrevisionno"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngorderno"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngorderdetailno"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngorderrevisionno"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngstockitemcode"]);
	$arySql[] = "  ,'" . $aryPurchaseOrderDetail["strstockitemname"] . "'";
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngdeliverymethodcode"]);
	$arySql[] = "  ,'" . $aryPurchaseOrderDetail["strdeliverymethodname"] . "'";
	$arySql[] = "  ,"  . floatval($aryPurchaseOrderDetail["curproductprice"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngproductquantity"]);
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngproductunitcode"]);
	$arySql[] = "  ,'" . $aryPurchaseOrderDetail["strproductunitname"] . "'";
	$arySql[] = "  ,"  . floatval($aryPurchaseOrderDetail["cursubtotalprice"]);
	$arySql[] = "  ,'" . $aryPurchaseOrderDetail["dtmdeliverydate"] . "'";
	$arySql[] = "  ,'" . $aryPurchaseOrderDetail["strnote"] . "'";
	$arySql[] = "  ,"  . intval($aryPurchaseOrderDetail["lngsortkey"]);
	$arySql[] = ")";

	return implode("\n", $arySql);
}

/**
 * ����оݤ�ȯ���ǡ�������
 * 
 * @param	Integer		$lngOrderNo		ȯ���ֹ�
 * @param	Integer		$lngRevisionNo	��ӥ�����ֹ�
 * @param	Object		$objDB					DB���֥�������
 * @access	public
 *	
 */
function fncGetDeletePurchaseOrderDetail($lngOrderNo, $lngRevisionNo, $objDB){
	$aryQuery[] = "SELECT";
	$aryQuery[] = "   all_detail.lngpurchaseorderno";
	$aryQuery[] = "  ,all_detail.lngpurchaseorderdetailno";
	$aryQuery[] = "  ,all_detail.lngrevisionno";
	$aryQuery[] = "  ,all_detail.lngorderno";
	$aryQuery[] = "  ,all_detail.lngorderdetailno";
	$aryQuery[] = "  ,all_detail.lngorderrevisionno";
	$aryQuery[] = "  ,all_detail.lngstocksubjectcode";
	$aryQuery[] = "  ,all_detail.lngstockitemcode";
	$aryQuery[] = "  ,all_detail.strstockitemname";
	$aryQuery[] = "  ,all_detail.lngdeliverymethodcode";
	$aryQuery[] = "  ,all_detail.strdeliverymethodname";
	$aryQuery[] = "  ,all_detail.curproductprice";
	$aryQuery[] = "  ,all_detail.lngproductquantity";
	$aryQuery[] = "  ,all_detail.lngproductunitcode";
	$aryQuery[] = "  ,all_detail.strproductunitname";
	$aryQuery[] = "  ,all_detail.cursubtotalprice";
	$aryQuery[] = "  ,TO_CHAR(all_detail.dtmdeliverydate, 'YYYY/MM/DD') as dtmdeliverydate";
	$aryQuery[] = "  ,all_detail.strnote";
	$aryQuery[] = "  ,all_detail.lngsortkey";
	$aryQuery[] = "FROM t_purchaseorderdetail target ";
	$aryQuery[] = "INNER JOIN t_purchaseorderdetail all_detail";
	$aryQuery[] = "    ON all_detail.lngpurchaseorderno = target.lngpurchaseorderno";
	$aryQuery[] = "    AND all_detail.lngrevisionno = target.lngrevisionno ";
	$aryQuery[] = "INNER JOIN(";
	$aryQuery[] = "SELECT lngpurchaseorderno, lngpurchaseorderdetailno, MAX(lngrevisionno) AS lngrevisionno FROM t_purchaseorderdetail GROUP BY lngpurchaseorderno, lngpurchaseorderdetailno";
	$aryQuery[] = ") rev_max ";
	$aryQuery[] = "on rev_max.lngpurchaseorderno = all_detail.lngpurchaseorderno ";
	$aryQuery[] = "AND rev_max.lngpurchaseorderdetailno = all_detail.lngpurchaseorderdetailno ";
	$aryQuery[] = "AND rev_max.lngrevisionno = all_detail.lngrevisionno ";
	$aryQuery[] = "WHERE target.lngorderno = "  . $lngOrderNo;
	$aryQuery[] = " AND target.lngorderrevisionno = "  .$lngRevisionNo;
	$aryQuery[] = " ORDER BY";
	$aryQuery[] = " all_detail.lngsortkey";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryCode = FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return $aryResult;

}

/**
 * ȯ����
 * 
 * @param	Integer		$lngOrderNo		ȯ���ֹ�
 * @param	Integer		$lngRevisionNo	��ӥ�����ֹ�
 * @param	Object		$objDB					DB���֥�������
 * @access	public
 * 
 */
function fncCancelOrder($lngOrderNo, $lngRevisionNo, $objDB){
	$aryQuery[] = "UPDATE m_order SET";
	$aryQuery[] = "   lngorderstatuscode = 1";
	$aryQuery[] = "WHERE lngorderno = "  . $lngOrderNo;
	$aryQuery[] = "AND   lngrevisionno = "  . $lngRevisionNo;

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "ȯ��ޥ����ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		return FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return true;
}

/**
 * ȯ�����ټ��
 * 
 * @param	Integer		$lngOrderNo		ȯ���ֹ�
 * @param	Integer		$lngRevisionNo	��ӥ�����ֹ�
 * @param	Object		$objDB					DB���֥�������
 * @access	public
 * 
 */
function fncGetDeleteOrderDetail($lngOrderNo, $lngRevisionNo, $objDB){
	$aryQuery[] = "SELECT";
	$aryQuery[] = "   lngorderno";
	$aryQuery[] = "  ,lngorderdetailno";
	$aryQuery[] = "  ,lngrevisionno";
	$aryQuery[] = "FROM t_orderdetail";
	$aryQuery[] = "WHERE lngorderno = "  . $lngOrderNo;
	$aryQuery[] = "AND   lngrevisionno = "  .$lngRevisionNo;

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryCode = FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return $aryResult;

}

/**
 * ȯ���������Ͽ
 * 
 * @param	Array		$aryDetail		ȯ������٥ǡ���
 * @param	Object		$objDB			DB���֥�������
 * @access	public
 * 
 */
function fncInsertPurchaseOrderDetail($aryDetail, $objDB){
	$aryQuery[] = "INSERT INTO t_purchaseorderdetail (";
	$aryQuery[] = "   lngpurchaseorderno";
	$aryQuery[] = "  ,lngpurchaseorderdetailno";
	$aryQuery[] = "  ,lngrevisionno";
	$aryQuery[] = "  ,lngorderno";
	$aryQuery[] = "  ,lngorderdetailno";
	$aryQuery[] = "  ,lngorderrevisionno";
	$aryQuery[] = "  ,lngstocksubjectcode";
	$aryQuery[] = "  ,lngstockitemcode";
	$aryQuery[] = "  ,strstockitemname";
	$aryQuery[] = "  ,lngdeliverymethodcode";
	$aryQuery[] = "  ,strdeliverymethodname";
	$aryQuery[] = "  ,curproductprice";
	$aryQuery[] = "  ,lngproductquantity";
	$aryQuery[] = "  ,lngproductunitcode";
	$aryQuery[] = "  ,strproductunitname";
	$aryQuery[] = "  ,cursubtotalprice";
	$aryQuery[] = "  ,dtmdeliverydate";
	$aryQuery[] = "  ,strnote";
	$aryQuery[] = "  ,lngsortkey";
	$aryQuery[] = ") VALUES (";
	$aryQuery[] = "   "  . $aryDetail["lngpurchaseorderno"];
	$aryQuery[] = "  ,"  . $aryDetail["lngpurchaseorderdetailno"];
	$aryQuery[] = "  ,"  . $aryDetail["lngrevisionno"];
	$aryQuery[] = "  ,"  . $aryDetail["lngorderno"];
	$aryQuery[] = "  ,"  . $aryDetail["lngorderdetailno"];
	$aryQuery[] = "  ,"  . $aryDetail["lngorderrevisionno"];
	$aryQuery[] = "  ,"  . ($aryDetail["lngstocksubjectcode"] ? $aryDetail["lngstocksubjectcode"] : 'null');
	$aryQuery[] = "  ,"  . ($aryDetail["lngstockitemcode"] ? $aryDetail["lngstockitemcode"] : 'null');
	$aryQuery[] = "  ,'" . $aryDetail["strstockitemname"] . "'";
	$aryQuery[] = "  ,"  . $aryDetail["lngdeliverymethodcode"];
	$aryQuery[] = "  ,'" . $aryDetail["strdeliverymethodname"] . "'";
	$aryQuery[] = "  ,"  . $aryDetail["curproductprice"];
	$aryQuery[] = "  ,"  . $aryDetail["lngproductquantity"];
	$aryQuery[] = "  ,"  . $aryDetail["lngproductunitcode"];
	$aryQuery[] = "  ,'" . $aryDetail["strproductunitname"] . "'";
	$aryQuery[] = "  ,"  . $aryDetail["cursubtotalprice"];
	$aryQuery[] = "  ,"  . ($aryDetail["dtmdeliverydate"] ? "'" . $aryDetail["dtmdeliverydate"] . "'" : 'null');
	$aryQuery[] = "  ,'" . $aryDetail["strnote"] . "'";
	$aryQuery[] = "  ,"  . $aryDetail["lngsortkey"];
	$aryQuery[] = ")";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "ȯ������٤ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		return FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return true;
}

/**
 * ȯ�����Ͽ
 * 
 * @param	Array		$aryOrder		ȯ���ǡ���
 * @param	Object		$objDB			DB���֥�������
 * @access	public
 * 
 */
function fncInsertPurchaseOrder($aryOrder, $objDB){
	$aryQuery[] = "INSERT INTO m_purchaseorder (";
	$aryQuery[] = "   lngpurchaseorderno";
	$aryQuery[] = "  ,lngrevisionno";
	$aryQuery[] = "  ,strordercode";
	$aryQuery[] = "  ,lngcustomercode";
	$aryQuery[] = "  ,strcustomername";
	$aryQuery[] = "  ,strcustomercompanyaddreess";
	$aryQuery[] = "  ,strcustomercompanytel";
	$aryQuery[] = "  ,strcustomercompanyfax";
	$aryQuery[] = "  ,strproductcode";
	$aryQuery[] = "  ,strrevisecode";
	$aryQuery[] = "  ,strproductname";
	$aryQuery[] = "  ,strproductenglishname";
	$aryQuery[] = "  ,dtmexpirationdate";
	$aryQuery[] = "  ,lngmonetaryunitcode";
	$aryQuery[] = "  ,strmonetaryunitname";
	$aryQuery[] = "  ,strmonetaryunitsign";
	$aryQuery[] = "  ,lngmonetaryratecode";
	$aryQuery[] = "  ,strmonetaryratename";
	$aryQuery[] = "  ,lngpayconditioncode";
	$aryQuery[] = "  ,strpayconditionname";
	$aryQuery[] = "  ,lnggroupcode";
	$aryQuery[] = "  ,strgroupname";
	$aryQuery[] = "  ,txtsignaturefilename";
	$aryQuery[] = "  ,lngusercode";
	$aryQuery[] = "  ,strusername";
	$aryQuery[] = "  ,lngdeliveryplacecode";
	$aryQuery[] = "  ,strdeliveryplacename";
	$aryQuery[] = "  ,curtotalprice";
	$aryQuery[] = "  ,dtminsertdate";
	$aryQuery[] = "  ,lnginsertusercode";
	$aryQuery[] = "  ,strinsertusername";
	$aryQuery[] = "  ,strnote";
	$aryQuery[] = "  ,lngprintcount";
	$aryQuery[] = ") VALUES (";
	$aryQuery[] = "   "  . $aryOrder["lngpurchaseorderno"];
	$aryQuery[] = "  ,"  . $aryOrder["lngrevisionno"];
	$aryQuery[] = "  ,'" . $aryOrder["strordercode"] . "'";
	$aryQuery[] = "  ,"  . $aryOrder["lngcustomercode"];
	$aryQuery[] = "  ,'" . $aryOrder["strcustomername"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strcustomercompanyaddreess"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strcustomercompanytel"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strcustomercompanyfax"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strproductcode"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strrevisecode"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strproductname"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strproductenglishname"] . "'";
	$aryQuery[] = "  ,"  . ($aryOrder["dtmexpirationdate"] ? "'" . $aryOrder["dtmexpirationdate"] . "'" : 'null');
	$aryQuery[] = "  ,"  . $aryOrder["lngmonetaryunitcode"];
	$aryQuery[] = "  ,'" . $aryOrder["strmonetaryunitname"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strmonetaryunitsign"] . "'";
	$aryQuery[] = "  ,"  . ($aryOrder["lngmonetaryratecode"] ? $aryOrder["lngmonetaryratecode"] : 'null');
	$aryQuery[] = "  ,'" . $aryOrder["strmonetaryratename"] . "'";
	$aryQuery[] = "  ,"  . ($aryOrder["lngpayconditioncode"] ? $aryOrder["lngpayconditioncode"] : 'null');
	$aryQuery[] = "  ,'" . $aryOrder["strpayconditionname"] . "'";
	$aryQuery[] = "  ,"  . ($aryOrder["lnggroupcode"] ? $aryOrder["lnggroupcode"] : 'null');
	$aryQuery[] = "  ,'" . $aryOrder["strgroupname"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["txtsignaturefilename"] . "'";
	$aryQuery[] = "  ,"  . $aryOrder["lngusercode"];
	$aryQuery[] = "  ,'" . $aryOrder["strusername"] . "'";
	$aryQuery[] = "  ,"  . ($aryOrder["lngdeliveryplacecode"] ? $aryOrder["lngdeliveryplacecode"] : 'null');
	$aryQuery[] = "  ,'" . $aryOrder["strdeliveryplacename"] . "'";
	$aryQuery[] = "  ,"  . $aryOrder["curtotalprice"];
	$aryQuery[] = "  ,"  . "NOW()";
	$aryQuery[] = "  ,"  . $aryOrder["lnginsertusercode"];
	$aryQuery[] = "  ,'" . $aryOrder["strinsertusername"] . "'";
	$aryQuery[] = "  ,'" . $aryOrder["strnote"] . "'";
	$aryQuery[] = "  ,"  . $aryOrder["lngprintcount"];
	$aryQuery[] = ")";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery );
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "ȯ���ޥ����ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
		return FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return true;
}

/**
 * ȯ��ޥ�������
 * 
 * @param	Integer		$lngOrderNo		ȯ���ֹ�
 * @param	Integer		$lngRevisionNo	��ӥ�����ֹ�
 * @param	Object		$objDB			DB���֥�������
 * @access	public
 * 
 */
function fncGetOrder($lngOrderNo, $lngRevisionNo, $objDB){
	$aryQuery[] = "SELECT";
	$aryQuery[] = "   TO_CHAR(mo.dtminsertdate, 'YYYY/MM/DD') AS dtminsertdate";
	$aryQuery[] = "  ,TO_CHAR(po.dtmexpirationdate, 'YYYY/MM/DD') AS dtmexpirationdate";
	$aryQuery[] = "  ,mo.strordercode";
	$aryQuery[] = "  ,mo.lngrevisionno";
	$aryQuery[] = "  ,od.strproductcode";
	$aryQuery[] = "  ,mp.strproductname";
	$aryQuery[] = "  ,mo.lnggroupcode";
	$aryQuery[] = "  ,mg.strgroupdisplaycode";
	$aryQuery[] = "  ,mg.strgroupdisplayname";
	$aryQuery[] = "  ,mo.lngusercode";
	$aryQuery[] = "  ,mu.struserdisplaycode";
	$aryQuery[] = "  ,mu.struserdisplayname";
	$aryQuery[] = "  ,od.lngstockitemcode";
	$aryQuery[] = "  ,ms.lngstocksubjectcode";
	$aryQuery[] = "  ,ms.strstockitemname";
	$aryQuery[] = "  ,mo.lngcustomercompanycode";
	$aryQuery[] = "  ,mc.strcompanydisplaycode";
	$aryQuery[] = "  ,mc.strcompanydisplayname";
	$aryQuery[] = "  ,TO_CHAR(od.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
	$aryQuery[] = "  ,od.curproductprice";
	$aryQuery[] = "  ,mm.strmonetaryunitsign";
	$aryQuery[] = "  ,od.lngproductquantity";
	$aryQuery[] = "  ,od.cursubtotalprice";
	$aryQuery[] = "  ,mpu.strproductunitname";
	$aryQuery[] = "  ,od.strnote as strdetailnote";
	$aryQuery[] = "  ,TO_CHAR(od.dtmdeliverydate, 'YYYY/MM/DD') as dtmdeliverydate";
	$aryQuery[] = "FROM m_order mo";
	$aryQuery[] = "LEFT JOIN t_orderdetail od ON mo.lngorderno = od.lngorderno AND mo.lngrevisionno = od.lngrevisionno";
	$aryQuery[] = "LEFT JOIN t_purchaseorderdetail tp ON od.lngorderno = tp.lngorderno AND od.lngrevisionno = tp.lngorderrevisionno";
	$aryQuery[] = "LEFT JOIN m_purchaseorder po ON tp.lngpurchaseorderno = po.lngpurchaseorderno AND tp.lngrevisionno = po.lngrevisionno";
	$aryQuery[] = "LEFT JOIN m_product mp ON od.strproductcode = mp.strproductcode and od.lngrevisionno = mp.lngrevisionno and od.strrevisecode = mp.strrevisecode";
	$aryQuery[] = "LEFT JOIN m_group mg ON mo.lnggroupcode = mg.lnggroupcode";
	$aryQuery[] = "LEFT JOIN m_user mu ON mo.lngusercode = mu.lngusercode";
	$aryQuery[] = "LEFT JOIN m_stockitem ms ON od.lngstockitemcode = ms.lngstockitemcode and od.lngstocksubjectcode = ms.lngstocksubjectcode";
	$aryQuery[] = "LEFT JOIN m_company mc ON mo.lngcustomercompanycode = mc.lngcompanycode";
	$aryQuery[] = "LEFT JOIN m_monetaryunit mm ON mo.lngmonetaryunitcode = mm.lngmonetaryunitcode";
	$aryQuery[] = "LEFT JOIN m_productunit mpu ON od.lngproductunitcode = mpu.lngproductunitcode";
	$aryQuery[] = "WHERE mo.lngorderno = " . $lngOrderNo;
	$aryQuery[] = "AND   mo.lngrevisionno = " . $lngRevisionNo;

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	$objDB->freeResult( $lngResultID );

	return $aryResult;
}

/**
 * ȯ����󥻥�ǡ���HTML����
 * 
 * @param	Array		$aryOrder		ȯ��ǡ���
 * @access	public
 * 
 */
function fncCancelOrderHtml($aryOrder){
	foreach($aryOrder as $row){
		$aryHtml[] = "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">��Ͽ��</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["dtminsertdate"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">ȯ��ͭ��������</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["dtmexpirationdate"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">ȯ��NO.</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["strordercode"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">���ʥ�����</td>";
		$aryHtml[] = "    <td class=\"Segs\">[" . $row["strproductcode"] . "]</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">����̾</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["strproductname"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">�Ķ�����</td>";
		$aryHtml[] = "    <td class=\"Segs\">[" . $row["strgroupdisplaycode"] . "] " . $row["strgroupdisplayname"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">��ȯô����</td>";
		$aryHtml[] = "    <td class=\"Segs\">[" . $row["struserdisplaycode"] . "] " . $row["struserdisplayname"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">��������</td>";
		$aryHtml[] = "    <td class=\"Segs\">[" . $row["lngstocksubjectcode"] . "] " . $row["strstockitemname"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">������</td>";
		$aryHtml[] = "    <td class=\"Segs\">[" . $row["strcompanydisplaycode"] . "] " . $row["strcompanydisplayname"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">Ǽ��</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["dtmdeliverydate"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">ñ��</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["strmonetaryunitsign"] . " " . $row["curproductprice"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">����</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["lngproductquantity"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">��ȴ���</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["strmonetaryunitsign"] . " " . $row["cursubtotalprice"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"SegColumn\">��������</td>";
		$aryHtml[] = "    <td class=\"Segs\">" . $row["strdetailnote"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "</table>";
		$aryHtml[] = "<br>";
	}

	$strHtml = implode("\n", $aryHtml);
	return $strHtml;
}

/**
 * ȯ��񥭥�󥻥�ǡ���HTML����
 * 
 * @param	Array		$aryOrder		ȯ���ǡ���
 * @param	Array		$aryDetail		ȯ������٥ǡ���
 * @access	public
 * 
 */
function fncCancelPurchaseOrderHtml($aryOrder, $aryDetail){
	for($i = 0; $i < count($aryDetail); $i++) {
		$strUrl = "/list/result/po/listoutput.php?strReportKeyCode=" . $aryDetail[$i]["lngpurchaseorderno"] . "&strSessionID=" . $strSessionID;
		$aryHtml[] = "<table class=\"ordercode\">";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <td class=\"ordercodetd\">" . sprintf("%s_%02d", $aryOrder["strordercode"], $aryOrder["lngrevisionno"]) . "</td>";
		$aryHtml[] = "    <td class=\"orderbuttontd\"><a href=\"" . $strUrl . "\"><img src=\"/img/type01/cmn/querybt/preview_off_ja_bt.gif\" alt=\"preview\"></a></td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "</table>";
		$aryHtml[] = "<p class=\"caption\">����о�</p>";
		$aryHtml[] = "<table class=\"orderdetail\">";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">��Ͽ��</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["dtminsertdate"] . "</td>";
		$aryHtml[] = "    <th class=\"SegColumn\">ȯ��ͭ��������</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . $aryOrder["dtmexpirationdate"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">ȯ��NO.</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . sprintf("%s_%02d", $aryOrder["strordercode"], $aryOrder["lngrevisionno"]) . "</td>";
		$aryHtml[] = "    <th class=\"SegColumn\">��������</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryDetail[$i]["lngstockitemcode"], $aryDetail[$i]["strstockitemname"]) . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">����̾</th>";
		$aryHtml[] = "    <td colspan=\"3\" class=\"Segs\">" . sprintf("[%s] %s", $aryOrder["strproductcode"], $aryOrder["strproductname"]) . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">�Ķ�����</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryOrder["lnggroupcode"], $aryOrder["strgroupname"]) . "</td>";
		$aryHtml[] = "    <th class=\"SegColumn\">��ȯô����</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryOrder["lngusercode"], $aryOrder["strusername"]) . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">������</th>";
		$aryHtml[] = "    <td colspan=\"3\" class=\"Segs\">". sprintf("[%s] %s", $aryOrder["strcompanydisplaycode"], $aryOrder["strcustomername"]) . "</td>";
		$aryHtml[] = "  </th>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">Ǽ��</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . $aryDetail[$i]["dtmdeliverydate"] . "</td>";
		$aryHtml[] = "    <th class=\"SegColumn\">ñ��</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . sprintf("%s %.4f", $aryOrder["strmonetaryunitsign"], $aryDetail[$i]["curproductprice"]) . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">����</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . $aryDetail[$i]["lngproductquantity"] . "</td>";
		$aryHtml[] = "    <th class=\"SegColumn\">��ȴ����</th>";
		$aryHtml[] = "    <td class=\"Segs\">" . sprintf("%s %.2f", $aryOrder["strmonetaryunitsign"], $aryDetail[$i]["cursubtotalprice"]) . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "  <tr>";
		$aryHtml[] = "    <th class=\"SegColumn\">��������</th>";
		$aryHtml[] = "    <td colspan=\"3\" class=\"Segs\">" . $aryDetail[$i]["strnote"] . "</td>";
		$aryHtml[] = "  </tr>";
		$aryHtml[] = "</table>";
		$aryHtml[] = "<br>";
	}

    $strHtml = "";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}
?>