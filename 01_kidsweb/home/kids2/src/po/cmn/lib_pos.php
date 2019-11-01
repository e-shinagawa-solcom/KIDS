<?
// ----------------------------------------------------------------------------
/**
 *       ȯ�����  ������Ϣ�ؿ���
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
 *         ��������̴�Ϣ�δؿ�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------


/**
 * �������ܤ�����פ���ǿ���ȯ��ǡ������������SQLʸ�κ����ؿ�
 *
 *	�������ܤ��� SQLʸ���������
 *
 *	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
 *	@param  Array 	$arySearchColumn 		�����оݥ����̾������
 *	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
 *	@param  Object	$objDB       			DB���֥�������
 *	@param	String	$strOrderCode			ȯ������	��������:������̽���	ȯ�����ɻ����:�����ѡ�Ʊ��ȯ�����ɤΰ�������
 *	@param	Integer	$lngOrderNo				ȯ��Σ�	0:������̽���	ȯ��Σ�����:�����ѡ�Ʊ��ȯ�����ɤȤ�������оݳ�ȯ��NO
 *	@param	Boolean	$bytAdminMode			ͭ���ʺ���ǡ����μ����ѥե饰	FALSE:������̽���	TRUE:�����ѡ�����ǡ�������
 *	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
 *	@access public
 */
 
 
function fncGetSearchPurchaseSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode)
{

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// ɽ�����ܡ������⡼�ɤβ���ӥ����ǡ���������ӡ����پ���ϸ�����̤�����

		// ��Ͽ��
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
		}

		// �׾���
		if ( $strViewColumnName == "dtmOrderAppDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
		}

		// ȯ��Σ�
		if ( $strViewColumnName == "strOrderCode" )
		{
			$arySelectQuery[] = ", o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode";
		}

		// ���ϼ�
		if ( $strViewColumnName == "lngInputUserCode" )
		{
			$arySelectQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
			$arySelectQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
			$flgInputUser = TRUE;
		}

		// ������
		if ( $strViewColumnName == "lngCustomerCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
			$flgCustomerCompany = TRUE;
		}
		// ����
		if ( $strViewColumnName == "lngOrderStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", o.lngOrderStatusCode as lngOrderStatusCode";
			$arySelectQuery[] = ", os.strOrderStatusName as strOrderStatusName";
			$flgOrderStatus = TRUE;
		}
		
		// ����ե�����
		if ( $strViewColumnName == "lngWorkFlowStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", (select strWorkflowStatusName from m_WorkflowStatus where lngWorkflowStatusCode = tw.lngWorkflowStatusCode) as lngWorkFlowStatusCode";
			$flgWorkFlowStatus = TRUE;
		}
		

		// ��ʧ���
		if ( $strViewColumnName == "lngPayConditionCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", o.lngPayConditionCode as lngPayConditionCode";
			$arySelectQuery[] = ", pc.strPayConditionName as strPayConditionName";
			$flgPayCondition = TRUE;
		}

		// ȯ��ͭ��������
		if ( $strViewColumnName == "dtmExpirationDate" and !$bytAdminMode )
		{
			// $arySelectQuery[] = ", to_char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
			$arySelectQuery[] = ", to_char( od.dtmExpirationDate, 'YYYY/MM/DD') as dtmExpirationDate";
		}

		// ����
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			//$arySelectQuery[] = ", o.strNote as strNote";
		}

		// ��׶��
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( o.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//
	$arySelectQuery[] = ", mm.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;


	// �����ɲ�
	$detailFlag = FALSE;

	// �����⡼�ɤθ�������Ʊ��ȯ�����ɤΥǡ��������������
	if ( $strOrderCode or $bytAdminMode )
	{
		// Ʊ��ȯ�����ɤ��Ф��ƻ����ȯ���ֹ�Υǡ����Ͻ�������
		if ( $lngOrderNo )
		{
			$aryQuery[] = " WHERE o.bytInvalidFlag = FALSE AND o.strOrderCode = '" . $strOrderCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// ����ǡ����������Ͼ���ɲ�
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND o.lngRevisionNo < 0";
		}
	}

	// �����⡼�ɤǤ�Ʊ��ȯ�����ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
	else
	{
		// ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ�ȯ��Τ�
		$aryQuery[] = " WHERE o.bytInvalidFlag = FALSE AND o.lngRevisionNo >= 0";

		// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////ȯ��ޥ�����θ������////
			// ��Ͽ��
			if ( $strSearchColumnName == "dtmInsertDate" )
			{
				if ( $arySearchDataColumn["dtmInsertDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND o.dtmInsertDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmInsertDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
					$aryQuery[] = " AND o.dtmInsertDate <= '" . $dtmSearchDate . "'";
				}
			}
			// �׾���
			if ( $strSearchColumnName == "dtmOrderAppDate" )
			{
				if ( $arySearchDataColumn["dtmOrderAppDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmOrderAppDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND o.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmOrderAppDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmOrderAppDateTo"] . " 23:59:59";
					$aryQuery[] = " AND o.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
				}
			}
			// ȯ��Σ�
			if ( $strSearchColumnName == "strOrderCode" )
			{
				if ( $arySearchDataColumn["strOrderCodeFrom"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeFrom"], "-") )
					{
						// ��Х����������դ�ȯ��Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
						$strNewOrderCode_from = preg_replace( strstr( $arySearchDataColumn["strOrderCodeFrom"], "_" ), "", $arySearchDataColumn["strOrderCodeFrom"] );
					}
					else
					{
						$strNewOrderCode_from = $arySearchDataColumn["strOrderCodeFrom"];
					}
//					$aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode_from . "'";

				}
				if ( $arySearchDataColumn["strOrderCodeTo"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeTo"], "-") )
					{
						// ��Х����������դ�ȯ��Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
						$strNewOrderCode_to = preg_replace( strstr( $arySearchDataColumn["strOrderCodeTo"], "_" ), "", $arySearchDataColumn["strOrderCodeTo"] );
					}
					else
					{
						$strNewOrderCode_to = $arySearchDataColumn["strOrderCodeTo"];
					}
//					$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode_to . "'";
				}
				if( ( $strNewOrderCode_from && $strNewOrderCode_to ) && ( $strNewOrderCode_from == $strNewOrderCode_to ) )
				{
					// from��to��Ʊ���ͤξ��ϡ��ϰϻ���ǤϤʤ�"="�ǻ���"
					$aryQuery[] = " AND o.strOrderCode = '" . $strNewOrderCode_to . "'";
				}
				else
				{
					if( $strNewOrderCode_from )
					{
						$aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode_from . "'";
					}
					if( $strNewOrderCode_to )
					{
						$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode_to . "'";
					}
				}
			}
			// ���ϼ�
			if ( $strSearchColumnName == "lngInputUserCode" )
			{
				if ( $arySearchDataColumn["lngInputUserCode"] )
				{
					$aryQuery[] = " AND input_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'";
					$flgInputUser = TRUE;
				}
				if ( $arySearchDataColumn["strInputUserName"] )
				{
					$aryQuery[] = " AND UPPER( input_u.strUserDisplayName ) LIKE UPPER( '%" . $arySearchDataColumn["strInputUserName"] . "%' )";
					$flgInputUser = TRUE;
				}
			}
			// ������
			if ( $strSearchColumnName == "lngCustomerCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCode"] . "'";
					$flgCustomerCompany = TRUE;
				}
				if ( $arySearchDataColumn["strCustomerName"] )
				{
					$aryQuery[] = " AND UPPER( cust_c.strCompanyDisplayName ) LIKE UPPER( '%" . $arySearchDataColumn["strCustomerName"] . "%' )";
					$flgCustomerCompany = TRUE;
				}
			}
			
			
			// ����
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if ( $arySearchDataColumn["lngInChargeGroupCode"] )
				{
					$aryQuery[] = " AND mg.strGroupDisplayCode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
					$flgGroup = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeGroupName"] )
				{
					$aryQuery[] = " AND UPPER(mg.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
					$flgGroup = TRUE;
				}
			}
			// ô����
			if ( $strSearchColumnName == "lngInChargeUserCode" )
			{
				if ( $arySearchDataColumn["lngInChargeUserCode"] )
				{
					$aryQuery[] = " AND mu.strUserDisplayCode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
					$flgUser = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeUserName"] )
				{
					$aryQuery[] = " AND UPPER(mu.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
					$flgUser = TRUE;
				}
			}
			// ����
			if ( $strSearchColumnName == "lngOrderStatusCode" )
			{
				if ( $arySearchDataColumn["lngOrderStatusCode"] )
				{
					// ȯ����֤� ","���ڤ��ʸ����Ȥ����Ϥ����
					//$arySearchStatus = explode( ",", $arySearchDataColumn["lngOrderStatusCode"] );
					// �����å��ܥå������ˤ�ꡢ����򤽤Τޤ�����
					$arySearchStatus = $arySearchDataColumn["lngOrderStatusCode"];

					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND ( ";
						// ȯ����֤�ʣ�����ꤵ��Ƥ����ǽ��������Τǡ�����Ŀ�ʬ�롼��
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// ������
							if ( $j <> 0 )
							{
								$aryQuery[] = " OR ";
							}
							$aryQuery[] = "o.lngOrderStatusCode = " . $arySearchStatus[$j] . "";
						}
						$aryQuery[] = " ) ";
					}
				}
			}
/*
			// ����ե�����
			if ( $strSearchColumnName == "lngWorkFlowStatusCode" )
			{
				if ( $arySearchDataColumn["lngWorkFlowStatusCode"] )
				{
					// �����å��ܥå����ͤ�ꡢ����򤽤Τޤ�����
					$arySearchStatus = $arySearchDataColumn["lngWorkFlowStatusCode"];
					
					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND tw.lngworkflowstatuscode in ( ";

						// WF���֤�ʣ�����ꤵ��Ƥ����ǽ��������Τǡ�����Ŀ�ʬ�롼��
						$strBuff = "";
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// ������
							if ( $j <> 0 )
							{
								$strBuff .= " ,";
							}
							$strBuff .= "" . $arySearchStatus[$j] . "";
						}
						$aryQuery[] = "\t".$strBuff . " )";
					}
					
					$flgWorkFlowStatus = true;
				}
			}
*/
			// ��ʧ���
			if ( $strSearchColumnName == "lngPayConditionCode" )
			{
				if ( $arySearchDataColumn["lngPayConditionCode"] )
				{
					$aryQuery[] = " AND o.lngPayConditionCode = " . $arySearchDataColumn["lngPayConditionCode"] . "";
				}
			}
			// ȯ��ͭ��������
			if ( $strSearchColumnName == "dtmExpirationDate" )
			{
				if ( $arySearchDataColumn["dtmExpirationDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
					// $aryQuery[] = " AND o.dtmExpirationDate >= '" . $dtmSearchDate . "'";
					$aryQuery[] = " AND od.dtmExpirationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmExpirationDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
					// $aryQuery[] = " AND o.dtmExpirationDate <= '" . $dtmSearchDate . "'";
					$aryQuery[] = " AND od.dtmExpirationDate <= '" . $dtmSearchDate . "'";
				}
			}


			//
			// ���٥ơ��֥�ξ��
			//
			
			// ���ʥ�����
			if ( $strSearchColumnName == "strProductCode" )
			{
			    if ( ( $arySearchDataColumn["strProductCodeFrom"] && $arySearchDataColumn["strProductCodeTo"] )
			     && ( $arySearchDataColumn["strProductCodeFrom"] == $arySearchDataColumn["strProductCodeTo"] ) )
			    {
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.strProductCode = '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
					$detailFlag = TRUE;
			    }
			    else
			    {
					if ( $arySearchDataColumn["strProductCodeFrom"] )
					{
						$aryDetailWhereQuery[] = "AND ";
						$aryDetailWhereQuery[] = "od1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
						$detailFlag = TRUE;
					}
					if ( $arySearchDataColumn["strProductCodeTo"] )
					{
						$aryDetailWhereQuery[] = "AND ";
						$aryDetailWhereQuery[] = "od1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
						$detailFlag = TRUE;
					}
				}
			}
			// ����̾�Ρ����ܸ��
			if ( $strSearchColumnName == "strProductName" )
			{
				if ( $arySearchDataColumn["strProductName"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "UPPER( p.strProductName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}
			// ����̾�ΡʱѸ��
			if ( $strSearchColumnName == "strProductEnglishName" )
			{
				if ( $arySearchDataColumn["strProductEnglishName"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "UPPER( p.strProductEnglishName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductEnglishName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}

			// ��������
			if ( $strSearchColumnName == "lngStockSubjectCode" )
			{
				if ( $arySearchDataColumn["lngStockSubjectCode"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
					$detailFlag = TRUE;
					$StockSubjectFlag = TRUE;
				}
			}
			// ��������
			if ( $strSearchColumnName == "lngStockItemCode" )
			{
				if ( $arySearchDataColumn["lngStockItemCode"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.lngStockItemCode = " . $arySearchDataColumn["lngStockItemCode"] . " ";
					if ( $StockSubjectFlag != TRUE )
					{
						$aryDetailWhereQuery[] = "AND od1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
					}
					$detailFlag = TRUE;
				}
			}

			// Ǽ��
			if ( $strSearchColumnName == "dtmDeliveryDate" )
			{
				if ( $arySearchDataColumn["dtmDeliveryDateFrom"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}

	// ���ٹԤθ����б�
/*
	$aryDetailWhereQuery[] = ") as od on od.lngOrderNo = o.lngOrderNo and od.lngrevisionno = o.lngrevisionno";
	// where������ٹԡ� �����꡼Ϣ��
	$strDetailQuery = "\n";
//	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤθ����б�
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
*/

	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT o.lngOrderNo as lngOrderNo";
	$aryOutQuery[] = "	,o.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,o.strOrderCode as strOrderCode";
	$aryOutQuery[] = "	,o.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,o.lngOrderStatusCode as lngOrderStatusCode";

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,od.lngOrderDetailNo";

	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Order o";
	if ( !$strOrderCode )
	{
	    $aryFromQuery[] = " INNER JOIN (";
	    $aryFromQuery[] = " select strordercode, MAX(lngrevisionno) as lngrevisionno from m_order group by strordercode ) rev";
	    $aryFromQuery[] = "on rev.strordercode = o.strordercode and rev.lngrevisionno = o.lngrevisionno ";
	}
	// ���ٸ����ѥơ��֥�����
//	$aryDetailFrom = array();
	$aryFromQuery[] = "INNER JOIN  (SELECT od1.lngOrderNo";
	$aryFromQuery[] = "	,od1.lngOrderDetailNo";
	$aryFromQuery[] = "	,od1.lngRevisionNo";
	$aryFromQuery[] = "    ,mp.dtmexpirationdate";
	$aryFromQuery[] = "	FROM t_OrderDetail od1";
	$aryFromQuery[] = "	INNER JOIN (";
	$aryFromQuery[] = "	    select m_product.*";
	$aryFromQuery[] = "		from m_product";
	$aryFromQuery[] = "		inner join (";
	$aryFromQuery[] = "			select";
	$aryFromQuery[] = "				strProductCode";
	$aryFromQuery[] = "				,strrevisecode";
	$aryFromQuery[] = "				,MAX(lngrevisionno) as lngrevisionno";
	$aryFromQuery[] = "			from m_product";
	$aryFromQuery[] = "			group by strProductCode, strrevisecode";
	$aryFromQuery[] = "		) a";
	$aryFromQuery[] = "			on a.strProductCode = m_product.strproductcode";
	$aryFromQuery[] = "			and a.strrevisecode = m_product.strrevisecode";
	$aryFromQuery[] = "			and a.lngrevisionno = m_product.lngrevisionno";
	$aryFromQuery[] = "	) p ON od1.strProductCode = p.strProductCode and p.strrevisecode = od1.strrevisecode";

//	$aryFromQuery[] = "	LEFT JOIN m_Product p ON od1.strProductCode = p.strProductCode";
	$aryFromQuery[] = "	left join t_purchaseorderdetail tp on  od1.lngorderno = tp.lngorderno and od1.lngorderdetailno = tp.lngorderdetailno and od1.lngrevisionno = tp.lngrevisionno";
	$aryFromQuery[] = "	left join m_purchaseorder mp on  tp.lngpurchaseorderno = mp.lngpurchaseorderno and tp.lngrevisionno = mp.lngrevisionno";
	$aryFromQuery[] = "	where mp.lngpurchaseorderno not in (select lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0) ";
	$aryDetailWhereQuery[] = ") as od on od.lngOrderNo = o.lngOrderNo and od.lngrevisionno = o.lngrevisionno";
	// where������ٹԡ� �����꡼Ϣ��
	$aryFromQuery[]  = "\n";
//	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤθ����б�
	// if ( $detailFlag )
	// {
	// 	$aryFromQuery[] = implode("\n", $aryDetailTargetQuery) . "\n";
	// }
	$aryFromQuery[] = implode("\n", $aryDetailWhereQuery) . "\n";


	
	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	
	if ( $flgGroup )
	{
		$aryFromQuery[] = " LEFT JOIN m_group mg ON o.lnggroupcode = mg.lnggroupcode";
	}
	
	if ( $flgUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User mu ON o.lngUserCode = mu.lngUserCode";
	}
	if ( $flgOrderStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
	}
	if ( $flgPayCondition )
	{
		$aryFromQuery[] = " LEFT JOIN m_PayCondition pc ON o.lngPayConditionCode = pc.lngPayConditionCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mm ON o.lngMonetaryUnitCode = mm.lngMonetaryUnitCode";
	}
	// if ( $flgWorkFlowStatus )
	// {
	// 	$aryFromQuery[] = " left join
	// 	( m_workflow mw
	// 		left join t_workflow tw
	// 		on mw.lngworkflowcode = tw.lngworkflowcode
	// 		and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
	// 	) on  mw.strworkflowkeycode = trim(to_char(o.lngOrderNo, '9999999'))
	// 		and mw.lngfunctioncode = " . DEF_FUNCTION_PO1; // ȯ����Ͽ����WF�ǡ������оݤˤ���٤˾�����
		
	// 	$aryFromQuery[] = "
	// 	 AND o.bytInvalidFlag = FALSE AND o.lngRevisionNo >= 0
	// 	 AND o.lngRevisionNo = ( SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )
	// 	 AND o.strReviseCode = ( SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )
	// 	 AND 0 <= ( SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
		
	// }
	
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹԾ�郎���ä����� ���Ϣ��
//	$aryOutQuery[] = " AND od.lngOrderNo = o.lngOrderNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ�ȯ��ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��       ////
	//// Ʊ��ȯ�����ɤ���ĥǡ�����̵��ȯ��ǡ���          ////
	/////////////////////////////////////////////////////////////
	// ȯ�����ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strOrderCode )
	{
//		$aryOutQuery[] = " AND o.lngRevisionNo = ( "
//			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )";
		// $aryOutQuery[] = " AND o.strReviseCode = ( "
		// 	. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
            $aryOutQuery[] = " AND o.lngorderno not in (select lngorderno from m_Order where bytInvalidFlag and lngrevisionno < 0 )"; 

		}
	}
	// �����⡼�ɤθ�������Ʊ��ȯ�����ɤΥǡ��������������
	if ( $strOrderCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY o.dtmInsertDate DESC";
	}
	else
	{
		// �����Ⱦ������
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
			case "strOrderCode":
			case "lngOrderStatusCode":
			case "lngPayConditionCode":
			case "dtmExpirationDate":
			case "strNote":
			case "curTotalPrice":
				$aryOutQuery[] = " ORDER BY o." . $arySearchDataColumn["strSort"] . " " . $strAsDs . ", o.lngOrderNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmOrderAppDate" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngWorkFlowStatusCode":
				$aryOutQuery[] = " ORDER BY lngWorkFlowStatusCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngOrderDetailNo":	// ���ٹ��ֹ�
				$aryOutQuery[] = " ORDER BY od.lngOrderDetailNo" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strProductCode":		// ���ʥ�����
				$aryOutQuery[] = " ORDER BY od.strProductCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngGroupCode":		// ����
				$aryOutQuery[] = " ORDER BY od.strGroupDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngUserCode":			// ô����
				$aryOutQuery[] = " ORDER BY od.strUserDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strProductName":		// ����̾��
				$aryOutQuery[] = " ORDER BY od.strProductName" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strProductEnglishName":	// ���ʱѸ�̾��
				$aryOutQuery[] = " ORDER BY od.strProductEnglishName" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngStockSubjectCode":	// ��������
				$aryOutQuery[] = " ORDER BY od.lngStockSubjectCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngStockItemCode":	// ��������
				$aryOutQuery[] = " ORDER BY od.lngStockItemCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strMoldNo":			// �ⷿNo.
				$aryOutQuery[] = " ORDER BY od.strMoldNo" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strGoodsCode":		// �ܵ�����
				$aryOutQuery[] = " ORDER BY od.strGoodsCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngDeliveryMethodCode":// ������ˡ
				$aryOutQuery[] = " ORDER BY od.lngDeliveryMethodCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "dtmDeliveryDate":		// Ǽ��
				$aryOutQuery[] = " ORDER BY od.dtmDeliveryDate" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "curProductPrice":		// ñ��
				$aryOutQuery[] = " ORDER BY od.curProductPrice" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngProductUnitCode":	// ñ��
				$aryOutQuery[] = " ORDER BY od.lngProductUnitCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngProductQuantity":	// ����
				$aryOutQuery[] = " ORDER BY od.lngProductQuantity" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "curSubTotalPrice":	// ��ȴ���
				$aryOutQuery[] = " ORDER BY od.curSubTotalPrice" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strDetailNote":		// ��������
				$aryOutQuery[] = " ORDER BY od.strNote" . $strAsDs . ", lngOrderNo DESC";
				break;
			default:
				$aryOutQuery[] = " ORDER BY o.lngOrderNo DESC";
		}
	}
	return implode("\n", $aryOutQuery);
}

/**
 * �������ܤ�����פ���ǿ���ȯ���ǡ������������SQLʸ�κ����ؿ�
 *
 *	�������ܤ��� SQLʸ���������
 *
 *	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
 *	@param  Array 	$arySearchColumn 		�����оݥ����̾������
 *	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
 *	@param  Object	$objDB       			DB���֥�������
 *	@param	String	$strOrderCode			ȯ������	��������:������̽���	ȯ�����ɻ����:�����ѡ�Ʊ��ȯ�����ɤΰ�������
 *	@param	Integer	$lngOrderNo				ȯ��Σ�	0:������̽���	ȯ��Σ�����:�����ѡ�Ʊ��ȯ�����ɤȤ�������оݳ�ȯ��NO
 *	@param	Boolean	$bytAdminMode			ͭ���ʺ���ǡ����μ����ѥե饰	FALSE:������̽���	TRUE:�����ѡ�����ǡ�������
 *	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
 *	@access public
 */
function fncGetSearchPurcheseOrderSQL( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode ){
	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for($i = 0; $i < count($aryViewColumn); $i++){
		$strViewColumnName = $aryViewColumn[$i];

		// ��Ͽ��
		if($strViewColumnName == "dtmInsertDate"){
			$arySelectQuery[] = "  ,to_char(mp.dtminsertdate, 'YYYY/MM/DD') as dtmInsertDate";
		}

		// ���ϼ�
		if($strViewColumnName == "lngInputUserCode"){
			$arySelectQuery[] = "  ,input_user.struserdisplaycode AS lngInsertUserCode";
			$arySelectQuery[] = "  ,mp.strinsertusername AS strInsertUserName";
		}

		// ȯ��ͭ��������
		if($strViewColumnName == "dtmExpirationDate"){
			$arySelectQuery[] = "  ,to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
		}
		
		// ȯ��NO.
		if($strViewColumnName == "strOrderCode"){
			$arySelectQuery[] = "  ,mp.strordercode as strOrderCode";
		}

		// ����
		if($strViewColumnName == "strProductCode"){
			$arySelectQuery[] = "  ,mp.strproductcode as strProductCode";
			$arySelectQuery[] = "  ,mp.strproductname as strProductName";
			$arySelectQuery[] = "  ,mp.strproductenglishname as strProductEnglishName";
		}

		// �Ķ�����
		if($strViewColumnName == "lngInChargeGroupCode"){
			$arySelectQuery[] = "  ,mg.strgroupdisplaycode AS lngGroupCode";
			$arySelectQuery[] = "  ,mp.strgroupname as strGroupName";
		}

		// ��ȯô����
		if($strViewColumnName == "lngInChargeUserCode"){
			$arySelectQuery[] = "  ,mu.struserdisplaycode as lngUserCode";
			$arySelectQuery[] = "  ,mp.strusername as strUserName";
		}

		// ������
		if($strViewColumnName == "lngCustomerCode"){
			$arySelectQuery[] = "  ,mc_stock.strcompanydisplaycode as lngCustomerCode";
			$arySelectQuery[] = "  ,mp.strcustomername as strCustomerName";
		}

		// Ǽ�ʾ��
		if($strViewColumnName == "lngDeliveryPlaceCode"){
			$arySelectQuery[] = "  ,mp.strdeliveryplacename as strDeliveryPlaceName";
		}

		// �̲�
		if($strViewColumnName == "lngMonetaryunitCode" or $strViewColumnName == "curTotalPrice"){
			$arySelectQuery[] = "  ,mp.lngmonetaryunitcode as lngMonetaryUnitCode";
			$arySelectQuery[] = "  ,mp.strmonetaryunitsign as strMonetaryUnitSign";
		}

		// �̲ߥ졼��
		if($strViewColumnName == "lngMonetaryRateCode"){
			$arySelectQuery[] = "  ,mp.lngmonetaryratecode as lngMonetaryRateCode";
			$arySelectQuery[] = "  ,mp.strmonetaryratename as strMonetaryRateName";
		}

		// ��ʧ���
		if($strViewColumnName == "lngPayConditionCode"){
			$arySelectQuery[] = "  ,mp.lngpayconditioncode as lngPayConditionCode";
			$arySelectQuery[] = "  ,mp.strpayconditionname as strPayConditionName";
		}

		// ��׶��
		if($strViewColumnName == "curTotalPrice"){
			$arySelectQuery[] = "  ,mp.curtotalprice as curTotalPrice";
		}

		// ����
		if($strViewColumnName == "strNote"){
			$arySelectQuery[] = "  ,mp.strnote as strNote";
		}

		// �������
		if($strViewColumnName == "lngPrintCount"){
			$arySelectQuery[] = "  ,mp.lngprintcount as lngPrintCount";
		}
	}

	$aryQuery[] = "WHERE mp.lngpurchaseorderno >= 0";
	// �����ѥ��������ꤵ��Ƥ������Ƥ򸡺�����ʸ��������
	for($i = 0; $i < count($arySearchColumn); $i++){
		$strSearchColumnName = $arySearchColumn[$i];

		// ȯ���ޥ����θ������
		// ȯ����
		if($strSearchColumnName == "dtmInsertDate"){
			if($arySearchDataColumn["dtmInsertDateFrom"]){
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
				$aryQuery[] = "AND   mp.dtminsertdate >= '" . $dtmSearchDate . "'";
			}
			if($arySearchDataColumn["dtmInsertDataTo"]){
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
				$aryQuery[] = "AND   mp.dtminsertdate <= '" . $dtmSearchDate . "'";
			}
		}

		// ���ϼ�
		if($strSearchColumnName == "lngInputUserCode"){
			if($arySearchDataColumn["lngInputUserCode"]){
//				$aryQuery[] = "AND   mp.lnginsertusercode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'";
				$aryQuery[] = "AND   input_user.struserdisplaycode = '" . $arySearchDataColumn["lngInputUserCode"] . "'";
			}
//			if($arySearchDataColumn["strInputUserName"]){
//				$aryQuery[] = "AND   UPPER(mp.strinsertusername) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')";
//			}
		}

		// ȯ��ͭ������
		if($arySearchColumnName == "dtmExpirationDate"){
			if($arySearchDataColumn["dtmExpirationDateFrom"]){
				$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
				$aryQuery[] = "AND   mp.dtmexpirationdate >= '" . $dtmSearchDate . "'";
			}
			if($arySearchDataColumn["dtmExpirationDateTo"]){
				$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
				$aryQuery[] = "AND   mp.dtmexpirationdate <= '" . $dtmSearchDate . "'";
			}
		}

		// ȯ��NO.
		if($strSearchColumnName == "strOrderCode"){
			$aryQuery[] = "AND   mp.strordercode = '" . $arySearchDataColumn["strOrderCode"] . "'";
		}

		// ����
		if($strSearchColumnName == "strProductCode"){
			if($arySearchDataColumn["strProductCode"]){
				$aryQuery[] = "AND   mp.strProductCode = '" . $arySearchDataColumn["strProductCode"] . "'";
			}
//			if($arySearchDataColumn["strProductName"]){
//				$aryQuery[] = "AND   UPPER(mp.strproductname) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%')";
//			}
		}

		// �Ķ�����
		if($strSearchColumnName == "lngInChargeGroupCode"){
			if($arySearchDataColumn["lngInChargeGroupCode"]){
//				$aryQuery[] = "AND   mp.lnggroupcode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
				$aryQuery[] = "AND   mg.strgroupdisplaycode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
			}
//			if($arySearchDataColumn["strInChargeGroupName"]){
//				$aryQuery[] = "AND   UPPER(mp.strgroupname) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
//			}
		}

		// ��ȯô����
		if($strSearchColumnName == "lngInChargeUserCode"){
			if($arySearchDataColumn["lngInChargeUserCode"]){
//				$aryQuery[] = "AND   mp.lngusercode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
				$aryQuery[] = "AND   mu.struserdisplaycode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
			}
//			if($arySearchDataColumn["strInChargeUserName"]){
//				$aryQuery[] = "AND   UPPER(mp.strusername) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
//			}
		}

		// ������
		if($strSearchColumnName == "lngCustomerCode"){
			if($arySearchDataColumn["lngCustomerCode"]){
//				$aryQuery[] = "AND   mp.lngcustomercode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
				$aryQuery[] = "AND   mc_stock.strcompanydisplaycode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
			}
//			if($arySearchDataColumn["strCustomerName"]){
//				$aryQuery[] = "AND   UPPER(mp.strcustomername) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
//			}
		}

		// Ǽ�ʾ��
		if($strSearchColumnName == "lngDeliveryPlaceCode"){
			if($arySearchDataColumn["lngDeliveryPlaceCode"]){
//				$aryQuery[] = "AND   mp.lngdeliveryplacecode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
				$aryQuery[] = "AND   mc_delivary.strcompanydisplaycode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
			}
//			if($arySearchDataColumn["strDeliveryPlaceName"]){
//				$aryQuery[] = "AND   UPPER(mp.strdeliveryplacename) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//			}
		}

		// �̲�
		if($strSearchColumnName == "lngMonetaryunitCode"){
			$aryQuery[] = "AND   mp.lngmonetaryunitcode = " . $arySearchDataColumn["lngMonetaryunitCode"];
		}

		// �̲ߥ졼��
		if($strSearchColumnName == "lngMonetaryRateCode"){
			$aryQuery[] = "AND   mp.lngmonetaryratecode = " . $arySearchDataColumn["lngMonetaryRateCode"];
		}

		// ��ʧ���
		if($strSearchColumnName == "lngPayConditionCode"){
			$aryQuery[] = "AND   mp.lngpayconditioncode = " . $arySearchDataColumn["lngPayConditionCode"];
		}
	}

	// SQL����
	$aryOutQuery[] = "SELECT";
	$aryOutQuery[] = "   mp.lngpurchaseorderno as lngPurchaseOrderNo";
	$aryOutQuery[] = "  ,mp.lngrevisionno as lngRevisionNo";
	$aryOutQuery[] = "  ,mp.strrevisecode as strReviseCode";
	$aryOutQuery[] = implode("\n", $arySelectQuery);
	$aryOutQuery[] = "FROM m_purchaseorder mp";
	$aryOutQuery[] = "inner join m_user input_user on input_user.lngusercode = mp.lnginsertusercode";
	$aryOutQuery[] = "inner join m_group mg on mg.lnggroupcode = mp.lnggroupcode";
	$aryOutQuery[] = "inner join m_user mu on mu.lngusercode = mp.lngusercode";
	$aryOutQuery[] = "inner join m_company mc_stock on mc_stock.lngcompanycode = mp.lngcustomercode";
	$aryOutQuery[] = "inner join m_company mc_delivary on mc_delivary.lngcompanycode = mp.lngdeliveryplacecode";
	$aryOutQuery[] = implode("\n", $aryQuery);
	$aryOutQuery[] = "ORDER BY";
	$aryOutQuery[] = "   mp.lngpurchaseorderno";
	$aryOutQuery[] = "  ,mp.lngrevisionno DESC";
	$aryOutQuery[] = "";

	switch($arySearchDataColumn["strSort"]){

	}

	return implode("\n", $aryOutQuery);
}

/**
 * �б�����ȯ��NO�Υǡ������Ф������ٹԤ��������SQLʸ�κ����ؿ�
 *
 *	ȯ��NO�������٤�������� SQLʸ���������
 *
 *	@param  Array 	$aryDetailViewColumn 	ɽ���о����٥����̾������
 *	@param  String 	$lngOrderNo 			�о�ȯ��NO
 *	@param  Array 	$aryData 				POST�ǡ���������
 *	@param  Object	$objDB       			DB���֥�������
 *	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
 *	@access public
 */
function fncGetOrderToProductSQL ( $aryDetailViewColumn, $lngOrderNo, $lngRevisionNo, $aryData, $objDB )
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
			$arySelectQuery[] = ", od.strProductCode || '_' || od.strReviseCode  as strProductCode";
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

		// ��������
		if ( $strViewColumnName == "lngStockSubjectCode" )
		{
			$arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
			$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
			$flgStockSubject = TRUE;
		}

		// ��������
		if ( $strViewColumnName == "lngStockItemCode" )
		{
			$arySelectQuery[] = ", od.lngStockItemCode as lngStockItemCode";
			$flgStockItem = TRUE;
		}

		// �ⷿ�ֹ�
		if ( $strViewColumnName == "strMoldNo" )
		{
			$arySelectQuery[] = ", od.strMoldNo as strMoldNo";
		}

		// �ܵ�����
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
			$flgProductCode = TRUE;
		}

		// ������ˡ
		if ( $strViewColumnName == "lngDeliveryMethodCode" )
		{
			$arySelectQuery[] = ", od.lngDeliveryMethodCode as lngDeliveryMethodCode";
			$arySelectQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
			$flgDeliveryMethod = TRUE;
		}

		// Ǽ��
		if ( $strViewColumnName == "dtmDeliveryDate" )
		{
			$arySelectQuery[] = ", to_char( od.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
		}

		// ñ��
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
		}

		// ñ��
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", od.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}

		// ����
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( od.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}

		// ��ȴ���
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( od.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}
		// ��������
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", od.strNote as strDetailNote";
		}
	}

	// �������ʤΤ�ɽ���оݤ��ä����ϻ������ܤˤĤ��Ƥ�ǡ������������
	if ( $flgStockItem == TRUE and $flgStockSubject == FALSE )
	{
		$arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
		$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
		$flgStockSubject = TRUE;
	}

	// ���о�� �о�ȯ��NO�λ���
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " AND od.lngrevisionno = " . $lngRevisionNo ;

	// �����ɲ�

	// ////ȯ��ޥ�����θ������////
	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT od.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,od.lngOrderNo as lngOrderNo";
	$aryOutQuery[] = "	,od.lngRevisionNo as lngRevisionNo";

	// select�� �����꡼Ϣ��
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_OrderDetail od";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	$aryFromQuery[] = "   LEFT JOIN m_Product p on p.strProductCode = od.strProductCode and p.strReviseCode = od.strReviseCode and p.lngrevisionno = od.lngrevisionno";
	$aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
	$aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";

	if ( $flgStockSubject )
	{
		$aryFromQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
	}
	if ( $flgStockItem )
	{
	//		$aryOutQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)\n";
	}
	if ( $flgDeliveryMethod )
	{
		$aryFromQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
	}

	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// �����Ⱦ�����
	if ( $aryData["strSortOrder"] == "ASC" )
	{
		$strAsDs = " DESC";	// �إå����ܤȤϵս�ˤ���
	}
	else
	{
		$strAsDs = " ASC";	//�߽�
	}

	switch($aryData["strSort"])
	{
		case "strDetailNote":
			$aryOutQuery[] = " ORDER BY od.strNote" . $strAsDs . ", od.lngSortKey ASC";
			break;
		case "lngOrderDetailNo":
			$aryOutQuery[] = " ORDER BY od.lngSortKey" . $strAsDs;
			break;
		case "strProductName":
		case "strProductEnglishName":
		case "strGoodsCode":
			$aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", od.lngSortKey ASC";
			break;
		default:
			$aryOutQuery[] = " ORDER BY od.lngSortKey ASC";
	}

	return implode("\n", $aryOutQuery);
}

/**
 * �������ɽ���ؿ��ʥإå��ѡ�
 *
 *	������̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
 *	�إå��Ԥ�ɽ������
 *
 *	@param  Integer $lngColumnCount 		�Կ�
 *	@param  Array 	$aryHeadResult 			�إå��Ԥθ�����̤���Ǽ���줿����
 *	@param  Array 	$aryDetailResult 		���ٹԤθ�����̤���Ǽ���줿����
 *	@param  Array 	$aryDetailViewColumn 	����ɽ���оݥ����̾������
 *	@param  Array 	$aryHeadViewColumn 		�إå�ɽ���оݥ����̾������
 *	@param  Array 	$aryData 				�Уϣӣԥǡ�����
 *	@param	Array	$aryUserAuthority		�桼�����������Ф��븢�¤����ä�����
 *	@param  Object 	$objDB 					DB���֥�������
 *	@param  Object 	$objCache 				����å��奪�֥�������
 *	@param	Integer	$lngReviseTotalCount	ɽ���оݤ�ȯ��β���Х����ι�׿�
 *	@param	Integer	$lngReviseCount			ɽ���оݤ�ȯ���ɽ����ʺǿ�ȯ��ʤ飰��
 *	@param	Array	$aryNewResult			ɽ���оݤ�ȯ��κǿ���ȯ��ǡ���
 *	@access public
 */
function fncSetPurchaseHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, 
									$aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $aryNewResult )
{
	include_once('conf.inc');
	require_once (LIB_DEBUGFILE);
	for ( $i = 0; $i < count($aryDetailResult); $i++ )
	{
		$aryHtml[] =  "<tr>";
		$aryHtml[] =  "\t<td class=\"rownum\">" . ($lngColumnCount + $i) . "</td>";
		// ɽ���оݥ������������̤ν���
		for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
		{
			$strColumnName = $aryHeadViewColumn[$j];
			$TdData = "";

			// ɽ���оݤ��ܥ���ξ��
			if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" or $strColumnName == "Record" or $strColumnName == "btnAdmin" )
			{
				// �ܥ����ˤ���ѹ�

				// �ܺ�ɽ��
				if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
				{
					// ȯ��ǡ���������оݤξ�硢�ܺ�ɽ���ܥ���������Բ�
					if ( $aryHeadResult["lngrevisionno"] >= 0 )
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" lngrevisionno=\"" . $aryDetailResult[$i]["lngrevisionno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// ����
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֲ�ȯ��פξ�����ܥ���������Բ�
					if ( $aryHeadResult["lngorderstatuscode"] == 1)
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"fix button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// ����
				if ( $strColumnName == "Record" ){
					if ( $aryHeadResult["lngrevisionno"] > 0 ) {
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" strordercode=\"" . $aryHeadResult["strordercode"] . "\" class=\"record button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// ������
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
					// �ǿ�ȯ������ǡ����ξ��������Բ�
					if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
/*
					//��Х�����¸�ߤ��ʤ����
					if ( $lngReviseTotalCount == 1 )
					{
						// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֲ�ȯ��ס�����ѡפξ�����ܥ���������Բ�
						// �ǿ�ȯ������ǡ����ξ��������Բ�
						if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					//ʣ����Х�����¸�ߤ�����
					else
					{
						// �ǿ�ȯ��ξ��
						if ( $lngReviseCount == 0 )
						{
							// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
							// �ǿ�ȯ������ǡ����ξ��������Բ�
							if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td></td>\n";
							}
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
*/
				}

				// �����
				if ( $strColumnName == "btnAdmin" and $aryUserAuthority["Admin"] ){
					if( $aryHeadResult["lngRevisionno"] == -1 ) {
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"admin button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}

			}
			else if ($strColumnName != "") {
				// $TdData = "\t<td>";
				$TdData = "";
				$TdDataUse = true;
				$strText = "";
				// ��Ͽ��
				if ( $strColumnName == "dtmInsertDate" )
				{
					$TdData .= "\t<td class=\"td-dtminsertdate\">";
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) );
				}
				// �׾���
				else if ( $strColumnName == "dtmOrderAppDate" )
				{
					$TdData .= "\t<td class=\"td-dtmorderappdate\">";
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmorderappdate"] );
				}
				// ȯ��NO
				else if ( $strColumnName == "strOrderCode" )
				{
					$baseOrderCode = explode("_", $aryHeadResult["strordercode"])[0];
					$TdData .= "\t<td class=\"td-strordercode\" baseordercode=\"". $baseOrderCode . "\">";
					$TdData .= $aryHeadResult["strordercode"];
					// // �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
					// if ( $aryData["Admin"] )
					// {
					// 	$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					// }
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
					$TdData .= "\t<td class=\"td-strinputuserdisplaycode\">";
					$TdData .= $strText;
				}
				// ������
				else if ( $strColumnName == "lngCustomerCode" )
				{
					if ( $aryHeadResult["strcustomerdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$strText .= " " . $aryHeadResult["strcustomerdisplayname"];
					$TdData .= "\t<td class=\"td-strcustomerdisplaycode\">";
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
					$TdData .= "\t<td class=\"td-curtotalprice\">";
					$TdData .= $strText;
				}
				// ����
				else if ( $strColumnName == "lngOrderStatusCode" )
				{
					$TdData .= "\t<td class=\"td-strorderstatusname\">";
					$TdData .= $aryHeadResult["strorderstatusname"];
				}
				// ��ʧ���
				else if ( $strColumnName == "lngPayConditionCode" )
				{
					$TdData .= "\t<td class=\"td-strpayconditionname\">";
					$TdData .= $aryHeadResult["strpayconditionname"];
				}
				// ȯ��ͭ��������
				else if ( $strColumnName == "dtmExpirationDate" )
				{
					$TdData .= "\t<td class=\"td-dtmexpirationdate\">";
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmexpirationdate"] );
				}
				// ���ٹ��ֹ�
				else if ( $strColumnName == "lngRecordNo" )
				{
					$TdData .= "\t<td class=\"td-lngrecordno\">";
					$TdData .= $aryDetailResult[$i]["lngrecordno"];
				}
				// 2004.03.31 suzukaze update start
				// ���ʥ�����
				else if ( $strColumnName == "strProductCode" )
				{
					if ( $aryDetailResult[$i]["strproductcode"] )
					{
						$strText .= "[" . $aryDetailResult[$i]["strproductcode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$TdData .= "\t<td class=\"td-strproductcode\">";
					$TdData .= $strText;
				}
				// 2004.03.31 suzukaze update start
				// ��������
				else if ( $strColumnName == "lngStockSubjectCode" )
				{
					if ( $aryDetailResult[$i]["lngstocksubjectcode"] )
					{
						$strText .= "[" . $aryDetailResult[$i]["lngstocksubjectcode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$strText .= " " . $aryDetailResult[$i]["strstocksubjectname"];
//fncDebug("kids2.log", $strText , __FILE__, __LINE__, "a" );
					$TdData .= "\t<td class=\"td-lngstocksubjectcode\">";
					$TdData .= $strText;
				}
				// ��������
				else if ( $strColumnName == "lngStockItemCode" )
				{
					if ( $aryDetailResult[$i]["lngstockitemcode"] )
					{
						$strText .= "[" . $aryDetailResult[$i]["lngstockitemcode"] ."]";
						// �������ܥ����ɤ�¸�ߤ���ʤ��
						if ( $aryDetailResult[$i]["lngstocksubjectcode"] )
						{
							$strSubjectItem = $aryDetailResult[$i]["lngstocksubjectcode"] . ":" . $aryDetailResult[$i]["lngstockitemcode"];
							$aryStockItem = $objCache->GetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem);
							if( !is_array($aryStockItem) )
							{
								// ����̾�Τμ���
								$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname" , 
									$aryDetailResult[$i]["lngstockitemcode"], "lngstocksubjectcode = " . $aryDetailResult[$i]["lngstocksubjectcode"], $objDB );
								// ����̾�Τ�����
								$aryStockItem = $strStockItemName;
								$objCache->SetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem, $aryStockItem);
							}
							$strText .= " " . $aryStockItem;
						}
					}
					else
					{
						$strText .= "      ";
						$strText .= " " . $aryDetailResult[$i]["strstockitemname"];
					}
					$TdData .= "\t<td class=\"td-lngstockitemcode\">";
					$TdData .= $strText;
				}
				// ������ˡ
				else if ( $strColumnName == "lngDeliveryMethodCode" )
				{
					if ( $aryDetailResult[$i]["strdeliverymethodname"] == "" )
					{
						$aryDetailResult[$i]["strdeliverymethodname"] = "̤��";
					}
					$strText .= $aryDetailResult[$i]["strdeliverymethodname"];
					$TdData .= "\t<td class=\"td-strdeliverymethodname\">";
					$TdData .= $strText;
				}
				// 2004.04.21 suzukaze update start
				// Ǽ��
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
					$TdData .= "\t<td class=\"td-dtmdeliverydate\">";
					$TdData .= str_replace( "-", "/", $aryDetailResult[$i]["dtmdeliverydate"] );
				}
				// 2004.04.21 suzukaze update end
				// ñ��
				else if ( $strColumnName == "curProductPrice" )
				{
					$TdDataUse = false;
					$strText = "\t<td align=\"right\">";
					$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
					if ( !$aryDetailResult[$i]["curproductprice"] )
					{
						$strText .= "0.00";
					}
					else
					{
						$strText .= $aryDetailResult[$i]["curproductprice"];
					}
					$aryHtml[] = $strText . "</td>\n";
				}
				// ñ��
				else if ( $strColumnName == "lngProductUnitCode" )
				{
					$TdData .= "\t<td class=\"td-strproductunitname\">";
					$TdData .= $aryDetailResult[$i]["strproductunitname"];
				}
				// ����
				else if ( $strColumnName == "lngProductQuantity" )
				{
					$TdDataUse = false;
					$aryHtml[] = "\t<td align=\"right\">" . $aryDetailResult[$i]["lngproductquantity"] .  "</td>\n";
				}
				// ��ȴ���
				else if ( $strColumnName == "curSubTotalPrice" )
				{
					$TdDataUse = false;
					$strText = "\t<td align=\"right\">";
					$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
					if ( !$aryDetailResult[$i]["cursubtotalprice"] )
					{
						$strText .= "0.00";
					}
					else
					{
						$strText .= $aryDetailResult[$i]["cursubtotalprice"];
					}
					$aryHtml[] = $strText . "</td>\n";
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
					$TdData .= "\t<td>";
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
 * ȯ���ǡ���HTML�Ѵ�
 * 
 * @param	Array	$aryViewColumn		��������
 * @param	Array	$aryResult			ȯ���ǡ���
 * @param	Array	$aryUserAuthority	����
 * @access	public
 * 
 */
function fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority){
	for($i = 0; $i < count($aryResult); $i++){
		$aryHtml[] = "<tr>";
		$aryHtml[] = "  <td class=\"rownum\">" . ($i + 1) . "</td>";
		for($j = 0; $j < count($aryViewColumn); $j++){
			$strColumn = $aryViewColumn[$j];
			// ɽ���оݤ��ܥ���ξ��
			if($strColumn == "btnEdit" or $strColumn == "btnRecord" or $strColumn == "btnDelete") {
				// �����ܥ���
				if($strColumn == "btnEdit" and $aryUserAuthority["Edit"]){
					// ȯ���ǡ���������Ѥߤξ�硢�����ܥ������ɽ��
					if($aryResult[$i]["lngrevisionno"] == -1){
						$aryHtml[] = "  <td></td>";
					} else {
						$aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" lngrevisionno=\"" . $aryResult[$i]["lngrevisionno"] . "\" class=\"edit button\"></td>";
					}
				}
				// ����ܥ���
				if($strColumn == "btnRecord"){
					// ��ӥ����0�ξ�硢����ܥ������ɽ��
					if($aryResult[$i]["lngrevisionno"] == 1) {
						$aryHtml[] = "  <td></td>";
					} else {
						$strOrderCode = sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]);
						$aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" strOrderCode=\"" . $strOrderCode . "\" class=\"record button\"></td>";
					}
				}
				// ����ѥܥ���
				if($strColumn == "btnDelete" and $aryUserAuthority["Admin"]) {
					// ����ѤߤΤ�ɽ��
					if($aryResult[$i]["lngrevisionno"] == -1){
						$aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" class=\"record button\"></td>";
					} else {
						$aryHtml[] = "  <td></td>";
					}
				}
			} else {
				// ȯ��NO.
				if($strColumn == "strOrderCode"){
					$aryHtml[] = "  <td class=\"td-strordercode\" baseordercode=\"" . $aryResult[$i]["strordercode"] . "\">" . sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]) . "</td>";
				}
				// ȯ��ͭ��������
				if($strColumn == "dtmExpirationDate"){
					$aryHtml[] = "  <td class=\"td-dtmexpirationdate\">" . $aryResult[$i]["dtmexpirationdate"] . "</td>";
				}
				// ���ʥ�����
				if($strColumn == "strProductCode"){
					$aryHtml[] = "  <td class=\"td-strproductcode\">" . sprintf("[%s]", $aryResult[$i]["strproductcode"]) . "</td>";
				}
				// ��Ͽ��
				if($strColumn == "dtmInsertDate"){
					$aryHtml[] = "  <td class=\"td-dtminsertdate\">" . $aryResult[$i]["dtminsertdate"] . "</td>";
				}
				// ���ϼ�
				if($strColumn == "lngInputUserCode"){
					$aryHtml[] = "  <td class=\"td-lnginsertusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lnginsertusercode"], $aryResult[$i]["strinsertusername"]) . "</td>";
				}
				// ����̾
				if($strColumn == "strProductName"){
					$aryHtml[] = "  <td class=\"td-strproductname\">" . $aryResult[$i]["strproductname"] . "</td>";
				}
				// ����̾(�Ѹ�)
				if($strColumn == "strProductEnglishName"){
					$aryHtml[] = "  <td class=\"td-strproductenglishname\">" . $aryResult[$i]["strproductenglishname"] . "</td>";
				}
				// �Ķ�����
				if($strColumn == "lngInChargeGroupCode"){
					$aryHtml[] = "  <td class=\"td-lnggroupcode\">" . sprintf("[%s] %s", $aryResult[$i]["lnggroupcode"], $aryResult[$i]["strgroupname"]) . "</td>";
				}
				// ��ȯô����
				if($strColumn == "lngInChargeUserCode"){
					$aryHtml[] = "  <td class=\"td-lngusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lngusercode"], $aryResult[$i]["strusername"]) . "</td>";
				}
				// ������
				if($strColumn == "lngCustomerCode"){
					$aryHtml[] = "  <td class=\"td-lngcustomercode\">" .sprintf("[%s] %s", $aryResult[$i]["lngcustomercode"], $aryResult[$i]["strcustomername"]) . "</td>";
				}
				// ��ʧ���
				if($strColumn == "lngPayConditionCode"){
					$aryHtml[] = "  <td class=\"td-strpaycnoditionname\">" . $aryResult[$i]["strpaycnoditionname"] . "</td>";
				}
				// ��ȴ���
				if($strColumn == "curTotalPrice"){
					$aryHtml[] = "  <td class=\"td-curtotalprice\">" . sprintf("%s %.2f", $aryResult[$i]["strmonetaryunitsign"], $aryResult[$i]["curtotalprice"]) . "</td>";
				}
				// Ǽ�ʾ��
				if($strColumn == "lngDeliveryPlaceCode"){
					$aryHtml[] = "  <td class=\"td-strdeliveryplacename\">" . $aryResult[$i]["strdeliveryplacename"] . "</td>";
				}
				// ��������
				if($strColumn == "strNote"){
					$aryHtml[] = "  <td class=\"td-strnote\">" . $aryResult[$i]["strnote"] . "</td>";
				}
			}
		}
		$aryHtml[] = "</tr>";
	}

	return implode("\n", $aryHtml);
}

/**
 * �������ɽ���ؿ�
 *
 *	������̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
 *
 *	@param  Array 	$aryResult 			������̤���Ǽ���줿����
 *	@param  Array 	$aryViewColumn 		ɽ���оݥ����̾������
 *	@param  Array 	$aryData 			�Уϣӣԥǡ�����
 *	@param	Array	$aryUserAuthority	�桼�����������Ф��븢�¤����ä�����
 *	@param	Array	$aryTytle			����̾����Ǽ���줿����ʸƤӽФ��������ܸ��ѡ��Ѹ��Ѥ��ڤ��ؤ���
 *	@param  Object	$objDB       		DB���֥�������
 *	@param  Object	$objCache       	����å��奪�֥�������
 *	@param	Array	$aryTableName		ɽ�������̾�ȥޥ����⥫���̾�ѹ���
 *	@access public
 */
function fncSetPurchaseTable ( $aryResult, $arySearchColumn, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
{
	// ����
	include_once('conf.inc');
	require_once (LIB_DEBUGFILE);

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
		else if( $strColumnName == "btnAdmin")
		{
			if ( $aryUserAuthority["Admin"]){
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		// 2004.03.31 suzukaze update start
		// �ܺ���
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode"
			or $strColumnName == "strGoodsCode" or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice"
			or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice"
			or $strColumnName == "strDetailNote" or $strColumnName == "dtmDeliveryDate" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" or $strColumnName == "strMoldNo" )
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
			or ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
			or ( $strColumnName == "btnAdmin" and $aryUserAuthority["Admin"] ) ) 
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
		// �����⡼�ɤξ�硡Ʊ��ȯ�����ɤΰ����������ɽ������

		// ��Х���������̵����ȯ�����ɤ��������
		if ( strlen($aryResult[$i]["strordercode"]) >= 9)
		{
			$strOrderCodeBase = preg_replace( "/" . strstr( $aryResult[$i]["strordercode"] . "/", "_" ), "", $aryResult[$i]["strordercode"] );
		}
		else
		{
			$strOrderCodeBase = $aryResult[$i]["strordercode"];
		}

//		$strSameOrderCodeQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strOrderCodeBase, $aryResult[$i]["lngorderno"], FALSE ,$aryResult[$i]["lngrevisionno"]);
//		// �ͤ�Ȥ� =====================================
//		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameOrderCodeQuery, $objDB );

		// ����Υ��ꥢ
//		unset( $arySameOrderCodeResult );

//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngResultNum; $j++ )
//			{
//				$arySameOrderCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
//			}
//			$lngSameOrderCount = $lngResultNum;
//		}
//		$objDB->freeResult( $lngResultID );

		// Ʊ��ȯ�����ɤǤβ���Х����ǡ�����¸�ߤ����
//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngSameOrderCount; $j++ )
//			{
				// ���������ʬ������

//				reset( $arySameOrderCodeResult[$j] );

				// ���ٽ����Ѥ�Ĵ��
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// ���ٹԿ���Ĵ��
					$strDetailQuery = fncGetOrderToProductSQL ( $aryDetailViewColumn, $aryResult[$i]["lngorderno"], $aryResult[$i]["lngrevisionno"], $aryData, $objDB );
//("kids2.log", $strDetailQuery , __FILE__, __LINE__, "a" );
					// �����꡼�¹�
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

				// ���쥳����ʬ�ν���
				$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $aryResult[$i]["lngrevisionno"], $aryResult[$i] );
//				$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $arySameOrderCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $j, $arySameOrderCodeResult[0] );
				$lngColumnCount = $lngColumnCount + count($aryDetailResult);
				
				$strColBuff = '';
				for ( $k = 0; $k < count($aryHtml_add); $k++ )
				{
					$strColBuff .= $aryHtml_add[$k];
				}
				$aryHtml[] =$strColBuff;
//			}
//		}

	// �����⡼���Ѳ���Х����ǡ�������end==================================

	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

/**
 * ȯ��ǡ���HTML�Ѵ�
 * 
 * @param	Array	$aryResult			ȯ��ǡ���
 * @param	Array	$aryViewColumn		ɽ����
 * @param	Array	$aryUserAuthority	����
 * @param	Array	$aryTitle			��̾
 * @param	Object	$objDB				DB���֥�������
 * @param	Object	$objCache			����å��奪�֥�������
 * @param	Array	$aryTableName		�ơ��֥�̾
 */
function fncSetPurchaseOrderTable( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName ){
	// ɽ�������Υإå�������������ʬΥ����
	for($i = 0; $i < count($aryViewColumn); $i++){
		$strColumnName = $aryViewColumn[$i];

		// �ܥ���ξ�礳����ɽ������ɽ���ڤ��ؤ�
		if($strColumnName == "btnEdit"){
			if($aryUserAuthority["Edit"]){
				$aryHeadViewColumn[] = $strColumnName;
			}
		} else if($strColumnName == "btnRecord"){
			$aryHeadViewColumn[] = $strColumnName;
		} else if($strColumnName == "btnDelete"){
			if($aryUserAuthority["Admin"]){
				$aryHeadViewColumn[] = $strColumnName;
			}
		} else if($strColumnName == "dtmInsertDate"
				or $strColumnName == "lngInputUserCode"
				or $strColumnName == "dtmExpirationDate"
				or $strColumnName == "strOrderCode"
				or $strColumnName == "strProductCode"
				or $strColumnName == "strProductName"
				or $strColumnName == "strProductEnglishName"
				or $strColumnName == "lngInChargeGroupCode"
				or $strColumnName == "lngInChargeUserCode"
				or $strColumnName == "lngCustomerCode"
				or $strColumnName == "strDeliveryPlaceName"
				or $strColumnName == "lngMonetaryunitCode"
				or $strColumnName == "lngMonetaryRateCode"
				or $strColumnName == "lngPayConditionCode")
		{
			$aryDetailViewColumn[] = $strColumnName;
			$aryHeadViewColumn[] = $strColumnName;
		} else {
			$aryHeadViewColumn[] = $strColumnName;
		}
	}

	// �ơ��֥�η���
	$lngColumnCount = 1;

	// ����̾�������
	$aryHtml[] = "<thead>";
	$aryHtml[] = "<tr>";
	$aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

	// ɽ���оݥ������������������
	for($i = 0; $i < count($aryViewColumn); $i++){
		$addTh = "\t<th>";
		$strColumnName = $aryViewColumn[$i];

		if($strColumnName == "btnPreview" or $strColumnName == "btnEdit" or $strColumnName == "btnRecord" or $strColumnName == "btnDelete"){
			// �����ȹ��ܰʳ��ξ��
			if(($strColumnName == "btnPreview" and $aryUserAuthority["Preview"])
				or ($strColumnName == "btnEdit" and $aryUserAuthority["Edit"])
				or ($strColumnName == "btnRecord")
				or ($strColumnName == "btnDelete" and $aryUserAuthority["Admin"])
			){
				$addTh .= $aryTitle[$strColumnName];
			} else {
				// ɽ���оݳ�
				continue;
			}
		} else {
			// �����ȹ��ܤξ��
			$addTh .= $aryTitle[$strColumnName];
		}

		$addTh .= "</th>";
		$aryHtml[] = $addTh;
	}
	$aryHtml[] = "</tr>";
	$aryHtml[] = "</thead>";

	// �ǡ�����
	$aryHtml[] = "<tbody>";
	$lngResultCount = count($aryResult);

	$aryHtml[] = fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority);
	$aryHtml[] = "</tbody>";
	$strHtml = implode("\n", $aryHtml);

	return $strHtml;
}

/**
 * ȯ���ǡ���HTML�Ѵ�
 * 
 * @param	Array	$aryResult			ȯ���ǡ���
 * @param	Array	$aryViewColumn		ɽ����
 * @param	Array	$aryUserAuthority	����
 * @param	Array	$aryTitle			��̾
 * @param	Object	$objDB				DB���֥�������
 * @param	Object	$objCache			����å��奪�֥�������
 * @param	Array	$aryTableName		�ơ��֥�̾
 */

function fncSetPurchaseOrderTable2( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName ){
	for($i = 0; $i < count($aryDetailResult); $i++){
		$aryHtml[] = "<tr>";
		$aryHtml[] = "\t<td>" . ($lngColumnCount + 1) . "</td>";

		// ɽ���оݥ������������̤ν���
		for($j = 0; $j < count($aryHeadViewColumn); $j++){
			$strColumnName = $aryHeadViewColumn[$j];
			$tdData = "";

			// ɽ���оݤ��ܥ���ξ��
			if($strColumnName == "btnEdit" or $strColumnName == "btnRecord" or $strColumnName == "btnDelete"){
				// �����ܥ���
				if($strColumnName == "btnEdit" and $aryUserAuthority["Edit"]){
					$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" class=\"detail button\"></td>\n";
				} else {
					$aryHtml[] = "\t<td></td>\n";
				}

				// ����ܥ���
				if($strColumnName == "btnRecord"){
					if($aryHeadResult["lngRevisionNo"] > 0){
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" strOrderCode =\"" . $aryResult["strordercode"] . "\" class=\"fix button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// ����ѥܥ���
				if($strColumnName == "btnDelete" and $aryUserAuthority["Admin"]){
					if($aryHeadResult["lngRevisionNo"] == -1){
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" class=\"fix button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}
			}
			$tdData .= "</td>\n";
			//if($tdDataUse){
				$aryHtml[] = $tdData;
			//}
		}
		$aryHtml[] = "</tr>";
	}
	return $aryHtml;
}

function fncResortSearchColumn($aryViewColumn){
	$aryResult = array();

	$aryResult[] = "btnDetail";
	$aryResult[] = "btnFix";
	$aryResult[] = "Record";
	if(in_array("btnAdmin",              $aryViewColumn)){ $aryResult[] = "btnAdmin"; }
	if(in_array("strOrderCode",          $aryViewColumn)){ $aryResult[] = "strOrderCode"; }
	if(in_array("dtmExpirationDate",     $aryViewColumn)){ $aryResult[] = "dtmExpirationDate"; }
	if(in_array("strProductCode",        $aryViewColumn)){ $aryResult[] = "strProductCode"; }
	if(in_array("dtmInsertDate",         $aryViewColumn)){ $aryResult[] = "dtmInsertDate"; }
	if(in_array("lngInputUserCode",      $aryViewColumn)){ $aryResult[] = "lngInputUserCode"; }
	if(in_array("strProductName",        $aryViewColumn)){
		$aryResult[] = "strProductName";
		$aryResult[] = "strProductEnglishName";
	}
	if(in_array("lngInChargeGroupCode",  $aryViewColumn)){ $aryResult[] = "lngInChargeGroupCode"; }
	if(in_array("lngInChargeUserCode",   $aryViewColumn)){ $aryResult[] = "lngInChargeUserCode"; }
	if(in_array("lngCustomerCode",       $aryViewColumn)){ $aryResult[] = "lngCustomerCode"; }
	if(in_array("lngStockSubjectCode",   $aryViewColumn)){ $aryResult[] = "lngStockSubjectCode"; }
	if(in_array("lngStockItemCode",      $aryViewColumn)){ $aryResult[] = "lngStockItemCode"; }
	if(in_array("dtmDeliveryDate",       $aryViewColumn)){ $aryResult[] = "dtmDeliveryDate"; }
	if(in_array("lngOrderStatusCode",    $aryViewColumn)){ $aryResult[] = "lngOrderStatusCode"; }
	if(in_array("lngRecordNo",           $aryViewColumn)){ $aryResult[] = "lngRecordNo"; }
	if(in_array("curProductPrice",       $aryViewColumn)){ $aryResult[] = "curProductPrice"; }
	if(in_array("lngProductQuantity",    $aryViewColumn)){ $aryResult[] = "lngProductQuantity"; }
	if(in_array("curSubTotalPrice",      $aryViewColumn)){ $aryResult[] = "curSubTotalPrice"; }
	if(in_array("strNote",               $aryViewColumn)){ $aryResult[] = "strNote"; }
	if(in_array("strDetailNote",         $aryViewColumn)){ $aryResult[] = "strDetailNote"; }
	if(in_array("btnDelete",             $aryViewColumn)){ $aryResult[] = "btnDelete"; }

	return $aryResult;
}

function fncResortSearchColumn2($aryViewColumn){
	$aryResult = array();

	if(in_array("btnPreview",            $aryViewColumn)){ $aryResult[] = "btnPreview"; }
	$aryResult[] = "btnEdit";
	$aryResult[] = "btnRecord";
	$aryResult[] = "btnDelete";
	if(in_array("strOrderCode",          $aryViewColumn)){ $aryResult[] = "strOrderCode"; }
	if(in_array("dtmExpirationDate",     $aryViewColumn)){ $aryResult[] = "dtmExpirationDate"; }
	if(in_array("strProductCode",        $aryViewColumn)){ $aryResult[] = "strProductCode";	}
	if(in_array("dtmInsertDate",         $aryViewColumn)){ $aryResult[] = "dtmInsertDate"; }
	if(in_array("lngInputUserCode",      $aryViewColumn)){ $aryResult[] = "lngInputUserCode"; }
	if(in_array("strProductCode",        $aryViewColumn)){
		$aryResult[] = "strProductName";
		$aryResult[] = "strProductEnglishName";
	}
	if(in_array("lngInChargeGroupCode",  $aryViewColumn)){ $aryResult[] = "lngInChargeGroupCode"; }
	if(in_array("lngInChargeUserCode",   $aryViewColumn)){ $aryResult[] = "lngInChargeUserCode"; }
	if(in_array("lngCustomerCode",       $aryViewColumn)){ $aryResult[] = "lngCustomerCode"; }
	if(in_array("lngPayConditionCode",   $aryViewColumn)){ $aryResult[] = "lngPayConditionCode"; }
	if(in_array("curTotalPrice",         $aryViewColumn)){ $aryResult[] = "curTotalPrice"; }
	if(in_array("lngDeliveryPlaceCode",  $aryViewColumn)){ $aryResult[] = "lngDeliveryPlaceCode"; }
	if(in_array("strNote",               $aryViewColumn)){ $aryResult[] = "strNote"; }

	return $aryResult;
}

?>