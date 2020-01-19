<?
/** 
*	Ǽ�ʽ񡡾ܺ١�����ؿ���
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	��������
*	��Ǽ�ʽ񸡺���̤���ξܺ�ɽ���Ⱥ���˴ؤ������
*
*	��������
*
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
function fncGetSlipHeadNoToInfoSQL ( $lngSlipNo, $lngRevisionNo )
{
	// Ǽ����ɼ�ֹ桢��ӥ�����ֹ�
	$aryQuery[] = "SELECT distinct on (s.lngSlipNo) s.lngSlipNo as lngslipno, s.lngRevisionNo as lngrevisionno";
	// Ǽ�ʽ�No
	$aryQuery[] = ", s.strSlipCode as strslipcode";
	// �ܵ�
	$aryQuery[] = ", c.strcompanydisplaycode as strcustomercode";	//�ܵҥ�����
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
	$aryQuery[] = ", u.struserdisplaycode as strinsertusercode";	//���ϼԥ�����
	$aryQuery[] = ", s.strInsertUserName as strinsertusername";	//���ϼ�̾
	// ��ɼ��
	$aryQuery[] = ", u2.struserdisplaycode as strusercode";	//���ϼԥ�����
	$aryQuery[] = ", s.strusername as strusername";	//���ϼ�̾
	// �������
	$aryQuery[] = ", s.lngPrintCount as lngprintcount";
	// ����ֹ�
	$aryQuery[] = ", s.lngSalesNo as lngsalesno";

	// FROM��
	$aryQuery[] = " FROM m_Slip s ";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_company c ON s.lngCustomerCode = c.lngcompanycode";
	$aryQuery[] = " LEFT JOIN m_user u ON s.lngInsertUserCode = u.lngusercode";
	$aryQuery[] = " LEFT JOIN m_user u2 ON s.lngUserCode = u2.lngusercode";

	// WHERE��
	$aryQuery[] = " WHERE s.lngSlipNo = " . $lngSlipNo . "";
	$aryQuery[] = " AND s.lngRevisionNo = " . $lngRevisionNo . "";

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
function fncGetSlipDetailNoToInfoSQL ( $lngSlipNo)
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
	$aryQuery[] = " AND sd.lngRevisionNo = (SELECT MAX( s.lngRevisionNo ) FROM m_slip s WHERE s.lngSlipNo = sd.lngSlipNo)";
	
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


	// ��ɼ��
	if ( $aryResult["strusercode"] )
	{
		$aryNewResult["strDrafter"] = "[" . $aryResult["strusercode"] ."]";
	}
	else
	{
		$aryNewResult["strDrafter"] = "      ";
	}
	$aryNewResult["strDrafter"] .= " " . $aryResult["strusername"];

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


function fncJapaneseInvoiceExists($lngCustomerCode, $lngSalesNo, $objDB)
{
	// �ܵҤι񥳡��ɼ���
	$strCompanyQuery = "SELECT lngcountrycode FROM m_Company WHERE strcompanydisplaycode = '" . $lngCustomerCode . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strCompanyQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngCountryCode = $objResult->lngcountrycode;
	}
	else
	{
		// �񥳡��ɼ������Ԣ�DB���顼
		fncOutputError ( 9501, DEF_FATAL, "����������å�������ȼ���񥳡��ɼ�������", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// ����������ֹ����
	$strSalesQuery = "SELECT lnginvoiceno FROM m_Sales WHERE lngSalesNo = " . $lngSalesNo . " FOR UPDATE";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strSalesQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngInvoiceNo = $objResult->lnginvoiceno;
	}
	else
	{
		// ������ֹ�������Ԣ������å����Ԣ�DB���顼
		fncOutputError ( 9501, DEF_FATAL, "����������å�������ȼ��������ֹ��������", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// �ܵҤι����ܤǡ�����Ǽ�ʽ�إå���ɳ�Ť���������٤�¸�ߤ���
	return ($lngCountryCode == 81) && ($lngInvoiceNo != null);

}

function fncReceiveStatusIsClosed($lngSlipNo, $objDB)
{
	// Ǽ����ɼ���٥ǡ����μ���
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo, $lngRevisionNo );
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
		// Ǽ����ɼ�ֹ��ɳ�Ť�Ǽ����ɼ���٤����Ĥ���ʤ���DB���顼
		fncOutputError ( 9501, DEF_FATAL, "����������å�������ȼ��Ǽ����ɼ�ֹ��������", TRUE, "", $objDB );
	}

	// Ǽ����ɼ���٤�ɳ�Ť�����Υ��ơ�������������ѡפ��ɤ���
	for ( $i = 0; $i < count($aryDetailResult); $i++)
	{
		// �����ֹ�
		$lngReceiveNo = $aryDetailResult[$i]["lngreceiveno"];

		// ����ޥ�����������֥����ɤ����
		$strReceiveCodeQuery = "SELECT lngreceivestatuscode FROM m_Receive WHERE lngReceiveNo = " . $lngReceiveNo . " FOR UPDATE";
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strReceiveCodeQuery, $objDB );
		if ( $lngResultNum )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$lngReceiveStatusCode = $objResult->lngreceivestatuscode;
		}
		else
		{
			// ������֥����ɼ������Ԣ�DB���顼
			fncOutputError ( 9051, DEF_FATAL, "����������å�������ȼ��������֥����ɼ�������", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngResultID );

		if ($lngReceiveStatusCode == DEF_RECEIVE_CLOSED){
			// ������֥����ɤ�������ѡפ����٤�1��ʾ�¸��
			return true;
		}
	}

	// ������֥����ɤ�������ѡפ����٤�1���̵��
	return false;
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
	$strSalesCodeQuery = "SELECT strsalescode FROM m_Sales WHERE lngSalesNo = " . $lngSalesNo;
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

	/*
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
	$lngMinRevisionNo--;
	*/
	// ��ӥ�����ֹ��-1����ʻ��ͽ�˽ऺ���
	$lngMinRevisionNo = -1;

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
function fncDeleteSlip($lngSlipNo, $objDB, $objAuth)
{
	// Ǽ�ʽ�ޥ����Υ������󥹤����
	//$sequence_m_slip = fncGetSequence( 'm_Slip.lngSlipNo', $objDB );

	/*
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
	*/

	// ��ӥ�����ֹ��-1����ʻ��ͽ�˽ऺ���
	$lngMinRevisionNo = -1;

	// Ǽ�ʽ�ޥ����˥�ӥ�����ֹ椬 -1 �Υ쥳���ɤ��ɲ�
	$aryQuery[] = "INSERT INTO m_slip (";
	$aryQuery[] = " lngSlipNo,";					// 1:Ǽ����ɼ�ֹ�
	$aryQuery[] = " lngRevisionNo, ";				// 2:��ӥ�����ֹ�
	$aryQuery[] = " lnginsertusercode, ";			// 4:���ϼԥ�����
	$aryQuery[] = " bytInvalidFlag, "; 				// 5:̵���ե饰
	$aryQuery[] = " dtmInsertDate";					// 6:��Ͽ��
	$aryQuery[] = ") values (";
	$aryQuery[] = $lngSlipNo . ", ";			// 1:Ǽ����ɼ�ֹ�
	$aryQuery[] = "-1, ";			// 2:��ӥ�����ֹ�
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
	// Ǽ����ɼ���٥ǡ����μ���
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo );
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
		$strReceiveCodeQuery = "SELECT strreceivecode FROM m_Receive WHERE lngReceiveNo = " . $lngReceiveNo;
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
		$strWhere .= "strReceiveCode = '" . $strReceiveCode . "'";
		$strWhere .= " and lngRevisionNo = (SELECT MAX(lngRevisionNo) FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "')";

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

?>
