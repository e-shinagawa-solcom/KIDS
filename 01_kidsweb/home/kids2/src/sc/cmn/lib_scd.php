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
//				if ( $arySearchDataColumn["strCustomerName"] )
//				{
//					$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
//				}
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
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateTo"] . " 23:59:59";
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
					$aryQuery[] = " AND s.strInsertUserCode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
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
	$aryOutQuery[] = "	,s.lngSalesNo as lngSalesNo";			    //����ֹ�
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";			//��ӥ�����ֹ�
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";			//������

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,sd.lngSlipDetailNo";		      // Ǽ����ɼ�����ֹ�
	$aryOutQuery[] = "	,sd.lngRecordNo";                 // ���ٹ�NO
	$aryOutQuery[] = "	,sd.strCustomerSalesCode";	      // ��ʸ��NO
	$aryOutQuery[] = "	,sd.strGoodsCode";                // �ܵ�����
	$aryOutQuery[] = "	,sd.strProductName";			  // ��̾
	$aryOutQuery[] = "	,sd.strSalesClassName";	          // ����ʬ
	$aryOutQuery[] = "	,sd.curProductPrice";		      // ñ��
	$aryOutQuery[] = "	,sd.lngQuantity";	              // ����
	$aryOutQuery[] = "	,sd.lngProductQuantity";	      // ����
	$aryOutQuery[] = "	,sd.strProductUnitName";	      // ñ��
	$aryOutQuery[] = "	,sd.curSubTotalPrice";		      // ��ȴ���
	$aryOutQuery[] = "	,sd.strNote";				      // ��������

	// �ܵ�
	$arySelectQuery[] = ", s.strCustomerCode as strCustomerDisplayCode";
	$arySelectQuery[] = ", s.strCustomerName as strCustomerDisplayName";
	// �ܵҤι�
	$arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
	// ������ֹ�
	$arySelectQuery[] = ", sa.lngInvoiceNo as lnginvoiceno";
	// ���Ƕ�ʬ
	$arySelectQuery[] = ", s.strTaxClassName as strTaxClassName";
	// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
	$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
	// Ǽ����
	$arySelectQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmDeliveryDate";
	// Ǽ����
	$arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
	// ��ɼ��
	$arySelectQuery[] = ", s.strInsertUserCode as strInsertUserCode";
	$arySelectQuery[] = ", s.strInsertUserName as strInsertUserName";
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
	$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON CAST(s.strCustomerCode AS INTEGER) = cust_c.lngCompanyCode";
	$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.strInsertUserCode = insert_u.strUserDisplayCode";
	$aryFromQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSlipNo ) sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";				// Ǽ����ɼ�����ֹ�
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


	/////////////////////////////////////////////////////////////
	//// �ǿ����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢     ////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��           ////
	//// Ʊ��Ǽ����ɼ�����ɤ���ĥǡ�����̵�����ǡ���          ////
	/////////////////////////////////////////////////////////////
	// Ǽ����ɼ�����ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strSlipCode )
	{
//		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
//			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Slip s1 WHERE s1.strSlipCode = s.strSlipCode AND s1.bytInvalidFlag = false )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
			$aryOutQuery[] = " AND s.lngslipno not in (SELECT lngslipno from m_slip where lngRevisionNo < 0 and bytInvalidFlag = false)";
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

		// �ä�����줿�����Ȼ��ͤ�̵�����ᥳ���ȥ�����
		/*
		if ( $arySearchDataColumn["strSortOrder"] == "ASC" )
		{
			$strAsDs = " ASC";	//����
		}
		else
		{
			$strAsDs = " DESC";	//�߽�
		}

		switch($arySearchDataColumn["strSort"])
		{
			case "dtmInsertDate":
			case "strSalesCode":
			case "strSlipCode":
			case "lngSalesStatusCode":
			case "lngWorkFlowStatusCode":
			case "strNote":
			case "curTotalPrice":
			case "strCustomerReceiveCode":
				$aryOutQuery[] = " ORDER BY " . $arySearchDataColumn["strSort"] . " " . $strAsDs . ", lngSalesNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmSalesAppDate" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "dtmSalesAppDate":
				$aryOutQuery[] = " ORDER BY dtmAppropriationDate" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngSalesDetailNo":	// ���ٹ��ֹ�
				$aryOutQuery[] = " ORDER BY sd.lngSalesDetailNo" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strProductCode":		// ���ʥ�����
				$aryOutQuery[] = " ORDER BY sd.strProductCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngGroupCode":		// ����
				$aryOutQuery[] = " ORDER BY sd.strGroupDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngUserCode":			// ô����
				$aryOutQuery[] = " ORDER BY sd.strUserDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strProductName":		// ����̾��
				$aryOutQuery[] = " ORDER BY sd.strProductName" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strProductEnglishName":	// ���ʱѸ�̾��
				$aryOutQuery[] = " ORDER BY sd.strProductEnglishName" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngSalesClassCode":	// ����ʬ
				$aryOutQuery[] = " ORDER BY sd.lngSalesClassCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strGoodsCode":		// �ܵ�����
				$aryOutQuery[] = " ORDER BY sd.strGoodsCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "dtmDeliveryDate":		// Ǽ��
				$aryOutQuery[] = " ORDER BY sd.dtmDeliveryDate" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curProductPrice":		// ñ��
				$aryOutQuery[] = " ORDER BY sd.curProductPrice" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngProductUnitCode":	// ñ��
				$aryOutQuery[] = " ORDER BY sd.lngProductUnitCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngProductQuantity":	// ����
				$aryOutQuery[] = " ORDER BY sd.lngProductQuantity" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curSubTotalPrice":	// ��ȴ���
				$aryOutQuery[] = " ORDER BY sd.curSubTotalPrice" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngTaxClassCode":		// �Ƕ�ʬ
				$aryOutQuery[] = " ORDER BY sd.lngTaxClassCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curTax":				// ��Ψ
				$aryOutQuery[] = " ORDER BY sd.curTax" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curTaxPrice":			// �ǳ�
				$aryOutQuery[] = " ORDER BY sd.curTaxPrice" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strDetailNote":		// ��������
				$aryOutQuery[] = " ORDER BY sd.strNote" . $strAsDs . ", lngSalesNo DESC";
				break;
				
			default:
				$aryOutQuery[] = " ORDER BY lngSalesNo DESC";
		}
		*/
		
		
	}
	return implode("\n", $aryOutQuery);
}



/**
* ���ꤷ��Ǽ����ɼ�ֹ�Υǡ������б���������ٹԡפ��������SQLʸ�κ����ؿ�
*
*	Ǽ����ɼ�ֹ椫�����٤��������SQLʸ���������
*
*	@param  String 	$lngSlipNo 			    �о�Ǽ����ɼ�ֹ�
*	@param  Array 	$aryData 				POST�ǡ���������
*	@param  Object	$objDB       			DB���֥�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSlipToProductSQL ( $lngSlipNo, $lngRevisionNo, $aryData, $objDB )
{
	// ----------------------
	//   SQLʸ�κ���
	// ----------------------
	$aryOutQuery = array();
	//���ٹ�NO
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	//Ǽ����ɼ�ֹ�
	$aryOutQuery[] = "	,sd.lngSlipNo as lngSlipNo";
	//��ӥ�����ֹ�	
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";
	// ��ʸ��NO.
	$aryOutQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
	// �ܵ�����
	$aryOutQuery[] = ", sd.strGoodsCode as strGoodsCode";
	// ��̾
	$aryOutQuery[] = ", sd.strProductName as strProductName";
	// ����ʬ
	$aryOutQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
	$aryOutQuery[] = ", sd.strSalesClassName as strSalesClassName";
	// ñ��
	$aryOutQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
	// ����
	$aryOutQuery[] = ", To_char( sd.lngQuantity, '9,999,999,990' )  as lngQuantity";
	// ����
	$aryOutQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// ñ��
	$aryOutQuery[] = ", sd.strProductUnitName as strProductUnitName";
	// ��ȴ���
	$aryOutQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// ��������
	$aryOutQuery[] = ", sd.strNote as strDetailNote";
	// �����ơ�����������
	$aryOutQuery[] = ", re.lngReceiveStatusCode as lngReceiveStatusCode";

	// From��
	$aryOutQuery[] = " FROM t_SlipDetail sd";
	$aryOutQuery[] = "    LEFT JOIN m_Receive re ON sd.lngReceiveNo = re.lngReceiveNo";

	// Where��
	$aryOutQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . " AND sd.lngRevisionNo = " . $lngRevisionNo . "";	// �о�Ǽ����ɼ�ֹ�λ���

	// OrderBy��
	$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";

	// �ä�����줿�����Ȼ��ͤ�̵�����ᥳ���ȥ�����
	/*
	if ( $aryData["strSortOrder"] == "ASC" )
	{
		$strAsDs = "DESC";	// �إå����ܤȤϵս�ˤ���
	}
	else
	{
		$strAsDs = "ASC";	//�߽�
	}

	switch($aryData["strSort"])
	{
		case "strDetailNote":
			$aryOutQuery[] = " ORDER BY sd.strNote " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngSalesDetailNo":
			$aryOutQuery[] = " ORDER BY sd.lngSortKey " . $strAsDs;
			break;
		case "strProductName":
		case "strProductEnglishName":
		case "strGoodsCode":
			$aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngUserCode":
			$aryOutQuery[] = " ORDER BY mu.struserdisplaycode " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngGroupCode":
			$aryOutQuery[] = " ORDER BY mg.strgroupdisplaycode " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		default:
			$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";
	}
	*/

	return implode("\n", $aryOutQuery);
}


/**
* Ǽ�ʽ񸡺����ɽ���ؿ�
*
*	Ǽ�ʽ񸡺���̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
*	1�쥳����ʬ��HTML�����
*
*	@param  Integer $lngColumnCount 		�Կ�
*	@param  Array 	$aryHeadResult 			�إå��Ԥθ�����̤���Ǽ���줿����
*	@param  Array 	$aryDetailResult 		���ٹԤθ�����̤���Ǽ���줿����
*	@param  Array 	$aryHeadViewColumn 		�إå�ɽ���оݥ����̾������
*	@param  Array 	$aryData 				�Уϣӣԥǡ�����
*	@param	Array	$aryUserAuthority		�桼�����������Ф��븢�¤����ä�����
*	@access public
*/
function fncSetSlipTableRow ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryHeadViewColumn, $aryData, $aryUserAuthority, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
{
	// �ܵҤι����ܤǡ�����Ǽ�ʽ�إå���ɳ�Ť���������٤�¸�ߤ���
	$japaneseInvoiceExists = ($aryHeadResult["lngcountrycode"] == 81) && ($aryHeadResult["lnginvoiceno"] != null);

	for ( $i = 0; $i < count($aryDetailResult); $i++ )
	{
		// Ǽ����ɼ���٤�ɳ�Ť������ơ������������ѤߡפǤ���
		$receiveStatusIsClosed = $aryDetailResult[$i]["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED;

		$aryHtml[] =  "<tr>";
		$aryHtml[] =  "\t<td>" . ($lngColumnCount + $i) . "</td>";
		
		// ɽ���оݥ������������̤ν���
		for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
		{
			$strColumnName = $aryHeadViewColumn[$j];
			$TdData = "";

			// ɽ���оݤ��ܥ���ξ��
			if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
			{
				// �ܥ����ˤ���ѹ�

				// �ܺ٥ܥ���
				if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
				{
					if ( $aryHeadResult["lngrevisionno"] >= 0 )
					{						
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// �����ܥ���
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// Ǽ�ʽ�ǡ����ξ��֤ˤ��ʬ�� 
					// �ǿ�Ǽ�ʽ񤬺���ǡ����ξ��������Բ�
					if ( $japaneseInvoiceExists
					    or $receiveStatusIsClosed
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\">"
									."<img src=\"/mold/img/renew_off_bt.gif\" "
									."lngslipno=\"" . $aryHeadResult["lngslipno"] . "\" "
									."lngrevisionno=\"" . $aryHeadResult["lngrevisionno"] . "\" "
									."strslipcode=\"" . $aryHeadResult["strslipcode"] . "\" "
									."lngsalesno=\"" . $aryHeadResult["lngsalesno"] . "\" "
									."strsalescode=\"" . $aryHeadResult["strsalescode"] . "\" "
									."strcustomercode=\"" . $aryHeadResult["strcustomerdisplaycode"] . "\" "
									."class=\"renew button\"></td>\n";
					}
				}

				// ����ܥ���
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// �����⡼�ɤ�̵�����⤷���ϥ�Х�����¸�ߤ��ʤ����
					if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
					{
						// Ǽ�ʽ�ǡ����ξ��֤ˤ��ʬ�� 
						// �ǿ�Ǽ�ʽ񤬺���ǡ����ξ��������Բ�
						if ( $japaneseInvoiceExists
						    or $receiveStatusIsClosed
					        or $bytDeleteFlag )
						{
							$aryHtml[] = "\t<td></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"delete button\"></td>\n";
						}
					}
					// �����⡼�ɤ�ʣ����Х�����¸�ߤ�����
					else
					{
						// �ǿ�����ξ��
						if ( $lngReviseCount == 0 )
						{
							// Ǽ�ʽ�ǡ����ξ��֤ˤ��ʬ�� 
							// �ǿ�Ǽ�ʽ񤬺���ǡ����ξ��������Բ�
							if ( $japaneseInvoiceExists
								or $receiveStatusIsClosed
								or $bytDeleteFlag )
							{
								$aryHtml[] = "\t<td></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
								
							}
						}
					}
				}
			}
			// ɽ���оݤ��ܥ���ʳ��ξ��
			else if ($strColumnName != "") {
				$TdData = "\t<td>";
				$TdDataUse = true;
				$strText = "";

				// �ܵ�
				if ( $strColumnName == "lngCustomerCode" )
				{
					if ( $aryHeadResult["strcustomerdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strcustomerdisplayname"];
					$TdData .= $strText;
				}
				// ���Ƕ�ʬ
				else if ($strColumnName == "lngTaxClassCode"){
					$TdData  .= $aryHeadResult["strtaxclassname"];
				}
				// Ǽ����
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtmdeliverydate"], 0, 19 ) );
				}
				// Ǽ����
				else if ( $strColumnName == "lngDeliveryPlaceCode" )
				{
					$TdData  .= $aryHeadResult["strdeliveryplacename"];
				}
				// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
				else if ( $strColumnName == "strSlipCode" )
				{
					$TdData .= $aryHeadResult["strslipcode"];
					// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
				}
				// ��ɼ��
				else if ( $strColumnName == "lngInsertUserCode" )
				{
					if ( $aryHeadResult["strinsertusercode"] )
					{
						$strText .= "[" . $aryHeadResult["strinsertusercode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strinsertusername"];
					$TdData .= $strText;
				}
				// ��׶��
				else if ( $strColumnName == "curTotalPrice" )
				{
					$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
					if ( !$aryHeadResult["curtotalprice"] )
					{
						$strText .= "0.00";
					}
					else
					{
						$strText .= $aryHeadResult["curtotalprice"];
					}
					$TdData .= $strText;
				}
				else
				{
					//�ʥ����̾��ʸ���Ѵ���
					$strLowColumnName = strtolower($strColumnName);

					// ����
					if ( $strLowColumnName == "strnote" )
					{
						$strText .= nl2br($aryHeadResult[$strLowColumnName]);
					}
					// �ܺٹ���
					else if ( array_key_exists( $strLowColumnName , $aryDetailResult[$i] ) )
					{
						$strText .= $aryDetailResult[$i][$strLowColumnName];
					}
					// ����¾�ι���
					else
					{
						$strText .= $aryHeadResult[$strLowColumnName];
					}
					$TdData .= $strText;
				}
				$TdData .= "</td>\n";
				if ($TdDataUse) {
					$aryHtml[] = $TdData;
				}
			}
		}
		$aryHtml[] = "</tr>";
	}
	return $aryHtml;
}


/**
* Ǽ�ʽ񸡺����ɽ���ؿ�
*
*	Ǽ�ʽ񸡺���̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
*
*	@param  Array 	$aryResult 			������̤���Ǽ���줿����
*	@param  Array 	$aryViewColumn 		ɽ���оݥ����̾������
*	@param  Array 	$aryData 			�Уϣӣԥǡ�����
*	@param	Array	$aryUserAuthority	�桼�����������Ф��븢�¤����ä�����
*	@param	Array	$aryTytle			����̾����Ǽ���줿����ʸƤӽФ��������ܸ��ѡ��Ѹ��Ѥ��ڤ��ؤ���
*	@param  Object	$objDB       		DB���֥�������
*	@param  Object	$objCache       	����å��奪�֥�������
*	@access public
*/
function fncSetSlipTableBody ( $aryResult, $arySearchColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache)
{
	// �ܺ٥ܥ����ɽ������
	if ( $aryUserAuthority["Detail"] )
	{
		$aryHeadViewColumn[] = "btnDetail";
	}

	// �����ܥ����ɽ������
	if ( $aryUserAuthority["Fix"] )
	{
		$aryHeadViewColumn[] = "btnFix";
	}

	// �إå���
	$aryHeadViewColumn[] = "lngCustomerCode";		//�ܵ�
	$aryHeadViewColumn[] = "lngTaxClassCode";		//���Ƕ�ʬ
	$aryHeadViewColumn[] = "strSlipCode";			//Ǽ�ʽ�NO
	$aryHeadViewColumn[] = "dtmDeliveryDate";		//Ǽ����
	$aryHeadViewColumn[] = "lngDeliveryPlaceCode";	//Ǽ����
	$aryHeadViewColumn[] = "lngInsertUserCode";		//��ɼ��
	$aryHeadViewColumn[] = "strNote";				//����
	$aryHeadViewColumn[] = "curTotalPrice";			//��׶��
	
	// ������
	$aryHeadViewColumn[] = "lngRecordNo";			//���ٹ�NO
	$aryHeadViewColumn[] = "strCustomerSalesCode";	//��ʸ��NO
	$aryHeadViewColumn[] = "strGoodsCode";			//�ܵ�����
	$aryHeadViewColumn[] = "strProductName";		//��̾
	$aryHeadViewColumn[] = "strSalesClassName";		//����ʬ
	$aryHeadViewColumn[] = "curProductPrice";		//ñ��
	$aryHeadViewColumn[] = "lngQuantity";			//����
	$aryHeadViewColumn[] = "lngProductQuantity";	//����
	$aryHeadViewColumn[] = "strProductUnitName";	//ñ��
	$aryHeadViewColumn[] = "curSubTotalPrice";		//��ȴ���
	$aryHeadViewColumn[] = "strDetailNote";			//��������
	
	// ����ܥ���ʸ��¤ˤ��ɽ��/��ɽ���ڤ��ؤ���
	if ( $aryUserAuthority["Delete"] )
	{
		$aryHeadViewColumn[] = "btnDelete";
	}

	// ���ͤ�̵��������ɽ���ˤ����2019/8/22 T.Miyata��
	/*
	// ̵���ܥ���ʸ��¤ˤ��ɽ��/��ɽ���ڤ��ؤ���
	if ( $aryUserAuthority["Invalid"] )
	{
		$aryHeadViewColumn[] = "btnInvalid";
	}
	*/

	// �ơ��֥�η���
	$lngResultCount = count($aryResult);
	$lngColumnCount = 1;
	
	// ����̾�����Ƭ�ԡˤ����� start=========================================
	$aryHtml[] = "<thead>";
	$aryHtml[] = "<tr>";
	$aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

	// ɽ���оݥ������������������
	for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
	{
		$Addth = "\t<th>";
		
		$strColumnName = $aryHeadViewColumn[$j];
		$Addth .= $aryTytle[$strColumnName];
		
		$Addth .= "</th>";

		$aryHtml[] = $Addth;
	}
	$aryHtml[] = "</tr>";
	$aryHtml[] = "</thead>";
	// ����̾�����Ƭ�ԡˤ����� end=========================================

	$aryHtml[] = "<tbody>";

	for ( $i = 0; $i < $lngResultCount; $i++ )
	{
		// Ʊ��Ǽ����ɼ�����ɤΰ����������ɽ������
//		$strSlipCodeBase = $aryResult[$i]["strslipcode"];
//		$strSameSlipCodeQuery = fncGetSearchSlipSQL( $arySearchColumn, $aryData, $objDB, $strSlipCodeBase, $aryResult[$i]["lngslipno"], $aryData["strSessionID"]);
//		fncDebug("kids2.log", $strSameSlipCodeQuery, __FILE__, __LINE__, "a+");

		// �ͤ�Ȥ� =====================================
//		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameSlipCodeQuery, $objDB );

		// ����Υ��ꥢ
//		unset( $arySameSlipCodeResult );

//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngResultNum; $j++ )
//			{
//				$arySameSlipCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
//			}
//			$lngSameSlipCount = $lngResultNum;
//		}
//		$objDB->freeResult( $lngResultID );

		// Ʊ��Ǽ����ɼ�����ɤǤβ���Х����ǡ�����¸�ߤ����
//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngSameSlipCount; $j++ )
//			{
//				// ���������ʬ������
//				reset( $arySameSlipCodeResult[$j] );

				// �������򥯥��꡼�¹�
				$strDetailQuery = fncGetSlipToProductSQL ( $aryResult[$i]["lngslipno"], $aryResult[$i]["lngrevisionno"], $aryData, $objDB );
//				fncDebug("kids2.log", $strDetailQuery, __FILE__, __LINE__, "a+");
				if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
				{
					$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}

				// ���������̤μ���
				unset( $aryDetailResult );
				$lngDetailCount = pg_num_rows( $lngDetailResultID );
				if ( $lngDetailCount )
				{
					for ( $k = 0; $k < $lngDetailCount; $k++ )
					{
						$aryDetailResult[] = pg_fetch_array( $lngDetailResultID, $k, PGSQL_ASSOC );
					}
				}

				$objDB->freeResult( $lngDetailResultID );

				// Ʊ�������ɤ����ǡ����ǰ��־��ɽ������Ƥ������ǡ���������ǡ����ξ��
				if ( $arySameSlipCodeResult[0]["lngrevisionno"] < 0 )
				{
					$bytDeleteFlag = TRUE;
				}
				else
				{
					$bytDeleteFlag = FALSE;
				}

				// ���쥳����ʬ�ν���
				$aryHtml_add = fncSetSlipTableRow ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryHeadViewColumn, $aryData, $aryUserAuthority, $lngSameSlipCount, $j, $bytDeleteFlag );
				$lngColumnCount = $lngColumnCount + count($aryDetailResult);
				
				$strColBuff = '';
				for ( $k = 0; $k < count($aryHtml_add); $k++ )
				{
					$strColBuff .= $aryHtml_add[$k];
				}
				$aryHtml[] =$strColBuff;
//			}
//		}
	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

?>