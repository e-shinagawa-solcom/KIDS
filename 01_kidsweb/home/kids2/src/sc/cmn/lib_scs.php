<?
/** 
*	��塡�ܺ١������̵�����ؿ���
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
*	2004.03.30	�ܺ�ɽ������ɽ��������ٹ��ֹ�礫��ɽ���ѥ����ȥ�������ѹ�
*
*/



/**
* ���ꤵ�줿����ֹ椫�����إå�������������ӣѣ�ʸ�����
*
*	��������ֹ�Υإå�����μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngSalesNo 			������������ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetSalesHeadNoToInfoSQL ( $lngSalesNo, $lngRevisionNo )
{
	// SQLʸ�κ���
	$aryQuery[] = "SELECT distinct on (s.lngSalesNo) s.lngSalesNo as lngSalesNo, s.lngRevisionNo as lngRevisionNo";

	// ��Ͽ��
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS' ) as dtmInsertDate";
	// �׾���
	$aryQuery[] = ", to_char( s.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmAppropriationDate";
	// ���No
	$aryQuery[] = ", s.strSalesCode as strSalesCode";
	// ����No
//	$aryQuery[] = ", r.strReceiveCode || '-' || r.strReviseCode as strReceiveCode";
//	$aryQuery[] = ", s.lngReceiveNo as lngReceiveNo";
	// �ܵҼ����ֹ�
	$aryQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
	$aryQuery[] = ", tsd.lngReceiveNo as lngReceiveNo";
	// ���No
	$aryQuery[] = ", s.strSlipCode as strSlipCode";
	// ���ϼ�
	$aryQuery[] = ", s.lngInputUserCode as lngInputUserCode";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
	// �ܵ�
	$aryQuery[] = ", s.lngCustomerCompanyCode as lngCustomerCompanyCode";
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
	$aryQuery[] = ", s.lngSalesStatusCode as lngSalesStatusCode";
	$aryQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
	// ����
	$aryQuery[] = ", s.strNote as strNote";
	// ��׶��
	$aryQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";

	$aryQuery[] = " FROM m_Sales s ";
	$aryQuery[] = " left join t_salesdetail tsd on tsd.lngsalesno = s.lngsalesno ";
	$aryQuery[] = " LEFT JOIN m_Receive r ON tsd.lngReceiveNo = r.lngReceiveNo and tsd.lngreceiverevisionno = r.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON s.lngGroupCode = inchg_g.lngGroupCode";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON s.lngUserCode = inchg_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_SalesStatus ss USING (lngSalesStatusCode)";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON s.lngMonetaryRateCode = mr.lngMonetaryRateCode";

	$aryQuery[] = " WHERE s.lngSalesNo = " . $lngSalesNo . "";
	$aryQuery[] = " AND s.lngRevisionNo = " . $lngRevisionNo . "";

	$strQuery = implode( "\n", $aryQuery );

//fncDebug("lib_scs1.txt", $strQuery, __FILE__, __LINE__);
//fncDebug("lib_scs1-1.txt", $arySalesResult, __FILE__, __LINE__);

	return $strQuery;
}






/**
* ���ꤵ�줿����ֹ椫��������پ�����������ӣѣ�ʸ�����
*
*	��������ֹ�����پ���μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngSalesNo 			������������ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetSalesDetailNoToInfoSQL ( $lngSalesNo, $lngRevisionNo )
{
// 2004.03.30 suzukaze update start
	// SQLʸ�κ���
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngRecordNo, ";
	$aryQuery[] = "sd.lngSalesNo as lngSalesNo, sd.lngRevisionNo as lngRevisionNo";

	// ���ʥ����ɡ�̾��
	$aryQuery[] = ", sd.strProductCode as strProductCode";
	$aryQuery[] = ", p.strProductName as strProductName";
	// ����ʬ
	$aryQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
	$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
	// �Ķ�����
	$aryQuery[] = ", mg.strGroupDisplayCode as lnginchargegroupcode";
	$aryQuery[] = ", mg.strGroupDisplayName as strinchargegroupName";
	// ��ȯô����
	$aryQuery[] = ", mu.struserdisplaycode as lnginchargeusercode";
	$aryQuery[] = ", mu.struserdisplayname as strinchargeuserName";
	// �ܵ�����
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode";
	// ñ��
	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
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
	$aryQuery[] = " FROM t_SalesDetail sd LEFT JOIN (";
    $aryQuery[] = "     SELECT m_product.* FROM m_product ";
    $aryQuery[] = "      INNER JOIN (";
    $aryQuery[] = "          SELECT ";
    $aryQuery[] = "              lngproductno,strrevisecode,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "          FROM m_product GROUP BY lngproductno,strrevisecode";
    $aryQuery[] = "      ) mp1 ON mp1.lngproductno = m_product.lngproductno
    $aryQuery[] = "      AND mp1.strrevisecode = m_product.strrevisecode";
    $aryQuery[] = "      AND mp1.lngrevisionno = m_product.lngrevisionno";
	$aryQuery[] = " ) p on p.strProductCode = sd.strProductCode AND p.strrevisecode = sd.strrevisecode";
	$aryQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
	$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	$aryQuery[] = " LEFT JOIN m_group mg ON p.lnginchargegroupcode = mg.lnggroupcode";
	$aryQuery[] = " LEFT JOIN m_user mu ON p.lnginchargeusercode = mu.lngusercode";
	$aryQuery[] = " LEFT JOIN m_TaxClass tc USING (lngTaxClassCode)";
	$aryQuery[] = " LEFT JOIN m_Tax t USING (lngTaxCode)";

	$aryQuery[] = " WHERE sd.lngSalesNo = " . $lngSalesNo . "";
	$aryQuery[] = " AND sd.lngRevisionNo = " . $lngRevisionNo . "";

	$aryQuery[] = " ORDER BY sd.lngSortKey ASC ";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* �ܺ�ɽ���ؿ��ʥإå��ѡ�
*
*	�ơ��֥빽�������ǡ����ܺ٤���Ϥ���ؿ�
*	�إå��Ԥ�ɽ������
*
*	@param  Array 	$aryResult 				�إå��Ԥθ�����̤���Ǽ���줿����
*	@access public
*/
function fncSetSalesHeadTabelData ( $aryResult )
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
		else if ( $strColumnName == "dtmsalesappdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmsalesappdate"] );
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

		// �ܵ�
		else if ( $strColumnName == "lngcustomercompanycode" )
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
		else if ( $strColumnName == "lngsalesstatuscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strsalesstatusname"];
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
*	�ơ��֥빽�������ǡ����ܺ٤���Ϥ���ؿ�
*	���ٹԤ�ɽ������
*
*	@param  Array 	$aryDetailResult 	���ٹԤθ�����̤���Ǽ���줿����ʣ��ǡ���ʬ��
*	@param  Array 	$aryHeadResult 		�إå��Ԥθ�����̤���Ǽ���줿����ʻ����ѡ�
*	@access public
*/
function fncSetSalesDetailTabelData ( $aryDetailResult, $aryHeadResult )
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

		// ����ʬ
		else if ( $strColumnName == "lngsalesclasscode" )
		{
			if ( $aryDetailResult["lngsalesclasscode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngsalesclasscode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strsalesclassname"];
		}

		// �Ķ�����
		else if ( $strColumnName == "lnginchargegroupcode" )
		{
			if ( $aryDetailResult["lnginchargegroupcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lnginchargegroupcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strinchargegroupname"];
		}

		// ��ȯô����
		else if ( $strColumnName == "lnginchargeusercode" )
		{
			if ( $aryDetailResult["lnginchargeusercode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lnginchargeusercode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strinchargeusername"];
		}

		// �ܵ�����
		else if ( $strColumnName == "strgoodscode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
		}

		// Ǽ��
		else if ( $strColumnName == "dtmdeliverydate" )
		{
			$aryNewDetailResult[$strColumnName] = str_replace( "-", "/", $aryDetailResult[$strColumnName] );
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
function fncSetSalesTabelName ( $aryResult, $aryTytle )
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
* ��������ǡ����ˤĤ���̵�������뤳�ȤǤɤ��ʤ뤫�������櫓����
*
*	��������ǡ����ξ��֤�Ĵ�������������櫓����ؿ�
*
*	@param  Array 		$arySalesData 	���ǡ���
*	@param  Object		$objDB			DB���֥�������
*	@return Integer 	$lngCase		���֤Υ�����
*										1: �о����ǡ�����̵�������Ƥ⡢�ǿ������ǡ������ƶ������ʤ�
*										2: �о����ǡ�����̵�������뤳�Ȥǡ��ǿ������ǡ����������ؤ��
*										3: �о����ǡ���������ǡ����ǡ���夬���褹��
*										4: �о����ǡ�����̵�������뤳�Ȥǡ��ǿ������ǡ����ˤʤꤦ�����ǡ������ʤ�
*	@access public
*/
function fncGetInvalidCodeToMaster ( $arySalesData, $objDB )
{
	// ����о�����Ʊ����女���ɤκǿ������No��Ĵ�٤�
	$strQuery = "SELECT s.lngSalesNo FROM m_Sales s WHERE s.strSalesCode = '" . $arySalesData["strsalescode"] . "' AND s.bytInvalidFlag = FALSE ";
	$strQuery .= " AND s.lngRevisionNo >= 0";
	$strQuery .= " AND s.lngRevisionNo = ( "
		. "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode )";

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewSalesNo = $objResult->lngsalesno;
//fncDebug("lib_scs1.txt",$objResult, __FILE__, __LINE__);

	}
	else
	{
		$lngCase = 4;
	}
	$objDB->freeResult( $lngResultID );

	// ����оݤ��ǿ����ɤ����Υ����å�
	if ( $lngCase != 4 )
	{
		if ( $lngNewSalesNo == $arySalesData["lngsalesno"] )
		{
			// �ǿ��ξ��
			// ����о����ʳ��Ǥ�Ʊ����女���ɤκǿ������No��Ĵ�٤�
			$strQuery = "SELECT s.lngSalesNo FROM m_Sales s WHERE s.strSalesCode = '" . $strSalesCode . "' AND s.bytInvalidFlag = FALSE ";
			$strQuery .= " AND s.lngSalesNo <> " . $arySalesData["lngsalesno"] . " AND s.lngRevisionNo >= 0";

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
		// �о���夬����ǡ������ɤ����γ�ǧ
		else if ( $arySalesData["lngrevisionno"] < 0 )
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
* ��������ǡ����κ���˴ؤ��ơ��������ǡ����������뤳�ȤǤξ����ѹ��ؿ�
*
*	���ξ��֤���Ǽ�ʺѡפξ�硢����No����ꤷ�Ƥ�����硢ʬǼ�Ǥ��ä����ʤ�
*	�ƾ��֤��Ȥˤ������˴ؤ���ǡ����ξ��֤��ѹ�����
*
*	@param  Array 		$arySalesData 	���ǡ���
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*/
function fncSalesDeleteSetStatus ( $arySalesData, $objDB )
{
/*	// ����о����ϡ�����No����ꤷ�ʤ����Ǥ���
	if ( $arySalesData["lngreceiveno"] == "" or $arySalesData["lngreceiveno"] == 0 )
	{
		return 0;
	}
*/
	// ����No����ꤷ�Ƥ������ξ��ϡ����ꤷ�Ƥ���ǿ��μ���Υǡ������������
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo";	//	as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode";	//	as strReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode";	//	as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode";	//	as lngMonetaryUnitCode";
	$arySql[] = "	,r.strcustomerreceivecode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
//	$arySql[] = "	r.strReceiveCode = (";
//	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $arySalesData["lngreceiveno"];
	$arySql[] = "	r.strReceiveCode in (";
	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo IN (SELECT ts.lngreceiveno FROM t_Salesdetail ts WHERE ts.lngsalesno = " . $arySalesData["lngsalesno"];
	$arySql[] = "	)";
//	$arySql[] = "SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo in (select ts.lngreceiveno from t_salesdetail ts where ts.lngsalesno = "  . $lngSalesNo .")";
	$arySql[] = "	)";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0";
	$arySql[] = "	AND r.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = (";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
	$arySql[] = "	)";
	$arySql[] = "	AND 0 <= (";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
	$arySql[] = "	)";
	$strQuery = implode("\n", $arySql);

	// ���������꡼�μ¹�
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum )
	{
		for ( $a = 0; $a < $lngResultNum; $a++ )
		{
			$objResult1[$a]= $objDB->fetchArray( $lngResultID, $a );
//fncDebug("lib_scs1.txt", $objResult1, __FILE__, __LINE__);
		}
	$objDB->freeResult( $lngResultID );
	}else{
		// ����No�ϻ��ꤷ�Ƥ��뤬����ͭ���ʺǿ�����¸�ߤ��ʤ����Ϥ��Τޤ޺����ǽ�Ȥ���
			return 0;
		}
	for($k=0;$k<count($objResult1);$k++)
		{
//               ����о�����Ʊ������No����ꤷ�Ƥ���ǿ����򸡺�
			$arySql = array();
			$arySql[] = "SELECT distinct";
			$arySql[] = "	s.lngSalesNo as lngSalesNo";
			$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
			$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";
//			$arySql[] = "	,r.lngreceiveno as lngreceiveno";
			$arySql[] = "FROM";
			$arySql[] = "	m_Sales s";
			$arySql[] = "	left join t_salesdetail tsd";
			$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
			$arySql[] = "	,m_Receive r";
			$arySql[] = "WHERE";
			$arySql[] = "	r.lngreceiveno = " . $objResult1[$k]["lngreceiveno"];
//			$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
			$arySql[] = "	AND tsd.lngReceiveNo in (select re1.lngReceiveNo from m_Receive re1 where re1.strreceivecode = (";
			$arySql[] = "	select re2.strreceivecode from m_Receive re2 where re2.lngreceiveno = " . $objResult1[$k]["lngreceiveno"];
			$arySql[] = "	))";
			$arySql[] = "	AND s.bytInvalidFlag = FALSE";
			$arySql[] = "	AND s.lngRevisionNo >= 0";
			$arySql[] = "	AND s.lngRevisionNo = (";
			$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
			$arySql[] = "		AND 0 <= (";
			$arySql[] = "		SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
			$arySql[] = "		)";
			$arySql[] = "	AND s.lngsalesno <> '"  . $arySalesData["lngsalesno"] . "'";
			$strQuery = implode("\n", $arySql);			
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			if ( $lngResultNum )
			{
				// ����оݰʳ������ǡ�����¸�ߤ�����
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$arySalesResult1[$i] = $objDB->fetchArray( $lngResultID, $i );
//fncDebug("lib_scs1-1.txt", $arySalesResult1, __FILE__, __LINE__);
			// ��廲�ȼ���ξ��֤ξ��֤��Ǽ����פȤ���
					// Ʊ������NO����ꤷ�Ƥ������ξ��֤��Ф��Ƥ��Ǽ����פȤ���
						// �����о����ǡ������å�����
					if($arySalesResult1[$i]["lngsalesstatuscode"] != 99){
							$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
									. "WHERE lngSalesNo = " . $arySalesResult1[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
							list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
							$objDB->freeResult( $lngLockResultID );
							// ��Ǽ����׾��֤ؤι�������
							$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_DELIVER 
									. " WHERE lngSalesNo = " . $arySalesResult1[$i]["lngsalesno"];
							list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
							$objDB->freeResult( $lngUpdateResultID );
					}	
				}
				// �����оݼ���ǡ������å�����
				$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $objResult1[$k]["lngreceiveno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );
				// ��Ǽ����׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_DELIVER . " WHERE lngReceiveNo = " . $objResult1[$k]["lngreceiveno"];
				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
				}
				else
				{
				// ����оݰʳ������ǡ�����¸�ߤ��ʤ����
				// ���λ��ȸ��ǿ�����ξ��֤�ּ���פ��᤹
				// �����оݼ���ǡ������å�����
				$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = "  . $objResult1[$k]["lngreceiveno"] .  " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				if ( !$lngLockResultNum )
				{
					fncOutputError ( 9051, DEF_ERROR, "DB�������顼", TRUE, "", $objDB );
				}
				$objDB->freeResult( $lngLockResultID );
				// �ּ���׾��֤ؤι�������
				$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_ORDER . " WHERE lngReceiveNo = "  . $objResult1[$k]["lngreceiveno"];
				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
		}
	return 0;
}
?>