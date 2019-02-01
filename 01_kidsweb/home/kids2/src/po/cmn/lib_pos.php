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
function fncGetSearchPurchaseSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode )
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
			$arySelectQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
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
			$arySelectQuery[] = ", to_char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
		}

		// ����
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", o.strNote as strNote";
		}

		// ��׶��
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( o.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
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
						$strNewOrderCode = ereg_replace( strstr( $arySearchDataColumn["strOrderCodeFrom"], "-" ), "", $arySearchDataColumn["strOrderCodeFrom"] );
					}
					else
					{
						$strNewOrderCode = $arySearchDataColumn["strOrderCodeFrom"];
					}
					$aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode . "'";

				}
				if ( $arySearchDataColumn["strOrderCodeTo"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeTo"], "-") )
					{
						// ��Х����������դ�ȯ��Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
						$strNewOrderCode = ereg_replace( strstr( $arySearchDataColumn["strOrderCodeTo"], "-" ), "", $arySearchDataColumn["strOrderCodeTo"] );
					}
					else
					{
						$strNewOrderCode = $arySearchDataColumn["strOrderCodeTo"];
					}
					$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode . "'";
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
					$aryQuery[] = " AND o.dtmExpirationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmExpirationDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
					$aryQuery[] = " AND o.dtmExpirationDate <= '" . $dtmSearchDate . "'";
				}
			}


			//
			// ���٥ơ��֥�ξ��
			//
			
			// ���ʥ�����
			if ( $strSearchColumnName == "strProductCode" )
			{
				if ( $arySearchDataColumn["strProductCodeFrom"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["strProductCodeTo"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
			
			// ����
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if( $arySearchDataColumn["lngInChargeGroupCode"] || $arySearchDataColumn["strInChargeGroupName"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
				}

				if ( $arySearchDataColumn["lngInChargeGroupCode"] )
				{
					$aryDetailWhereQuery[] = " mg.strGroupDisplayCode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeGroupName"] )
				{
					if( $arySearchDataColumn["lngInChargeGroupCode"] )
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = " UPPER(mg.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
					$detailFlag = TRUE;
				}
			}
			// ô����
			if ( $strSearchColumnName == "lngInChargeUserCode" )
			{
				if( $arySearchDataColumn["lngInChargeUserCode"] || $arySearchDataColumn["strInChargeUserName"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
				}

				if ( $arySearchDataColumn["lngInChargeUserCode"] )
				{
					$aryDetailWhereQuery[] = " mu.strUserDisplayCode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeUserName"] )
				{
					if( $arySearchDataColumn["lngInChargeUserCode"] )
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = " UPPER(mu.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
					$detailFlag = TRUE;
				}
			}
			// ����̾�Ρ����ܸ��
			if ( $strSearchColumnName == "strProductName" )
			{
				if ( $arySearchDataColumn["strProductName"] )
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
					$aryDetailWhereQuery[] = "UPPER( p.strProductName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}
			// ����̾�ΡʱѸ��
			if ( $strSearchColumnName == "strProductEnglishName" )
			{
				if ( $arySearchDataColumn["strProductEnglishName"] )
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
					$aryDetailWhereQuery[] = "UPPER( p.strProductEnglishName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductEnglishName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}

			// ��������
			if ( $strSearchColumnName == "lngStockSubjectCode" )
			{
				if ( $arySearchDataColumn["lngStockSubjectCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
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
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
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
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}

	// ���ٹԤθ����б�

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( od1.lngOrderNo ) od1.lngOrderNo";
	$aryDetailFrom[] = "	,od1.lngOrderDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,od1.lngStockSubjectCode";	// ��������
	$aryDetailFrom[] = "	,od1.lngStockItemCode";		// ��������
	$aryDetailFrom[] = "	,od1.strMoldNo";			// �ⷿNo.
	$aryDetailFrom[] = "	,p.strGoodsCode";			// �ܵ�����
	$aryDetailFrom[] = "	,od1.lngDeliveryMethodCode";// ������ˡ
	$aryDetailFrom[] = "	,od1.dtmDeliveryDate";		// Ǽ��
	$aryDetailFrom[] = "	,od1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,od1.lngProductUnitCode";	// ñ��
	$aryDetailFrom[] = "	,od1.lngProductQuantity";	// ����
	$aryDetailFrom[] = "	,od1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,od1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_OrderDetail od1";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON od1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = od1.lngtaxcode";

	$aryDetailWhereQuery[] = ") as od";
	// where������ٹԡ� �����꡼Ϣ��
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤθ����б�
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";


	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT o.lngOrderNo as lngOrderNo";
	$aryOutQuery[] = "	,o.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,o.strOrderCode as strOrderCode";
	$aryOutQuery[] = "	,o.lngOrderStatusCode as lngOrderStatusCode";

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,od.lngOrderDetailNo";


	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Order o";
	
	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
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
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(o.lngOrderNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_PO1; // ȯ����Ͽ����WF�ǡ������оݤˤ���٤˾�����
		
		$aryFromQuery[] = "
		 AND o.bytInvalidFlag = FALSE AND o.lngRevisionNo >= 0
		 AND o.lngRevisionNo = ( SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )
		 AND o.strReviseCode = ( SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )
		 AND 0 <= ( SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
		
	}
	
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹԾ�郎���ä����� ���Ϣ��
	$aryOutQuery[] = " AND od.lngOrderNo = o.lngOrderNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ�ȯ��ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��       ////
	//// Ʊ��ȯ�����ɤ���ĥǡ�����̵��ȯ��ǡ���          ////
	/////////////////////////////////////////////////////////////
	// ȯ�����ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strOrderCode )
	{
		$aryOutQuery[] = " AND o.lngRevisionNo = ( "
			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )";
		$aryOutQuery[] = " AND o.strReviseCode = ( "
			. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
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
	
//fncDebug( 'lib_pos.txt', implode("\n", $aryOutQuery), __FILE__, __LINE__);
//fncDebug( 'lib_pos.txt', $arySearchDataColumn["strSort"], __FILE__, __LINE__);

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
function fncGetOrderToProductSQL ( $aryDetailViewColumn, $lngOrderNo, $aryData, $objDB )
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
			$arySelectQuery[] = ", od.strProductCode as strProductCode";
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
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . "";

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
	$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
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
* �������ɽ���ؿ������ٹ��ѡ�
*
*	������̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
*	���ٹԤ�ɽ������
*
*	@param  Array 	$aryHeadResult 			�إå��Ԥθ�����̤���Ǽ���줿����
*	@param  Array 	$aryDetailResult 		���ٹԤθ�����̤���Ǽ���줿����
*	@param  Array 	$aryDetailViewColumn 	ɽ���оݥ����̾������
*	@param  Array 	$aryData 				�Уϣӣԥǡ�����
*	@param	Integer	$lngMode				���ϥ⡼�ɡ�0: �����ܤ�ɽ��		����ʳ�: �����ܰʹߤ�ɽ��
*	@param	Integer	$lngColumnCount			ɽ���Կ�
*	@param	Object	$objDB					DB���֥�������
*	@param	Object	$objCache				����å��奪�֥�������
*	@access public
*/
function fncSetPurchaseDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, $lngMode, $lngColumnCount, $objDB, $objCache )
{
	// ���ٹԿ�
	$lngDetailCount = count($aryDetailResult);

	// �⡼������
	if ( $lngMode == 0 )
	{
		$lngStart = 0;
		$lngEnd = 1;
	}
	else
	{
		$lngStart = 1;
		$lngEnd = $lngDetailCount;
	}

	// $aryDetailResult[] ������پ���ν��ϡʣ����ܰʹߡ�
	for ( $x = $lngStart; $x < $lngEnd; $x++ )
	{
//		reset( $aryDetailResult[$x] );

		if ( $lngMode )
		{
			$lngColumnCountMinus = $lngColumnCount - 1;
			if ( $lngDetailCount == "" )
			{
				$lngDetailCount = 0;
			}
			$aryHtml[] = "<tr id=\"TD" . $lngColumnCountMinus . "_" . $x . "\" class=\"Segs\" name=\"strTrName" . $lngColumnCountMinus . "\" onclick=\"fncSelectSomeTrColor( this,  'TD" . $lngColumnCountMinus . "_', " . $lngDetailCount . " );\" style=\"background:#FFB2B2;\">";
		}

		// ɽ���оݥ������������̤ν���
		for ( $y = 0; $y < count($aryDetailViewColumn); $y++ )
		{
			$strDetailColumnName = $aryDetailViewColumn[$y];

			// ���ٹ��ֹ�
			if ( $strDetailColumnName == "lngRecordNo" )
			{
				$aryHtml[] = "<td align=\"center\" nowrap>" . $aryDetailResult[$x]["lngrecordno"] . "</td>";
			}

// 2004.03.31 suzukaze update start
			// ���ʥ�����
			else if ( $strDetailColumnName == "strProductCode" )
			{
				$strText = "<td align=\"center\" nowrap>";
				if ( $aryDetailResult[$x]["strproductcode"] )
				{
					$strText .= "[" . $aryDetailResult[$x]["strproductcode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= "</td>";
				$aryHtml[] = $strText;
			}
// 2004.03.31 suzukaze update start

			// ��������
			else if ( $strDetailColumnName == "lngStockSubjectCode" )
			{
				$strText = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["lngstocksubjectcode"] )
				{
					$strText .= "[" . $aryDetailResult[$x]["lngstocksubjectcode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= " " . $aryDetailResult[$x]["strstocksubjectname"] . "</td>";
				$aryHtml[] = $strText;
			}

			// ��������
			else if ( $strDetailColumnName == "lngStockItemCode" )
			{
				$strText = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["lngstockitemcode"] )
				{
					$strText .= "[" . $aryDetailResult[$x]["lngstockitemcode"] ."]";
					// �������ܥ����ɤ�¸�ߤ���ʤ��
					if ( $aryDetailResult[$x]["lngstocksubjectcode"] )
					{
						$strSubjectItem = $aryDetailResult[$x]["lngstocksubjectcode"] . ":" . $aryDetailResult[$x]["lngstockitemcode"];
						$aryStockItem = $objCache->GetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem);
						if( !is_array($aryStockItem) )
						{
							// ����̾�Τμ���
							$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname" , 
								$aryDetailResult[$x]["lngstockitemcode"], "lngstocksubjectcode = " . $aryDetailResult[$x]["lngstocksubjectcode"], $objDB );
							// ����̾�Τ�����
							$aryStockItem[0] = $strStockItemName;
							$objCache->SetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem, $aryStockItem);
						}
						$strText .= " " . $aryStockItem[0] . "</td>";
					}
					else
					{
						$strText .= "</td>";
					}
				}
				else
				{
					$strText .= "      ";
					$strText .= " " . $aryDetailResult[$x]["strstockitemname"] . "</td>";
				}
				$aryHtml[] = $strText;
			}

			// ������ˡ
			else if ( $strDetailColumnName == "lngDeliveryMethodCode" )
			{
				$strText = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["strdeliverymethodname"] == "" )
				{
					$aryDetailResult[$x]["strdeliverymethodname"] = "̤��";
				}
				$strText .= $aryDetailResult[$x]["strdeliverymethodname"] . "</td>";
				$aryHtml[] = $strText;
			}

// 2004.04.21 suzukaze update start
			// Ǽ��
			else if ( $strDetailColumnName == "dtmDeliveryDate" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				$aryHtml[] = str_replace( "-", "/", $aryDetailResult[$x]["dtmdeliverydate"] ) . "</td>";
			}
// 2004.04.21 suzukaze update end

			// ñ��
			else if ( $strDetailColumnName == "curProductPrice" )
			{
				$strText = "<td align=\"right\" nowrap>";
				$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["curproductprice"] )
				{
					$strText .= "0.00</td>";
				}
				else
				{
					$strText .= $aryDetailResult[$x]["curproductprice"] . "</td>";
				}
				$aryHtml[] = $strText;
			}

			// ñ��
			else if ( $strDetailColumnName == "lngProductUnitCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>" . $aryDetailResult[$x]["strproductunitname"] . "</td>";
			}

			// ����
			else if ( $strDetailColumnName == "lngProductQuantity" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>" . $aryDetailResult[$x]["lngproductquantity"] . "</td>";
			}

			// ��ȴ���
			else if ( $strDetailColumnName == "curSubTotalPrice" )
			{
				$strText = "<td align=\"right\" nowrap>";
				$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["cursubtotalprice"] )
				{
					$strText .= "0.00</td>";
				}
				else
				{
					$strText .= $aryDetailResult[$x]["cursubtotalprice"] . "</td>";
				}
				$aryHtml[] = $strText;
			}

			// ����¾�ι��ܤϤ��Τޤ޽���
			else
			{
				$strLowDetailColumnName = strtolower($strDetailColumnName);
				$strText = "<td align=\"left\" nowrap>";
				if ( $strLowDetailColumnName == "strdetailnote" )
				{
					$strText .= nl2br($aryDetailResult[$x][$strLowDetailColumnName]) . "</td>";
				}
				else
				{
					$strText .= $aryDetailResult[$x][$strLowDetailColumnName] . "</td>";
				}
				$aryHtml[] = $strText;
			}
		}
		if ( $lngMode )
		{
			$aryHtml[] = "</tr>";
		}
	}

	return $aryHtml;
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
	// ���ٹԤιԿ�
	$lngDetailCount = count($aryDetailResult);
	if ( !$lngDetailCount )
	{
		$lngDetailCount = 1;
	}
	// �����⡼�ɤξ��⣱
	if ( $aryData["Admin"] )
	{
		$lngDetailCount = 1;
	}

	$aryHtml[] =  "<td nowrap align=\"center\" rowspan=\"" . $lngDetailCount . "\">" . $lngColumnCount . "</td>";

	// �����⡼�ɤǤʤ��������ٹԽ����б������󥿡�
	$count = 0;

	// ɽ���оݥ������������̤ν���
	for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
	{
		$strColumnName = $aryHeadViewColumn[$j];

		// ɽ���оݤ��ܥ���ξ��
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
		{
			// �ܥ����ˤ���ѹ�

			// �ܺ�ɽ��
			if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
			{
				// ȯ��ǡ���������оݤξ�硢�ܺ�ɽ���ܥ���������Բ�
				if ( $aryHeadResult["lngrevisionno"] >= 0 )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/po/result/index2.php?lngOrderNo=" . $aryHeadResult["lngorderno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . ", 'detail' )\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
			}

			// ����
			if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
			{
				// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�����ѡס��ޤ�����оݤξ�罤���ܥ���������Բ�
				// �ǿ�ȯ������ǡ����ξ��������Բ�
				// Ǽ�ʺѤǴ����⡼�ɤ�̵�����������Բ�
				// �ǿ�ȯ��ξ��֤��ֿ�����פξ��������Բ�
				if ( $aryHeadResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE or $aryHeadResult["lngorderstatuscode"] == DEF_ORDER_CLOSED 
					or ( $aryHeadResult["lngorderstatuscode"] == DEF_ORDER_END and !$aryData["Admin"] ) 
					or $aryHeadResult["lngrevisionno"] < 0 
					or ( $aryNewResult and $aryNewResult["lngrevisionno"] < 0 ) 
					or ( $aryNewResult and $aryNewResult["lngorderstatuscode"] == DEF_ORDER_APPLICATE ) )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/po/regist/renew.php?lngOrderNo=" . $aryHeadResult["lngorderno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " )\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				}
			}

			// ���
			if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
			{
				// �����⡼�ɤ�̵�����⤷���ϥ�Х�����¸�ߤ��ʤ����
				if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
				{
					// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
					// �ǿ�ȯ������ǡ����ξ��������Բ�
					if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED 
						and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_DELIVER and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_END 
						and ( !$aryNewResult or $aryNewResult["lngrevisionno"] >= 0 ) )
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/po/result/index3.php?lngOrderNo=" . $aryHeadResult["lngorderno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
					}
					else
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
					}
				}
				// �����⡼�ɤ�ʣ����Х�����¸�ߤ�����
				else
				{
					// �ǿ�ȯ��ξ��
					if ( $lngReviseCount == 0 )
					{
						// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
						// �ǿ�ȯ������ǡ����ξ��������Բ�
						if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED 
							and ( !$aryNewResult or $aryNewResult["lngrevisionno"] >= 0 ) )
						{
							$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngReviseTotalCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/po/result/index3.php?lngOrderNo=" . $aryHeadResult["lngorderno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
						}
						else
						{
							$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngReviseTotalCount . "\"></td>";
						}
					}
				}
			}

			// ̵����
			if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
			{
				// ȯ��ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
				if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED 
						and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_DELIVER and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_END )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/po/result/index4.php?lngOrderNo=" .$aryHeadResult["lngorderno"]. "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'Invalid01' )\"><img onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" onmouseover=\"fncInvalidSmallButton( 'on' , this );\" onmouseout=\"fncInvalidSmallButton( 'off' , this );fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/invalid_small_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"INVALID\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
			}
		}

		// ��Ͽ��
		else if ( $strColumnName == "dtmInsertDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">" . str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) ) . "</td>";
		}

		// �׾���
		else if ( $strColumnName == "dtmOrderAppDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">" . str_replace( "-", "/", $aryHeadResult["dtmorderappdate"] ) . "</td>";
		}

		// ȯ��NO
		else if ( $strColumnName == "strOrderCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["strordercode"] . "</td>";
			// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
			if ( $aryData["Admin"] )
			{
				$aryHtml[] = "<td align=\"center\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["lngrevisionno"] . "</td>";
			}
		}

		// ���ϼ�
		else if ( $strColumnName == "lngInputUserCode" )
		{
			$strText = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinputuserdisplaycode"] )
			{
				$strText .= "[" . $aryHeadResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$strText .= "     ";
			}
			$strText .= " " . $aryHeadResult["strinputuserdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}

		// ������
		else if ( $strColumnName == "lngCustomerCode" )
		{
			$strText = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strcustomerdisplaycode"] )
			{
				$strText .= "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryHeadResult["strcustomerdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
/*
		// ����
		else if ( $strColumnName == "lngInChargeGroupCode" )
		{
			$strText = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinchargegroupdisplaycode"] )
			{
				$strText .= "[" . $aryHeadResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$strText .= "    ";
			}
			$strText .= " " . $aryHeadResult["strinchargegroupdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}

		// ô����
		else if ( $strColumnName == "lngInChargeUserCode" )
		{
			$strText = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinchargeuserdisplaycode"] )
			{
				$strText .= "[" . $aryHeadResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$strText .= "     ";
			}
			$strText .= " " . $aryHeadResult["strinchargeuserdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
*/
		// ��׶��
		else if ( $strColumnName == "curTotalPrice" )
		{
			$strText = "<td align=\"right\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryHeadResult["curtotalprice"] )
			{
				$strText .= "0.00</td>";
			}
			else
			{
				$strText .= $aryHeadResult["curtotalprice"] . "</td>";
			}
			$aryHtml[] = $strText;
		}

		// ����
		else if ( $strColumnName == "lngOrderStatusCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["strorderstatusname"] . "</td>";
		}

		// ��ʧ���
		else if ( $strColumnName == "lngPayConditionCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["strpayconditionname"] . "</td>";
		}

		// ȯ��ͭ��������
		else if ( $strColumnName == "dtmExpirationDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">" . str_replace( "-", "/", $aryHeadResult["dtmexpirationdate"] ) . "</td>";
		}
		// ���ٹԤν���
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode"
			or $strColumnName == "strGoodsCode" or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice"
			or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice"
			or $strColumnName == "strDetailNote" or $strColumnName == "dtmDeliveryDate" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" or $strColumnName == "strMoldNo" )
		{
			if ( !$aryData["Admin"] and $count == 0 )
			{
				// ���ٹԤν���
				$aryDetailHtml = fncSetPurchaseDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 0, $lngColumnCount, $objDB, $objCache );
				for ( $k = 0; $k < count($aryDetailHtml); $k++ )
				{
					$aryHtml[] = $aryDetailHtml[$k];
				}
				$count++;
			}
			// �����⡼�ɤξ�硢����̾�Τϣ�������ɽ������
			else if ( $aryData["Admin"] and $strColumnName == "strProductCode" )
			{
				$strText = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
				for ( $k = 0; $k < count($aryDetailResult); $k++ )
				{
					if ( $aryDetailResult[$k]["strproductcode"] )
					{
						$strText .= "[" . $aryDetailResult[$k]["strproductcode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$strText .= " " . $aryDetailResult[$k]["strproductname"] . "<br>";
				}
				$strText .= "</td>";
				$aryHtml[] = $strText;
			}
		}

		// ����¾�ι��ܤϤ��Τޤ޽���
		else
		{
			$strLowColumnName = strtolower($strColumnName);
			$strText = "<td align=\"left\" nowrap";
			$strText .= " rowspan=\"" . $lngDetailCount . "\">";
			if ( $strLowColumnName == "strnote" )
			{
				$strText .= nl2br($aryHeadResult[$strLowColumnName]) . "</td>";
			}
			else
			{
				$strText .= $aryHeadResult[$strLowColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}
	}

	$aryHtml[] = "</tr>";

	// �⤷�����ٹԤ�ʣ����¸�ߤ��Ƥ����
	if ( ( !$aryData["Admin"] ) and ( count($aryDetailResult) >= 2 ) )
	{
		// ���ٹԤν���
		$aryDetailHtml = fncSetPurchaseDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 1, $lngColumnCount, $objDB, $objCache );
		for ( $k = 0; $k < count($aryDetailHtml); $k++ )
		{
			$aryHtml[] = $aryDetailHtml[$k];
		}
	}

	return $aryHtml;
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
function fncSetPurchaseTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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

// var_dump( $aryDetailViewColumn );
// exit;

	// �ơ��֥�η���
	$lngResultCount = count($aryResult);

	$aryHtml[] = "<span id=\"COPYAREA1\">";
	$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";

	$lngColumnCount = 0;

	for ( $i = 0; $i < $lngResultCount; $i++ )
	{

// ����̾������� start=========================================

		if ($i == 0)
		{
			$aryHtml[] = "<tr id=\"SegTitle\">";
			$aryHtml[] = "<td valign=\"top\" valign=\"center\"><a href=\"#\" onclick=\"fncDoCopy( copyhidden , document.getElementById('COPYAREA1') , document.getElementById('COPYAREA2') );return false;\"><img onmouseover=\"CopyOn(this);\" onmouseout=\"CopyOff(this);\" src=\"/img/type01/cmn/seg/copy_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"COPY\"></a></td>";

			// ɽ���оݥ������������������
			for ( $j = 0; $j < count($aryViewColumn); $j++ )
			{
				$strColumnName = $aryViewColumn[$j];
				// �����⡼�ɤξ�硢����̾�Τϣ�������ɽ������
				if ( $aryData["Admin"] and $strColumnName == "strProductName" )
				{
					// �����⡼�ɤ�����̾�Τ�ɽ���оݤˤʤäƤ�����ϥ���ॿ���ȥ����ɽ��
				}
				else
				{
					// �����ȹ��ܰʳ��ξ��
					if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
					{
						// �ܺ٥ܥ���ξ��ϡ��ܺ�ɽ����ǽ�ʥ桼�����Τ�ɽ������
						if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
						{
							$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
	//						$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// �����ܥ���ξ��ϡ�����������ǽ�ʥ桼�����Τ�ɽ������
						if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
						{
							$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
	//						$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// ����ܥ���ξ��ϡ����������ǽ�ʥ桼�����Τ�ɽ������
						if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
						{
							$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
	//						$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// ̵���ܥ���ξ��ϡ������⡼�ɡ������̵��������ǽ�ʥ桼�����ξ��Τ�ɽ������
						if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
						{
							$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
	//						$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
					}
					// �����ȹ��ܤξ��
					else
					{
						$strText = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" ";
						if ( $aryData["strSort"] == $aryTableName[$strColumnName] )
						{
							if ( $aryData["strSortOrder"] == "DESC" )
							{
								$strSortOrder = "ASC";
							}
							else
							{
								$strSortOrder = "DESC";
							}
						}
						else
						{
							$strSortOrder = "DESC";
						}
						$strText .= "onclick=\"fncSort2('" . $aryTableName[$strColumnName] . "', '" . $strSortOrder . "');\">";
						$strText .= "<a href=\"#\">".$aryTytle[$strColumnName]."</a></td>";
						// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
						if ( $aryData["Admin"] and $strColumnName == "strOrderCode" )
						{
							$strText .= "<td id=\"Columns\" nowrap>".$aryTytle["lngRevisionNo"]."</td>";
						}
						$aryHtml[] = $strText;
					}
				}
			}
			$aryHtml[] = "</tr>";
//			$aryHtml[] = "</span>";

			// ���ߡ�TR
			$aryHtml[] = "<tr id=\"DummyTR\"><td colspan=\"" . count($aryViewColumn) . "\">&nbsp;</td></tr>";

//			$aryHtml[] = "<span id=\"COPYAREA2\">";
		}

// ����̾������� end=========================================

// ������̽��ϡ�������start==================================
		// �����⡼�ɤǤʤ����
		if ( !$aryData["Admin"] )
		{
			reset( $aryResult[$i] );

			// ���ٽ����Ѥ�Ĵ��
			$lngDetailViewCount = count( $aryDetailViewColumn );

			if ( $lngDetailViewCount )
			{
				// ���ٹԿ���Ĵ��
				$strDetailQuery = fncGetOrderToProductSQL ( $aryDetailViewColumn, $aryResult[$i]["lngorderno"], $aryData, $objDB );
//fncDebug('lib_pos.txt', $strDetailQuery, __FILE__, __LINE__);
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
					for ( $j = 0; $j < $lngDetailCount; $j++ )
					{
						$aryDetailResult[] = pg_fetch_array( $lngDetailResultID, $j, PGSQL_ASSOC );
					}
				}

				$objDB->freeResult( $lngDetailResultID );
			}

			// ���������ʬ������
			if ( $lngDetailCount == "" )
			{
				$lngDetailCount = 0;
			}
			$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\" style=\"background:#FFB2B2\">";

			$lngColumnCount++;

			// ���쥳����ʬ�ν���
			$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, 1, 0, FALSE );
			$strColBuff = '';
			for ( $j = 0; $j < count($aryHtml_add); $j++ )
			{
				$strColBuff .= $aryHtml_add[$j];
			}
			$aryHtml[] = $strColBuff;
		}
// ������̽��ϡ�������end==================================

// �����⡼���Ѳ���Х���������ǡ�������start==================================
		// �����⡼�ɤξ��
		else
		{
			// �����⡼�ɤξ�硡Ʊ��ȯ�����ɤΰ����������ɽ������

			// ��Х���������̵����ȯ�����ɤ��������
			if ( strlen($aryResult[$i]["strordercode"]) >= 9)
			{
				$strOrderCodeBase = ereg_replace( strstr( $aryResult[$i]["strordercode"], "-" ), "", $aryResult[$i]["strordercode"] );
			}
			else
			{
				$strOrderCodeBase = $aryResult[$i]["strordercode"];
			}

			$strSameOrderCodeQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strOrderCodeBase, $aryResult[$i]["lngorderno"], FALSE );

			// �ͤ�Ȥ� =====================================
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameOrderCodeQuery, $objDB );

			// ����Υ��ꥢ
			unset( $arySameOrderCodeResult );

			if ( $lngResultNum )
			{
				for ( $j = 0; $j < $lngResultNum; $j++ )
				{
					$arySameOrderCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
				}
				$lngSameOrderCount = $lngResultNum;
			}
			$objDB->freeResult( $lngResultID );

			// Ʊ��ȯ�����ɤǤβ���Х����ǡ�����¸�ߤ����
			if ( $lngResultNum )
			{
				for ( $j = 0; $j < $lngSameOrderCount; $j++ )
				{
					// ���������ʬ������

					reset( $arySameOrderCodeResult[$j] );

					// ���ٽ����Ѥ�Ĵ��
					$lngDetailViewCount = count( $aryDetailViewColumn );

					if ( $lngDetailViewCount )
					{
						// ���ٹԿ���Ĵ��
						$strDetailQuery = fncGetOrderToProductSQL ( $aryDetailViewColumn, $arySameOrderCodeResult[$j]["lngorderno"], $aryData, $objDB );

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
					// �ǡ����ξ��֤ˤ���طʿ����ѹ�
					if ( $lngDetailCount == "" )
					{
						$lngDetailCount = 0;
					}
					if ( $arySameOrderCodeResult[$j]["lngrevisionno"] < 0 )
					{
						// ����ǡ����ξ��
						$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 1 );\" style=\"background:#B3E0FF;\">";
					}
					else if ( $j == 0 )
					{
						// �ǿ��Υǡ����ξ��
						$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 1 );\" style=\"background:#FFB2B2;\">";
					}
					else
					{
						$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 1 );\" style=\"background:#FEEF8B;\">";
					}

					$lngColumnCount++;

					// ���쥳����ʬ�ν���
					$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $arySameOrderCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $j, $arySameOrderCodeResult[0] );
					$strColBuff = '';
					for ( $k = 0; $k < count($aryHtml_add); $k++ )
					{
						$strColBuff .= $aryHtml_add[$k];
					}
					$aryHtml[] =$strColBuff;
				}
			}
		}

// �����⡼���Ѳ���Х����ǡ�������end==================================

	}

	$aryHtml[] = "</table>";
	$aryHtml[] = "</span>";

	// ���ԡ��Զ���б� ���ߡ��Ԥ�ȴ���������򸽾���ά���뤳�Ȥ��б�
	$aryHtml[] = "<span id=\"COPYAREA2\">";
	$aryHtml[] = "</span>";

//fncDebug("posearch.txt", $aryHtml, __FILE__ , __LINE__ );


	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}




?>