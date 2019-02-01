<?
// ----------------------------------------------------------------------------
/**
*       �������  ������Ϣ�ؿ���
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
* �������ܤ�����פ���ǿ��μ���ǡ������������SQLʸ�κ����ؿ�
*
*	�������ܤ��� SQLʸ���������
*
*	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
*	@param  Array 	$arySearchColumn 		�����оݥ����̾������
*	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
*	@param  Object	$objDB       			DB���֥�������
*	@param	String	$strReceiveCode			��������	��������:������̽���	�������ɻ����:�����ѡ�Ʊ���������ɤΰ�������
*	@param	Integer	$lngReceiveNo				����Σ�	0:������̽���	����Σ�����:�����ѡ�Ʊ���������ɤȤ�������оݳ�����NO
*	@param	Boolean	$bytAdminMode			ͭ���ʺ���ǡ����μ����ѥե饰	FALSE:������̽���	TRUE:�����ѡ�����ǡ�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchReceiveSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strReceiveCode, $lngReceiveNo, $bytAdminMode )
{

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// ɽ�����ܡ������⡼�ɤβ���ӥ����ǡ���������ӡ����پ���ϸ�����̤�����

		// ��Ͽ��
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( r.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
		}

		// �׾���
		if ( $strViewColumnName == "dtmReceiveAppDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( r.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmReceiveAppDate";
		}

		// �ܵҼ����ֹ�
		if ( $strViewColumnName == "strCustomerReceiveCode" )
		{
			$arySelectQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
		}

		// ����Σ�
		if ( $strViewColumnName == "strReceiveCode" )
		{
			$arySelectQuery[] = ", r.strReceiveCode || '-' || r.strReviseCode as strReceiveCode";
		}

		// ���ϼ�
		if ( $strViewColumnName == "lngInputUserCode" )
		{
			$arySelectQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
			$arySelectQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
			$flgInputUser = TRUE;
		}

		// �ܵ�
		if ( $strViewColumnName == "lngCustomerCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
			$flgCustomerCompany = TRUE;
		}
		// ����
		if ( $strViewColumnName == "lngReceiveStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", r.lngReceiveStatusCode as lngReceiveStatusCode";
			$arySelectQuery[] = ", rs.strReceiveStatusName as strReceiveStatusName";
			$flgReceiveStatus = TRUE;
		}
		
		
		// ����ե�����
		if ( $strViewColumnName == "lngWorkFlowStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", (select strWorkflowStatusName from m_WorkflowStatus where lngWorkflowStatusCode = tw.lngWorkflowStatusCode) as lngWorkFlowStatusCode";
			$flgWorkFlowStatus = TRUE;
		}
		

		// ����
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", r.strNote as strNote";
		}

		// ��׶��
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( r.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	// 2005.11.02 ���ʬ����̵�����ʤ����嵭�롼������Ȥ߹��ޤ��ʣ�������ꤵ��Ƥ��ޤäƤ����١�������ȴ���Ф�����
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;

	// �����ɲ�
	$detailFlag = FALSE;

	// �����⡼�ɤθ�������Ʊ���������ɤΥǡ��������������
	if ( $strReceiveCode or $bytAdminMode )
	{
		// Ʊ���������ɤ��Ф��ƻ���μ����ֹ�Υǡ����Ͻ�������
		if ( $lngReceiveNo )
		{
			$aryQuery[] = " WHERE r.bytInvalidFlag = FALSE AND r.strReceiveCode = '" . $strReceiveCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// ����ǡ����������Ͼ���ɲ�
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND r.lngRevisionNo < 0";
		}
	}

	// �����⡼�ɤǤ�Ʊ���������ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
	else
	{
		// ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ�����Τ�
		$aryQuery[] = " WHERE r.bytInvalidFlag = FALSE AND r.lngRevisionNo >= 0";

		// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////����ޥ�����θ������////
			// ��Ͽ��
			if ( $strSearchColumnName == "dtmInsertDate" )
			{
				if ( $arySearchDataColumn["dtmInsertDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND r.dtmInsertDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmInsertDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
					$aryQuery[] = " AND r.dtmInsertDate <= '" . $dtmSearchDate . "'";
				}
			}
			// �׾���
			if ( $strSearchColumnName == "dtmReceiveAppDate" )
			{
				if ( $arySearchDataColumn["dtmReceiveAppDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmReceiveAppDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND r.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmReceiveAppDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmReceiveAppDateTo"] . " 23:59:59";
					$aryQuery[] = " AND r.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
				}
			}
			// �ܵҼ����ֹ�
			if ( $strSearchColumnName == "strCustomerReceiveCode" )
			{
				if ( $arySearchDataColumn["strCustomerReceiveCodeFrom"] )
				{
					$strNewCustomerReceiveCode = $arySearchDataColumn["strCustomerReceiveCodeFrom"];
					$aryQuery[] = " AND r.strCustomerReceiveCode >= '" . $strNewCustomerReceiveCode . "'";

				}
				if ( $arySearchDataColumn["strCustomerReceiveCodeTo"] )
				{
					$strNewCustomerReceiveCode = $arySearchDataColumn["strCustomerReceiveCodeTo"];
					$aryQuery[] = " AND r.strCustomerReceiveCode <= '" . $strNewCustomerReceiveCode . "'";
				}
			}
			// ����Σ�
			if ( $strSearchColumnName == "strReceiveCode" )
			{
				if ( $arySearchDataColumn["strReceiveCodeFrom"] )
				{
					if ( strpos($arySearchDataColumn["strReceiveCodeFrom"], "-") )
					{
						// ��Х����������դμ���Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
						$strNewReceiveCode = ereg_replace( strrchr( $arySearchDataColumn["strReceiveCodeFrom"], "-" ), "", $arySearchDataColumn["strReceiveCodeFrom"] );
					}
					else
					{
						$strNewReceiveCode = $arySearchDataColumn["strReceiveCodeFrom"];
					}
					$aryQuery[] = " AND r.strReceiveCode >= '" . $strNewReceiveCode . "'";

				}
				if ( $arySearchDataColumn["strReceiveCodeTo"] )
				{
					if ( strpos($arySearchDataColumn["strReceiveCodeTo"], "-") )
					{
						// ��Х����������դμ���Σ�Υ�Х��������ɤϸ�����̤ǤϺǿ��Ǥ�ɽ�����뤿�ᡢ̵�뤹��
						$strNewReceiveCode = ereg_replace( strrchr( $arySearchDataColumn["strReceiveCodeTo"], "-" ), "", $arySearchDataColumn["strReceiveCodeTo"] );
					}
					else
					{
						$strNewReceiveCode = $arySearchDataColumn["strReceiveCodeTo"];
					}
					$aryQuery[] = " AND r.strReceiveCode <= '" . $strNewReceiveCode . "'";
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
			// �ܵ�
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
			/*
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if ( $arySearchDataColumn["lngInChargeGroupCode"] )
				{
					$aryQuery[] = " AND inchg_g.strGroupDisplayCode ~* '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
					$flgInChargeGroup = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeGroupName"] )
				{
					$aryQuery[] = " AND UPPER(inchg_g.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
					$flgInChargeGroup = TRUE;
				}
			}
			*/
			// ô����
			/*
			if ( $strSearchColumnName == "lngInChargeUserCode" )
			{
				if ( $arySearchDataColumn["lngInChargeUserCode"] )
				{
					$aryQuery[] = " AND inchg_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
					$flgInChargeUser = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeUserName"] )
				{
					$aryQuery[] = " AND UPPER(inchg_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
					$flgInChargeUser = TRUE;
				}
			}
			*/

			// ����
			if ( $strSearchColumnName == "lngReceiveStatusCode" )
			{
				if ( $arySearchDataColumn["lngReceiveStatusCode"] )
				{
					// ������֤� ","���ڤ��ʸ����Ȥ����Ϥ����
					//$arySearchStatus = explode( ",", $arySearchDataColumn["lngReceiveStatusCode"] );
					// �����å��ܥå������ˤ�ꡢ����򤽤Τޤ�����
					$arySearchStatus = $arySearchDataColumn["lngReceiveStatusCode"];
					
					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND ( ";
						// ������֤�ʣ�����ꤵ��Ƥ����ǽ��������Τǡ�����Ŀ�ʬ�롼��
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// ������
							if ( $j <> 0 )
							{
								$aryQuery[] = " OR ";
							}
							$aryQuery[] = "r.lngReceiveStatusCode = " . $arySearchStatus[$j] . "";
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

			//
			// ���٥ơ��֥�ξ��
			//
//			$strDetailFrom1 = ", (SELECT distinct on ( rd1.lngReceiveNo ) rd1.lngReceiveNo FROM t_ReceiveDetail rd1 WHERE";
			$strDetailFrom2 = ", (SELECT distinct on ( rd1.lngReceiveNo ) rd1.lngReceiveNo, mg.strGroupDisplayCode, mg.strGroupDisplayName, mu.struserdisplaycode, mu.struserdisplayname FROM t_ReceiveDetail rd1 "
							."LEFT JOIN m_Product p ON rd1.strProductCode = p.strProductCode "
							."left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode "
							."left join m_user  mu on p.lnginchargeusercode = mu.lngusercode WHERE ";
			
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
					$aryDetailWhereQuery[] = "rd1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
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
					$aryDetailWhereQuery[] = "rd1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
			// ����
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{

				if( $arySearchDataColumn["lngInChargeGroupCode"] || $strSearchColumnName == "lngInChargeUserCode")
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
				if( $arySearchDataColumn["lngInChargeUserCode"] ||  $arySearchDataColumn["strInChargeUserName"])
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
					$aryDetailWhereQuery[] = "rd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
					$detailFlag = TRUE;
				}
			}
			
//20170719kou�ɲ�
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
//20170719kou�ɲá�END
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
					$aryDetailWhereQuery[] = "rd1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
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
					$aryDetailWhereQuery[] = "rd1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}

	// ���ٸ����ѥơ��֥�����
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( rd1.lngReceiveNo ) rd1.lngReceiveNo";
	$aryDetailFrom[] = "	,rd1.lngReceiveDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,rd1.lngSalesClassCode";
	$aryDetailFrom[] = "	,p.strGoodsCode";
	$aryDetailFrom[] = "	,rd1.dtmDeliveryDate";		// Ǽ��
	$aryDetailFrom[] = "	,rd1.curProductPrice";		// ñ��
	$aryDetailFrom[] = "	,rd1.lngProductUnitCode";	// ñ��
	$aryDetailFrom[] = "	,rd1.lngProductQuantity";	// ���ʿ���
	$aryDetailFrom[] = "	,rd1.curSubTotalPrice";		// ��ȴ���
	$aryDetailFrom[] = "	,rd1.lngTaxClassCode";		// �Ƕ�ʬ
	$aryDetailFrom[] = "	,mt.curTax";				// ��Ψ
	$aryDetailFrom[] = "	,rd1.curTaxPrice";			// �ǳ�
	$aryDetailFrom[] = "	,rd1.strNote";				// ��������
	$aryDetailFrom[] = "	FROM t_ReceiveDetail rd1";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON rd1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = rd1.lngtaxcode ";

	$aryDetailWhereQuery[] = ") as rd";
	// where������ٹԡ� �����꡼Ϣ��
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// ���ٹԤξ�郎¸�ߤ�����
	if ( $detailFlag )
	{
		// where������ٹԡ� �����꡼Ϣ��
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";


	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT r.lngReceiveNo as lngReceiveNo";
	$aryOutQuery[] = "	,r.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,r.strReceiveCode as strReceiveCode";
	$aryOutQuery[] = "	,r.lngReceiveStatusCode as lngReceiveStatusCode";

	// ���ٹԤ� 'order by' �Ѥ��ɲ�
	$aryOutQuery[] = "	,rd.lngReceiveDetailNo";
	$aryOutQuery[] = "	,rd.strProductCode";
	$aryOutQuery[] = "	,rd.strGroupDisplayCode";
	$aryOutQuery[] = "	,rd.strUserDisplayCode";
	$aryOutQuery[] = "	,rd.strProductName";
	$aryOutQuery[] = "	,rd.strProductEnglishName";
	$aryOutQuery[] = "	,rd.lngSalesClassCode";
	$aryOutQuery[] = "	,rd.strGoodsCode";
	$aryOutQuery[] = "	,rd.dtmDeliveryDate";
	$aryOutQuery[] = "	,rd.curProductPrice";
	$aryOutQuery[] = "	,rd.lngProductUnitCode";
	$aryOutQuery[] = "	,rd.lngProductQuantity";
	$aryOutQuery[] = "	,rd.curSubTotalPrice";
	$aryOutQuery[] = "	,rd.lngTaxClassCode";
	$aryOutQuery[] = "	,rd.curTax";
	$aryOutQuery[] = "	,rd.curTaxPrice";
	$aryOutQuery[] = "	,rd.strNote";

	// select�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $arySelectQuery) . "\n";

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Receive r";
	
	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON r.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	if ( $flgReceiveStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_ReceiveStatus rs USING (lngReceiveStatusCode)";
	}
	if ( $flgPayCondition )
	{
		$aryFromQuery[] = " LEFT JOIN m_PayCondition pc ON r.lngPayConditionCode = pc.lngPayConditionCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(r.lngReceiveNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_SO1; // ������Ͽ����WF�ǡ������оݤˤ���٤˾�����
	}
	
	// From�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = $strDetailQuery;
	
	// Where�� �����꡼Ϣ��
	$aryOutQuery[] = implode("\n", $aryQuery);
	
	// ���ٹ��Ѥξ��Ϣ��
	$aryOutQuery[] = " AND rd.lngReceiveNo = r.lngReceiveNo";


	/////////////////////////////////////////////////////////////
	//// �ǿ�����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢////
	//// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��       ////
	//// Ʊ���������ɤ���ĥǡ�����̵������ǡ���          ////
	/////////////////////////////////////////////////////////////
	// �������ɤ����ꤵ��Ƥ��ʤ����ϸ����������ꤹ��
	if ( !$strReceiveCode )
	{
		$aryOutQuery[] = " AND r.lngRevisionNo = ( "
			. "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode AND r1.bytInvalidFlag = false";
		$aryOutQuery[] = " AND r1.strReviseCode = ( "
			. "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r1.strReceiveCode AND r2.bytInvalidFlag = false ) )";

		// �����⡼�ɤξ��Ϻ���ǡ����⸡���оݤȤ��뤿��ʲ��ξ����оݳ�
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( r3.lngRevisionNo ) FROM m_Receive r3 WHERE r3.bytInvalidFlag = false AND r3.strReceiveCode = r.strReceiveCode )";
		}
	}

	// �����⡼�ɤθ�������Ʊ���������ɤΥǡ��������������
	if ( $strReceiveCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY r.lngRevisionNo < 0 DESC, r.strReviseCode DESC, r.lngRevisionNo DESC";
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
			case "strReceiveCode":
			case "strCustomerReceiveCode":
			case "lngReceiveStatusCode":
			case "strNote":
			case "curTotalPrice":
				$aryOutQuery[] = " ORDER BY r." . $arySearchDataColumn["strSort"] . " " . $strAsDs . " , lngReceiveNo DESC";
				break;
			case "lngWorkFlowStatusCode":
				$aryOutQuery[] = " ORDER BY lngWorkFlowStatusCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmReceiveAppDate" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngReceiveDetailNo":	// ���ٹ��ֹ�
				$aryOutQuery[] = " ORDER BY rd.lngReceiveDetailNo" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strProductCode":		// ���ʥ�����
				$aryOutQuery[] = " ORDER BY rd.strProductCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngGroupCode":		// ����
				$aryOutQuery[] = " ORDER BY rd.strGroupDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngUserCode":			// ô����
				$aryOutQuery[] = " ORDER BY rd.strUserDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strProductName":		// ����̾��
				$aryOutQuery[] = " ORDER BY rd.strProductName" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strProductEnglishName":	// ���ʱѸ�̾��
				$aryOutQuery[] = " ORDER BY rd.strProductEnglishName" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngSalesClassCode":	// ����ʬ
				$aryOutQuery[] = " ORDER BY rd.lngSalesClassCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strGoodsCode":		// �ܵ�����
				$aryOutQuery[] = " ORDER BY rd.strGoodsCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "dtmDeliveryDate":		// Ǽ��
				$aryOutQuery[] = " ORDER BY rd.dtmDeliveryDate" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "curProductPrice":		// ñ��
				$aryOutQuery[] = " ORDER BY rd.curProductPrice" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngProductUnitCode":	// ñ��
				$aryOutQuery[] = " ORDER BY rd.lngProductUnitCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngProductQuantity":	// ����
				$aryOutQuery[] = " ORDER BY rd.lngProductQuantity" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "curSubTotalPrice":	// ��ȴ���
				$aryOutQuery[] = " ORDER BY rd.curSubTotalPrice" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strDetailNote":		// ��������
				$aryOutQuery[] = " ORDER BY rd.strNote" . $strAsDs . ", lngReceiveNo DESC";
				break;
			default:
				$aryOutQuery[] = " ORDER BY lngReceiveNo DESC";
		}

	}

//fncDebug( 'lib_sos.txt', implode("\n", $aryOutQuery), __FILE__, __LINE__);
//fncDebug( 'lib_sos.txt', $arySearchDataColumn["strSort"], __FILE__, __LINE__);

	return implode("\n", $aryOutQuery);
}






/**
* �б��������NO�Υǡ������Ф������ٹԤ��������SQLʸ�κ����ؿ�
*
*	����NO�������٤�������� SQLʸ���������
*
*	@param  Array 	$aryDetailViewColumn 	ɽ���о����٥����̾������
*	@param  String 	$lngReceiveNo 			�оݼ���NO
*	@param  Array 	$aryData 				POST�ǡ���������
*	@param  Object	$objDB       			DB���֥�������
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetReceiveToProductSQL ( $aryDetailViewColumn, $lngReceiveNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];

		// ɽ�����ܡ�
// 2004.03.31 suzukaze update start
		// ���ʥ�����
		if ( $strViewColumnName == "strProductCode" )
		{
			$arySelectQuery[] = ", rd.strProductCode as strProductCode";
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
// 2004.03.31 suzukaze update end
		// ����ʬ
		if ( $strViewColumnName == "lngSalesClassCode" )
		{
			$arySelectQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode";
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
			$arySelectQuery[] = ", to_char( rd.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
		}
		// ñ��
		if ( $strViewColumnName == "curProductPrice" )
		{
// 2004.03.17 suzukaze update start
			$arySelectQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
//			$arySelectQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.99' )  as curProductPrice\n";
// 2004.03.17 suzukaze update end
		}
		// ñ��
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}
		// ����
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( rd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}
		// ��ȴ���
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}
		// ��������
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", rd.strNote as strDetailNote";
		}
	}

	// ���о�� �оݼ���NO�λ���
	$aryQuery[] = " WHERE rd.lngReceiveNo = " . $lngReceiveNo . "";

	// �����ɲ�

	// ////����ޥ�����θ������////
	// SQLʸ�κ���
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT rd.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,rd.lngReceiveNo as lngReceiveNo";
	$aryOutQuery[] = "	,rd.lngRevisionNo as lngRevisionNo";


	// select�� �����꡼Ϣ��
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From�� ������
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_ReceiveDetail rd";

	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
	$aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
	$aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";

	if ( $flgSalesClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
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
			$aryOutQuery[] = " ORDER BY rd.strNote " . $strAsDs . ", rd.lngSortKey ASC";
			break;
		case "lngReceiveDetailNo":
			$aryOutQuery[] = " ORDER BY rd.lngSortKey " . $strAsDs;
			break;
		case "strProductName":
		case "strProductEnglishName":
		case "strGoodsCode":
			$aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", rd.lngSortKey ASC";
			break;
		default:
			$aryOutQuery[] = " ORDER BY rd.lngSortKey ASC";
			break;
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
function fncSetReceiveDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, $lngMode, $lngColumnCount, $objDB, $objCache )
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
// 2004.03.31 suzukaze update end

			// ����ʬ
			else if ( $strDetailColumnName == "lngSalesClassCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["lngsalesclasscode"] )
				{
					$aryHtml[] = "[" . $aryDetailResult[$x]["lngsalesclasscode"] ."]";
				}
				else
				{
					$aryHtml[] = "      ";
				}
				$aryHtml[] = " " . $aryDetailResult[$x]["strsalesclassname"] . "</td>";
			}

			// Ǽ��
			else if ( $strDetailColumnName == "dtmDeliveryDate" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				$aryHtml[] = str_replace( "-", "/", $aryDetailResult[$x]["dtmdeliverydate"] ) . "</td>";
			}

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
function fncSetReceiveHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
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
				// ����ǡ���������оݤξ�硢�ܺ�ɽ���ܥ���������Բ�
				if ( $aryHeadResult["lngrevisionno"] >= 0 )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index2.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . ", 'detail' )\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
			}

			// ����
			if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
			{
				// ����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡס��ޤ�����оݤξ�罤���ܥ���������Բ�
				// �ǿ���������ǡ����ξ��������Բ�
				// Ǽ�ʺѤǴ����⡼�ɤ�̵�����������Բ�
				if ( $aryHeadResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED 
					or ( $aryHeadResult["lngreceivestatuscode"] == DEF_RECEIVE_END and !$aryData["Admin"] ) 
					or $aryHeadResult["lngrevisionno"] < 0 
					or $bytDeleteFlag )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/so/regist/renew.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " )\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				}
			}

			// ���
			if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
			{
				// �����⡼�ɤ�̵�����⤷���ϥ�Х�����¸�ߤ��ʤ����
				if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
				{
					// ����ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
					// �ǿ���������ǡ����ξ��������Բ�
					if (    $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_APPLICATE
						and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_DELIVER
						and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_END
						and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED
						and !$bytDeleteFlag )
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index3.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
					}
					else
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
					}
				}
				// �����⡼�ɤ�ʣ����Х�����¸�ߤ�����
				else
				{
					// �ǿ�����ξ��
					if ( $lngReviseCount == 0 )
					{
						// ����ǡ����ξ��֤ˤ��ʬ��  //// ���֤�������ѡװʳ�
						// �ǿ���������ǡ����ξ��������Բ�
						if ( $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED 
							and !$bytDeleteFlag )
						{
							$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngReviseTotalCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index3.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
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
				// ����ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ��̵�����ܥ���������Բ�
				// �嵭���˲ä����оݼ�������ǡ����ξ��������Բ�
				if (    $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_APPLICATE
					and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_DELIVER
					and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_END
					and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED
					and $aryHeadResult["lngrevisionno"] >= 0 )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index4.php?lngReceiveNo=" .$aryHeadResult["lngreceiveno"]. "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'Invalid01' )\"><img onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" onmouseover=\"fncInvalidSmallButton( 'on' , this );\" onmouseout=\"fncInvalidSmallButton( 'off' , this );fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/invalid_small_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"INVALID\"></a></td>";
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
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) ) . "</td>";
		}

		// �׾���
		else if ( $strColumnName == "dtmReceiveAppDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", $aryHeadResult["dtmreceiveappdate"] ) . "</td>";
		}

		// ����NO
		else if ( $strColumnName == "strReceiveCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strreceivecode"] . "</td>";
			// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
			if ( $aryData["Admin"] )
			{
				$aryHtml[] = "<td align=\"center\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["lngrevisionno"] . "</td>";
			}
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

		// �ܵ�
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
		
/*
		// ����
		else if ( $strColumnName == "lngInChargeGroupCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinchargegroupdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "    ";
			}
			$aryHtml[] = " " . $aryHeadResult["strinchargegroupdisplayname"] . "</td>";
		}

		// ô����
		else if ( $strColumnName == "lngInChargeUserCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinchargeuserdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "     ";
			}
			$aryHtml[] = " " . $aryHeadResult["strinchargeuserdisplayname"] . "</td>";
		}
*/

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
		else if ( $strColumnName == "lngReceiveStatusCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strreceivestatusname"] . "</td>";
		}
		// ���ٹԤν���
		else if ( $strColumnName == "strProductCode"
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" 
			or $strColumnName == "lngSalesClassCode" or $strColumnName == "strGoodsCode"
			or $strColumnName == "dtmDeliveryDate" or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode"
			or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" )
		{
			if ( !$aryData["Admin"] and $count == 0 )
			{
				// ���ٹԤν���
				$aryDetailHtml = fncSetReceiveDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 0, $lngColumnCount, $objDB, $objCache );
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
		$aryDetailHtml = fncSetReceiveDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 1, $lngColumnCount, $objDB, $objCache );
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
*	@param  Object	$objCache       		DB���֥�������
*	@access public
*/
function fncSetReceiveTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" 
			or $strColumnName == "lngSalesClassCode" or $strColumnName == "strGoodsCode"
			or $strColumnName == "dtmDeliveryDate" or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode"
			or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" )
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
						if ( $aryData["Admin"] and $strColumnName == "strReceiveCode" )
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
				$strDetailQuery = fncGetReceiveToProductSQL( $aryDetailViewColumn, $aryResult[$i]["lngreceiveno"], $aryData, $objDB );
//fncDebug('lib_sos.txt', $strDetailQuery, __FILE__, __LINE__);
				// �����꡼�¹�
				if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
				{
					$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
			$aryHtml_add = fncSetReceiveHeadTable ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, 1, 0, FALSE );
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
			// �����⡼�ɤξ�硡Ʊ���������ɤΰ����������ɽ������

			// ��Х���������̵���μ������ɤ��������
			$strSubText = strrchr( $aryResult[$i]["strreceivecode"], "-" );
			if ( $strSubText )
			{
				$strReceiveCodeBase = ereg_replace( $strSubText, "", $aryResult[$i]["strreceivecode"] );
			}
			else
			{
				$strReceiveCodeBase = $aryResult[$i]["strreceivecode"];
			}

			$strSameReceiveCodeQuery = fncGetSearchReceiveSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strReceiveCodeBase, $aryResult[$i]["lngreceiveno"], FALSE );

			// �ͤ�Ȥ� =====================================
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameReceiveCodeQuery, $objDB );

			// ����Υ��ꥢ
			unset( $arySameReceiveCodeResult );

			if ( $lngResultNum )
			{
				for ( $j = 0; $j < $lngResultNum; $j++ )
				{
					$arySameReceiveCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
				}
				$lngSameReceiveCount = $lngResultNum;
			}
			$objDB->freeResult( $lngResultID );

			// Ʊ���������ɤǤβ���Х����ǡ�����¸�ߤ����
			if ( $lngResultNum )
			{
				for ( $j = 0; $j < $lngSameReceiveCount; $j++ )
				{
					// ���������ʬ������

					reset( $arySameReceiveCodeResult[$j] );

					// ���ٽ����Ѥ�Ĵ��
					$lngDetailViewCount = count( $aryDetailViewColumn );

					if ( $lngDetailViewCount )
					{
						// ���ٹԿ���Ĵ��
						$strDetailQuery = fncGetReceiveToProductSQL ( $aryDetailViewColumn, $arySameReceiveCodeResult[$j]["lngreceiveno"], $aryData, $objDB );

						// �����꡼�¹�
						if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
						{
							$strMessage = fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
					if ( $arySameReceiveCodeResult[$j]["lngrevisionno"] < 0 )
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

					// Ʊ�������ɤμ���ǡ����ǰ��־��ɽ������Ƥ������ǡ���������ǡ����ξ��
					if ( $arySameReceiveCodeResult[0]["lngrevisionno"] < 0 )
					{
						$bytDeleteFlag = TRUE;
					}
					else
					{
						$bytDeleteFlag = FALSE;
					}

					// ���쥳����ʬ�ν���
					$aryHtml_add = fncSetReceiveHeadTable ( $lngColumnCount, $arySameReceiveCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameReceiveCount, $j, $bytDeleteFlag );
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