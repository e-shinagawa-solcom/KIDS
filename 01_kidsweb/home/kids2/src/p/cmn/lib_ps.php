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
	$aryHtml[] =  "<tr>";
	$aryHtml[] =  "\t<td>" . ($lngColumnCount) . "</td>";
	
	// ɽ���оݥ������������̤ν���
	for ( $j = 0; $j < count($aryViewColumn); $j++ )
	{
		$strColumnName = $aryViewColumn[$j];
		$TdData = "";

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
					$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngproductno=\"" . $aryResult["lngproductno"] . "\" class=\"detail button\"></td>\n";
				}
				else
				{
					$aryHtml[] = "\t<td></td>\n";
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
					$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" strproductcode=\"" . $aryResult["strproductcode"] . "\" class=\"fix button\"></td>\n";
				}
				else
				{
					$aryHtml[] = "\t<td></td>\n";
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
					$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngproductno=\"" . $aryResult["lngproductno"] . "\" class=\"detail button\"></td>\n";
				}
				else
				{
					$aryHtml[] = "\t<td></td>\n";
				}
			}
		}
		else if ($strColumnName != "") {
			$TdData = "\t<td>";
			$TdDataUse = true;
			$strText = "";
			///////////////////////////////////
			////// ɽ���оݤ����դξ�� ///////
			///////////////////////////////////
			// ������������������
			if ( $strColumnName == "dtmInsertDate" or $strColumnName == "dtmRevisionDate" )
			{
				$strLowerColumnName = strtolower($strColumnName);
				if ( $aryResult[$strLowerColumnName] )
				{
					$TdData .= str_replace( "-", "/", $aryResult[$strLowerColumnName] );
				}
			}
			// Ǽ��
			else if ( $strColumnName == "dtmDeliveryLimitDate" )
			{
				if ( $aryResult["dtmdeliverylimitdate"] )
				{
					$dtmNewDate = substr( $aryResult["dtmdeliverylimitdate"], 0, 7 );
					$TdData .= str_replace( "-", "/", $dtmNewDate );
				}
			}

			/////////////////////////////////////////////////
			////// ɽ���оݤ������ɤ���̾�λ��Ȥξ�� ///////
			/////////////////////////////////////////////////
			// ���ʹԾ���
			else if ( $strColumnName == "lngGoodsPlanProgressCode" )
			{
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
					$TdData .= $aryGoodsPlanProgressCode[0] . "</td>";
				}
			}
			// ���ϼ�
			else if ( $strColumnName == "lngInputUserCode" )
			{
				if ( $aryResult["strinputuserdisplaycode"] )
				{
					$strText .= "[" . $aryResult["strinputuserdisplaycode"] ."]";
				}
				else
				{
					$strText .= "     ";
				}
				$strText .= " " . $aryResult["strinputuserdisplayname"];
				$TdData .= $strText;
			}
			// ����
			else if ( $strColumnName == "lngInChargeGroupCode" )
			{
				if ( $aryResult["strinchargegroupdisplaycode"] )
				{
					$strText .= "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
				}
				else
				{
					$strText .= "    ";
				}
				$strText .= " " . $aryResult["strinchargegroupdisplayname"] . "</td>";
				$TdData .= $strText;
			}
			// ô����
			else if ( $strColumnName == "lngInChargeUserCode" )
			{
				if ( $aryResult["strinchargeuserdisplaycode"] )
				{
					$strText .= "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
				}
				else
				{
					$strText .= "     ";
				}
				$strText .= " " . $aryResult["strinchargeuserdisplayname"];
				$TdData .= $strText;
			}
			// �ܵ�
			else if ( $strColumnName == "lngCustomerCompanyCode" )
			{
				if ( $aryResult["strcustomercompanydisplaycode"] )
				{
					$strText .= "[" . $aryResult["strcustomercompanydisplaycode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= " " . $aryResult["strcustomercompanydisplayname"];
				$TdData .= $strText;
			}
			// �ܵ�ô����
			else if ( $strColumnName == "lngCustomerUserCode" )
			{
				if ( $aryResult["strcustomeruserdisplaycode"] )
				{
					$strText .= "[" . $aryResult["strcustomeruserdisplaycode"] ."]";
					$strText .= " " . $aryResult["strcustomeruserdisplayname"];
				}
				else
				{
					$strText .= "      ";
					$strText .= " " . $aryResult["strcustomerusername"];
				}
				$TdData .= $strText;
			}
			// �ٻ�ñ��
			else if ( $strColumnName == "lngPackingUnitCode" )
			{
				$TdData .= $aryResult["strpackingunitname"];
			}
			// ����ñ��
			else if ( $strColumnName == "lngProductUnitCode" )
			{
				$TdData .= $aryResult["strproductunitname"];
			}
			// ���ʷ���
			else if ( $strColumnName == "lngProductFormCode" )
			{
				$TdData .= $aryResult["strproductformname"];
			}
			// ��������
			else if ( $strColumnName == "lngFactoryCode" )
			{
				if ( $aryResult["strfactorydisplaycode"] )
				{
					$strText .= "[" . $aryResult["strfactorydisplaycode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= " " . $aryResult["strfactorydisplayname"];
				$TdData .= $strText;
			}
			// ���å���֥깩��
			else if ( $strColumnName == "lngAssemblyFactoryCode" )
			{
				if ( $aryResult["strassemblyfactorydisplaycode"] )
				{
					$strText .= "[" . $aryResult["strassemblyfactorydisplaycode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= " " . $aryResult["strassemblyfactorydisplayname"];
				$TdData .= $strText;
			}
			// Ǽ�ʾ��
			else if ( $strColumnName == "lngDeliveryPlaceCode" )
			{
				if ( $aryResult["strdeliveryplacedisplaycode"] )
				{
					$strText .= "[" . $aryResult["strdeliveryplacedisplaycode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= " " . $aryResult["strdeliveryplacedisplayname"];
				$TdData .= $strText;
			}
			// �о�ǯ��
			else if ( $strColumnName == "lngTargetAgeCode" )
			{
				$TdData .= $aryResult["strtargetagename"];
			}
			// �ڻ�
			else if ( $strColumnName == "lngCertificateClassCode" )
			{
				$TdData .= $aryResult["strcertificateclassname"];
			}
			// �Ǹ���
			else if ( $strColumnName == "lngCopyrightCode" )
			{
				$TdData .= $aryResult["strcopyrightname"];
			}

			///////////////////////////////////
			////// ɽ���оݤ����̤ξ�� ///////
			///////////////////////////////////
			// ��Ȣ���ޡ������������ȥ�����
			else if ( $strColumnName == "lngBoxQuantity" or $strColumnName == "lngCartonQuantity" )
			{
				$strLowerColumnName = strtolower($strColumnName);
				if ( !$aryResult[$strLowerColumnName] )
				{
					$strText .= "0";
				}
				else
				{
					$strText .= $aryResult[$strLowerColumnName];
				}
				$TdData .= $strText;
			}
			// ����ͽ���
			else if ( $strColumnName == "lngProductionQuantity" )
			{
				if ( !$aryResult["lngproductionquantity"] )
				{
					$strText .= "0";
				}
				else
				{
					$strText .= $aryResult["lngproductionquantity"];
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
				$TdData .= $strText;
			}
			// ���Ǽ�ʿ�
			else if ( $strColumnName == "lngFirstDeliveryQuantity" )
			{
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
				$TdData .= $strText;
			}

			///////////////////////////////////
			////// ɽ���оݤ����ʤξ�� ///////
			///////////////////////////////////
			// Ǽ��������
			else if ( $strColumnName == "curProductPrice" or $strColumnName == "curRetailPrice" )
			{
				$strLowerColumnName = strtolower($strColumnName);
				$strText .= DEF_PRODUCT_MONETARYSIGN . " ";
				if ( !$aryResult[$strLowerColumnName] )
				{
					$strText .= "0.00";
				}
				else
				{
					$strText .= $aryResult[$strLowerColumnName];
				}
				$TdData .= $strText;
			}

			///////////////////////////////////
			////// ɽ���оݤ����ͤξ�� ///////
			///////////////////////////////////
			// �����ƥ�
			else if ( $strColumnName == "lngRoyalty" )
			{
				$TdData .= $aryResult["lngroyalty"];
			}

			/////////////////////////////////////////
			////// ɽ���оݤ�ʸ������ܤξ�� ///////
			/////////////////////////////////////////
			// ����¾�ι��ܤϤ��Τޤ޽���
			else
			{
				$strLowerColumnName = strtolower($strColumnName);
				// ���;ܺ٤ϲ�������
				if ( $strColumnName == "strSpecificationDetails" )
				{
					$strText .= $aryResult[$strLowerColumnName];
				}
				// ���ʹ�����ʸ�����ɲ�
				else if ( $strColumnName == "strProductComposition" )
				{
					if ( $aryResult[$strLowerColumnName] )
					{
						$strText .= "��" . $aryResult[$strLowerColumnName] . "�異�å���֥�";
					}
					else
					{
						$strText .= $aryResult[$strLowerColumnName];
					}
				}
				else
				{
					$strText .= $aryResult[$strLowerColumnName];
				}
				$TdData .= $strText;
			}
			$TdData .= "</td>\n";
			$aryHtml[] = $TdData;
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

// ������̽��ϡ�������start==================================
	$lngResultCount = count($aryResult);
	$lngColumnCount = 0;
	
	for ( $i = 0; $i < $lngResultCount; $i++ )
	{
		reset( $aryResult[$i] );

		$lngColumnCount++;

		// ���쥳����ʬ�ν���
		$aryHtml_add = fncSetProductViewTable ( $lngColumnCount, $aryResult[$i], $aryViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache );
		
		$strColBuff = '';
		for ( $j = 0; $j < count($aryHtml_add); $j++ )
		{
			$strColBuff .= $aryHtml_add[$j];
		}
		$aryHtml[] =$strColBuff;
// ������̽��ϡ�������end==================================
	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

?>