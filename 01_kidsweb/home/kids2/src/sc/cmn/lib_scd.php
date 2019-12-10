<?
// ----------------------------------------------------------------------------
/**
*       ������  Ǽ�ʽ񸡺���Ϣ�ؿ���
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
*         ��Ǽ�ʽ񸡺���̴�Ϣ�δؿ�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



/**
* Ǽ�ʽ񸡺��θ������ܤ�����פ���ǿ������ǡ������������SQLʸ�κ����ؿ�
*
*	Ǽ�ʽ񸡺��θ������ܤ��� SQLʸ���������
*
*	@param  Array 	$arySearchColumn 		�����оݥ����̾������
*	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
*	@param  Object	$objDB       			DB���֥�������
*	@param	String	$strSlipCode			Ǽ����ɼ������	��������:������̽���	Ǽ����ɼ�����ɻ����:�����ѡ�Ʊ��Ǽ�ʽ�ΣϤΰ�������
*	@param	Integer	$lngSlipNo				Ǽ����ɼ�ֹ�	0:������̽���	Ǽ����ɼ�ֹ�����:�����ѡ�Ʊ��Ǽ����ɼ�����ɤȤ�������оݳ�Ǽ����ɼ�ֹ�
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $arySearchColumn, $arySearchDataColumn, $objDB, $strSlipCode, $lngSlipNo, $strSessionID)
{
	// -----------------------------
	//  ��������ưŪ����
	// -----------------------------
	// ���پ���ɲúѤߥե饰
	$detailFlag = FALSE;

	// Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
	if ( $strSlipCode )
	{
		// Ʊ��Ǽ����ɼ�����ɤ��Ф��ƻ����Ǽ����ɼ�ֹ�Υǡ����Ͻ�������
		if ( $lngSlipNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strSlipCode = '" . $strSlipCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../sc/search2/index.php?strSessionID=".$strSessionID, $objDB );
		}
	}
	// �����⡼�ɤǤ�Ʊ��Ǽ����ɼ�����ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
	else
	{
		// ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ����Τ�
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// ���������å��ܥå�����ON�ι��ܤΤ߸��������ɲ�
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];
			
			// ----------------------------------------------
			//   Ǽ�ʽ�ޥ����ʥإå����ˤθ������
			// ----------------------------------------------
			// �ܵҡ�������
			if ( $strSearchColumnName == "lngCustomerCompanyCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'";
				}
			}

			// ���Ƕ�ʬ�ʾ����Ƕ�ʬ��
			if ( $strSearchColumnName == "lngTaxClassCode" )
			{
				if ( $arySearchDataColumn["lngTaxClassCode"] )
				{
					$aryQuery[] = " AND s.lngTaxClassCode = '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
				}
			}

			// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					// ����޶��ڤ�������ͤ�OR����Ÿ��
					$arySCValue = explode(",",$arySearchDataColumn["strSlipCode"]);
					foreach($arySCValue as $strSCValue){
						$arySCOr[] = "UPPER(s.strSlipCode) LIKE UPPER('%" . $strSCValue . "%')";
					}
					$aryQuery[] = " AND (";
					$aryQuery[] = implode(" OR ", $arySCOr);
					$aryQuery[] = ") ";
				}
			}

			// Ǽ����
			if ( $strSearchColumnName == "dtmDeliveryDate" )
			{
				if ( $arySearchDataColumn["dtmDeliveryDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmDeliveryDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateTo"] . " 23:59:59.99999";
					$aryQuery[] = " AND s.dtmDeliveryDate <= '" . $dtmSearchDate . "'";
				}
			}

			// Ǽ����
			if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
			{
				if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
				{
					//��ҥޥ�����ɳ�Ť����ͤ����
					$aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
				}
//				if ( $arySearchDataColumn["strDeliveryPlaceName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//				}
			}

			// ��ɼ��
			if ( $strSearchColumnName == "lngInsertUserCode" )
			{
				if ( $arySearchDataColumn["lngInsertUserCode"] )
				{
					$aryQuery[] = " AND insert_u.struserdisplaycode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
				}
//				if ( $arySearchDataColumn["strInsertUserName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strInsertUserName) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
//				}
			}

			// ----------------------------------------------
			//   Ǽ����ɼ���٥ơ��֥���������ˤθ������
			// ----------------------------------------------
			// ��ʸ��NO.
			if ( $strSearchColumnName == "strCustomerSalesCode" )
			{
				if ( $arySearchDataColumn["strCustomerSalesCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// ����޶��ڤ�������ͤ�OR����Ÿ��
					$aryCSCValue = explode(",",$arySearchDataColumn["strCustomerSalesCode"]);
					foreach($aryCSCValue as $strCSCValue){
						$aryCSCOr[] = "UPPER(sd1.strCustomerSalesCode) LIKE UPPER('%" . $strCSCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryCSCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}
		
			// �ܵ�����
			if ( $strSearchColumnName == "strGoodsCode" )
			{
				if ( $arySearchDataColumn["strGoodsCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// ����޶��ڤ�������ͤ�OR����Ÿ��
					$aryGCValue = explode(",",$arySearchDataColumn["strGoodsCode"]);
					foreach($aryGCValue as $strGCValue){
						$aryGCOr[] = "UPPER(sd1.strGoodsCode) LIKE UPPER('%" . $strGCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryGCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}

			// ����ʬ
			if ( $strSearchColumnName == "lngSalesClassCode" )
			{
				if ( $arySearchDataColumn["lngSalesClassCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "sd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
					$detailFlag = TRUE;
				}
			}
		}
	}



	// ---------------------------------
	//   SQLʸ�κ���
	// ---------------------------------
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT distinct s.lngSlipNo as lngSlipNo";	//Ǽ����ɼ�ֹ�
	$aryOutQuery[] = "	,s.lngSlipNo as lngpkno";			    //����ֹ�
	$aryOutQuery[] = "	,s.lngSalesNo as lngSalesNo";			    //����ֹ�
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";			//��ӥ�����ֹ�
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";			//������
	// �ܵ�
	$arySelectQuery[] = ", cust_c.strcompanydisplaycode as strCustomerDisplayCode";
	$arySelectQuery[] = ", s.strcustomername as strCustomerDisplayName";
	// �ܵҤι�
	$arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
	// ������ֹ�
	$arySelectQuery[] = ", sa.lngInvoiceNo as lnginvoiceno";
	// ���Ƕ�ʬ
	$arySelectQuery[] = ", s.strTaxClassName as strTaxClassName";
	// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
	$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
	// Ǽ����
	$arySelectQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
	// Ǽ����
	$arySelectQuery[] = " , delv_c.strcompanydisplaycode as strdeliveryplacecode";
	$arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
	// ��ɼ��
	$arySelectQuery[] = ", u.struserdisplaycode as strusercode";
	$arySelectQuery[] = ", s.strUserName as strusername";
	// ����
	$arySelectQuery[] = ", s.strNote as strNote";
	// ��׶��
	$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
	//// ���Σ�
	$arySelectQuery[] = ", sa.strSalesCode as strSalesCode";
	// �����֥�����
	$arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
	$arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
	// �̲�ñ��
	$arySelectQuery[] = ", s.lngMonetaryUnitCode";
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";

	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Slip s";
//	if ( !$strSlipCode )
//	{
		 $aryFromQuery[] = "INNER JOIN (SELECT lngSlipNo, MAX(lngRevisionNo) AS lngRevisionNo from m_slip group by lngSlipNo) max_rev "
		 . "on max_rev.lngSlipNo = s.lngslipno and max_rev.lngRevisionNo = s.lngrevisionno";

//    }
	$aryFromQuery[] = " INNER JOIN m_Sales sa ON s.lngSalesNo = sa.lngSalesNo AND s.lngRevisionNo = sa.lngRevisionNo";
	$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";
	$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCode = cust_c.lngCompanyCode";
	$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryFromQuery[] = " LEFT JOIN m_User u ON s.lngusercode = u.lngusercode";
	$aryFromQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";				// Ǽ����ɼ�����ֹ�
	$aryDetailFrom[] = "	,sd1.lngrevisionno";	// ��ӥ�����ֹ�
	$aryDetailFrom[] = "	,sd1.lngSortKey as lngRecordNo";	// ���ٹ�NO
	$aryDetailFrom[] = "	,sd1.strCustomerSalesCode";			// ��ʸ��NO
	$aryDetailFrom[] = "	,sd1.strGoodsCode";					// �ܵ�����
	$aryDetailFrom[] = "	,sd1.strProductName";				// ��̾
	$aryDetailFrom[] = "	,sd1.strSalesClassName";	// ����ʬ
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,sd1.lngQuantity";	        // ����
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// ����
	$aryDetailFrom[] = "	,sd1.strProductUnitName";	// ñ��
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,sd1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_SlipDetail sd1 ";
	// where������ٹԡ� �����꡼Ϣ��
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤξ�郎¸�ߤ�����
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$aryDetailWhereQuery[] = ") as sd";
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹ��Ѥξ��Ϣ��
	$aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";
	$aryOutQuery[] = " AND sd.lngrevisionno = s.lngrevisionno";


	/////////////////////////////////////////////////////////////
	//// �ǿ����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢     ////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��           ////
	//// Ʊ��Ǽ����ɼ�����ɤ���ĥǡ�����̵�����ǡ���          ////
	/////////////////////////////////////////////////////////////
	// Ǽ����ɼ�����ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strSlipCode )
	{
		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
			$aryOutQuery[] = " AND not exists (SELECT lngslipno from m_slip s1 where s1.lngslipno=s.lngslipno and s1.lngRevisionNo < 0 and s1.bytInvalidFlag = false)";
		}
	}

	// Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
	if ($strSlipCode)
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// �����Ⱦ������
		$aryOutQuery[] = " ORDER BY lngSlipNo DESC";		
	}
	return implode("\n", $aryOutQuery);
}

?>