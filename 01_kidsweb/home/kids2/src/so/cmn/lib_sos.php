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
					// ����ǡ���������оݤξ�硢�ܺ�ɽ���ܥ���������Բ�
					if ( $aryHeadResult["lngrevisionno"] >= 0 )
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngreceiveno=\"" . $aryDetailResult[$i]["lngreceiveno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// ����
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// ����ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֲ�����פξ�����ܥ���������Բ�
					if ( $aryHeadResult["lngreceivestatuscode"] == DEF_RECEIVE_PREORDER )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td>����ܥ����֤�</td>\n";
					}
				}

				// ������
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// ��Х�����¸�ߤ��ʤ����
					if ( $lngReviseTotalCount == 1 )
					{
						// ����ǡ����ξ��֤ˤ��ʬ��  //// ���֤��ֿ�����ס�Ǽ����ס�Ǽ�ʺѡס�����ѡפξ�����ܥ���������Բ�
						// �ǿ���������ǡ����ξ��������Բ�
						if (    $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_APPLICATE
							and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_DELIVER
							and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_END
							and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED
							and !$bytDeleteFlag )
						{
							$aryHtml[] = "\t<td>�����åܥ����֤�</td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					//ʣ����Х�����¸�ߤ�����
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
								$aryHtml[] = "\t<td>�����åܥ����֤�</td>\n";
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
			}
			else if ($strColumnName != "")
			{
				$TdData = "\t<td>";
				$TdDataUse = true;
				$strText = "";
				// ��Ͽ��
				if ( $strColumnName == "dtmInsertDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) );
				}

				// �׾���
				else if ( $strColumnName == "dtmReceiveAppDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmreceiveappdate"] );
				}

				// ����NO
				else if ( $strColumnName == "strReceiveCode" )
				{
					$TdData .= $aryHeadResult["strreceivecode"];
					// �����⡼�ɤξ�硡��ӥ�����ֹ��ɽ������
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
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
						$strText .= "      ";
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
				else if ( $strColumnName == "lngReceiveStatusCode" )
				{
					$TdData .= $aryHeadResult["strreceivestatusname"];
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
		// �����⡼�ɤξ�硡Ʊ���������ɤΰ����������ɽ������

		// ��Х���������̵���μ������ɤ��������
		$strSubText = strrchr( $aryResult[$i]["strreceivecode"], "-" );
		if ( $strSubText )
		{
			$strReceiveCodeBase = preg_replace( "/" . strstr( $aryResult[$i]["strreceivecode"] . "/", "-" ), "", $aryResult[$i]["strreceivecode"] );
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