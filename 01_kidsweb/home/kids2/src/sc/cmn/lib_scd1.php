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
	$aryQuery[] = "SELECT distinct on (s.lngSlipNo) s.lngSlipNo as lngSlipNo, s.lngRevisionNo as lngRevisionNo";
	// Ǽ�ʽ�No
	$aryQuery[] = ", s.strSlipCode as strSlipCode";
	// �ܵ�
	$aryQuery[] = ", s.strCustomerCode as strCustomerCode";	//�ܵҥ�����
	$aryQuery[] = ", s.strCustomerName as strCustomerName";	//�ܵ�̾
	// Ǽ����
	$aryQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmDeliveryDate";
	// Ǽ�ʾ��̾
	$aryQuery[] = ", s.strDeliveryPlaceName as strDeliveryPlaceName";
	// Ǽ�ʾ��ô����̾
	$aryQuery[] = ", s.strDeliveryPlaceUserName as strDeliveryPlaceUserName";
	// ���Ƕ�ʬ
	$aryQuery[] = ", s.strTaxClassName as strTaxClassName";
	// �̲ߵ��档�إå����ι�׶�ۡ���������ñ������ȴ���ʤ���Ϳ�����
	$aryQuery[] = ", s.strMonetaryUnitSign as strMonetaryUnitSign";
	// ��׶��
	$aryQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
	// �̲ߡʤ��ι��ܤ����ޥ�����ɳ�Ť��Ƽ�����
	$aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
	// ����
	$aryQuery[] = ", s.strNote as strNote";
	// ������
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
	// ���ϼԡᵯɼ��
	$aryQuery[] = ", s.strInsertUserCode as strInsertUserCode";	//���ϼԥ�����
	$aryQuery[] = ", s.strInsertUserName as strInsertUserName";	//���ϼ�̾
	// �������
	$aryQuery[] = ", s.lngPrintCount as lngPrintCount";

	// FROM��
	$aryQuery[] = " FROM m_Slip s ";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";

	// WHERE��
	$aryQuery[] = " WHERE s.lngSlipNo = " . $lngSlipNo . "";

	$strQuery = implode( "\n", $aryQuery );

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
function fncGetSlipDetailNoToInfoSQL ( $lngSlipNo )
{
	// �����ȥ���
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngRecordNo, ";
	// Ǽ����ɼ�ֹ桢��ӥ�����ֹ�
	$aryQuery[] = "sd.lngSlipNo as lngSlipNo, sd.lngRevisionNo as lngRevisionNo";
	// �ܵҼ����ֹ�
	$aryQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
	// ����ʬ
	$aryQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";	//����ʬ������
	$aryQuery[] = ", sd.strSalesClassName as strSalesClassName";	//����ʬ̾
	// �ܵ�����
	$aryQuery[] = ", sd.strGoodsCode as strGoodsCode";
	// ���ʥ����ɡ�̾��
	$aryQuery[] = ", sd.strProductCode as strProductCode";	//���ʥ�����
	$aryQuery[] = ", sd.strProductName as strProductName";	//����̾
	// ̾�ΡʱѸ��
	$aryQuery[] = ", sd.strProductEnglishName as strProductEnglishName";	//����̾�ʱѸ��
	// ñ��
	$aryQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
	// ����
	$aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// ñ��
	$aryQuery[] = ", sd.strProductUnitName as strProductUnitName";
	// ��ȴ���
	$aryQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// ��������
	$aryQuery[] = ", sd.strNote as strDetailNote";

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
?>