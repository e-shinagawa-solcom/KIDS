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
*	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
*	@param  Array 	$arySearchColumn 		�����оݥ����̾������
*	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
*	@param  Object	$objDB       			DB���֥�������
*	@param	String	$strSlipCode			Ǽ����ɼ������	��������:������̽���	Ǽ����ɼ�����ɻ����:�����ѡ�Ʊ��Ǽ�ʽ�ΣϤΰ�������
*	@param	Integer	$lngSlipNo				Ǽ����ɼ�ֹ�	0:������̽���	Ǽ����ɼ�ֹ�����:�����ѡ�Ʊ��Ǽ����ɼ�����ɤȤ�������оݳ�Ǽ����ɼ�ֹ�
*	@param	Boolean	$bytAdminMode			ͭ���ʺ���ǡ����μ����ѥե饰	FALSE:������̽���	TRUE:�����ѡ�����ǡ�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strSlipCode, $lngSlipNo, $bytAdminMode, $strSessionID)
{

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// ɽ�����ܡ������⡼�ɤβ���ӥ����ǡ���������ӡ����پ���ϸ�����̤�����

		// �ܵ�
		if ( $strViewColumnName == "lngCustomerCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
			$flgCustomerCompany = TRUE;
		}

		// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
		if ( $strViewColumnName == "strSlipCode" )
		{
			$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
		}
		
		// ����
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", s.strNote as strNote";
		}

		// ��׶��
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//// ���Σ�
	//$arySelectQuery[] = ", s.strSalesCode as strSalesCode";

	// �����֥�����
	$arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
	$arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";

	// �̲�ñ��
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;

	// �����ɲ�
	$detailFlag = FALSE;

	// �����⡼�ɤθ�������Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
	if ( $strSlipCode or $bytAdminMode )
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

		// ����ǡ����������Ͼ���ɲ�
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND s.lngRevisionNo < 0\n";
		}
	}
	// �����⡼�ɤǤ�Ʊ��Ǽ����ɼ�����ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
	else
	{
		// ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ����Τ�
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////Ǽ�ʽ�ޥ�����θ������////
			// �ܵҡ�������
			if ( $strSearchColumnName == "lngCustomerCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCode"] . "'";
					$flgCustomerCompany = TRUE;
				}
				if ( $arySearchDataColumn["strCustomerName"] )
				{
					$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
					$flgCustomerCompany = TRUE;
				}
			}

			// ���Ƕ�ʬ�ʾ����Ƕ�ʬ��
			if ( $strSearchColumnName == "lngTaxClassCode" )
			{
				if ( $arySearchDataColumn["lngTaxClassCode"] )
				{
					$aryQuery[] = " AND s.strTaxClassName ~* '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
				}
			}

			// Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					$aryQuery[] = " AND UPPER(s.strSlipCode) LIKE UPPER('%" . $arySearchDataColumn["strSlipCode"] . "%')";
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
					$aryQuery[] = " AND s.strDeliveryPlaceCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
					$flgCustomerCompany = TRUE;
				}
				if ( $arySearchDataColumn["strDeliveryPlaceName"] )
				{
					$aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
					$flgCustomerCompany = TRUE;
				}
			}

			// ��ɼ��
			if ( $strSearchColumnName == "lngInsertUserCode" )
			{
				if ( $arySearchDataColumn["lngInsertUserCode"] )
				{
					$aryQuery[] = " AND insert_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
					$flgInsertUser = TRUE;
				}
				if ( $arySearchDataColumn["strInsertUserName"] )
				{
					$aryQuery[] = " AND UPPER(insert_u.strInsertUserName) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
					$flgInsertUser = TRUE;
				}
			}

			//
			// ���٥ơ��֥�ξ��
			//

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
					$aryDetailWhereQuery[] = "UPPER(p.strCustomerSalesCode) LIKE UPPER('%" . $arySearchDataColumn["strCustomerSalesCode"] . "%') ";
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
					$aryDetailWhereQuery[] = "UPPER(p.strGoodsCode) LIKE UPPER('%" . $arySearchDataColumn["strGoodsCode"] . "%') ";
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

	// ���ٹԤθ����б�

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSlipNo ) sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";		      // Ǽ����ɼ�����ֹ�
	$aryDetailFrom[] = "	,sd1.lngSortKey as lngRecordNo";  // ���ٹ�NO
	$aryDetailFrom[] = "	,sd1.strCustomerSalesCode";	      // ��ʸ��NO
	$aryDetailFrom[] = "	,p.strGoodsCode";                 // �ܵ�����
	$aryDetailFrom[] = "	,p.strProductName";			      // ��̾
	$aryDetailFrom[] = "	,sd1.lngSalesClassCode";	// ����ʬ
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,sd1.lngQuantity";	        // ����
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// ����
	$aryDetailFrom[] = "	,sd1.lngProductUnitCode";	// ñ��
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,sd1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_SlipDetail sd1 ";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON sd1.strProductCode = p.strProductCode";

	$aryDetailWhereQuery[] = ") as sd";
	// where������ٹԡ� �����꡼Ϣ��
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤξ�郎¸�ߤ�����
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
	

	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT distinct s.lngSlipNo as lngSlipNo";
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,sd.lngSlipDetailNo";		      // Ǽ����ɼ�����ֹ�
	$aryOutQuery[] = "	,sd.lngRecordNo";                 // ���ٹ�NO
	$aryOutQuery[] = "	,sd.strCustomerSalesCode";	      // ��ʸ��NO
	$aryOutQuery[] = "	,sd.strGoodsCode";                // �ܵ�����
	$aryOutQuery[] = "	,sd.strProductName";			  // ��̾
	$aryOutQuery[] = "	,sd.lngSalesClassCode";	          // ����ʬ
	$aryOutQuery[] = "	,sd.curProductPrice";		      // ñ��
	$aryOutQuery[] = "	,sd.lngQuantity";	              // ����
	$aryOutQuery[] = "	,sd.lngProductQuantity";	      // ����
	$aryOutQuery[] = "	,sd.lngProductUnitCode";	      // ñ��
	$aryOutQuery[] = "	,sd.curSubTotalPrice";		      // ��ȴ���
	$aryOutQuery[] = "	,sd.strNote";				      // ��������

	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Slip s";
	$aryFromQuery[] = " LEFT JOIN m_Sales sa ON s.lngSalesNo = sa.lngSalesNo";
	$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.strCustomerCode = cust_c.strCompanyDisplayCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgInsertUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.strInsertUserCode = insert_u.strUserDisplayCode";
	}

	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;

	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹ��Ѥξ��Ϣ��
	$aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��       ////
	//// Ʊ��Ǽ����ɼ�����ɤ���ĥǡ�����̵�����ǡ���          ////
	/////////////////////////////////////////////////////////////
	// Ǽ����ɼ�����ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strSlipCode )
	{
		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Slip s1 WHERE s1.strSlipCode = s.strSlipCode AND s1.bytInvalidFlag = false )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
		}
	}

	// �����⡼�ɤθ�������Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
	if ( $strSlipCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// �����Ⱦ������
		$aryOutQuery[] = " ORDER BY lngSlipNo DESC";

		// TODO:�����ȵ�ǽɬ�פ������׳�ǧ��
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
*	@param  Array 	$aryDetailViewColumn 	ɽ���о����٥����̾������
*	@param  String 	$lngSlipNo 			    �о�Ǽ����ɼ�ֹ�
*	@param  Array 	$aryData 				POST�ǡ���������
*	@param  Object	$objDB       			DB���֥�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSlipToProductSQL ( $aryDetailViewColumn, $lngSlipNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];
		
		// ��ʸ��NO.
		if ( $strViewColumnName == "strCustomerSalesCode" )
		{
			$arySelectQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
		}

		// �ܵ�����
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
			$flgProductCode = TRUE;
		}

		// ��̾
		if ( $strViewColumnName == "strProductName" )
		{
			$arySelectQuery[] = ", p.strProductName as strProductName";
			$flgProductCode = TRUE;
		}

		// ����ʬ
		if ( $strViewColumnName == "lngSalesClassCode" )
		{
			$arySelectQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
			$arySelectQuery[] = ", sc.strSalesClassName as strSalesClassName";
			$flgSalesClass = TRUE;
		}
		
		// ñ��
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
		}

		// ����
		if ( $strViewColumnName == "lngQuantity" )
		{
			$arySelectQuery[] = ", To_char( sd.lngQuantity, '9,999,999,990' )  as lngQuantity";
		}

		// ����
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}
		
		// ñ��
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}

		// ��ȴ���
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}

		// ��������
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", sd.strNote as strDetailNote";
		}

	}

	// ���о�� �о�Ǽ����ɼ�ֹ�λ���
	$aryQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . "";

	// �����ɲ�

	// ////Ǽ����ɼ�ޥ�����θ������////
	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";		//���ٹ�NO
	$aryOutQuery[] = "	,sd.lngSlipNo as lngSlipNo";			//Ǽ����ɼ�ֹ�
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";	//��ӥ�����ֹ�
	
	// select�� �����꡼Ϣ��
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_SlipDetail sd";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
		 
	if ( $flgSalesClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesClass sc USING (lngSalesClassCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	}

	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// �����Ⱦ�����
	if ( $aryData["strSortOrder"] == "ASC" )
	{
		$strAsDs = "DESC";	// �إå����ܤȤϵս�ˤ���
	}
	else
	{
		$strAsDs = "ASC";	//�߽�
	}

	$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";

	// TODO:�����ȵ�ǽɬ�פ������׳�ǧ��
	/*
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
* Ǽ�ʽ񸡺����ɽ���ؿ��ʥإå��ѡ�
*
*	Ǽ�ʽ񸡺���̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
*	�إå��Ԥ�ɽ������
*
*	@param  Integer $lngColumnCount 		�Կ�
*	@param  Array 	$aryHeadResult 			�إå��Ԥθ�����̤���Ǽ���줿����
*	@param  Array 	$aryDetailResult 		���ٹԤθ�����̤���Ǽ���줿����
*	@param  Array 	$aryHeadViewColumn 		�إå�ɽ���оݥ����̾������
*	@param  Array 	$aryDetailViewColumn 	����ɽ���оݥ����̾������
*	@param  Array 	$aryData 				�Уϣӣԥǡ�����
*	@param	Array	$aryUserAuthority		�桼�����������Ф��븢�¤����ä�����
*	@access public
*/
function fncSetSlipHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
{
	for ( $i = 0; $i < count($aryDetailResult); $i++ )
	{
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

				// �ܺ�ɽ��
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

				// ����
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// ���ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡס��ޤ�����оݤξ�罤���ܥ���������Բ�
					// �ǿ���夬����ǡ����ξ��������Բ�
					if ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_CLOSED 
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
					}
				}

				// ���
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// �����⡼�ɤ�̵�����⤷���ϥ�Х�����¸�ߤ��ʤ����
					if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
					{
						// ���ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡפξ�����ܥ���������Բ�
						// �ǿ�ȯ������ǡ����ξ��������Բ�
						if ( $aryHeadResult["lngsalesstatuscode"] != DEF_SALES_CLOSED 
							and !$bytDeleteFlag )

						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					// �����⡼�ɤ�ʣ����Х�����¸�ߤ�����
					else
					{
						// �ǿ�����ξ��
						if ( $lngReviseCount == 0 )
						{
							// ���ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡפξ�����ܥ���������Բ�
							// �ǿ���夬����ǡ����ξ��������Բ�
							if ( $aryHeadResult["lngsalesstatuscode"] != DEF_SALES_CLOSED 
								and !$bytDeleteFlag )
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td></td>\n";
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
				// Ǽ����
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtmdeliverydate"], 0, 19 ) );
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
					if ( $aryHeadResult["strinsertuserdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strinsertuserdisplaycode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strinsertuserdisplayname"];
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
function fncSetSlipTable ( $aryResult, $aryViewColumn, $arySearchColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName)
{
	// ����

	// ɽ�������Υإå�������������ʬΥ����
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strColumnName = $aryViewColumn[$i];

		// �ܥ���ξ�礳����ɽ������ɽ���ڤ��ؤ�
		if ( $strColumnName == "btnDetail" )
		{
			if ( $aryUserAuthority["Detail"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		else if ( $strColumnName == "btnFix" )
		{
			if ( $aryUserAuthority["Fix"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		else if ( $strColumnName == "btnDelete" )
		{
			if ( $aryUserAuthority["Delete"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		else if ( $strColumnName == "btnInvalid" )
		{
			if ( $aryUserAuthority["Invalid"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		// �ܺ���
		else if ( $strColumnName == "lngRecordNo"				//���ٹ�NO
			or $strColumnName == "strCustomerSalesCode"			//��ʸ��NO
			or $strColumnName == "strGoodsCode"					//�ܵ�����
			or $strColumnName == "strProductName"				//��̾
			or $strColumnName == "lngSalesClassCode"			//����ʬ
			or $strColumnName == "curProductPrice"				//ñ��
			or $strColumnName == "lngQuantity"					//����
			or $strColumnName == "lngProductQuantity"			//����
			or $strColumnName == "lngProductUnitCode"			//ñ��
			or $strColumnName == "curSubTotalPrice"				//��ȴ���
			or $strColumnName == "strDetailNote"      )			//��������
		{
			$aryDetailViewColumn[] = $strColumnName;
			$aryHeadViewColumn[] = $strColumnName;
		}
		// �إå���
		else
		{
			$aryHeadViewColumn[] = $strColumnName;
		}
	}

	// �ơ��֥�η���
	$lngResultCount = count($aryResult);

	$lngColumnCount = 1;
	
	// ����̾������� start=========================================
	$aryHtml[] = "<thead>";
	$aryHtml[] = "<tr>";
	$aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

	// ɽ���оݥ������������������
	for ( $j = 0; $j < count($aryViewColumn); $j++ )
	{
		$Addth = "\t<th>";
		$strColumnName = $aryViewColumn[$j];
		
		// �����ȹ��ܰʳ��ξ��
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" )
		{
			// �����ȹ��ܰʳ��ξ��
			if ( ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] ) 
			or ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] ) 
			or ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] ) )
			{
				$Addth .= $aryTytle[$strColumnName];
			}
		}
		// �����ȹ��ܤξ��
		else
		{
			$Addth .= $aryTytle[$strColumnName];
		}

		$Addth .= "</th>";
		$aryHtml[] = $Addth;
	}
	$aryHtml[] = "</tr>";
	$aryHtml[] = "</thead>";

// ����̾������� end=========================================

	$aryHtml[] = "<tbody>";

	for ( $i = 0; $i < $lngResultCount; $i++ )
	{
// �����⡼���Ѳ���Х���������ǡ�������start==================================
		// �����⡼�ɤξ�硡Ʊ��Ǽ����ɼ�����ɤΰ����������ɽ������

		$strSlipCodeBase = $aryResult[$i]["strslipcode"];

		$strSameSlipCodeQuery = fncGetSearchSlipSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strSlipCodeBase, $aryResult[$i]["lngslipno"], FALSE, $aryData["strSessionID"]);

		// �ͤ�Ȥ� =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameSlipCodeQuery, $objDB );

		// ����Υ��ꥢ
		unset( $arySameSlipCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameSlipCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameSalesCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// Ʊ��Ǽ����ɼ�����ɤǤβ���Х����ǡ�����¸�ߤ����
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameSalesCount; $j++ )
			{
				// ���������ʬ������

				reset( $arySameSlipCodeResult[$j] );

				// ���ٽ����Ѥ�Ĵ��
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// ���ٹԿ���Ĵ��
					$strDetailQuery = fncGetSlipToProductSQL ( $aryDetailViewColumn, $arySameSlipCodeResult[$j]["lngslipno"], $aryData, $objDB );

					// �����꡼�¹�
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
					}

					$lngDetailCount = pg_num_rows( $lngDetailResultID );

					// ����Υ��ꥢ
					unset( $aryDetailResult );

					// ��̤μ���
					if ( $lngDetailCount )
					{
						for ( $k = 0; $k < $lngDetailCount; $k++ )
						{
							$aryDetailResult[] = pg_fetch_array( $lngDetailResultID, $k, PGSQL_ASSOC );
						}
					}

					$objDB->freeResult( $lngDetailResultID );
				}

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
				$aryHtml_add = fncSetSlipHeadTable ( $lngColumnCount, $arySameSlipCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameSalesCount, $j, $bytDeleteFlag );
				$lngColumnCount = $lngColumnCount + count($aryDetailResult);
				
				$strColBuff = '';
				for ( $k = 0; $k < count($aryHtml_add); $k++ )
				{
					$strColBuff .= $aryHtml_add[$k];
				}
				$aryHtml[] =$strColBuff;
			}
		}

// �����⡼���Ѳ���Х����ǡ�������end==================================

	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

?>