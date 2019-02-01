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
*	@access public
*/
function fncSetStockDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, $lngMode, $lngColumnCount, $objDB, $objCache )
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
				$aryHtml[] = "<td align=\"center\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["lngrecordno"] . "</td>";
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
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["lngstocksubjectcode"] )
				{
					$aryHtml[] = "[" . $aryDetailResult[$x]["lngstocksubjectcode"] ."]";
				}
				else
				{
					$aryHtml[] = "      ";
				}
				$aryHtml[] = " " . $aryDetailResult[$x]["strstocksubjectname"] . "</td>";
			}

			// ��������
			else if ( $strDetailColumnName == "lngStockItemCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["lngstockitemcode"] )
				{
					$aryHtml[] = "[" . $aryDetailResult[$x]["lngstockitemcode"] ."]";
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
						$aryHtml[] = " " . $aryStockItem[0] . "</td>";
					}
					else
					{
						$aryHtml[] = "</td>";
					}
				}
				else
				{
					$aryHtml[] = "      ";
					$aryHtml[] = " " . $aryDetailResult[$x]["strstockitemname"] . "</td>";
				}
			}

			// ������ˡ
			else if ( $strDetailColumnName == "lngDeliveryMethodCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["strdeliverymethodname"] == "" )
				{
					$aryDetailResult[$x]["strdeliverymethodname"] = "̤��";
				}
				$aryHtml[] = $aryDetailResult[$x]["strdeliverymethodname"] . "</td>";
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
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["curproductprice"] )
				{
					$aryHtml[] = "0.00</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x]["curproductprice"] . "</td>";
				}
			}

			// ñ��
			else if ( $strDetailColumnName == "lngProductUnitCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["strproductunitname"] . "</td>";
			}

			// ����
			else if ( $strDetailColumnName == "lngProductQuantity" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["lngproductquantity"] . "</td>";
			}

			// ��ȴ���
			else if ( $strDetailColumnName == "curSubTotalPrice" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["cursubtotalprice"] )
				{
					$aryHtml[] = "0.00</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x]["cursubtotalprice"] . "</td>";
				}
			}

			// �Ƕ�ʬ
			else if ( $strDetailColumnName == "lngTaxClassCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["strtaxclassname"] . "</td>";
			}

			// ��Ψ
			else if ( $strDetailColumnName == "curTax" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				if ( !$aryDetailResult[$x]["curtax"] )
				{
					$aryHtml[] = "</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x]["curtax"] . "</td>";
				}
			}

			// �ǳ�
			else if ( $strDetailColumnName == "curTaxPrice" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["curtaxprice"] )
				{
					$aryHtml[] = "0.00</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x]["curtaxprice"] . "</td>";
				}
			}

			// ����¾�ι��ܤϤ��Τޤ޽���
			else
			{
				$strLowDetailColumnName = strtolower($strDetailColumnName);
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $strLowDetailColumnName == "strdetailnote" )
				{
					$aryHtml[] = nl2br($aryDetailResult[$x][$strLowDetailColumnName]) . "</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x][$strLowDetailColumnName] . "</td>";
				}
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
*	@param  Array 	$aryHeadViewColumn 		�إå�ɽ���оݥ����̾������
*	@param  Array 	$aryDetailViewColumn 	����ɽ���оݥ����̾������
*	@param  Array 	$aryData 				�Уϣӣԥǡ�����
*	@param	Array	$aryUserAuthority		�桼�����������Ф��븢�¤����ä�����
*	@access public
*/
function fncSetStockHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
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
				if ( $aryHeadResult["lngrevisionno"] >= 0 )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\">";
					$aryHtml[] = "<a class=\"cells\" href=\"javascript:fncShowDialogCommon('/pc/result/index2.php?lngStockNo=" . $aryHeadResult["lngstockno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . ", 'detail' )\">";
					$aryHtml[] = "<img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
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
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\">";
					$aryHtml[] = "<a class=\"cells\" href=\"javascript:fncShowDialogRenew('/pc/regist/renew.php?lngStockNo=" . $aryHeadResult["lngstockno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " )\">";
					$aryHtml[] = "<img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
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
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/pc/result/index3.php?lngStockNo=" . $aryHeadResult["lngstockno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
					}
					else
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
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
							$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngReviseTotalCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/pc/result/index3.php?lngStockNo=" . $aryHeadResult["lngstockno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
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
				// �����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡס��ޤ�����оݤξ��̵�����ܥ���������Բ�
				// �ǿ�����������ǡ����ξ��������Բ�
// 2004.03.01 Suzukaze update start
				if ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
//					or ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_END and !$aryData["Admin"] ) )
// 2004.03.01 Suzukaze update end
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/pc/result/index4.php?lngStockNo=" .$aryHeadResult["lngstockno"]. "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'Invalid01' )\"><img onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" onmouseover=\"fncInvalidSmallButton( 'on' , this );\" onmouseout=\"fncInvalidSmallButton( 'off' , this );fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/invalid_small_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"INVALID\"></a></td>";
				}
			}
		}

		// ��Ͽ��
		else if ( $strColumnName == "dtmInsertDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) ) . "</td>";
		}

		// �׾���
		else if ( $strColumnName == "dtmStockAppDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", $aryHeadResult["dtmstockappdate"] ) . "</td>";
		}

		// ����NO
		else if ( $strColumnName == "strStockCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strstockcode"] . "</td>";
			// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
			if ( $aryData["Admin"] )
			{
				$aryHtml[] = "<td align=\"center\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["lngrevisionno"] . "</td>";
			}
		}

		// ȯ��NO
		else if ( $strColumnName == "strOrderCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strordercode"] . "</td>";
		}

		// ��ɼ������
		else if ( $strColumnName == "strSlipCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strslipcode"] . "</td>";
		}

		// ���ϼ�
		else if ( $strColumnName == "lngInputUserCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinputuserdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "     ";
			}
			$aryHtml[] = " " . $aryHeadResult["strinputuserdisplayname"] . "</td>";
		}

		// ������
		else if ( $strColumnName == "lngCustomerCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strcustomerdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "      ";
			}
			$aryHtml[] = " " . $aryHeadResult["strcustomerdisplayname"] . "</td>";
		}
		// ��׶��
		else if ( $strColumnName == "curTotalPrice" )
		{
			$aryHtml[] = "<td align=\"right\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryHeadResult["curtotalprice"] )
			{
				$aryHtml[] = "0.00</td>";
			}
			else
			{
				$aryHtml[] = $aryHeadResult["curtotalprice"] . "</td>";
			}
		}

		// ����
		else if ( $strColumnName == "lngStockStatusCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strstockstatusname"] . "</td>";
		}

		// ��ʧ���
		else if ( $strColumnName == "lngPayConditionCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strpayconditionname"] . "</td>";
		}

		// ȯ��ͭ��������
		else if ( $strColumnName == "dtmExpirationDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", $aryHeadResult["dtmexpirationdate"] ) . "</td>";
		}

// 2004.03.31 suzukaze update start
		// ���ٹԤν���
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
			if ( !$aryData["Admin"] and $count == 0 )
			{
				// ���ٹԤν���
				$aryDetailHtml = fncSetStockDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 0, $lngColumnCount, $objDB, $objCache );
				for ( $k = 0; $k < count($aryDetailHtml); $k++ )
				{
					$aryHtml[] = $aryDetailHtml[$k];
				}
				$count++;
			}
			// �����⡼�ɤξ�硢����̾�Τϣ�������ɽ������
			else if ( $aryData["Admin"] and $strColumnName == "strProductCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
				for ( $k = 0; $k < count($aryDetailResult); $k++ )
				{
					if ( $aryDetailResult[$k]["strproductcode"] )
					{
						$aryHtml[] = "[" . $aryDetailResult[$k]["strproductcode"] ."]";
					}
					else
					{
						$aryHtml[] = "      ";
					}
					$aryHtml[] = " " . $aryDetailResult[$k]["strproductname"] . "<br>";
				}
				$aryHtml[] = "</td>";
			}
		}

		// ����¾�ι��ܤϤ��Τޤ޽���
		else
		{
			$strLowColumnName = strtolower($strColumnName);
			$aryHtml[] = "<td align=\"left\" nowrap";
			$aryHtml[] = " rowspan=\"" . $lngDetailCount . "\">";
			if ( $strLowColumnName == "strnote" )
			{
				$aryHtml[] = nl2br($aryHeadResult[$strLowColumnName]) . "</td>";
			}
			else
			{
				$aryHtml[] = $aryHeadResult[$strLowColumnName] . "</td>";
			}
		}
	}

	$aryHtml[] = "</tr>";

	// �⤷�����ٹԤ�ʣ����¸�ߤ��Ƥ����
	if ( ( !$aryData["Admin"] ) and ( count($aryDetailResult) >= 2 ) )
	{
		// ���ٹԤν���
		$aryDetailHtml = fncSetStockDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 1, $lngColumnCount, $objDB, $objCache );
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
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// �����ܥ���ξ��ϡ�����������ǽ�ʥ桼�����Τ�ɽ������
						if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// ����ܥ���ξ��ϡ����������ǽ�ʥ桼�����Τ�ɽ������
						if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// ̵���ܥ���ξ��ϡ������⡼�ɡ������̵��������ǽ�ʥ桼�����ξ��Τ�ɽ������
						if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
					}
					// �����ȹ��ܤξ��
					else
					{
						$aryHtml[] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" ";
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
						$aryHtml[] = "onclick=\"fncSort2('" . $aryTableName[$strColumnName] . "', '" . $strSortOrder . "');\">";
						$aryHtml[] = "<a href=\"#\">".$aryTytle[$strColumnName]."</a></td>";
						// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
						if ( $aryData["Admin"] and $strColumnName == "strStockCode" )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle["lngRevisionNo"]."</td>";
						}
					}
				}
			}
			$aryHtml[] = "</tr>";
// ������̤Υ��ԡ���ǽ�б��ΰٰʲ��ιԤ򥳥��ȥ�����
//			$aryHtml[] = "</span>";

			// ���ߡ�TR
			$aryHtml[] = "<tr id=\"DummyTR\"><td colspan=\"" . count($aryViewColumn) . "\">&nbsp;</td></tr>";

// ������̤Υ��ԡ���ǽ�б��ΰٰʲ��ιԤ򥳥��ȥ�����
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
				$strDetailQuery = fncGetStockToProductSQL ( $aryDetailViewColumn, $aryResult[$i]["lngstockno"], $aryData, $objDB );

	// var_dump ( $strDetailQuery );
	// exit;

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
			$aryHtml_add = fncSetStockHeadTable ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, 1, 0, FALSE );
			for ( $j = 0; $j < count($aryHtml_add); $j++ )
			{
				$aryHtml[] = $aryHtml_add[$j];
			}
		}
// ������̽��ϡ�������end==================================

// �����⡼���Ѳ���Х���������ǡ�������start==================================
		// �����⡼�ɤξ��
		else
		{
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
					// �ǡ����ξ��֤ˤ���طʿ����ѹ�
					if ( $lngDetailCount == "" )
					{
						$lngDetailCount = 0;
					}
					if ( $arySameStockCodeResult[$j]["lngrevisionno"] < 0 )
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
					for ( $k = 0; $k < count($aryHtml_add); $k++ )
					{
						$aryHtml[] = $aryHtml_add[$k];
					}
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

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}




?>