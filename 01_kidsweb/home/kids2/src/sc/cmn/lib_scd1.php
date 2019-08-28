<?
/** 
*	Ǽ�ʽ񡡾ܺ١������̵�����ؿ���
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
* ���ꤵ�줿Ǽ����ɼ�ֹ椫��Ǽ�ʽ�إå�������������ӣѣ�ʸ�����
*
*	����Ǽ����ɼ�ֹ�Υإå�����μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngSlipNo 			��������Ǽ����ɼ�ֹ�
*	@return strQuery 	$strQuery ������SQLʸ
*	@access public
*/
function fncGetSlipHeadNoToInfoSQL ( $lngSlipNo )
{
	// Ǽ����ɼ�ֹ桢��ӥ�����ֹ�
	$aryQuery[] = "SELECT distinct on (s.lngSlipNo) s.lngSlipNo as lngslipno, s.lngRevisionNo as lngrevisionno";
	// Ǽ�ʽ�No
	$aryQuery[] = ", s.strSlipCode as strslipcode";
	// �ܵ�
	$aryQuery[] = ", s.strCustomerCode as strcustomercode";	//�ܵҥ�����
	$aryQuery[] = ", s.strCustomerName as strcustomername";	//�ܵ�̾
	// Ǽ����
	$aryQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmdeliverydate";
	// Ǽ�ʾ��̾
	$aryQuery[] = ", s.strDeliveryPlaceName as strdeliveryplacename";
	// Ǽ�ʾ��ô����̾
	$aryQuery[] = ", s.strDeliveryPlaceUserName as strdeliveryplaceusername";
	// ���Ƕ�ʬ
	$aryQuery[] = ", s.strTaxClassName as strtaxclassname";
	// �̲ߵ��档�إå����ι�׶�ۡ���������ñ������ȴ���ʤ���Ϳ�����
	$aryQuery[] = ", s.strMonetaryUnitSign as strmonetaryunitsign";
	// ��׶��
	$aryQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curtotalprice";
	// �̲ߡʤ��ι��ܤ����ޥ�����ɳ�Ť��Ƽ�����
	$aryQuery[] = ", mu.strMonetaryUnitName as strmonetaryunitname";
	// ����
	$aryQuery[] = ", s.strNote as strnote";
	// ������
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";
	// ���ϼԡᵯɼ��
	$aryQuery[] = ", s.strInsertUserCode as strinsertusercode";	//���ϼԥ�����
	$aryQuery[] = ", s.strInsertUserName as strinsertusername";	//���ϼ�̾
	// �������
	$aryQuery[] = ", s.lngPrintCount as lngprintcount";
	// ����ֹ�
	$aryQuery[] = ", s.lngSalesNo as lngsalesno";

	// FROM��
	$aryQuery[] = " FROM m_Slip s ";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";

	// WHERE��
	$aryQuery[] = " WHERE s.lngSlipNo = " . $lngSlipNo . "";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}



/**
* ���ꤵ�줿Ǽ����ɼ�ֹ椫��Ǽ����ɼ���پ�����������ӣѣ�ʸ�����
*
*	����Ǽ����ɼ�ֹ�����پ���μ����ѣӣѣ�ʸ�����ؿ�
*
*	@param  Integer 	$lngSalesNo �����Ȥʤ�Ǽ����ɼ�ֹ�
*	@return strQuery 	$strQuery 	������SQLʸ
*	@access public
*/
function fncGetSlipDetailNoToInfoSQL ( $lngSlipNo )
{
	// �����ȥ���
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngrecordno, ";
	// Ǽ����ɼ�ֹ桢��ӥ�����ֹ�
	$aryQuery[] = "sd.lngSlipNo as lngslipno, sd.lngRevisionNo as lngrevisionno";
	// �ܵҼ����ֹ�
	$aryQuery[] = ", sd.strCustomerSalesCode as strcustomersalescode";
	// ����ʬ
	$aryQuery[] = ", sd.lngSalesClassCode as lngsalesclasscode";	//����ʬ������
	$aryQuery[] = ", sd.strSalesClassName as strsalesclassname";	//����ʬ̾
	// �ܵ�����
	$aryQuery[] = ", sd.strGoodsCode as strgoodscode";
	// ���ʥ����ɡ�̾��
	$aryQuery[] = ", sd.strProductCode as strproductcode";	//���ʥ�����
	$aryQuery[] = ", sd.strProductName as strproductname";	//����̾
	// ̾�ΡʱѸ��
	$aryQuery[] = ", sd.strProductEnglishName as strproductenglishname";	//����̾�ʱѸ��
	// ñ��
	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curproductprice";
	// ����
	$aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngproductquantity";
	// ñ��
	$aryQuery[] = ", sd.strProductUnitName as strproductunitname";
	// ��ȴ���
	$aryQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as cursubtotalprice";
	// ��������
	$aryQuery[] = ", sd.strNote as strDetailNote";
	// �����ֹ�
	$aryQuery[] = ", sd.lngReceiveNo as lngreceiveno";
	// ���������ֹ�
	$aryQuery[] = ", sd.lngReceiveDetailNo as lngreceivedetailno";
	// �����ӥ�����ֹ�
	$aryQuery[] = ", sd.lngReceiveRevisionNo as lngreceiverevisionno";

	// FROM��
	$aryQuery[] = " FROM t_SlipDetail sd";

	$aryQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . "";

	$aryQuery[] = " ORDER BY sd.lngSortKey ASC ";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}


/**
* �إå����ǡ����ù�
*
*	SQL�Ǽ��������إå������ͤ�ɽ���Ѥ˲ù�����
*	��SQL������̤Υ���̾�Ϥ��٤ƾ�ʸ���ˤʤ뤳�Ȥ����
*
*	@param  Array 	$aryResult 				�إå��Ԥθ�����̤���Ǽ���줿����
*	@access public
*/
function fncSetSlipHeadTableData ( $aryResult )
{
	// Ǽ����ɼ�ֹ�
	$aryNewResult["lngSlipNo"] = $aryResult["lngslipno"];
	// ��ӥ�����ֹ�
	$aryNewResult["lngRevisionNo"] = $aryResult["lngrevisionno"];
	// Ǽ�ʽ�No
	$aryNewResult["strSlipCode"] = $aryResult["strslipcode"];

	// �ܵ�
	if ( $aryResult["strcustomercode"] )
	{
		$aryNewResult["strCustomer"] = "[" . $aryResult["strcustomercode"] ."]";
	}
	else
	{
		$aryNewResult["strCustomer"] = "      ";
	}
	$aryNewResult["strCustomer"] .= " " . $aryResult["strcustomername"];

	// Ǽ����
	$aryNewResult["dtmDeliveryDate"] = $aryResult["dtmdeliverydate"];
	// Ǽ�ʾ��̾
	$aryNewResult["strDeliveryPlaceName"] = $aryResult["strdeliveryplacename"];
	// Ǽ�ʾ��ô����̾
	$aryNewResult["strDeliveryPlaceUserName"] = $aryResult["strdeliveryplaceusername"];
	// ���Ƕ�ʬ
	$aryNewResult["strTaxClassName"] = $aryResult["strtaxclassname"];

	// �̲ߵ��档�إå����ι�׶�ۡ���������ñ������ȴ���ʤ���Ϳ�����
	$aryNewResult["strMonetaryUnitSign"] = $aryResult["strmonetaryunitsign"];
	// ��׶��
	$aryNewResult["curTotalPrice"] = $aryNewResult["strMonetaryUnitSign"] . " ";
	if ( !$aryResult["curtotalprice"] )
	{
		$aryNewResult["curTotalPrice"] .= "0.00";
	}
	else
	{
		$aryNewResult["curTotalPrice"] .= $aryResult["curtotalprice"];
	}

	// �̲�
	$aryNewResult["strMonetaryUnitName"] = $aryResult["strmonetaryunitname"];

	// ����
	$aryNewResult["strNote"] = nl2br($aryResult["strnote"]);

	// ������
	$aryNewResult["dtmInsertDate"] = $aryResult["dtminsertdate"];

	// ���ϼ�
	if ( $aryResult["strinsertusercode"] )
	{
		$aryNewResult["strInsertUser"] = "[" . $aryResult["strinsertusercode"] ."]";
	}
	else
	{
		$aryNewResult["strInsertUser"] = "      ";
	}
	$aryNewResult["strInsertUser"] .= " " . $aryResult["strinsertusername"];

	// ��ɼ�ԡ����ϼ�
	$aryNewResult["strDrafter"] = $aryNewResult["strInsertUser"];

	// �������
	$aryNewResult["lngPrintCount"] = $aryResult["lngprintcount"];

	return $aryNewResult;
}



/**
* �ܺ����ǡ����ù�
*
*	SQL�Ǽ��������ܺ������ͤ�ɽ���Ѥ˲ù�����
*	��SQL������̤Υ���̾�Ϥ��٤ƾ�ʸ���ˤʤ뤳�Ȥ����
*
*	@param  Array 	$aryDetailResult 	���ٹԤθ�����̤���Ǽ���줿����ʣ��ǡ���ʬ��
*	@param  Array 	$aryHeadResult 		�إå��Ԥθ�����̤���Ǽ���줿����ʻ����ѡ�
*	@access public
*/
function fncSetSlipDetailTableData ( $aryDetailResult, $aryHeadResult )
{

	// �����ȥ���
	$aryNewDetailResult["lngRecordNo"] = $aryDetailResult["lngrecordno"];
	// Ǽ����ɼ�ֹ�
	$aryNewDetailResult["lngSlipNo"] = $aryDetailResult["lngslipno"];
	// ��ӥ�����ֹ�
	$aryNewDetailResult["lngRevisionNo"] = $aryDetailResult["lngrevisionno"];
	// �ܵҼ����ֹ�
	$aryNewDetailResult["strCustomerSalesCode"] = $aryDetailResult["strcustomersalescode"];
	// ����ʬ
	if ( $aryDetailResult["lngsalesclasscode"] )
	{
		$aryNewDetailResult["lngSalesClassCode"] = "[" . $aryDetailResult["lngsalesclasscode"] ."]";
	}
	else
	{
		$aryNewDetailResult["lngSalesClassCode"] = "      ";
	}
	$aryNewDetailResult["lngSalesClassCode"] .= " " . $aryDetailResult["strsalesclassname"];

	// �ܵ�����
	$aryNewDetailResult["strGoodsCode"] = $aryDetailResult["strgoodscode"];
	
	// ���ʥ����ɡ�̾��
	if ( $aryDetailResult["strproductcode"] )
	{
		$aryNewDetailResult["strProductCode"] = "[" . $aryDetailResult["strproductcode"] ."]";
	}
	else
	{
		$aryNewDetailResult["strProductCode"] = "      ";
	}
	$aryNewDetailResult["strProductCode"] .= " " . $aryDetailResult["strproductname"];
	
	// ̾�ΡʱѸ��
	$aryNewDetailResult["strProductEnglishName"] = $aryDetailResult["strproductenglishname"];

	// ñ��
	$aryNewDetailResult["curProductPrice"] = $aryHeadResult["strMonetaryUnitSign"] . " ";
	if ( !$aryDetailResult["curproductprice"] )
	{
		$aryNewDetailResult["curProductPrice"] .= "0.00";
	}
	else
	{
		$aryNewDetailResult["curProductPrice"] .= $aryDetailResult["curproductprice"];
	}

	// ����
	$aryNewDetailResult["lngProductQuantity"] = $aryDetailResult["lngproductquantity"];
	// ñ��
	$aryNewDetailResult["strProductUnitName"] = $aryDetailResult["strproductunitname"];

	// ��ȴ���
	$aryNewDetailResult["curSubTotalPrice"] = $aryHeadResult["strMonetaryUnitSign"] . " ";
	if ( !$aryDetailResult["cursubtotalprice"] )
	{
		$aryNewDetailResult["curSubTotalPrice"] .= "0.00";
	}
	else
	{
		$aryNewDetailResult["curSubTotalPrice"] .= $aryDetailResult["cursubtotalprice"];
	}

	// ��������
	$aryNewDetailResult["strDetailNote"] = nl2br($aryDetailResult["strdetailnote"]);

	return $aryNewDetailResult;
}


/**
* �����̾���Ǽ��������Υ�����"CN"����Ϳ����
*
*	@param  Array 	$aryColumnNames 		�����̾����Ǽ���줿����
*	@access public
*/
function fncAddColumnNameArrayKeyToCN ($aryColumnNames)
{
	$arrayKeys = array_keys($aryColumnNames);

	// ɽ���оݥ������������̤ν���
	for ( $i = 0; $i < count($arrayKeys); $i++ )
	{
		$key = $arrayKeys[$i];
		$strNewColumnName = "CN" . $key;
		$aryNames[$strNewColumnName] = $aryColumnNames[$key];
	}

	return $aryNames;
}

/**
 * ���ǡ����κ��
 * 
 *	@param  Long 		$lngSalesNo ����ֹ�
 *	@param  Object		$objDB		DB���֥�������
 *	@param  Object		$objAuth	���¥��֥�������
 *	@return Boolean 	true		�¹�����
 *						false		�¹Լ��� �����������
 */
function fncDeleteSales($lngSalesNo, $objDB, $objAuth)
{
	// ����ֹ�򥭡�����女���ɤ����
	$strSalesCodeQuery = "SELECT strSalesCode FROM m_Sales WHERE lngSalesNo = " . $lngSalesNo;
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strSalesCodeQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$strSalesCode = $objResult->strsalescode;
	}
	else
	{
		// ��女���ɼ�������
		return false;
	}
	$objDB->freeResult( $lngResultID );
	
	// ���ޥ����Υ������󥹤����
	$sequence_m_sales = fncGetSequence( 'm_Sales.lngSalesNo', $objDB );

	// �Ǿ���ӥ�����ֹ�μ���
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Sales WHERE strSalesCode = '" . $strSalesCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	// ���ܤ����� -1 �ˤʤ�
	$lngMinRevisionNo--;

	// ���ޥ����˥�ӥ�����ֹ椬 -1 �Υ쥳���ɤ��ɲ�
	$aryQuery[] = "INSERT INTO m_sales (";
	$aryQuery[] = " lngSalesNo,";				// 1:����ֹ�
	$aryQuery[] = " lngRevisionNo, ";			// 2:��ӥ�����ֹ�
	$aryQuery[] = " strSalesCode, ";    		// 3:��女����
	$aryQuery[] = " lngInputUserCode, ";		// 4:���ϼԥ�����
	$aryQuery[] = " bytInvalidFlag, "; 			// 5:̵���ե饰
	$aryQuery[] = " dtmInsertDate";				// 6:��Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_sales . ", ";		// 1:����ֹ�
	$aryQuery[] = $lngMinRevisionNo . ", ";		// 2:��ӥ�����ֹ�
	$aryQuery[] = "'" . $strSalesCode . "', ";	// 3:��女���ɡ�
	$aryQuery[] = $objAuth->UserCode . ", ";	// 4:���ϼԥ�����
	$aryQuery[] = "false, ";					// 5:̵���ե饰
	$aryQuery[] = "now()";						// 6:��Ͽ��
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// �쥳�����ɲü���
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// ��������
	return true;
}

/**
 * Ǽ�ʽ�ǡ����κ��
 * 
 *	@param  String 		$strSlipCode	Ǽ����ɼ������
 *	@param  Object		$objDB			DB���֥�������
 *	@param  Object		$objAuth		���¥��֥�������
 *	@return Boolean 	true			�¹�����
 *						false			�¹Լ��� �����������
 */
function fncDeleteSlip($strSlipCode, $objDB, $objAuth)
{
	// Ǽ�ʽ�ޥ����Υ������󥹤����
	$sequence_m_slip = fncGetSequence( 'm_Slip.lngSlipNo', $objDB );

	// �Ǿ���ӥ�����ֹ�μ���
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Slip WHERE strSlipCode = '" . $strSlipCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	// ���ܤ����� -1 �ˤʤ�
	$lngMinRevisionNo--;

	// Ǽ�ʽ�ޥ����˥�ӥ�����ֹ椬 -1 �Υ쥳���ɤ��ɲ�
	$aryQuery[] = "INSERT INTO m_slip (";
	$aryQuery[] = " lngSlipNo,";					// 1:Ǽ����ɼ�ֹ�
	$aryQuery[] = " lngRevisionNo, ";				// 2:��ӥ�����ֹ�
	$aryQuery[] = " strSlipCode, ";    				// 3:Ǽ����ɼ������
	$aryQuery[] = " strInsertUserCode, ";			// 4:���ϼԥ�����
	$aryQuery[] = " bytInvalidFlag, "; 				// 5:̵���ե饰
	$aryQuery[] = " dtmInsertDate";					// 6:��Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_slip . ", ";			// 1:Ǽ����ɼ�ֹ�
	$aryQuery[] = $lngMinRevisionNo . ", ";			// 2:��ӥ�����ֹ�
	$aryQuery[] = "'" . $strSlipCode . "', ";		// 3:Ǽ����ɼ������
	$aryQuery[] = "'" . $objAuth->UserCode . "', ";	// 4:���ϼԥ�����
	$aryQuery[] = "false, ";						// 5:̵���ե饰
	$aryQuery[] = "now()";							// 6:��Ͽ��
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// �쥳�����ɲü���
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// ��������
	return true;
}

/**
 * �������٤Υ��ơ���������
 * 
 *	@param  Long 		$lngSlipNo	Ǽ����ɼ�ֹ�
 *	@param  Object		$objDB		DB���֥�������
 *	@return Boolean 	true		�¹�����
 *						false		�¹Լ��� �����������
 */
function fncUpdateReceiveStatus($lngSlipNo, $objDB)
{
	// ����Ǽ����ɼ�ֹ��������٥ǡ���������SQLʸ�κ���
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo );

	// Ǽ����ɼ���٥ǡ����μ���
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// Ǽ����ɼ�ֹ��ɳ�Ť�Ǽ����ɼ���٤����Ĥ���ʤ�
		return false;
	}

	for ( $i = 0; $i < count($aryDetailResult); $i++)
	{
		// �����ֹ�
		$lngReceiveNo = $aryDetailResult[$i]["lngreceiveno"];

		// ����ޥ������������ɤ����
		$strReceiveCodeQuery = "SELECT strReceiveCode FROM m_Receive WHERE lngReceiveNo = " . $lngReceiveNo;
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strReceiveCodeQuery, $objDB );
		if ( $lngResultNum )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$strReceiveCode = $objResult->strreceivecode;
		}
		else
		{
			// �������ɼ�������
			return false;
		}
		$objDB->freeResult( $lngResultID );

		// ����ޥ����ι����оݥ쥳����������
		$strWhere = "WHERE ";
		$strWhere .= "strReceiveCode = " . $strReceiveCode;
		$strWhere .= " and lngRevisionNo = SELECT MAX(lngRevisionNo) FROM m_Receive WHERE strReceiveCode = " . $strReceiveCode;

		// �����оݥ쥳���ɤιԥ�å������򤷤��쥳���ɤ��Ф����ߤΥȥ�󥶥�������λ����ޤ�¾�Υȥ�󥶥������ˤ��UPDATE��ػߤ����
		$strLockQuery = "SELECT * FROM m_Receive ";
		$strLockQuery .= $strWhere;
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if (!$lngLockResultID){ return false; }
		$objDB->freeResult( $lngLockResultID );

		// �����оݥ쥳���ɤμ�����֥����ɤ�ּ���פ˹���
		$strUpdateQuery = "UPDATE m_Receive ";
		$strUpdateQuery .= "SET lngReceiveStatusCode = " . DEF_RECEIVE_ORDER;
		$strUpdateQuery .= $strWhere;
		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		if (!$lngUpdateResultID){ return false; }
		$objDB->freeResult( $lngUpdateResultID );
	}

	// ��������
	return true;

}





/**
* �����Ǽ�ʽ�ǡ����Ȥ����ɳ�Ť����ǡ������������������٤Υ��ơ������򹹿�����
*
*	@param  Array 		$arySalesData 	���ǡ���
*	@param  Object		$objDB			DB���֥�������
*	@return Boolean 	0				�¹�����
*						1				�¹Լ��� �����������
*	@access public
*/
function fncDeleteSlipAndUpdateReceiveStatus ( $arySalesData, $objDB )
{

}




// TODO:���פˤʤä�����
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
/*
function fncSalesDeleteSetStatus ( $arySalesData, $objDB )
{

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
*/

?>
