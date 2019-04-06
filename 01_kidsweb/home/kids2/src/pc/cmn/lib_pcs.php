<?
// ----------------------------------------------------------------------------
/**
*       ��������  ������Ϣ�ؿ���
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
* �������ܤ�����פ���ǿ��λ����ǡ������������SQLʸ�κ����ؿ�
*
*	�������ܤ��� SQLʸ���������
*
*	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
*	@param  Array 	$arySearchColumn 		�����оݥ����̾������
*	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
*	@param  Object	$objDB       			DB���֥�������
*	@param	String	$strStockCode			����������	��������:������̽���	���������ɻ����:�����ѡ�Ʊ�����������ɤΰ�������
*	@param	Integer	$lngStockNo				�����Σ�	0:������̽���	�����Σ�����:�����ѡ�Ʊ�����������ɤȤ�������оݳ�����NO
*	@param	Boolean	$bytAdminMode			ͭ���ʺ���ǡ����μ����ѥե饰	FALSE:������̽���	TRUE:�����ѡ�����ǡ�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchStockSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strStockCode, $lngStockNo, $bytAdminMode )
{

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// ɽ�����ܡ������⡼�ɤβ���ӥ����ǡ���������ӡ����پ���ϸ�����̤�����

		// ��Ͽ��
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
		}

		// �׾���
		if ( $strViewColumnName == "dtmStockAppDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( s.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmStockAppDate";
		}

		// �����Σ�
		if ( $strViewColumnName == "strStockCode" )
		{
			$arySelectQuery[] = ", s.strStockCode as strStockCode";
		}

		// ȯ��Σ�
		if ( $strViewColumnName == "strOrderCode" )
		{
			$arySelectQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
			$flgOrder = TRUE;
		}

		// ��ɼ������
		if ( $strViewColumnName == "strSlipCode" )
		{
			$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
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
		if ( $strViewColumnName == "lngStockStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", s.lngStockStatusCode as lngStockStatusCode";
			$arySelectQuery[] = ", ss.strStockStatusName as strStockStatusName";
			$flgStockStatus = TRUE;
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
			$arySelectQuery[] = ", s.lngPayConditionCode as lngPayConditionCode";
			$arySelectQuery[] = ", pc.strPayConditionName as strPayConditionName";
			$flgPayCondition = TRUE;
		}

		// ����ͭ��������
		if ( $strViewColumnName == "dtmExpirationDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( s.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
		}

		// ����
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", s.strNote as strNote";
		}

		// ��׶��
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
			$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;


	// �����ɲ�
	$detailFlag = FALSE;

	// �����⡼�ɤθ�������Ʊ�����������ɤΥǡ��������������
	if ( $strStockCode or $bytAdminMode )
	{
		// Ʊ�����������ɤ��Ф��ƻ���λ����ֹ�Υǡ����Ͻ�������
		if ( $lngStockNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strStockCode = '" . $strStockCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// ����ǡ����������Ͼ���ɲ�
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND s.lngRevisionNo < 0";
		}
	}

	// �����⡼�ɤǤ�Ʊ�����������ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
	else
	{
		// ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ������Τ�
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////�����ޥ�����θ������////
			// ��Ͽ��
			if ( $strSearchColumnName == "dtmInsertDate" )
			{
				if ( $arySearchDataColumn["dtmInsertDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmInsertDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmInsertDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmInsertDate <= '" . $dtmSearchDate . "'";
				}
			}
			// �׾���
			if ( $strSearchColumnName == "dtmStockAppDate" )
			{
				if ( $arySearchDataColumn["dtmStockAppDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmStockAppDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmStockAppDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmStockAppDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
				}
			}
			// �����Σ�
			if ( $strSearchColumnName == "strStockCode" )
			{
				if ( $arySearchDataColumn["strStockCodeFrom"] )
				{
					$aryQuery[] = " AND s.strStockCode >= '" . $arySearchDataColumn["strStockCodeFrom"] . "'";
				}
				if ( $arySearchDataColumn["strStockCodeTo"] )
				{
					$aryQuery[] = " AND s.strStockCode <= '" . $arySearchDataColumn["strStockCodeTo"] . "'";
				}
			}
			// ȯ��Σ�
			if ( $strSearchColumnName == "strOrderCode" )
			{
				if ( $arySearchDataColumn["strOrderCodeFrom"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeFrom"], "-") )
					{
						// ��Х����������դλ����Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
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
						// ��Х����������դλ����Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
						$strNewOrderCode = ereg_replace( strstr( $arySearchDataColumn["strOrderCodeTo"], "-" ), "", $arySearchDataColumn["strOrderCodeTo"] );
					}
					else
					{
						$strNewStockCode = $arySearchDataColumn["strOrderCodeTo"];
					}
					$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode . "'";
				}
				$flgOrder = TRUE;
			}
			// ��ɼ������
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					$aryQuery[] = " AND UPPER(s.strSlipCode) LIKE UPPER('%" . $arySearchDataColumn["strSlipCode"] . "%')";
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
					$aryQuery[] = " AND UPPER(input_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')";
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
					$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
					$flgCustomerCompany = TRUE;
				}
			}
			// ����
			if ( $strSearchColumnName == "lngStockStatusCode" )
			{
				if ( $arySearchDataColumn["lngStockStatusCode"] )
				{
					// �������֤� ","���ڤ��ʸ����Ȥ����Ϥ����
					//$arySearchStatus = explode( ",", $arySearchDataColumn["lngStockStatusCode"] );
					// �����å��ܥå������ˤ�ꡢ����򤽤Τޤ�����
					$arySearchStatus = $arySearchDataColumn["lngStockStatusCode"];
					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND ( ";
						// �������֤�ʣ�����ꤵ��Ƥ����ǽ��������Τǡ�����Ŀ�ʬ�롼��
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// ������
							if ( $j <> 0 )
							{
								$aryQuery[] = " OR ";
							}
							$aryQuery[] = "s.lngStockStatusCode = " . $arySearchStatus[$j] . "";
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
					$aryQuery[] = " AND s.lngPayConditionCode = " . $arySearchDataColumn["lngPayConditionCode"] . "";
				}
			}
			// ����������
			if ( $strSearchColumnName == "dtmExpirationDate" )
			{
				if ( $arySearchDataColumn["dtmExpirationDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmExpirationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmExpirationDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmExpirationDate <= '" . $dtmSearchDate . "'";
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
					$aryDetailWhereQuery[] = "sd1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "'";
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
					$aryDetailWhereQuery[] = "sd1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
			// ����
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if( $arySearchDataColumn["lngInChargeGroupCode"] || $arySearchDataColumn["strInChargeGroupName"])
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
				if( $arySearchDataColumn["lngInChargeUserCode"] || $arySearchDataColumn["strInChargeUserName"])
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
					$aryDetailWhereQuery[] = "UPPER(p.strProductName) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%') ";
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
					$aryDetailWhereQuery[] = "UPPER(p.strProductEnglishName) LIKE UPPER('%" . $arySearchDataColumn["strProductEnglishName"] . "%') ";
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
					$aryDetailWhereQuery[] = "sd1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
					$StockSubjectFlag = TRUE;
					$detailFlag = TRUE;
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
					$aryDetailWhereQuery[] = "sd1.lngStockItemCode = " . $arySearchDataColumn["lngStockItemCode"] . " ";
					if ( $StockSubjectFlag != TRUE )
					{
						$aryDetailWhereQuery[] = "AND sd1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
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
					$aryDetailWhereQuery[] = "sd1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
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
					$aryDetailWhereQuery[] = "sd1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}



	// ���ٹԤθ����б�

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngStockNo ) sd1.lngStockNo";
	$aryDetailFrom[] = "	,sd1.lngStockDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,sd1.lngStockSubjectCode";	// ��������
	$aryDetailFrom[] = "	,sd1.lngStockItemCode";		// ��������
	$aryDetailFrom[] = "	,sd1.strMoldNo";			// �ⷿNo.
	$aryDetailFrom[] = "	,p.strGoodsCode";			// �ܵ�����
	$aryDetailFrom[] = "	,sd1.lngDeliveryMethodCode";// ������ˡ
	$aryDetailFrom[] = "	,sd1.dtmDeliveryDate";		// Ǽ��
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,sd1.lngProductUnitCode";	// ñ��
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// ����
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,sd1.lngTaxClassCode";		// �Ƕ�ʬ
	$aryDetailFrom[] = "	,mt.curtax";				// ��Ψ
	$aryDetailFrom[] = "	,sd1.curtaxprice";			// �ǳ�
	$aryDetailFrom[] = "	,sd1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_StockDetail sd1";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON sd1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = sd1.lngtaxcode";


	$aryDetailWhereQuery[] = ") as sd";
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
	$aryOutQuery[] = "SELECT s.lngStockNo as lngStockNo";
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,s.lngStockStatusCode as lngStockStatusCode";

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,sd.lngStockDetailNo";


	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Stock s";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgOrder )
	{
		$aryFromQuery[] = " LEFT JOIN m_Order o USING (lngOrderNo)";
	}
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	if ( $flgStockStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_StockStatus ss USING (lngStockStatusCode)";
	}
	if ( $flgPayCondition )
	{
		$aryFromQuery[] = " LEFT JOIN m_PayCondition pc ON s.lngPayConditionCode = pc.lngPayConditionCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(s.lngStockNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_PC1; // ������Ͽ����WF�ǡ������оݤˤ���٤˾�����
	}
	
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ���ٹԾ�郎���ä����� ���Ϣ��
	$aryOutQuery[] = " AND sd.lngStockNo = s.lngStockNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ������ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��       ////
	//// Ʊ�����������ɤ���ĥǡ�����̵�������ǡ���          ////
	/////////////////////////////////////////////////////////////
	// ���������ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strStockCode )
	{
		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Stock s1 WHERE s1.strStockCode = s.strStockCode AND s1.bytInvalidFlag = false )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.bytInvalidFlag = false AND s2.strStockCode = s.strStockCode )";
		}
	}

	// �����⡼�ɤθ�������Ʊ�����������ɤΥǡ��������������
	if ( $strStockCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY s.dtmInsertDate DESC";
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
			case "strStockCode":
			case "strSlipCode":
			case "lngStockStatusCode":
			case "lngPayConditionCode":
			case "dtmExpirationDate":
			case "strNote":
			case "curTotalPrice":
				$aryOutQuery[] = " ORDER BY s." . $arySearchDataColumn["strSort"] . " " . $strAsDs . ", s.lngStockNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmStockAppDate" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strOrderCode":
				$aryOutQuery[] = " ORDER BY strOrderCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngWorkFlowStatusCode":
				$aryOutQuery[] = " ORDER BY lngWorkFlowStatusCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngStockDetailNo":	// ���ٹ��ֹ�
				$aryOutQuery[] = " ORDER BY sd.lngStockDetailNo" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strProductCode":		// ���ʥ�����
				$aryOutQuery[] = " ORDER BY sd.strProductCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngGroupCode":		// ����
				$aryOutQuery[] = " ORDER BY sd.strGroupDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngUserCode":			// ô����
				$aryOutQuery[] = " ORDER BY sd.strUserDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strProductName":		// ����̾��
				$aryOutQuery[] = " ORDER BY sd.strProductName" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strProductEnglishName":	// ���ʱѸ�̾��
				$aryOutQuery[] = " ORDER BY sd.strProductEnglishName" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngStockSubjectCode":	// ��������
				$aryOutQuery[] = " ORDER BY sd.lngStockSubjectCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngStockItemCode":	// ��������
				$aryOutQuery[] = " ORDER BY sd.lngStockItemCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strMoldNo":			// �ⷿNo.
				$aryOutQuery[] = " ORDER BY sd.strMoldNo" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strGoodsCode":		// �ܵ�����
				$aryOutQuery[] = " ORDER BY sd.strGoodsCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngDeliveryMethodCode":// ������ˡ
				$aryOutQuery[] = " ORDER BY sd.lngDeliveryMethodCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "dtmDeliveryDate":		// Ǽ��
				$aryOutQuery[] = " ORDER BY sd.dtmDeliveryDate" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curProductPrice":		// ñ��
				$aryOutQuery[] = " ORDER BY sd.curProductPrice" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngProductUnitCode":	// ñ��
				$aryOutQuery[] = " ORDER BY sd.lngProductUnitCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngProductQuantity":	// ����
				$aryOutQuery[] = " ORDER BY sd.lngProductQuantity" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curSubTotalPrice":	// ��ȴ���
				$aryOutQuery[] = " ORDER BY sd.curSubTotalPrice" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngTaxClassCode":		// �Ƕ�ʬ
				$aryOutQuery[] = " ORDER BY sd.lngTaxClassCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curTax":				// ��Ψ
				$aryOutQuery[] = " ORDER BY sd.curTax" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curTaxPrice":			// �ǳ�
				$aryOutQuery[] = " ORDER BY sd.curTaxPrice" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strDetailNote":		// ��������
				$aryOutQuery[] = " ORDER BY sd.strNote" . $strAsDs . ", lngStockNo DESC";
				break;
			default:
				$aryOutQuery[] = " ORDER BY s.lngStockNo DESC";
		}
	}

//fncDebug( 'lib_pcs.txt', implode("\n", $aryOutQuery), __FILE__, __LINE__);
//fncDebug( 'lib_pcs.txt', fncGetSearchStockSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strStockCode, $lngStockNo, $bytAdminMode ), __FILE__, __LINE__);

	return implode("\n", $aryOutQuery);
}



/**
* �б��������NO�Υǡ������Ф������ٹԤ��������SQLʸ�κ����ؿ�
*
*	����NO�������٤�������� SQLʸ���������
*
*	@param  Array 	$aryDetailViewColumn 	ɽ���о����٥����̾������
*	@param  String 	$lngStockNo 			�оݻ���NO
*	@param  Array 	$aryData 				POST�ǡ���������
*	@param  Object	$objDB       			DB���֥�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetStockToProductSQL ( $aryDetailViewColumn, $lngStockNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];

		// ɽ�����ܡ�
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
		// ��������
		if ( $strViewColumnName == "lngStockSubjectCode" )
		{
			$arySelectQuery[] = ", sd.lngStockSubjectCode as lngStockSubjectCode";
			$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
			$flgStockSubject = TRUE;
		}
		// ��������
		if ( $strViewColumnName == "lngStockItemCode" )
		{
			$arySelectQuery[] = ", sd.lngStockItemCode as lngStockItemCode";
			$flgStockItem = TRUE;
		}
		// �ⷿ�ֹ�
		if ( $strViewColumnName == "strMoldNo" )
		{
			$arySelectQuery[] = ", sd.strMoldNo as strMoldNo";
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
			$arySelectQuery[] = ", sd.lngDeliveryMethodCode as lngDeliveryMethodCode";
			$arySelectQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
			$flgDeliveryMethod = TRUE;
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

	// �������ʤΤ�ɽ���оݤ��ä����ϻ������ܤˤĤ��Ƥ�ǡ������������
	if ( $flgStockItem == TRUE and $flgStockSubject == FALSE )
	{
		$arySelectQuery[] = ", sd.lngStockSubjectCode as lngStockSubjectCode";
		$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
		$flgStockSubject = TRUE;
	}

	// ���о�� �оݻ���NO�λ���
	$aryQuery[] = " WHERE sd.lngStockNo = " . $lngStockNo . "";

	// �����ɲ�

	// ////�����ޥ�����θ������////
	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,sd.lngStockNo as lngStockNo";
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";

	// select�� �����꡼Ϣ��
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_StockDetail sd";

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
		$strAsDs = " DESC";	// �إå����ܤȤϵս�ˤ���
	}
	else
	{
		$strAsDs = " ASC";	//�߽�
	}
	switch($aryData["strSort"])
	{
		case "strDetailNote":
			$aryOutQuery[] = " ORDER BY sd.strNote" . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngStockDetailNo":
			$aryOutQuery[] = " ORDER BY sd.lngSortKey" . $strAsDs;
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
*	@param  Array 	$aryHeadViewColumn 		�إå�ɽ���оݥ����̾������
*	@param  Array 	$aryDetailViewColumn 	����ɽ���оݥ����̾������
*	@param  Array 	$aryData 				�Уϣӣԥǡ�����
*	@param	Array	$aryUserAuthority		�桼�����������Ф��븢�¤����ä�����
*	@access public
*/
function fncSetStockHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
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
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// ����
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// �����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡס��ޤ�����оݤξ�罤���ܥ���������Բ�
					// �ǿ�����������ǡ����ξ��������Բ�
					if ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_CLOSED 
	// 2004.03.01 Suzukaze update start
	//					or ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_END and !$aryData["Admin"] ) 
	// 2004.03.01 Suzukaze update end
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
					}
				}

				// ���
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// �����⡼�ɤ�̵�����⤷���ϥ�Х�����¸�ߤ��ʤ����
					if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
					{
						// �����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡפξ�����ܥ���������Բ�
						// �ǿ�ȯ������ǡ����ξ��������Բ�
						if ( $aryHeadResult["lngstockstatuscode"] != DEF_STOCK_CLOSED 
	// 2004.03.01 Suzukaze update start
	//						and $aryHeadResult["lngstockstatuscode"] != DEF_STOCK_END 
	// 2004.03.01 Suzukaze update end
							and !$bytDeleteFlag )

						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					// �����⡼�ɤ�ʣ����Х�����¸�ߤ�����
					else
					{
						// �ǿ������ξ��
						if ( $lngReviseCount == 0 )
						{
							// �����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡפξ�����ܥ���������Բ�
							// �ǿ�ȯ������ǡ����ξ��������Բ�
							if ( $aryHeadResult["lngstockstatuscode"] != DEF_STOCK_CLOSED 
								and !$bytDeleteFlag )
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
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
				}
				// ̵����
				if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
				{
					// �����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡס��ޤ�����оݤξ��̵�����ܥ���������Բ�
					// �ǿ�����������ǡ����ξ��������Բ�
	// 2004.03.01 Suzukaze update start
					if ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
	//					or ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_END and !$aryData["Admin"] ) )
	// 2004.03.01 Suzukaze update end
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						//$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
						$aryHtml[] = "\t<td>�ܥ����������</td>\n";
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
				else if ( $strColumnName == "dtmStockAppDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmsalesappdate"] );
				}

				// ����NO
				else if ( $strColumnName == "strStockCode" )
				{
					$TdData .= $aryHeadResult["strstockcode"];
					// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
				}

				// ȯ��NO
				else if ( $strColumnName == "strOrderCode" )
				{
					$TdData .= $aryHeadResult["strordercode"];
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

				// ������
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
				else if ( $strColumnName == "lngStockStatusCode" )
				{
					$TdData .= $aryHeadResult["strstockstatusname"];
				}

				// ��ʧ���
				else if ( $strColumnName == "lngPayConditionCode" )
				{
					$TdData .= $aryHeadResult["strpayconditionname"];
				}

				// ȯ��ͭ��������
				else if ( $strColumnName == "dtmExpirationDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmexpirationdate"] );
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
*	@access public
*/
function fncSetStockTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
		// ������
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo"
			or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode" or $strColumnName == "strGoodsCode"
			or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode"
			or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice" or $strColumnName == "lngTaxClassCode"
			or $strColumnName == "curTax" or $strColumnName == "curTaxPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "dtmDeliveryDate" 
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
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
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
		// �����⡼�ɤξ�硡Ʊ�����������ɤΰ����������ɽ������

		$strStockCodeBase = $aryResult[$i]["strstockcode"];

		$strSameStockCodeQuery = fncGetSearchStockSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strStockCodeBase, $aryResult[$i]["lngstockno"], FALSE );

		// �ͤ�Ȥ� =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameStockCodeQuery, $objDB );

		// ����Υ��ꥢ
		unset( $arySameStockCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameStockCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameStockCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// Ʊ�����������ɤǤβ���Х����ǡ�����¸�ߤ����
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameStockCount; $j++ )
			{
				// ���������ʬ������

				reset( $arySameStockCodeResult[$j] );

				// ���ٽ����Ѥ�Ĵ��
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// ���ٹԿ���Ĵ��
					$strDetailQuery = fncGetStockToProductSQL ( $aryDetailViewColumn, $arySameStockCodeResult[$j]["lngstockno"], $aryData, $objDB );

					// �����꡼�¹�
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

				// Ʊ�������ɤλ����ǡ����ǰ��־��ɽ������Ƥ���ȯ��ǡ���������ǡ����ξ��
				if ( $arySameStockCodeResult[0]["lngrevisionno"] < 0 )
				{
					$bytDeleteFlag = TRUE;
				}
				else
				{
					$bytDeleteFlag = FALSE;
				}

				// ���쥳����ʬ�ν���
				$aryHtml_add = fncSetStockHeadTable ( $lngColumnCount, $arySameStockCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameStockCount, $j, $bytDeleteFlag );
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