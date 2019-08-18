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
*	@param	String	$strSalesCode			��女����	��������:������̽���	��女���ɻ����:�����ѡ�Ʊ����女���ɤΰ�������
*	@param	Integer	$lngSalesNo				���Σ�	0:������̽���	���Σ�����:�����ѡ�Ʊ����女���ɤȤ�������оݳ����NO
*	@param	Boolean	$bytAdminMode			ͭ���ʺ���ǡ����μ����ѥե饰	FALSE:������̽���	TRUE:�����ѡ�����ǡ�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strSalesCode, $lngSalesNo, $bytAdminMode )
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

		// Ǽ����ɼ�����ɡ�Ǽ�ǽ�NO��
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

	// ���Σ�
	$arySelectQuery[] = ", s.strSalesCode as strSalesCode";

	// �̲�ñ��
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;

	// �����ɲ�
	$detailFlag = FALSE;

	// �����⡼�ɤθ�������Ʊ����女���ɤΥǡ��������������
	if ( $strSalesCode or $bytAdminMode )
	{
		// Ʊ����女���ɤ��Ф��ƻ��������ֹ�Υǡ����Ͻ�������
		if ( $lngSalesNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strSalesCode = '" . $strSalesCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// ����ǡ����������Ͼ���ɲ�
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND s.lngRevisionNo < 0\n";
		}
	}
	// �����⡼�ɤǤ�Ʊ����女���ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
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

			// Ǽ�ʽ�NO.��Ǽ����ɼ�����ɡ�
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
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSalesNo ) sd1.lngSalesNo ";
	$aryDetailFrom[] = "	,sd1.lngSalesDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,sd1.lngSalesClassCode";	// ����ʬ
	$aryDetailFrom[] = "	,p.strGoodsCode";
	$aryDetailFrom[] = "	,sd1.dtmDeliveryDate";		// Ǽ��
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,sd1.lngProductUnitCode";	// ñ��
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// ���ʿ���
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,sd1.lngTaxClassCode";		// �Ƕ�ʬ
	$aryDetailFrom[] = "	,mt.curtax";				// ��Ψ
	$aryDetailFrom[] = "	,sd1.curtaxprice";			// �ǳ�
	$aryDetailFrom[] = "	,sd1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_SalesDetail sd1 ";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON sd1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = sd1.lngtaxcode";

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
	$aryOutQuery[] = "SELECT distinct s.lngSalesNo as lngSalesNo";
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,sd.lngSalesDetailNo";
	$aryOutQuery[] = "	,sd.strProductCode";
	$aryOutQuery[] = "	,sd.struserdisplaycode";
	$aryOutQuery[] = "	,sd.strGroupDisplayCode";
	$aryOutQuery[] = "	,sd.strProductName";
	$aryOutQuery[] = "	,sd.strProductEnglishName";
	$aryOutQuery[] = "	,sd.lngSalesClassCode";
	$aryOutQuery[] = "	,sd.strGoodsCode";
	$aryOutQuery[] = "	,sd.dtmDeliveryDate";
	$aryOutQuery[] = "	,sd.curProductPrice";
	$aryOutQuery[] = "	,sd.lngProductUnitCode";
	$aryOutQuery[] = "	,sd.lngProductQuantity";
	$aryOutQuery[] = "	,sd.curSubTotalPrice";
	$aryOutQuery[] = "	,sd.lngTaxClassCode";
	$aryOutQuery[] = "	,sd.curTax";
	$aryOutQuery[] = "	,sd.curTaxPrice";
	$aryOutQuery[] = "	,sd.strNote";

	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Sales s";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgInsertUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.lngInsertUserCode = insert_u.lngUserCode";
	}

	/*
	if ( $flgReceive )
	{
		$aryFromQuery[] = "	left join t_salesdetail tsd";
		$aryFromQuery[] = "	on tsd.lngsalesno = s.lngsalesno";
		$aryFromQuery[] = "		left join m_Receive r on r.lngreceiveno = tsd.lngreceiveno";
	}
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgSalesStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss USING (lngSalesStatusCode)";
	}
	*/

	/*
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(s.lngSalesNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_SC1; // �����Ͽ����WF�ǡ������оݤˤ���٤˾�����
	}
	*/

	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;

	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹ��Ѥξ��Ϣ��
	$aryOutQuery[] = " AND sd.lngSalesNo = s.lngSalesNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��       ////
	//// Ʊ����女���ɤ���ĥǡ�����̵�����ǡ���          ////
	/////////////////////////////////////////////////////////////
	// ��女���ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strSalesCode )
	{
		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode AND s1.bytInvalidFlag = false )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.bytInvalidFlag = false AND s2.strSalesCode = s.strSalesCode )";
		}
	}

	// �����⡼�ɤθ�������Ʊ����女���ɤΥǡ��������������
	if ( $strSalesCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// �����Ⱦ������
		$aryOutQuery[] = " ORDER BY lngSalesNo DESC";


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
* �б�������ʸ��NO�Υǡ������Ф������ٹԤ��������SQLʸ�κ����ؿ�
*
*	��ʸ��NO�������٤�������� SQLʸ���������
*
*	@param  Array 	$aryDetailViewColumn 	ɽ���о����٥����̾������
*	@param  String 	$lngSalesNo 			�о���ʸ��NO
*	@param  Array 	$aryData 				POST�ǡ���������
*	@param  Object	$objDB       			DB���֥�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSalesToProductSQL ( $aryDetailViewColumn, $lngSalesNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];

		// ɽ������
		// ���ʥ�����
		if ( $strViewColumnName == "strProductCode" )
		{
			$arySelectQuery[] = ", sd.strProductCode as strProductCode";
		}

		// ����
		if ( $strViewColumnName == "lngInChargeGroupCode" )
		{
			$arySelectQuery[] = ", '['||mg.strgroupdisplaycode||'] '|| mg.strgroupdisplayname as lngInChargeGroupCode";
		}
		// ô����
		if ( $strViewColumnName == "lngInChargeUserCode" )
		{
			$arySelectQuery[] = ", '['||mu.struserdisplaycode ||'] '|| mu.struserdisplayname  as lngInChargeUserCode";
		}

		// ����̾�Ρ����ܸ��
		if ( $strViewColumnName == "strProductName" )
		{
			$arySelectQuery[] = ", p.strProductName as strProductName";
			$flgProductCode = TRUE;
		}

		// ����̾�ΡʱѸ��
		if ( $strViewColumnName == "strProductEnglishName" )
		{
			$arySelectQuery[] = ", p.strProductEnglishName as strProductEnglishName";
			$flgProductCode = TRUE;
		}
		// ����ʬ
		if ( $strViewColumnName == "lngSalesClassCode" )
		{
			$arySelectQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
			$arySelectQuery[] = ", ss.strSalesClassName as strSalesClassName";
			$flgSalesClass = TRUE;
		}

		// �ܵ�����
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
			$flgProductCode = TRUE;
		}

		// Ǽ��
		if ( $strViewColumnName == "dtmDeliveryDate" )
		{
			$arySelectQuery[] = ", to_char( sd.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
		}

		// ñ��
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
		}

		// ñ��
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}

		// ����
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}

		// ��ȴ���
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}

		// �Ƕ�ʬ
		if ( $strViewColumnName == "lngTaxClassCode" )
		{
			$arySelectQuery[] = ", sd.lngTaxClassCode as lngTaxClassCode";
			$arySelectQuery[] = ", tc.strTaxClassName as strTaxClassName";
			$flgTaxClass = TRUE;
		}

		// ��Ψ
		if ( $strViewColumnName == "curTax" )
		{
			$arySelectQuery[] = ", sd.lngTaxCode as lngTaxCode";
			$arySelectQuery[] = ", To_char( t.curTax, '9,999,999,990.999' ) as curTax";
			$flgTax = TRUE;
		}

		// �ǳ�
		if ( $strViewColumnName == "curTaxPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curTaxPrice, '9,999,999,990.99' )  as curTaxPrice";
		}

		// ��������
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", sd.strNote as strDetailNote";
		}
	}

	// ���о�� �о����NO�λ���
	$aryQuery[] = " WHERE sd.lngSalesNo = " . $lngSalesNo . "";

	// �����ɲ�

	// ////���ޥ�����θ������////
	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,sd.lngSalesNo as lngSalesNo";
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";
	
	// select�� �����꡼Ϣ��
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_SalesDetail sd";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
//	if ( $flgProductCode )
//	{
		$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
		$aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
 		$aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";
//	}
	if ( $flgSalesClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	}
	if ( $flgTaxClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_TaxClass tc USING (lngTaxClassCode)";
	}
	if ( $flgTax )
	{
		$aryFromQuery[] = " LEFT JOIN m_Tax t USING (lngTaxCode)";
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
			//$aryOutQuery[] = " ORDER BY sd." . $aryData["strSort"] . " " . $strAsDs . ", sd.lngSortKey ASC";
			$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";
	}

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
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
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
	// 2004.03.01 Suzukaze update start
	//					or ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_END and !$aryData["Admin"] ) 
	// 2004.03.01 Suzukaze update end
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
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
	// 2004.03.01 Suzukaze update start
	//						and $aryHeadResult["lngsalesstatuscode"] != DEF_SALES_END 
	// 2004.03.01 Suzukaze update end
							and !$bytDeleteFlag )

						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
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
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td></td>\n";
							}
						}
					}
				}
			}
			else if ($strColumnName != "") {
				$TdData = "\t<td>";
				$TdDataUse = true;
				$strText = "";
				// ��Ͽ��
				if ( $strColumnName == "dtmInsertDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) );
				}

				// �׾���
				else if ( $strColumnName == "dtmSalesAppDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmsalesappdate"] );
				}

				// ���NO
				else if ( $strColumnName == "strSalesCode" )
				{
					$TdData .= $aryHeadResult["strsalescode"];
					// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
				}

				// �ܵҼ����ֹ�
				else if ( $strColumnName == "strCustomerReceiveCode" )
				{
					$TdData .= $aryHeadResult["strcustomerreceivecode"];
				}

				// ��ɼ������
				else if ( $strColumnName == "strSlipCode" )
				{
					$TdData .= $aryHeadResult["strslipcode"];
				}

				// ���ϼ�
				else if ( $strColumnName == "lngInputUserCode" )
				{
					if ( $aryHeadResult["strinputuserdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strinputuserdisplaycode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strinputuserdisplayname"];
					$TdData .= $strText;
				}

				// �ܵ�
				else if ( $strColumnName == "lngCustomerCode" )
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

				// ����
				else if ( $strColumnName == "lngSalesStatusCode" )
				{
					$TdData .= $aryHeadResult["strsalesstatusname"];
				}

				// ����¾�ι��ܤϤ��Τޤ޽���
				else
				{
					$strLowColumnName = strtolower($strColumnName);
					if ( $strLowColumnName == "strnote" )
					{
						$strText .= nl2br($aryHeadResult[$strLowColumnName]);
					}
					else if ( array_key_exists( $strLowColumnName , $aryDetailResult[$i] ) )
					{
						$strText .= $aryDetailResult[$i][$strLowColumnName];
					}
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
function fncSetSlipTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
// 2004.03.31 suzukaze update start
		// �ܺ���
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo"
			or $strColumnName == "lngSalesClassCode" or $strColumnName == "strGoodsCode" or $strColumnName == "dtmDeliveryDate"
			or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity"
			or $strColumnName == "curSubTotalPrice" or $strColumnName == "lngTaxClassCode" or $strColumnName == "curTax"
			or $strColumnName == "curTaxPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" )
// 2004.03.31 suzukaze update end
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
		// �����⡼�ɤξ�硡Ʊ����女���ɤΰ����������ɽ������

		$strSalesCodeBase = $aryResult[$i]["strsalescode"];

		$strSameSalesCodeQuery = fncGetSearchSalesSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strSalesCodeBase, $aryResult[$i]["lngsalesno"], FALSE );

		// �ͤ�Ȥ� =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameSalesCodeQuery, $objDB );

		// ����Υ��ꥢ
		unset( $arySameSalesCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameSalesCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameSalesCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// Ʊ����女���ɤǤβ���Х����ǡ�����¸�ߤ����
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameSalesCount; $j++ )
			{
				// ���������ʬ������

				reset( $arySameSalesCodeResult[$j] );

				// ���ٽ����Ѥ�Ĵ��
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// ���ٹԿ���Ĵ��
					$strDetailQuery = fncGetSalesToProductSQL ( $aryDetailViewColumn, $arySameSalesCodeResult[$j]["lngsalesno"], $aryData, $objDB );

					// �����꡼�¹�
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
				if ( $arySameSalesCodeResult[0]["lngrevisionno"] < 0 )
				{
					$bytDeleteFlag = TRUE;
				}
				else
				{
					$bytDeleteFlag = FALSE;
				}

				// ���쥳����ʬ�ν���
				$aryHtml_add = fncSetSlipHeadTable ( $lngColumnCount, $arySameSalesCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameSalesCount, $j, $bytDeleteFlag );
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