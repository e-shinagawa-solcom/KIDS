<?

// ----------------------------------------------------------------------------
/**
*       ���ʡ�������Ϣ�ؿ���
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
*		������̴�Ϣ�δؿ�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



/**
* �������ܤ�����פ���ǿ��ξ��ʥǡ������������SQLʸ�κ����ؿ�
*
*	�������ܤ��� SQLʸ���������
*
*	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
*	@param  Array 	$arySearchColumn 		�����оݥ����̾������
*	@param  Array 	$arySearchDataColumn 	�������Ƥ�����
*	@param  Object	$objDB       			DB���֥�������
*	@param	Array	$aryUserAuthority		�桼�����θ��¾��������ʾ��ʴ������Ф��븢�¾����
*	@return Array 	$strSQL ������SQLʸ OR Boolean FALSE
*	@access public
*/
function fncGetSearchProductSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $aryUserAuthority )
{

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// ɽ������

		// ��������
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( p.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate\n";
		}
		// ���ʹԾ���
		if ( $strViewColumnName == "lngGoodsPlanProgressCode" )
		{
			$arySelectQuery[] = ", t_gp.lngGoodsPlanProgressCode as lngGoodsPlanProgressCode\n";
			$flgT_GoodsPlan = TRUE;
		}
		// ��������
		if ( $strViewColumnName == "dtmRevisionDate" )
		{
			$arySelectQuery[] = ", to_char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmRevisionDate\n";
		}
		// ���ʥ�����
		if ( $strViewColumnName == "strProductCode" )
		{
			$arySelectQuery[] = ", p.strProductCode as strProductCode\n";
		}
		// ����̾��
		if ( $strViewColumnName == "strProductName" )
		{
			$arySelectQuery[] = ", p.strProductName as strProductName\n";
		}
		// ����̾�ΡʱѸ��
		if ( $strViewColumnName == "strProductEnglishName" )
		{
			$arySelectQuery[] = ", p.strProductEnglishName as strProductEnglishName\n";
		}
		// ���ϼ�
		if ( $strViewColumnName == "lngInputUserCode" )
		{
			$arySelectQuery[] = ", p.lngInputUserCode as lngInputUserCode\n";
			$arySelectQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode\n";
			$arySelectQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName\n";
			$flgInputUser = TRUE;
		}
		// ����
		if ( $strViewColumnName == "lngInChargeGroupCode" )
		{
			$arySelectQuery[] = ", p.lngInChargeGroupCode as lngInChargeGroupCode\n";
			$arySelectQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode\n";
			$arySelectQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName\n";
			$flgInChargeGroup = TRUE;
		}
		// ô����
		if ( $strViewColumnName == "lngInChargeUserCode" )
		{
			$arySelectQuery[] = ", p.lngInChargeUserCode as lngInChargeUserCode\n";
			$arySelectQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode\n";
			$arySelectQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName\n";
			$flgInChargeUser = TRUE;
		}
		// ���ƥ��꡼
		if ( $strViewColumnName == "lngCategoryCode" )
		{
			$arySelectQuery[] = ", mc.strCategoryName as lngCategoryCode\n";
			$flgCategory = TRUE;
		}
		// �ܵ�����
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode\n";
		}
		// ����̾��
		if ( $strViewColumnName == "strGoodsName" )
		{
			$arySelectQuery[] = ", p.strGoodsName as strGoodsName\n";
		}
		// �ܵ�
		if ( $strViewColumnName == "lngCustomerCompanyCode" )
		{
			$arySelectQuery[] = ", p.lngCustomerCompanyCode as lngCustomerCompanyCode\n";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerCompanyDisplayCode\n";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerCompanyDisplayName\n";
			$flgCustomerCompany = TRUE;
		}
		// �ܵ�ô����
		if ( $strViewColumnName == "lngCustomerUserCode" )
		{
			$arySelectQuery[] = ", p.lngCustomerUserCode as lngCustomerUserCode\n";
			$arySelectQuery[] = ", cust_u.strUserDisplayCode as strCustomerUserDisplayCode\n";
			$arySelectQuery[] = ", cust_u.strUserDisplayName as strCustomerUserDisplayName\n";
			$arySelectQuery[] = ", p.strCustomerUserName as strCustomerUserName\n";
			$flgCustomerUser = TRUE;
		}
		// �ٻ�ñ��
		if ( $strViewColumnName == "lngPackingUnitCode" )
		{
			$arySelectQuery[] = ", p.lngPackingUnitCode as lngPackingUnitCode\n";
			$arySelectQuery[] = ", packingunit.strProductUnitName as strPackingUnitName\n";
			$flgPackingUnit = TRUE;
		}
		// ����ñ��
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", p.lngProductUnitCode as lngProductUnitCode\n";
			$arySelectQuery[] = ", productunit.strProductUnitName as strProductUnitName\n";
			$flgProductUnit = TRUE;
		}
		// ���ʷ���
		if ( $strViewColumnName == "lngProductFormCode" )
		{
			$arySelectQuery[] = ", p.lngProductFormCode as lngProductFormCode\n";
			$arySelectQuery[] = ", productform.strProductFormName as strProductFormName\n";
			$flgProductForm = TRUE;
		}
		// ��Ȣ���ޡ�����
		if ( $strViewColumnName == "lngBoxQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngBoxQuantity, '9,999,999,990' ) as lngBoxQuantity\n";
		}
		// �����ȥ�����
		if ( $strViewColumnName == "lngCartonQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngCartonQuantity, '9,999,999,990' ) as lngCartonQuantity\n";
		}
		// ����ͽ���
		if ( $strViewColumnName == "lngProductionQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngProductionQuantity, '9,999,999,990' ) as lngProductionQuantity\n";
			$arySelectQuery[] = ", p.lngProductionUnitCode as lngProductionUnitCode\n";
		}
		// ���Ǽ�ʿ�
		if ( $strViewColumnName == "lngFirstDeliveryQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngFirstDeliveryQuantity, '9,999,999,990' ) as lngFirstDeliveryQuantity\n";
			$arySelectQuery[] = ", p.lngFirstDeliveryUnitCode as lngFirstDeliveryUnitCode\n";
		}
		// ��������
		if ( $strViewColumnName == "lngFactoryCode" )
		{
			$arySelectQuery[] = ", p.lngFactoryCode as lngFactoryCode\n";
			$arySelectQuery[] = ", fact_c.strCompanyDisplayCode as strFactoryDisplayCode\n";
			$arySelectQuery[] = ", fact_c.strCompanyDisplayName as strFactoryDisplayName\n";
			$flgFactory = TRUE;
		}
		// ���å���֥깩��
		if ( $strViewColumnName == "lngAssemblyFactoryCode" )
		{
			$arySelectQuery[] = ", p.lngAssemblyFactoryCode as lngAssemblyFactoryCode\n";
			$arySelectQuery[] = ", assemfact_c.strCompanyDisplayCode as strAssemblyFactoryDisplayCode\n";
			$arySelectQuery[] = ", assemfact_c.strCompanyDisplayName as strAssemblyFactoryDisplayName\n";
			$flgAssemblyFactory = TRUE;
		}
		// Ǽ�ʾ��
		if ( $strViewColumnName == "lngDeliveryPlaceCode" )
		{
			$arySelectQuery[] = ", p.lngDeliveryPlaceCode as lngDeliveryPlaceCode\n";
			$arySelectQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryPlaceDisplayCode\n";
			$arySelectQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryPlaceDisplayName\n";
			$flgDeliveryPlace = TRUE;
		}
		// Ǽ��
		if ( $strViewColumnName == "dtmDeliveryLimitDate" )
		{
			$arySelectQuery[] = ", to_char( p.dtmDeliveryLimitDate, 'YYYY/MM' ) as dtmDeliveryLimitDate";
		}
		// Ǽ��
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( p.curProductPrice, '9,999,999,990.99' )  as curProductPrice\n";
		}
		// ����
		if ( $strViewColumnName == "curRetailPrice" )
		{
			$arySelectQuery[] = ", To_char( p.curRetailPrice, '9,999,999,990.99' )  as curRetailPrice\n";
		}
		// �о�ǯ��
		if ( $strViewColumnName == "lngTargetAgeCode" )
		{
			$arySelectQuery[] = ", p.lngTargetAgeCode as lngTargetAgeCode\n";
			$arySelectQuery[] = ", targetage.strTargetAgeName as strTargetAgeName\n";
			$flgTargetAge = TRUE;
		}
		// �����ƥ�
		if ( $strViewColumnName == "lngRoyalty" )
		{
			$arySelectQuery[] = ", To_char( p.lngRoyalty, '9,999,999,990.99' )  as lngRoyalty\n";
		}
		// �ڻ�
		if ( $strViewColumnName == "lngCertificateClassCode" )
		{
			$arySelectQuery[] = ", p.lngCertificateClassCode as lngCertificateClassCode\n";
			$arySelectQuery[] = ", certificate.strCertificateClassName as strCertificateClassName\n";
			$flgCertificateClass = TRUE;
		}
		// �Ǹ���
		if ( $strViewColumnName == "lngCopyrightCode" )
		{
			$arySelectQuery[] = ", p.lngCopyrightCode as lngCopyrightCode\n";
			$arySelectQuery[] = ", copyright.strCopyrightName as strCopyrightName\n";
			$flgCopyright = TRUE;
		}
		// �Ǹ�������
		if ( $strViewColumnName == "strCopyrightNote" )
		{
			$arySelectQuery[] = ", p.strCopyrightNote as strCopyrightNote\n";
		}
		// �Ǹ�ɽ���ʹ����
		if ( $strViewColumnName == "strCopyrightDisplayStamp" )
		{
			$arySelectQuery[] = ", p.strCopyrightDisplayStamp as strCopyrightDisplayStamp\n";
		}
		// �Ǹ�ɽ���ʰ���ʪ��
		if ( $strViewColumnName == "strCopyrightDisplayPrint" )
		{
			$arySelectQuery[] = ", p.strCopyrightDisplayPrint as strCopyrightDisplayPrint\n";
		}
		// ���ʹ���
		if ( $strViewColumnName == "strProductComposition" )
		{
			$arySelectQuery[] = ", p.strProductComposition as strProductComposition\n";
		}
		// ���å���֥�����
		if ( $strViewColumnName == "strAssemblyContents" )
		{
			$arySelectQuery[] = ", p.strAssemblyContents as strAssemblyContents\n";
		}
		// ���;ܺ�
		if ( $strViewColumnName == "strSpecificationDetails" )
		{
			$arySelectQuery[] = ", p.strSpecificationDetails as strSpecificationDetails\n";
		}
		
		// ����ե�����
		if ( $strViewColumnName == "lngWorkFlowStatusCode" )
		{
			$arySelectQuery[] = ", (select strWorkflowStatusName from m_WorkflowStatus where lngWorkflowStatusCode = tw.lngWorkflowStatusCode) as lngWorkFlowStatusCode";
			$arySelectQuery[] = ",lngproductstatuscode";
			$flgWorkFlowStatus = TRUE;
		}
	}

	// �����ɲ�
	$detailFlag = FALSE;

	// ������桼�����θ��¤ˤ�������줿���ʤ�ɽ����ɽ�����ڤ��ؤ���
	if ( !$aryUserAuthority["SearchDelete"] )
	{
		$aryQuery[] = " WHERE p.bytInvalidFlag = FALSE\n";
	}
	else
	{
		$aryQuery[] = " WHERE p.lngProductNo >= 0\n";
	}

	// ɽ���ѥ��������ꤵ��Ƥ������Ƥ򸡺��Ѥ�ʸ��������
	for ( $i = 0; $i < count($arySearchColumn); $i++ )
	{
		$strSearchColumnName = $arySearchColumn[$i];

		// ////���ʥޥ�����θ������////
		// ���ʥ�����
		if ( $strSearchColumnName == "strProductCode" )
		{
			if ( $arySearchDataColumn["strProductCodeFrom"] )
			{
				$strNewProductCode = $arySearchDataColumn["strProductCodeFrom"];
				$aryQuery[] = " AND p.strProductCode >= '" . $strNewProductCode . "'\n";
			}
			if ( $arySearchDataColumn["strProductCodeTo"] )
			{
				$strNewProductCode = $arySearchDataColumn["strProductCodeTo"];
				$aryQuery[] = " AND p.strProductCode <= '" . $strNewProductCode . "'\n";
			}
		}
// 2004.04.12 suzukaze update start
		// ����̾��
		if ( $strSearchColumnName == "strProductName" )
		{
			if ( $arySearchDataColumn["strProductName"] )
			{
				$aryQuery[] = " AND UPPER(p.strProductName) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%')\n";
			}
		}
		// ����̾�ΡʱѸ��
		if ( $strSearchColumnName == "strProductEnglishName" )
		{
			if ( $arySearchDataColumn["strProductEnglishName"] )
			{
				$aryQuery[] = " AND UPPER(p.strProductEnglishName) LIKE UPPER('%" . $arySearchDataColumn["strProductEnglishName"] . "%')\n";
			}
		}
		// ���ϼ�
		if ( $strSearchColumnName == "lngInputUserCode" )
		{
			if ( $arySearchDataColumn["lngInputUserCode"] )
			{
				$aryQuery[] = " AND input_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'\n";
				$flgInputUser = TRUE;
			}
			if ( $arySearchDataColumn["strInputUserName"] )
			{
				$aryQuery[] = " AND UPPER(input_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')\n";
				$flgInputUser = TRUE;
			}
		}
		// ����
		if ( $strSearchColumnName == "lngInChargeGroupCode" )
		{
			if ( $arySearchDataColumn["lngInChargeGroupCode"] )
			{
				$aryQuery[] = " AND inchg_g.strGroupDisplayCode ~* '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'\n";
				$flgInChargeGroup = TRUE;
			}
			if ( $arySearchDataColumn["strInChargeGroupName"] )
			{
				$aryQuery[] = " AND UPPER(inchg_g.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')\n";
				$flgInChargeGroup = TRUE;
			}
		}
		// ô����
		if ( $strSearchColumnName == "lngInChargeUserCode" )
		{
			if ( $arySearchDataColumn["lngInChargeUserCode"] )
			{
				$aryQuery[] = " AND inchg_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInChargeUserCode"] . "'\n";
				$flgInChargeUser = TRUE;
			}
			if ( $arySearchDataColumn["strInChargeUserName"] )
			{
				$aryQuery[] = " AND UPPER(inchg_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')\n";
				$flgInChargeUser = TRUE;
			}
		}
		// ���ƥ��꡼
		if ( $strSearchColumnName == "lngCategoryCode" )
		{
			if ( $arySearchDataColumn["lngCategoryCode"] )
			{
				$aryQuery[] = " AND p.lngCategoryCode = " . $arySearchDataColumn["lngCategoryCode"]. "\n";
				$flgCategory = TRUE;
			}
		}
		// �ܵ�����
		if ( $strSearchColumnName == "strGoodsCode" )
		{
			if ( $arySearchDataColumn["strGoodsCode"] )
			{
				$aryQuery[] = " AND UPPER(p.strGoodsCode) LIKE UPPER('%" . $arySearchDataColumn["strGoodsCode"] . "%')\n";
			}
		}
		// ����̾��
		if ( $strSearchColumnName == "strGoodsName" )
		{
			if ( $arySearchDataColumn["strGoodsName"] )
			{
				$aryQuery[] = " AND UPPER(p.strGoodsName) LIKE UPPER('%" . $arySearchDataColumn["strGoodsName"] . "%')\n";
			}
		}
		// �ܵ�
		if ( $strSearchColumnName == "lngCustomerCompanyCode" )
		{
			if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
			{
				$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'\n";
				$flgCustomerCompany = TRUE;
			}
			if ( $arySearchDataColumn["strCustomerCompanyName"] )
			{
				$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerCompanyName"] . "%')\n";
				$flgCustomerCompany = TRUE;
			}
		}
		// �ܵ�ô����
		if ( $strSearchColumnName == "lngCustomerUserCode" )
		{
			if ( $arySearchDataColumn["lngCustomerUserCode"] )
			{
				$aryQuery[] = " AND cust_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngCustomerUserCode"] . "'\n";
				$flgCustomerUser = TRUE;
			}
			else if ( $arySearchDataColumn["strCustomerUserName"] )
			{
				$aryQuery[] = " AND (UPPER(cust_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerUserName"] . "%')\n";
				$aryQuery[] = " OR UPPER(p.strCustomerUserName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerUserName"] . "%'))\n";
				$flgCustomerUser = TRUE;
			}
		}
		// ��������
		if ( $strSearchColumnName == "lngFactoryCode" )
		{
			if ( $arySearchDataColumn["lngFactoryCode"] )
			{
				$aryQuery[] = " AND fact_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngFactoryCode"] . "'\n";
				$flgFactory = TRUE;
			}
			if ( $arySearchDataColumn["strFactoryName"] )
			{
				$aryQuery[] = " AND UPPER(fact_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strFactoryName"] . "%')\n";
				$flgFactory = TRUE;
			}
		}
		// ���å���֥깩��
		if ( $strSearchColumnName == "lngAssemblyFactoryCode" )
		{
			if ( $arySearchDataColumn["lngAssemblyFactoryCode"] )
			{
				$aryQuery[] = " AND assemfact_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngAssemblyFactoryCode"] . "'\n";
				$flgAssemblyFactory = TRUE;
			}
			if ( $arySearchDataColumn["strAssemblyFactoryName"] )
			{
				$aryQuery[] = " AND UPPER(assemfact_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strAssemblyFactoryName"] . "%')\n";
				$flgAssemblyFactory = TRUE;
			}
		}
		// Ǽ�ʾ��
		if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
		{
			if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
			{
				$aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'\n";
				$flgDeliveryPlace = TRUE;
			}
			if ( $arySearchDataColumn["strDeliveryPlaceName"] )
			{
				$aryQuery[] = " AND UPPER(delv_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')\n";
				$flgDeliveryPlace = TRUE;
			}
		}
// 2004.04.12 suzukaze update end
		// Ǽ��
		if ( $strSearchColumnName == "dtmDeliveryLimitDate" )
		{
			if ( $arySearchDataColumn["dtmDeliveryLimitDateFrom"] )
			{
				$aryQuery[] = " AND p.dtmDeliveryLimitDate >= To_Date( '" . $arySearchDataColumn["dtmDeliveryLimitDateFrom"] . "', 'YYYY/MM' )\n";
			}
			if ( $arySearchDataColumn["dtmDeliveryLimitDateTo"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmDeliveryLimitDateTo"] . " 23:59:59";
				$aryQuery[] = " AND p.dtmDeliveryLimitDate <= To_Date( '" . $arySearchDataColumn["dtmDeliveryLimitDateTo"] . "', 'YYYY/MM' )\n";
			}
		}
		// �ڻ�
		if ( $strSearchColumnName == "lngCertificateClassCode" )
		{
			if ( $arySearchDataColumn["lngCertificateClassCode"] )
			{
				$aryQuery[] = " AND p.lngCertificateClassCode = " . $arySearchDataColumn["lngCertificateClassCode"] . "\n";
			}
		}
		// �Ǹ���
		if ( $strSearchColumnName == "lngCopyrightCode" )
		{
			if ( $arySearchDataColumn["lngCopyrightCode"] )
			{
				$aryQuery[] = " AND p.lngCopyrightCode = " . $arySearchDataColumn["lngCopyrightCode"] . "\n";
			}
		}
		// ��������
		if ( $strSearchColumnName == "dtmInsertDate" )
		{
			if ( $arySearchDataColumn["dtmInsertDateFrom"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
				$aryQuery[] = " AND p.dtmInsertDate >= '" . $dtmSearchDate . "'\n";
			}
			if ( $arySearchDataColumn["dtmInsertDateTo"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
				$aryQuery[] = " AND p.dtmInsertDate <= '" . $dtmSearchDate . "'\n";
			}
		}
		// ��������
		if ( $strSearchColumnName == "dtmRevisionDate" )
		{
			if ( $arySearchDataColumn["dtmRevisionDateFrom"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmRevisionDateFrom"] . " 00:00:00";
				$aryQuery[] = " AND p.dtmUpdateDate >= '" . $dtmSearchDate . "'\n";
			}
			if ( $arySearchDataColumn["dtmRevisionDateTo"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmRevisionDateTo"] . " 23:59:59";
				$aryQuery[] = " AND p.dtmUpdateDate <= '" . $dtmSearchDate . "'\n";
			}
		}

		//////  �ʲ����ʴ��ޥ����⸡��  //////////
		// ����Ľ����
		if ( $strSearchColumnName == "lngGoodsPlanProgressCode" )
		{
			if ( $arySearchDataColumn["lngGoodsPlanProgressCode"] )
			{
				$aryQuery[] = " AND t_gp.lngGoodsPlanProgressCode = " . $arySearchDataColumn["lngGoodsPlanProgressCode"] . " ";
				$flgT_GoodsPlan = TRUE;
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
				}
			}

	}

	// SQLʸ�κ���
	$strQuery = "SELECT distinct p.lngProductNo as lngProductNo, p.lngInChargeGroupCode as lngGroupCode, p.bytInvalidFlag as bytInvalidFlag\n";

	// ɽ���ѥ���������
	for ( $i = 0; $i < count( $arySelectQuery ); $i++ )
	{
		$strQuery .= $arySelectQuery[$i];
	}

	$strQuery .= " FROM m_Product p\n";
	
	// �ɲ�ɽ���Ѥλ��ȥޥ����б�
	if ( $flgInputUser )
	{
		$strQuery .= " LEFT JOIN m_User input_u ON p.lngInputUserCode = input_u.lngUserCode\n";
	}
	if ( $flgInChargeGroup )
	{
		$strQuery .= " LEFT JOIN m_Group inchg_g ON p.lngInChargeGroupCode = inchg_g.lngGroupCode\n";
	}
	if ( $flgInChargeUser )
	{
		$strQuery .= " LEFT JOIN m_User inchg_u ON p.lngInChargeUserCode = inchg_u.lngUserCode\n";
	}
	if ( $flgCategory )
	{
		$strQuery .= " LEFT JOIN m_Category mc ON p.lngCategoryCode = mc.lngCategoryCode\n";
	}
	if ( $flgCustomerCompany )
	{
		$strQuery .= " LEFT JOIN m_Company cust_c ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode\n";
	}
	if ( $flgCustomerUser )
	{
		$strQuery .= " LEFT JOIN m_User cust_u ON p.lngCustomerUserCode = cust_u.lngUserCode\n";
	}
	if ( $flgPackingUnit )
	{
		$strQuery .= " LEFT JOIN m_ProductUnit packingunit ON p.lngPackingUnitCode = packingunit.lngProductUnitCode\n";
	}
	if ( $flgProductUnit )
	{
		$strQuery .= " LEFT JOIN m_ProductUnit productunit ON p.lngProductUnitCode = productunit.lngProductUnitCode\n";
	}
	if ( $flgProductForm )
	{
		$strQuery .= " LEFT JOIN m_ProductForm productform ON p.lngProductFormCode = productform.lngProductFormCode\n";
	}
	if ( $flgFactory )
	{
		$strQuery .= " LEFT JOIN m_Company fact_c ON p.lngFactoryCode = fact_c.lngCompanyCode\n";
	}
	if ( $flgAssemblyFactory )
	{
		$strQuery .= " LEFT JOIN m_Company assemfact_c ON p.lngAssemblyFactoryCode = assemfact_c.lngCompanyCode\n";
	}
	if ( $flgDeliveryPlace )
	{
		$strQuery .= " LEFT JOIN m_Company delv_c ON p.lngDeliveryPlaceCode = delv_c.lngCompanyCode\n";
	}
	if ( $flgTargetAge )
	{
		$strQuery .= " LEFT JOIN m_TargetAge targetage ON p.lngTargetAgeCode = targetage.lngTargetAgeCode\n";
	}
	if ( $flgCertificateClass )
	{
		$strQuery .= " LEFT JOIN m_CertificateClass certificate ON p.lngCertificateClassCode = certificate.lngCertificateClassCode\n";
	}
	if ( $flgCopyright )
	{
		$strQuery .= " LEFT JOIN m_Copyright copyright ON p.lngCopyrightCode = copyright.lngCopyrightCode\n";
	}

	if ( $flgWorkFlowStatus )
	{
//		$aryFromQuery[] = " left join
		$strQuery .= " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = p.strProductCode
		and mw.dtmstartdate = (select max(dtmstartdate) from m_workflow where strworkflowkeycode = mw.strworkflowkeycode)
			and mw.lngfunctioncode = " . DEF_FUNCTION_P1; // ������Ͽ����WF�ǡ������оݤˤ���٤˾�����
	}



	// ���ʴ��ޥ����б�
	if ( $flgT_GoodsPlan )
	{
		$strQuery .= ", t_GoodsPlan t_gp\n";
	}

	for ( $i = 0; $i < count( $aryQuery ); $i++ )
	{
		$strQuery .= $aryQuery[$i];
	}

	if ( $flgT_GoodsPlan )
	{
		$strQuery .= " AND t_gp.lngProductNo = p.lngProductNo\n";
		$strQuery .= " AND t_gp.lngRevisionNo = ( "
			. "SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo )\n";
	}

	if( $arySearchDataColumn["strSort"] )
	{
		if ( $arySearchDataColumn["strSortOrder"] == "ASC" )
		{
			$strAsDs = "ASC";	//����
		}
		else
		{
			$strAsDs = "DESC";	//�߽�
		}
		$strColumnName = $arySearchDataColumn["strSort"];
		if ( $arySearchDataColumn["strSort"] == "lnggoodsplanprogresscode" )
		{
			$strQuery .= " ORDER BY " . $arySearchDataColumn["strSort"] . " " . $strAsDs . " , p.lngProductNo ASC" ;
		}
		else
		{
			$strQuery .= " ORDER BY " . $arySearchDataColumn["strSort"] . " " . $strAsDs . " , p.lngProductNo ASC" ;
		}
	}
	else
	{
		$strQuery .= " ORDER BY p.lngProductNo ASC\n";
	}

//fncDebug("lib_ps.txt", $strQuery, __FILE__, __LINE__);

	return $strQuery;
}






/**
* �������ɽ���ؿ�
*
*	������̤���ơ��֥빽���Ƿ�̤���Ϥ���ؿ�
*
*	@param  Integer $lngColumnCount 		�Կ�
*	@param  Array 	$aryResult 				������̤���Ǽ���줿����
*	@param  Array 	$aryViewColumn 			ɽ���оݥ����̾������
*	@param  Array 	$aryData 				�Уϣӣԥǡ�����
*	@param	Array	$aryUserAuthority		�桼�����������Ф��븢�¤����ä�����
*	@access public
*/
function fncSetProductViewTable ( $lngColumnCount, $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache )
{
	$aryHtml[] =  "<td nowrap align=\"center\">" . $lngColumnCount . "</td>";

	// ɽ���оݥ������������̤ν���
	for ( $j = 0; $j < count($aryViewColumn); $j++ )
	{
		$strColumnName = $aryViewColumn[$j];

		if ( $aryResult["bytinvalidflag"] == "f" )
		{
			$aryResult["bytinvalidflag"] = 0;
		}
		else
		{
			$aryResult["bytinvalidflag"] = 1;
		}

		///////////////////////////////////
		////// ɽ���оݤ��ܥ���ξ�� /////
		///////////////////////////////////
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" )
		{
			// �ܥ����ˤ���ѹ�
			// �ܺ�ɽ��
			if ( $strColumnName == "btnDetail" )
			{
				if ( ( $aryResult["bytinvalidflag"] and $aryUserAuthority["DetailDelete"] ) 
					or ( !$aryResult["bytinvalidflag"] and $aryUserAuthority["Detail"] ) )
				{
					// ���ʥǡ���������оݤξ�硢�ܺ�ɽ���ܥ���������Բ�
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 0 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/p/result/index2.php?lngProductNo=" . $aryResult["lngproductno"] . "&strSessionID=" . $aryData["strSessionID"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'detail' )\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"></td>";
				}
			}

			// ����
			if ( $strColumnName == "btnFix" )
			{
				// ���ʤ�����ǡ����ξ��������Բ�
				if ( (!$aryResult["bytinvalidflag"] and $aryUserAuthority["Fix"])
					and $aryResult["lngproductstatuscode"] != DEF_PRODUCT_APPLICATE
				)
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 0 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/p/regist/renew.php?strProductCode=" . $aryResult["strproductcode"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " )\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"></td>";
				}
			}

			// ���
			if ( $strColumnName == "btnDelete" )
			{
				// ���ʤ�����ǡ����ξ��������Բġʿ�����ξ����
				if ( (!$aryResult["bytinvalidflag"] and $aryUserAuthority["Delete"]) 
					and $aryResult["lngproductstatuscode"] != DEF_PRODUCT_APPLICATE
				)
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 0 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon( '/p/result/index3.php?lngProductNo=" . $aryResult["lngproductno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"></td>";
				}
			}
		}

		///////////////////////////////////
		////// ɽ���оݤ����դξ�� ///////
		///////////////////////////////////
		// ������������������
		else if ( $strColumnName == "dtmInsertDate" or $strColumnName == "dtmRevisionDate" )
		{
			$strLowerColumnName = strtolower($strColumnName);
			if ( $aryResult[$strLowerColumnName] )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>" . str_replace( "-", "/", $aryResult[$strLowerColumnName] ) . "</td>";
			}
			else
			{
				$aryHtml[] = "<td align=\"left\" nowrap></td>";
			}
		}
		// Ǽ��
		else if ( $strColumnName == "dtmDeliveryLimitDate" )
		{
			if ( $aryResult["dtmdeliverylimitdate"] )
			{
				$dtmNewDate = substr( $aryResult["dtmdeliverylimitdate"], 0, 7 );
				$aryHtml[] = "<td align=\"left\" nowrap>" . str_replace( "-", "/", $dtmNewDate ) . "</td>";
			}
			else
			{
				$aryHtml[] = "<td align=\"left\" nowrap></td>";
			}
		}

		/////////////////////////////////////////////////
		////// ɽ���оݤ������ɤ���̾�λ��Ȥξ�� ///////
		/////////////////////////////////////////////////
		// ���ʹԾ���
		else if ( $strColumnName == "lngGoodsPlanProgressCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["lnggoodsplanprogresscode"] )
			{
				$aryGoodsPlanProgressCode = $objCache->GetValue("lnggoodsplanprogresscode", $aryResult["lnggoodsplanprogresscode"]);
				if( !is_array($aryGoodsPlanProgressCode) )
				{
					// ���ʹԾ���̾�Τμ���
					$strGoodsPlanProgressName = fncGetMasterValue( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname" , 
						$aryResult["lnggoodsplanprogresscode"], "", $objDB );
					// ���ʹԾ���̾�Τ�����
					$aryGoodsPlanProgressCode[0] = $strGoodsPlanProgressName;
					$objCache->SetValue("lnggoodsplanprogresscode", $strGoodsPlanProgressName, $aryGoodsPlanProgressCode);
				}
				$strText .= $aryGoodsPlanProgressCode[0] . "</td>";
			}
			$aryHtml[] = $strText;
		}
		// ���ϼ�
		else if ( $strColumnName == "lngInputUserCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strinputuserdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$strText .= "     ";
			}
			$strText .= " " . $aryResult["strinputuserdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// ����
		else if ( $strColumnName == "lngInChargeGroupCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strinchargegroupdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$strText .= "    ";
			}
			$strText .= " " . $aryResult["strinchargegroupdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// ô����
		else if ( $strColumnName == "lngInChargeUserCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strinchargeuserdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$strText .= "     ";
			}
			$strText .= " " . $aryResult["strinchargeuserdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// �ܵ�
		else if ( $strColumnName == "lngCustomerCompanyCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strcustomercompanydisplaycode"] )
			{
				$strText .= "[" . $aryResult["strcustomercompanydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strcustomercompanydisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// �ܵ�ô����
		else if ( $strColumnName == "lngCustomerUserCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strcustomeruserdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strcustomeruserdisplaycode"] ."]";
				$strText .= " " . $aryResult["strcustomeruserdisplayname"] . "</td>";
			}
			else
			{
				$strText .= "      ";
				$strText .= " " . $aryResult["strcustomerusername"] . "</td>";
			}
			$aryHtml[] = $strText;
		}
		// �ٻ�ñ��
		else if ( $strColumnName == "lngPackingUnitCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strpackingunitname"] . "</td>";
		}
		// ����ñ��
		else if ( $strColumnName == "lngProductUnitCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strproductunitname"] . "</td>";
		}
		// ���ʷ���
		else if ( $strColumnName == "lngProductFormCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strproductformname"] . "</td>";
		}
		// ��������
		else if ( $strColumnName == "lngFactoryCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strfactorydisplaycode"] )
			{
				$strText .= "[" . $aryResult["strfactorydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strfactorydisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// ���å���֥깩��
		else if ( $strColumnName == "lngAssemblyFactoryCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strassemblyfactorydisplaycode"] )
			{
				$strText .= "[" . $aryResult["strassemblyfactorydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strassemblyfactorydisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// Ǽ�ʾ��
		else if ( $strColumnName == "lngDeliveryPlaceCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strdeliveryplacedisplaycode"] )
			{
				$strText .= "[" . $aryResult["strdeliveryplacedisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strdeliveryplacedisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// �о�ǯ��
		else if ( $strColumnName == "lngTargetAgeCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strtargetagename"] . "</td>";
		}
		// �ڻ�
		else if ( $strColumnName == "lngCertificateClassCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strcertificateclassname"] . "</td>";
		}
		// �Ǹ���
		else if ( $strColumnName == "lngCopyrightCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strcopyrightname"] . "</td>";
		}

		///////////////////////////////////
		////// ɽ���оݤ����̤ξ�� ///////
		///////////////////////////////////
		// ��Ȣ���ޡ������������ȥ�����
		else if ( $strColumnName == "lngBoxQuantity" or $strColumnName == "lngCartonQuantity" )
		{
			$strLowerColumnName = strtolower($strColumnName);
			$strText = "<td align=\"right\" nowrap>";
			if ( !$aryResult[$strLowerColumnName] )
			{
				$strText .= "0</td>";
			}
			else
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}
		// ����ͽ���
		else if ( $strColumnName == "lngProductionQuantity" )
		{
			$strText = "<td align=\"right\" nowrap>";
			if ( !$aryResult["lngproductionquantity"] )
			{
				$strText .= "0";
			}
			else
			{
				$strText .= $aryResult[lngproductionquantity];
			}
			// ñ�̤�����
			if ( $aryResult["lngproductionunitcode"] )
			{
				$aryProductUnit = $objCache->GetValue("lngproductunitcode", $aryResult["lngproductionunitcode"]);
				if( !is_array($aryProductUnit) )
				{
					// ñ��̾�Τμ���
					$strProductUnitName = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname" , 
						$aryResult["lngproductionunitcode"], "", $objDB );
					// ñ��̾�Τ�����
					$aryProductUnit[0] = $strProductUnitName;
					$objCache->SetValue("lngproductunitcode", $strProductUnitName, $aryProductUnit);
				}
				$strText .= " " . $aryProductUnit[0];
			}
			$strText .= "</td>";
			$aryHtml[] = $strText;
		}
		// ���Ǽ�ʿ�
		else if ( $strColumnName == "lngFirstDeliveryQuantity" )
		{
			$strText = "<td align=\"right\" nowrap>";
			if ( !$aryResult["lngfirstdeliveryquantity"] )
			{
				$strText .= "0";
			}
			else
			{
				$strText .= $aryResult[lngfirstdeliveryquantity];
			}
			// ñ�̤�����
			if ( $aryResult["lngfirstdeliveryunitcode"] )
			{
				$aryProductUnit = $objCache->GetValue("lngproductunitcode", $aryResult["lngfirstdeliveryunitcode"]);
				if( !is_array($aryProductUnit) )
				{
					// ñ��̾�Τμ���
					$strProductUnitName = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname" , 
						$aryResult["lngfirstdeliveryunitcode"], "", $objDB );
					// ñ��̾�Τ�����
					$aryProductUnit[0] = $strProductUnitName;
					$objCache->SetValue("lngproductunitcode", $strProductUnitName, $aryProductUnit);
				}
				$strText .= " " . $aryProductUnit[0];
			}
			$strText .= "</td>";
			$aryHtml[] = $strText;
		}

		///////////////////////////////////
		////// ɽ���оݤ����ʤξ�� ///////
		///////////////////////////////////
		// Ǽ��������
		else if ( $strColumnName == "curProductPrice" or $strColumnName == "curRetailPrice" )
		{
			$strLowerColumnName = strtolower($strColumnName);
			$strText = "<td align=\"right\" nowrap>";
			$strText .= DEF_PRODUCT_MONETARYSIGN . " ";
			if ( !$aryResult[$strLowerColumnName] )
			{
				$strText .= "0.00</td>";
			}
			else
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}

		///////////////////////////////////
		////// ɽ���оݤ����ͤξ�� ///////
		///////////////////////////////////
		// �����ƥ�
		else if ( $strColumnName == "lngRoyalty" )
		{
			$aryHtml[] = "<td align=\"right\" nowrap>" . $aryResult["lngroyalty"] . "</td>";
		}

		/////////////////////////////////////////
		////// ɽ���оݤ�ʸ������ܤξ�� ///////
		/////////////////////////////////////////
		// ����¾�ι��ܤϤ��Τޤ޽���
		else
		{
			$strLowerColumnName = strtolower($strColumnName);
			$strText = "<td align=\"left\" nowrap>";
			// ���;ܺ٤ϲ�������
			if ( $strColumnName == "strSpecificationDetails" )
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			// ���ʹ�����ʸ�����ɲ�
			else if ( $strColumnName == "strProductComposition" )
			{
				if ( $aryResult[$strLowerColumnName] )
				{
					$strText .= "��" . $aryResult[$strLowerColumnName] . "�異�å���֥�</td>";
				}
				else
				{
					$strText .= $aryResult[$strLowerColumnName] . "</td>";
				}
			}
			else
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}
	}

	$aryHtml[] = "</tr>";

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
function fncSetProductTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
{
	// �ơ��֥�η���
	$lngResultCount = count($aryResult);

	$aryHtml[] = "<span id=\"COPYAREA1\">";
	$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" bproduct=\"0\" bgcolor=\"#6f8180\" align=\"center\">";

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
				// �����ȹ��ܰʳ��ξ��
				if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" )
				{
					// �ܺ٥ܥ���ξ��ϡ��ܺ�ɽ����ǽ�ʥ桼�����Τ�ɽ������
					if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
					{
						$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
					}
					// �����ܥ���ξ��ϡ�����������ǽ�ʥ桼�����Τ�ɽ������
					if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
					{
						$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
					}
					// ����ܥ���ξ��ϡ����������ǽ�ʥ桼�����Τ�ɽ������
					if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"])
					{
						$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
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
						$strSortOrder = "ASC";
					}
					$strText .= "onclick=\"fncSort2('" . $aryTableName[$strColumnName] . "', '" . $strSortOrder . "');\">";
					$strText .= "<a href=\"#\">".$aryTytle[$strColumnName]."</a></td>";
					$aryHtml[] = $strText;
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
		reset( $aryResult[$i] );

		// �Ԥ��طʿ��μ���
		if ( $aryResult[$i]["lnggroupcode"] != "" )
		{
			$aryGroupColor = $objCache->GetValue("lnggroupcode", $aryResult[$i]["lnggroupcode"]);
			if( !is_array($aryGroupColor) )
			{
				// ���롼�׿��μ���
				$strGroupDisplayColor = fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycolor" , 
					$aryResult[$i]["lnggroupcode"], "", $objDB );
				// ���롼�׿�������
				$aryGroupColor[0] = $strGroupDisplayColor;
				$objCache->SetValue("lnggroupcode", $strGroupDisplayColor, $aryGroupColor);
			}
			$strGroupColor = $aryGroupColor[0];
		}
		else
		{
			$strGroupColor = "#FFFFFF";
		}

		// ���������ʬ������
		$aryHtml[] = "<tr class=\"Segs\" name=\"strTrName" . $i . "\" style=\"background:" . $strGroupColor . "\" onclick=\"fncSelectTrColor( this );\">";

//		$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\" style=\"background:#99FF99\">";

		$lngColumnCount++;

		// ���쥳����ʬ�ν���
		$aryHtml_add = fncSetProductViewTable ( $lngColumnCount, $aryResult[$i], $aryViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache );
		for ( $j = 0; $j < count($aryHtml_add); $j++ )
		{
			$aryHtml[] = $aryHtml_add[$j];
		}
// ������̽��ϡ�������end==================================
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