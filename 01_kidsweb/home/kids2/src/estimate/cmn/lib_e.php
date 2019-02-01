<?
/** 
*	���Ѹ��������ѥ饤�֥��
*
*	���Ѹ��������Ѵؿ��饤�֥��
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*	��������
*	2004.09.28	fncGetEstimateToProductCode �ؿ����ɲ�
*/


// ����졼�����
define ("DEF_MONETARYCLASS_SHANAI", 	2);		// ����


/**
* ���Ѹ�������(�����˻���)
*
*	���Ѹ����ǡ����ɤ߹��ߡ��������ܺپ������������ؿ�
*
*	@param  String $lngEstimateCode ���Ѹ���������
*	@param  Array  $aryData     FORM�ǡ���
*	@param  Object $objDB       DB���֥�������
*	@access public
*/
function getEstimateQuery( $lngUserCode, $aryData, $objDB )
{

	require_once( LIB_DEBUGFILE );
fncDebug( 'getEstimateQuery_01.txt', $lngUserCode, __FILE__, __LINE__);
fncDebug( 'getEstimateQuery_01.txt', $aryData, __FILE__, __LINE__,'a');

	// �����Ȥ��륫�����о��ֹ�����

	$arySortColumn = array ( 1 => "p.strProductCode",
	                         2 => "p.strProductName",
	                         3 => "g.strGroupDisplayCode",
	                         4 => "u1.strUserDisplayCode",
	                         5 => "u2.strUserDisplayCode",
	                         6 => "e.dtmInsertDate",
	                         7 => "p.dtmDeliveryLimitDate",
	                         8 => "p.curProductPrice",
	                         9 => "p.curRetailPrice",
	                        10 => "p.lngCartonQuantity",
	                        11 => "lngPlanCartonProduction",
	                        12 => "lngProductionQuantity",
	                        13 => "",
	                        14 => "",
	                        15 => "",
	                        16 => "e.curFixedCost",
	                        17 => "",
	                        18 => "",
	                        19 => "",
	                        20 => "",
	                        21 => "",
	                        22 => "e.curMemberCost",
	                        23 => "curManufacturingCost",
	                        24 => "curAmountOfSales",
	                        25 => "curTargetProfit",
	                        26 => "curAchievementRatio",
	                        27 => "curStandardRate",
	                        28 => "curProfitOnSales",
	                        29 => "",
	                        30 => "" );


	//////////////////////////////////////////////////////////////////////////
	// ��������
	//////////////////////////////////////////////////////////////////////////
	list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT curStandardRate FROM m_EstimateStandardRate WHERE dtmApplyStartDate <= NOW() AND dtmApplyEndDate >= NOW()", $objDB );
	if ( $lngResultNum < 1 )
	{
// 2004.10.01 suzukaze update start
		// �⤷�����ɸ���礬���ȤǤ��ʤ����ǿ������դ�ɸ����򻲾�
		list ( $lngResultMaxID, $lngResultMaxNum ) = fncQuery( "select * from m_estimatestandardrate where dtmapplyenddate = (select max(dtmapplyenddate) from m_estimatestandardrate);", $objDB );

		if ( $lngResultMaxNum < 1 )
		{
			fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
		}
		else
		{
			$lngResultNum = $lngResultMaxNum;
			$lngResultID  = $lngResultMaxID;
		}
// 2004.10.01 suzukaze update end
	}
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$objDB->freeResult( $lngResultID );

	// SELECT
	$aryQuery[] = "SELECT";
	$aryQuery[] = " e.lngEstimateNo,";
	$aryQuery[] = " e.strProductCode,";
	$aryQuery[] = " e.bytDecisionFlag,";
	$aryQuery[] = " to_char( e.curFixedCost, '999,999,990.99' ) AS curFixedCost,";
	$aryQuery[] = " to_char( e.curMemberCost, '999,999,990.99' ) AS curMemberCost,";
	$aryQuery[] = " to_char( e.curManufacturingCost, '999,999,990.99' ) AS curManufacturingOfCost,";
	$aryQuery[] = " to_char( e.curSalesAmount, '999,999,990.99' ) AS curAmountOfSales,";
	$aryQuery[] = " e.curManufacturingCost,";
	$aryQuery[] = " e.curSalesAmount,";
//	$aryQuery[] = " to_char( e.curProfit, '999,999,990.99' ) AS curTargetProfit,";
	$aryQuery[] = " e.lngInputUserCode,";
	$aryQuery[] = " e.lngEstimateStatusCode,";
	$aryQuery[] = " p.strProductName,";
	$aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM/DD') AS dtmDeliveryLimitDate,";
	$aryQuery[] = " to_char(p.curProductPrice,'999,999,990.9999') AS curProductPrice,";
	$aryQuery[] = " to_char(p.curRetailPrice,'999,999,990.9999') AS curRetailPrice,";
	$aryQuery[] = " to_char( p.lngCartonQuantity, '9,999,999,999' ) AS lngCartonQuantity,";

	// ����ñ�̤�ctn�ʤ�С�����ͽ�����pcs���Ѵ�����
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN to_char( p.lngProductionQuantity * p.lngCartonQuantity, '9,999,999,999' ) ";
	$aryQuery[] = "  ELSE to_char( p.lngProductionQuantity, '9,999,999,999' ) ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	// ����ñ�̤�ctn�ʤ�С��ײ�C/t�Ϥ��Τޤ�����ͽ���
	$aryQuery[] = " CASE WHEN ( p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_PCS . " OR p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_SET . " ) AND p.lngCartonQuantity <> 0 ";
	$aryQuery[] = "  THEN to_char( p.lngProductionQuantity / p.lngCartonQuantity, '9,999,999,999' )";
	$aryQuery[] = "  ELSE to_char( p.lngProductionQuantity, '9,999,999,999' ) ";
	$aryQuery[] = " END AS lngPlanCartonProduction,";

	$aryQuery[] = " g.strGroupDisplayCode AS strInchargeGroupDisplayCode,";
	$aryQuery[] = " g.strGroupDisplayName AS strInchargeGroupDisplayName,";
	$aryQuery[] = " u1.strUserDisplayCode AS strInchargeUserDisplayCode,";
	$aryQuery[] = " u1.strUserDisplayName AS strInchargeUserDisplayName,";
	$aryQuery[] = " u2.strUserDisplayCode AS strInputUserDisplayCode,";
	$aryQuery[] = " u2.strUserDisplayName AS strInputUserDisplayName,";
// 2004.10.04 suzukaze update start
	$aryQuery[] = " to_char(e.dtmInsertDate,'YYYY/MM/DD') AS dtmCreationDate,";
	$aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM/DD') AS dtmDeliveryLimitDate,";
// 2004.10.04 suzukaze update end
	$aryQuery[] = " ed.bytPayOffTargetFlag,";
	$aryQuery[] = " ed.bytPercentInputFlag,";
	$aryQuery[] = " to_char( ed.lngProductQuantity, '9,999,999,999' ) AS lngProductQuantity,";
	$aryQuery[] = " to_char( ed.curProductPrice, '999,999,990.9999' ) AS curDetailProductPrice,";
	$aryQuery[] = " to_char( ed.curProductRate, '999,999,990.9999' ) AS curProductRate,";
	$aryQuery[] = " to_char( ed.curSubTotalPrice, '999,999,990.99' ) AS curSubTotalPrice,";
	$aryQuery[] = " ed.curSubTotalPrice as curSubTotalPriceDefault,";
	$aryQuery[] = " ed.strNote,";
	$aryQuery[] = " ed.lngMonetaryUnitCode,";
	$aryQuery[] = " ed.curConversionRate,";
	$aryQuery[] = " ed.lngCustomerCompanyCode,";
	$aryQuery[] = " ss.lngStockClassCode,";
	$aryQuery[] = " ss.lngStockSubjectCode,";
	$aryQuery[] = " ss.strStockSubjectName,";
	$aryQuery[] = " si.strStockItemName,";
	$aryQuery[] = " msd.lngsalesdivisioncode,";
	$aryQuery[] = " msd.strsalesdivisionname,";
	$aryQuery[] = " msc.lngsalesclasscode,";
	$aryQuery[] = " msc.strsalesclassname,";

	$aryQuery[] = " si.lngStockItemCode,";
	$aryQuery[] = " c.strCompanyDisplayCode,";
	$aryQuery[] = " c.strCompanyDisplayName,";
	//$aryQuery[] = " tw.lngWorkflowStatusCode,";

	// �����ɸ���� �� ͽ������ �� ����¤����
	$aryQuery[] = " to_char( e.curSalesAmount - e.curManufacturingCost, '999,999,990.9999' )  AS curTargetProfit, ";

	// ��ɸ����Ψ �� �����ɸ���� / ͽ������
	$aryQuery[] = " CASE WHEN e.curSalesAmount <> 0 ";
	$aryQuery[] = "  THEN to_char( (e.curSalesAmount - e.curManufacturingCost) / e.curSalesAmount * 100, '9,999,999,990.99' ) || ' %' ";
	$aryQuery[] = "  ELSE to_char( 0, '0.99' ) || ' %' ";
	$aryQuery[] = " END AS curAchievementRatio,";

	// ������¤����
//	$aryQuery[] = " to_char( e.curSalesAmount * " . $objResult->curstandardrate . ", '9,999,999,990.99' ) AS curStandardRate,";
	// ɸ����
	$aryQuery[] = $objResult->curstandardrate . " AS curStandardRate,";

	// ���������
	$aryQuery[] = " to_char( (e.curSalesAmount - e.curManufacturingCost) - (e.curSalesAmount * " . $objResult->curstandardrate . "), '9,999,999,990.99' ) AS curProfitOnSales ";

	unset ( $objResult );

	// FROM
	$aryQuery[] = "FROM m_Estimate e";
	$aryQuery[] = " INNER JOIN m_Product p ON p.strProductCode       = e.strProductCode";
	$aryQuery[] = "  AND p.bytInvalidFlag = FALSE";
	$aryQuery[] = " LEFT OUTER JOIN m_Group g   ON p.lngInChargeGroupCode = g.lngGroupCode";
	$aryQuery[] = " LEFT OUTER JOIN m_User u1   ON p.lngInChargeUserCode  = u1.lngUserCode";
//	$aryQuery[] = "  AND u1.bytInvalidFlag = FALSE";
// 2004.10.04 suzukaze update start
	$aryQuery[] = " INNER JOIN m_User u2   ON e.lngInputUserCode     = u2.lngUserCode";
// 2004.10.04 suzukaze update end
//	$aryQuery[] = "  AND u2.bytInvalidFlag = FALSE";
// 2004.10.04 suzukaze update start
//	$aryQuery[] = " INNER JOIN t_GoodsPlan gp ON p.lngProductNo      = gp.lngProductNo";
//	$aryQuery[] = "  AND gp.lngRevisionNo =";
//	$aryQuery[] = "   ( SELECT MAX(gp2.lngRevisionNo) FROM t_GoodsPlan gp2 WHERE gp.lngProductNo = gp2.lngProductNo )";
// 2004.10.04 suzukaze update end

	$aryQuery[] = " LEFT OUTER JOIN t_EstimateDetail ed ON ed.lngEstimateNo          = e.lngEstimateNo";
	$aryQuery[] = "  AND ed.lngRevisionNo =";
//	$aryQuery[] = "   ( SELECT MAX(ed2.lngRevisionNo) FROM t_EstimateDetail ed2 WHERE ed.lngEstimateNo = ed2.lngEstimateNo AND ed.lngEstimateDetailNo = ed2.lngEstimateDetailNo )";
//=======================================================================================================
// 050407 bykou revision��MAX�ͤ���ʤ�����ν���
	$aryQuery[] = "   ( SELECT MAX(ed2.lngRevisionNo) FROM t_EstimateDetail ed2 WHERE ed.lngEstimateNo = ed2.lngEstimateNo)";
//========================================================================================================
	$aryQuery[] = "  AND 0 <=";
	$aryQuery[] = "   ( SELECT MIN(ed3.lngRevisionNo) FROM t_EstimateDetail ed3 WHERE ed.lngEstimateNo = ed3.lngEstimateNo AND ed.lngEstimateDetailNo = ed3.lngEstimateDetailNo )";
	$aryQuery[] = " LEFT JOIN m_StockSubject ss   ON ed.lngStockSubjectCode    = ss.lngStockSubjectCode";
	$aryQuery[] = " LEFT JOIN m_StockItem si      ON ed.lngStockItemCode       = si.lngStockItemCode";
	$aryQuery[] = "  AND ed.lngStockSubjectCode    = si.lngStockSubjectCode";
	$aryQuery[] = " LEFT JOIN m_Company c         ON ed.lngCustomerCompanyCode = c.lngCompanyCode";

	$aryQuery[] = " LEFT JOIN m_salesdivision msd ON ed.lngsalesdivisioncode = msd.lngsalesdivisioncode";
	$aryQuery[] = " LEFT JOIN m_salesclass msc ON ed.lngsalesclasscode = msc.lngsalesclasscode";


	//$aryQuery[] = " LEFT OUTER JOIN m_Workflow w  ON w.strWorkflowKeyCode = e.lngEstimateNo";
	//$aryQuery[] = "  AND w.bytInvalidFlag = FALSE";
	//$aryQuery[] = " LEFT OUTER JOIN t_Workflow tw ON w.lngWorkflowCode = tw.lngWorkflowCode";
	//$aryQuery[] = "  AND lngWorkflowSubCode =";
	//$aryQuery[] = "   ( SELECT MAX(tw2.lngWorkflowSubCode) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode )";


	// WHERE
	$aryQueryWhere[] = " e.lngRevisionNo = ( SELECT MAX ( e2.lngRevisionNo ) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo )";
	$aryQueryWhere[] = " 0 <= ( SELECT MIN ( e3.lngRevisionNo ) FROM m_Estimate e3 WHERE e.lngEstimateNo = e3.lngEstimateNo )";

	//////////////////////////////////////////////////////////////////////////
	// ������
	//////////////////////////////////////////////////////////////////////////
	// A:���ꤷ�����Ѹ���������
	if ( $aryData["lngUserCodeConditions"] && $lngUserCode != "" )
	{
		$aryQueryWhere[] = " u.lngUserCode = $lngUserCode \n";
	}

	// B:�Ƹ������
	// ���ʥ�����
	if ( $aryData["strProductCodeConditions"] )
	{
		if ( $aryData["strProductCodeFrom"] != "" )
		{
			$aryQueryWhere[] = " e.strProductCode >= '" . sprintf ( "%05d", $aryData["strProductCodeFrom"] ) . "'\n";
		}
		if ( $aryData["strProductCodeTo"] != "" )
		{
			$aryQueryWhere[] = " e.strProductCode <= '" . sprintf ( "%05d", $aryData["strProductCodeTo"] ) . "'\n";
		}
	}

	// ����̾
	if ( $aryData["strProductNameConditions"] && $aryData["strProductName"] != "" )
	{
		$aryQueryWhere[] = " p.strProductName LIKE '%" . $aryData["strProductName"] . "%' \n";
	}

	// ô�����롼��ɽ��������
	if ( $aryData["strInchargeGroupDisplayCodeConditions"] && $aryData["lngInChargeGroupCode"] != "" )
	{
		$aryQueryWhere[] = " g.strGroupDisplayCode = '" . $aryData["lngInChargeGroupCode"] . "' \n";
	}

	// ô����ɽ��������
	if ( $aryData["strInchargeUserDisplayCodeConditions"] && $aryData["lngInChargeUserCode"] != "" )
	{
		$aryQueryWhere[] = " u1.strUserDisplayCode = '" . $aryData["lngInChargeUserCode"] . "' \n";
	}

	// ���ϼ�ɽ��������
	if ( $aryData["strInputUserDisplayCodeConditions"] && $aryData["lngInputUserCode"] != "" )
	{
		$aryQueryWhere[] = " u2.strUserDisplayCode = '" . $aryData["lngInputUserCode"] . "' \n";
	}

	// ������
	if ( $aryData["dtmCreationDateConditions"] )
	{
		if ( $aryData["dtmCreationDateFrom"] != "" )
		{
			$aryQueryWhere[] = " to_char( e.dtmInsertDate,'YYYY/MM/DD' ) >= '" . $aryData["dtmCreationDateFrom"] . "'\n";
		}
		if ( $aryData["dtmCreationDateTo"] != "" )
		{
			$aryQueryWhere[] = " to_char( e.dtmInsertDate,'YYYY/MM/DD' ) <= '" . $aryData["dtmCreationDateTo"] . "'\n";
		}
	}

	// Ǽ��
	if ( $aryData["dtmDeliveryLimitDateConditions"] )
	{
		if ( $aryData["dtmDeliveryLimitDateFrom"] != "" )
		{
			$aryQueryWhere[] = " p.dtmDeliveryLimitDate >= to_date( '" . $aryData["dtmDeliveryLimitDateFrom"] . "', 'YYYY/MM' )\n";
		}
		if ( $aryData["dtmDeliveryLimitDateTo"] != "" )
		{
			$aryQueryWhere[] = " p.dtmDeliveryLimitDate <= to_date( '" . $aryData["dtmDeliveryLimitDateTo"] . "', 'YYYY/MM' )\n";
		}
	}
	// ����ե�����
	if ( $aryData["lngWorkFlowStatusCodeConditions"] )
	{
		// ����ե�����
		if ( $aryData["lngWorkFlowStatusCode"] != "" )
		{
			if ( $aryData["lngWorkFlowStatusCode"] )
			{
				// �����å��ܥå����ͤ�ꡢ����򤽤Τޤ�����
				$arySearchStatus = $aryData["lngWorkFlowStatusCode"];
				
				if ( is_array( $arySearchStatus ) )
				{
					$aryQueryWF[] = " AND e.lngestimatestatuscode in ( ";

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
					$aryQueryWF[] = "\t".$strBuff . " )";
				}
			}
		}
	}

	$aryQuery[] = " WHERE " . join ( "\n AND ", $aryQueryWhere );

	if( is_array( $aryQueryWF ) )
	{
		$aryQuery[] = implode( "\n", $aryQueryWF );
	}


	//////////////////////////////////////////////////////////////////////////
	// �����Ƚ���
	//////////////////////////////////////////////////////////////////////////
	// $strSort ��¤ "sort_[�о��ֹ�]_[�߽硦����]"
	// $strSort �����о��ֹ桢�߽硦��������
	list ( $sort, $column, $DESC ) = split ( "_", $aryData["strSort"] );
	if ( $column )
	{
		$aryQuery[] = "ORDER BY $arySortColumn[$column] $DESC, e.dtmInsertDate DESC\n";
	}
	else
	{
		$aryQuery[] = "ORDER BY e.dtmInsertDate DESC\n";
	}


	$strQuery = "";
	$strQuery = join( "\n", $aryQuery );

//require_once( LIB_DEBUGFILE );
//fncDebug( 'lib_es.txt', $strQuery, __FILE__, __LINE__);
fncDebug( 'getEstimateQuery_01.txt', $strQuery, __FILE__, __LINE__,'a');

	//////////////////////////////////////////////////////////////////////////
	// ������¹�
	//////////////////////////////////////////////////////////////////////////
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	unset ( $aryQuery );

	if ( !$lngResultNum )
	{
// 2004.10.09 suzukaze update start
		$strErrorMessage = fncOutputError( 1507, DEF_WARNING, "", FALSE, "/estimate/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		$strErrorMessage = "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f818\"><tr bgcolor=\"#FFFFFF\"><th>" . $strErrorMessage . "</th></tr></table>";
// 2004.10.09 suzukaze update end
	}

	return array ( $lngResultID, $lngResultNum, $strErrorMessage );
}



/**
* GET�ǡ�������URL�����ؿ�
*
*	@param  Array  $aryData GET�ǡ���
*	@return String          URL(**.php?�������ʹߤ�ʸ����)
*	@access public
*/
function fncGetURL( $aryData )
{
	$artyKeys = array_keys ( $aryData );

	foreach ( $artyKeys as $strKey )
	{
		$aryURL[] = $strKey . "=" . $aryData[$strKey];
	}

	return join ( "&", $aryURL );
}



/**
* ɸ��������ؿ�
*
*	ɸ����ǡ���������ؿ�
*
*	@param  String  $strProductCode	 ���ʥ�����
*	@param  Object  $objDB			 DB���֥�������
*	@return Integer $curStandardRate ɸ����
*	@access public
*/
function fncGetEstimateDefault( $objDB )
{
	list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT To_char( curstandardrate, '990.9999' ) as curstandardrate FROM m_EstimateStandardRate WHERE dtmApplyStartDate < NOW() AND dtmApplyEndDate > NOW()", $objDB );

	if ( $lngResultNum < 1 )
	{
// 2004.10.01 suzukaze update start
		// �⤷�����ɸ���礬���ȤǤ��ʤ����ǿ������դ�ɸ����򻲾�
		list ( $lngResultMaxID, $lngResultMaxNum ) = fncQuery( "select To_char( curstandardrate, '990.9999' ) as curstandardrate from m_estimatestandardrate where dtmapplyenddate = (select max(dtmapplyenddate) from m_estimatestandardrate);", $objDB );

		if ( $lngResultMaxNum < 1 )
		{
			fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
		}
		else
		{
			$lngResultNum = $lngResultMaxNum;
			$lngResultID  = $lngResultMaxID;
		}
// 2004.10.01 suzukaze update end
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$objDB->freeResult( $lngResultID );

	$curStandardRate = $objResult->curstandardrate;

// ɸ������ͤˤĤ��Ƥ� ��ɽ���ˤư���
	$curStandardRate = $curStandardRate * 100;

	unset ( $objResult );

	return $curStandardRate;
}



/**
* ���Ѹ����������μ����̲߼����ؿ�
*
*	�����̲ߥǡ���������ؿ�
*
*	@param  String  $dtmInsertDate	 ���Ѹ�����Ͽ��
*	@param  Object  $objDB			 DB���֥�������
*	@return Integer $curStandardRate ɸ����
*	@access public
*/
function fncGetUSConversionRate( $dtmInsertDate, $objDB )
{
	if ( $dtmInsertDate == "" )
	{
		$dtmInsertDate = date("Y-m-d");
	}

	$aryQuery[] = "SELECT mmr.lngMonetaryRateCode, mmr.curConversionRate, MAX(mmr.dtmapplystartdate) ";
	$aryQuery[] = "FROM m_MonetaryRate mmr ";
	$aryQuery[] = "JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
	$aryQuery[] = "WHERE mmr.lngmonetaryratecode = '" . DEF_MONETARYCLASS_SHANAI . "' ";
	$aryQuery[] = "	AND mmu.lngmonetaryunitcode = '" . DEF_MONETARY_USD . "' ";
	$aryQuery[] = "	AND mmr.dtmapplystartdate = (SELECT MAX(mmr2.dtmapplystartdate) FROM m_MonetaryRate mmr2 WHERE mmr2.lngMonetaryRateCode = mmr.lngMonetaryRateCode AND mmr2.lngMonetaryUnitCode = mmr.lngMonetaryUnitCode) ";
	$aryQuery[] = "GROUP BY mmr.lngMonetaryRateCode, mmr.curConversionRate ";
	$aryQuery[] = "UNION ";
	$aryQuery[] = "SELECT mmr.lngMonetaryRateCode, mmr.curConversionRate, MAX(mmr.dtmapplystartdate) ";
	$aryQuery[] = "FROM m_MonetaryRate mmr ";
	$aryQuery[] = "JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
	$aryQuery[] = "WHERE mmr.dtmapplystartdate <= '" . $dtmInsertDate . "' ";
	$aryQuery[] = "	AND mmr.dtmapplyenddate >= '" . $dtmInsertDate . "' ";
	$aryQuery[] = "	AND mmr.lngmonetaryratecode = '" . DEF_MONETARYCLASS_SHANAI . "' ";
	$aryQuery[] = "	AND mmu.lngmonetaryunitcode = '" . DEF_MONETARY_USD . "' ";
	$aryQuery[] = "GROUP BY mmr.lngMonetaryRateCode, mmr.curConversionRate ";
	$aryQuery[] = "ORDER BY 3 ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 9061, DEF_WARNING, "", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$objDB->freeResult( $lngResultID );

	$curConversionRate = $objResult->curconversionrate;

	unset ( $objResult );

	return $curConversionRate;
}



/**
* �ǥե���ȸ��Ѹ��������ؿ�
*
*	�ǥե���ȸ��Ѹ��������ǡ���������ؿ�
*
*	@param  Integer $lngProductionQuantity	����ͽ���
*	@param  Array  	$curProductPrice		Ǽ��
*	@param  Array  	$aryRate		 �̲ߥ졼�ȥ����ɤ򥭡��Ȥ����̲ߥ졼��
*	@param  Object  $objDB			 DB���֥�������
*	@return Array 	$aryDefaultValue �ǥե������
*	@access public
*/
function fncGetEstimateDefaultValue( $lngProductionQuantity, $curProductPrice, $aryRate, $objDB, $sessionid )
{
	require_once( LIB_DEBUGFILE );

	global $g_aryTemp;



	$aryQuery[] = "SELECT *";
	$aryQuery[] = "FROM m_EstimateDefault e";
	$aryQuery[] = " LEFT JOIN m_Company c ON ( e.lngCustomerCompanyCode = c.lngCompanyCode )";
	$aryQuery[] = "WHERE e.dtmApplyStartDate < NOW() AND e.dtmApplyEndDate > NOW()";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum < 1 )
	{
		fncOutputError( 1502, DEF_WARNING, "", TRUE, "estimate/regist/edit.php?strSessionID=" . $sessionid . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}

	// �������ܤ�����ο��ͥ������б������뤿�����������
	$aryStockKey = Array ( "431" => 0 , "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7 );

	// ����������Υ����󥿡����������
	$aryCount = Array ( "431" => 0 , "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0 );

	// Boolean���б������뤿�����������
	$aryBooleanString = Array ( "t" => "true" , "f" => "false", "true" => "true" , "false" => "false", "" => "false" );

	$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );




	// Temp�ե饰��ͭ��
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
		$aryDefaultValue	= $g_aryTemp["aryDitail"];
	}
	else
	{
		// ���Ѹ����ơ��֥�ǡ�������
		// ���٤ο������롼��
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngResultID, $i );

			// $aryDetail[�����������ֹ�][�����襫���󥿡�][���٥����̾]
			// �������ܥ�����
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockSubjectCode"]
			= $objResult->lngstocksubjectcode;

			// �������ʥ�����
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockItemCode"]
			= $objResult->lngstockitemcode;

			// �����оݥե饰
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPayOffTargetFlag"]
			= $aryBooleanString[$objResult->bytpayofftargetflag];

			// �����襳����
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngCustomerCompanyCode"]
			= $objResult->strcompanydisplaycode;

			// %���ϥե饰
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPercentInputFlag"]
			= $aryBooleanString[$objResult->bytpercentinputflag];

			// �⤷���ѡ���������ϥե饰�����ꤵ��Ƥ���аʲ����ͤ����������ꤹ��
			if ( $aryBooleanString[$objResult->bytpercentinputflag] == "true" )
			{
				// �ײ�Ŀ�
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
				= $lngProductionQuantity;

				// �ײ�Ψ
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductRate"]
				= $objResult->curproductrate;

				// �ײ踶��
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				= $lngProductionQuantity * $curProductPrice * $objResult->curproductrate;
			}
			else
			{
				// �ײ�Ŀ�
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
				= $objResult->lngproductquantity;

				// �ײ�Ψ
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductRate"]
				= $objResult->curproductrate;

				// �ײ踶��
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				= $objResult->cursubtotalprice;
			}

			// ñ��
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductPrice"]
			= $objResult->curproductprice;

			// ����
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strNote"]
			= $objResult->strnote;

			// �̲ߥ�����
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngMonetaryUnitCode"]
			= $aryMonetaryUnit[$objResult->lngmonetaryunitcode];


			// ���ܱ߰ʳ��ηײ踶������
			if ( count ( $aryRate ) > 0 )
			{
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
				= $aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				 * $aryRate[$objResult->lngmonetaryunitcode];
			}
			// ���ܱߤηײ踶������
			else
			{
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
				= $aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				 * $objResult->curconversionrate;
			}

			// �����졼��
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curConversionRate"]
			= $objResult->curconversionrate;


			//$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
			//= $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname;

			// ������̾��
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
			= $objResult->strcompanydisplayname;

			$aryCount[$objResult->lngstocksubjectcode]++;
			unset ( $objResult );
		}
	}


//fncDebug( 'es_temp.txt', $aryDefaultValue, __FILE__, __LINE__);


	unset ( $lngResultID );
	unset ( $lngResultNum );
	unset ( $aryCount );

	return $aryDefaultValue;
}



/**
* �̲ߥ졼�ȼ����ؿ�
*
*	�̲ߥ졼�ȥ�����ؿ�
*
*	@param  Object $objDB	DB���֥�������
*	@return Array  $aryRate	�̲ߥ졼��
*	@access public
*/
function fncGetMonetaryRate( $objDB )
{
	$aryQuery[] = "SELECT mmr.lngMonetaryUnitCode, mmr.curConversionRate ";
	$aryQuery[] = "FROM m_MonetaryRate mmr ";
	$aryQuery[] = "JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
	$aryQuery[] = "WHERE mmr.lngmonetaryratecode = '" . DEF_MONETARYCLASS_SHANAI . "' ";
	$aryQuery[] = "	AND mmr.dtmapplystartdate = (SELECT MAX(mmr2.dtmapplystartdate) FROM m_MonetaryRate mmr2 WHERE mmr2.lngMonetaryRateCode = mmr.lngMonetaryRateCode AND mmr2.lngMonetaryUnitCode = mmr.lngMonetaryUnitCode) ";
	$aryQuery[] = "GROUP BY mmr.lngMonetaryUnitCode, mmr.curConversionRate ";
	$aryQuery[] = "UNION ";
	$aryQuery[] = "SELECT mmr.lngMonetaryUnitCode, mmr.curConversionRate ";
	$aryQuery[] = "FROM m_MonetaryRate mmr ";
	$aryQuery[] = "JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
	$aryQuery[] = "WHERE mmr.dtmapplystartdate <= 'now()' ";
	$aryQuery[] = "	AND mmr.dtmapplyenddate >= 'now()' ";
	$aryQuery[] = "	AND mmr.lngmonetaryratecode = '" . DEF_MONETARYCLASS_SHANAI . "' ";
	$aryQuery[] = "GROUP BY mmr.lngMonetaryUnitCode, mmr.curConversionRate ";
	$aryQuery[] = "ORDER BY 1 ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum > 0 )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngResultID, $i );
			$aryRate[$objResult->lngmonetaryunitcode] = $objResult->curconversionrate;
		}
		$objDB->freeResult( $lngResultID );
		unset ( $objResult );
	}

	return $aryRate;
}



/**
* ���ʾ�������ؿ�
*
*	strProductCode ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ���ǡ������������ؿ�
*
*	@param  String $strProductCode	���ʥ�����
*	@param  Object $objDB			DB���֥�������
*	@return Array  $aryData			���ʥǡ���
*	@access public
*/
function fncGetProduct( $strProductCode, $objDB, $lngUserCode = "" )
{
	require_once( LIB_DEBUGFILE );



	$aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
	$aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM') AS dtmDeliveryLimitDate,";
	$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
	$aryQuery[] = " u.strUserDisplayCode AS strInChargeUserDisplayCode,";
	$aryQuery[] = " u.strUserDisplayName AS strInChargeUserDisplayName,";
	$aryQuery[] = " p.curRetailPrice, ";
	$aryQuery[] = " p.lngCartonQuantity,";
	$aryQuery[] = " p.lngproductstatuscode,";

	// ����ñ�̤�ctn�ʤ�С�����ͽ�����pcs���Ѵ�����
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	$aryQuery[] = " p.lngProductionUnitCode,";

	// ����ñ�̤�ctn�ʤ�С��ײ�C/t�Ϥ��Τޤ�����ͽ���
	$aryQuery[] = " CASE WHEN ( p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_PCS . " OR p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_SET . " ) AND p.lngCartonQuantity <> 0 ";
	$aryQuery[] = "  THEN p.lngProductionQuantity / p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngPlanCartonProduction,";

	$aryQuery[] = " p.curProductPrice, ";
	$aryQuery[] = " to_char(p.dtmInsertDate, 'YYYY/MM/DD') AS dtmInsertDate ";

	$aryQuery[] = "FROM m_Product p";
	$aryQuery[] = "LEFT OUTER JOIN m_Group g ON ( p.lngInChargeGroupCode = g.lngGroupCode )";
	$aryQuery[] = "LEFT OUTER JOIN m_User u ON ( p.lngInChargeUserCode = u.lngUserCode )";
	$aryQuery[] = "WHERE strProductCode = '" . $strProductCode . "'";
	$aryQuery[] = " AND p.bytInvalidFlag = FALSE";



	$aryQuery[] = "and " . $lngUserCode . " in";
	$aryQuery[] = "(";
	$aryQuery[] = "select";
	$aryQuery[] = "mu1.lngusercode";
	$aryQuery[] = "from";
	$aryQuery[] = "m_user mu1";
	$aryQuery[] = ",m_grouprelation mgr1";
	$aryQuery[] = ",";
	$aryQuery[] = "(";
	$aryQuery[] = "select";
	$aryQuery[] = "mu.lngusercode";
	$aryQuery[] = ",mg.lnggroupcode";
	$aryQuery[] = "from";
	$aryQuery[] = "m_user mu";
	$aryQuery[] = "left join m_grouprelation mgr";
	$aryQuery[] = "on mgr.lngusercode = mu.lngusercode";
	//$aryQuery[] = "and mgr.bytdefaultflag = true";
	$aryQuery[] = "left join m_group mg";
	$aryQuery[] = "on mg.lnggroupcode = mgr.lnggroupcode";
	$aryQuery[] = "where";
	$aryQuery[] = "mu.lngusercode = p.lnginchargeusercode";
	$aryQuery[] = ") as mst1";
	$aryQuery[] = "where";
	$aryQuery[] = "mgr1.lnggroupcode = mst1.lnggroupcode";
	//$aryQuery[] = "and mgr1.bytdefaultflag = true";
	$aryQuery[] = "and mu1.bytinvalidflag = false";
	$aryQuery[] = "and mu1.lngusercode = mgr1.lngusercode";
	$aryQuery[] = "and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode or mu1.lngusercode in ('15','29','242','343'))";
	$aryQuery[] = ")";


	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );




	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum < 1 )
	{
		return FALSE;
	}


	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$objDB->freeResult( $lngResultID );
	unset ( $lngResultID );
	unset ( $lngResultNum );




	global $g_aryTemp;
	global $aryEstimateData;

	// �ƥ�ݥ��ե饰��ͭ��
	if( $aryEstimateData["blnTempFlag"] )
	{
//		$aryData["strProductCode"]				= $aryEstimateData["strProductCode"];
		$aryData["strProductName"]				= $aryEstimateData["strProductName"];
		$aryData["dtmDeliveryLimitDate"]		= $aryEstimateData["dtmDeliveryLimitDate"];
		$aryData["strInChargeGroupDisplayCode"]	= $aryEstimateData["strInChargeGroupDisplayCode"];
		$aryData["strInChargeUserDisplayCode"]	= $aryEstimateData["strInChargeUserDisplayCode"];
		$aryData["strInChargeUserDisplayName"]	= $aryEstimateData["strInChargeUserDisplayName"];
		$aryData["curRetailPrice"]				= $aryEstimateData["curRetailPrice"];
		$aryData["lngCartonQuantity"]			= $aryEstimateData["lngCartonQuantity"];
		$aryData["lngPlanCartonProduction"]		= $aryEstimateData["lngPlanCartonProduction"];
		$aryData["lngProductionQuantity"]		= $aryEstimateData["lngProductionQuantity"];
		$aryData["curProductPrice"]				= $aryEstimateData["curProductPrice"];
		$aryData["curProductPrice_hidden"]		= $aryEstimateData["curProductPrice"];

//		$aryData["curConversionRate"]			= $aryEstimateData["curConversionRate"];	// �����졼��
//		$aryData["curStandardRate"]				= $aryEstimateData["curStandardRate"];		// ɸ����
		$aryData["blnTempFlag"]					= $aryEstimateData["blnTempFlag"];			// �ƥ�ݥ��ե饰
	}
	// �ե�����ƥ�ݥ��ե饰��ͭ��
	else if( $g_aryTemp["bytTemporaryFlg"] )
	{
//fncDebug( 'es_temp2.txt', $g_aryTemp["lngCartonQuantity"], __FILE__, __LINE__);

//		$aryData["strProductCode"]				= $g_aryTemp["strProductCode"];
		$aryData["strProductName"]				= $g_aryTemp["strProductName"];
		$aryData["dtmDeliveryLimitDate"]		= $g_aryTemp["dtmDeliveryLimitDate"];
		$aryData["strInChargeGroupDisplayCode"]	= $g_aryTemp["strGroupDisplayCode"];
		$aryData["strInChargeUserDisplayCode"]	= $g_aryTemp["strUserDiplayCode"];
		$aryData["strInChargeUserDisplayName"]	= $g_aryTemp["strUserDisplayName"];
		$aryData["curRetailPrice"]				= $g_aryTemp["curRetailPrice"];
		$aryData["lngCartonQuantity"]			= $g_aryTemp["lngCartonQuantity"];				// lngCartonQuantity
		$aryData["lngPlanCartonProduction"]		= $g_aryTemp["lngPlanCartonProduction"];
		$aryData["lngProductionQuantity"]		= $g_aryTemp["lngProductionQuantity_hidden"];	// lngProductionQuantity
		$aryData["curProductPrice"]				= $g_aryTemp["curProductPrice_hidden"];			// curProductPrice
		$aryData["curProductPrice_hidden"]		= $g_aryTemp["curProductPrice_hidden"];			// curProductPrice_hidden

		$aryData["strRemark"]					= $g_aryTemp["strRemark"];						// ������

fncDebug( 'tempdata.txt', $g_aryTemp, __FILE__, __LINE__);
	}
	// �̾�
	else
	{


//		$aryData["strProductCode"]				= $objResult->strproductcode;
		$aryData["strProductName"]				= $objResult->strproductname;
		$aryData["dtmDeliveryLimitDate"]		= $objResult->dtmdeliverylimitdate;
		$aryData["strInChargeGroupDisplayCode"]	= $objResult->strinchargegroupdisplaycode;
		$aryData["strInChargeUserDisplayCode"]	= $objResult->strinchargeuserdisplaycode;
		$aryData["strInChargeUserDisplayName"]	= $objResult->strinchargeuserdisplayname;
		$aryData["curRetailPrice"]				= $objResult->curretailprice;
		$aryData["lngCartonQuantity"]			= $objResult->lngcartonquantity;
		$aryData["lngPlanCartonProduction"]		= $objResult->lngplancartonproduction;
		$aryData["lngProductionQuantity"]		= $objResult->lngproductionquantity;
		$aryData["curProductPrice"]				= $objResult->curproductprice;
		$aryData["curProductPrice_hidden"]		= $objResult->curproductprice;
	}

	$aryData["strProductCode"]			= $objResult->strproductcode;
	$aryData["lngProductionUnitCode"]	= $objResult->lngproductionunitcode;
	$aryData["dtmInsertDate"]			= $objResult->dtminsertdate;
	$aryData["lngproductstatuscode"]	= $objResult->lngproductstatuscode;	// ���ʾ���

	unset ( $objResult );

//fncDebug( 'tempdata.txt', $aryData, __FILE__, __LINE__);
	return $aryData;
}



/**
* ���Ѹ����׻������ؿ�
*
*	lngEstimateNo ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ���ǡ������������ؿ�
*
*	@param  String $lngEstimateNo	���Ѹ����ʥ�С�
*	@param  Object $objDB			DB���֥�������
*	@return Array  $aryData			���Ѹ����ǡ���
*	@access public
*/
function fncGetEstimate( $lngEstimateNo, $objDB )
{
	$aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
	$aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM') AS dtmDeliveryLimitDate,";
	$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
	$aryQuery[] = " u.strUserDisplayCode AS strInChargeUserDisplayCode,";
	$aryQuery[] = " u.strUserDisplayName AS strInChargeUserDisplayName,";
	$aryQuery[] = " p.curRetailPrice, ";
	$aryQuery[] = " p.lngCartonQuantity,";

	// ����ñ�̤�ctn�ʤ�С�����ͽ�����pcs���Ѵ�����
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	// ����ñ�̤�ctn�ʤ�С��ײ�C/t�Ϥ��Τޤ�����ͽ���
	$aryQuery[] = " CASE WHEN ( p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_PCS . " OR p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_SET . " ) AND p.lngCartonQuantity <> 0 ";
	$aryQuery[] = "  THEN p.lngProductionQuantity / p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngPlanCartonProduction,";

	$aryQuery[] = " p.lngProductionUnitCode,";
	$aryQuery[] = " p.curProductPrice, ";
	$aryQuery[] = " e.lngRevisionNo, ";
	$aryQuery[] = " e.lngEstimateStatusCode, ";
	$aryQuery[] = " e.curFixedCost, ";
	$aryQuery[] = " e.curMemberCost, ";
	$aryQuery[] = " e.curManufacturingCost, ";
	$aryQuery[] = " to_char( e.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate, ";
	$aryQuery[] = " e.lngInputUserCode, ";
	$aryQuery[] = " e.bytDecisionFlag, ";
	$aryQuery[] = " e.lngProductionQuantity AS lngOldProductionQuantity ";
	$aryQuery[] = " ,e.strNote ";

	$aryQuery[] = "FROM m_Estimate e";
	$aryQuery[] = "INNER JOIN m_Product p ON ( e.strProductCode = p.strProductCode AND p.bytInvalidFlag = FALSE )";
	$aryQuery[] = "LEFT JOIN m_Group g ON ( p.lngInChargeGroupCode = g.lngGroupCode )";
	$aryQuery[] = "LEFT OUTER JOIN m_User u ON ( p.lngInChargeUserCode = u.lngUserCode )";
	$aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo;
	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );

	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$objDB->freeResult( $lngResultID );
	unset ( $lngResultID );
	unset ( $lngResultNum );





	$blnTempFlag	= false;	// �ƥ�ݥ��ե饰
	$lngTempNo		= 0;		// �ƥ�ݥ���ֹ�
	$aryTemp		= array();	// �ƥ�ݥ��ǡ�������

	// ���Ѿ��֤��־�ǧ�װʳ��ξ��
	if( $objResult->lngestimatestatuscode != DEF_ESTIMATE_APPROVE )
	{
		// �ƥ�ݥ���ֹ����
		$lngTempNo = fncGetMasterValue( "m_estimate", "lngestimateno", "lngtempno",  $lngEstimateNo, '', $objDB );

		// �ƥ�ݥ���ֹ椬¸�ߤ�����
		if( $lngTempNo )
		{
			// �ƥ�ݥ��ǡ�������
			$aryTemp = fncGetTempData($objDB, $lngTempNo);

			// �ƥ�ݥ��ǡ����������Ԥξ��
			if( !$aryTemp ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

			// ����
			else $blnTempFlag	= true;
		}
	}

//fncDebug( 'tempno.txt', $aryTemp, __FILE__, __LINE__);


	// �ƥ�ݥ��ե饰ͭ��
	if( $blnTempFlag )
	{
		$aryData["strProductName"]				= $aryTemp["strproductname"];
		$aryData["dtmDeliveryLimitDate"]		= $aryTemp["dtmdeliverylimitdate"];
		$aryData["strInChargeGroupDisplayCode"]	= $aryTemp["strgroupdisplaycode"];
		$aryData["lngCartonQuantity"]			= $aryTemp["lngcartonquantity"];
		$aryData["lngProductionQuantity"]		= $aryTemp["lngproductionquantity"];
		$aryData["curProductPrice"]				= $aryTemp["curproductprice"];
		$aryData["curProductPrice_hidden"]		= $aryTemp["curproductprice"];
		$aryData["curRetailPrice"]				= $aryTemp["curretailprice"];

		$aryData["lngPlanCartonProduction"]		= $aryTemp["lngplancartonproduction"];	// �ײ�C/t

//		$aryData["curConversionRate"]			= $aryTemp["curconversionrate"];		// �����졼��
//		$aryData["curStandardRate"]				= $aryTemp["curstandardrate"];			// ɸ����
	}
	// �̾�
	else
	{
		$aryData["strProductName"]				= $objResult->strproductname;
		$aryData["dtmDeliveryLimitDate"]		= $objResult->dtmdeliverylimitdate;
		$aryData["strInChargeGroupDisplayCode"]	= $objResult->strinchargegroupdisplaycode;
		$aryData["lngCartonQuantity"]			= $objResult->lngcartonquantity;
		$aryData["lngProductionQuantity"]		= $objResult->lngproductionquantity;
		$aryData["curProductPrice"]				= $objResult->curproductprice;
		$aryData["curProductPrice_hidden"]		= $objResult->curproductprice;
		$aryData["curRetailPrice"]				= $objResult->curretailprice;

		$aryData["lngPlanCartonProduction"]		= $objResult->lngplancartonproduction;
	}

	$aryData["blnTempFlag"]					= $blnTempFlag;	// �ƥ�ݥ��ե饰


	$aryData["lngEstimateNo"]				= $lngEstimateNo;
	$aryData["strProductCode"]				= $objResult->strproductcode;
	$aryData["strInChargeUserDisplayCode"]	= $objResult->strinchargeuserdisplaycode;
	$aryData["strInChargeUserDisplayName"]	= $objResult->strinchargeuserdisplayname;
	$aryData["lngProductionUnitCode"]		= $objResult->lngproductionunitcode;
	$aryData["lngRevisionNo"]				= $objResult->lngrevisionno;
	$aryData["lngEstimateStatusCode"]		= $objResult->lngestimatestatuscode;
	$aryData["curFixedCostSubtotal"]		= 0;
	$aryData["curFixedCost"]				= $objResult->curfixedcost;
	$aryData["curMemberCost"]				= $objResult->curmembercost;
	$aryData["curManufacturingCost"]		= $objResult->curmanufacturingcost;
	$aryData["dtmInsertDate"]				= $objResult->dtminsertdate;
	$aryData["lngInputUserCode"]			= $objResult->lnginputusercode;
	$aryData["bytDecisionFlag"]				= $objResult->bytdecisionflag;
	$aryData["lngOldProductionQuantity"]	= $objResult->lngoldproductionquantity;

	$aryData["strRemark"]					= $objResult->strnote;	// ������

	unset ( $objResult );

//fncDebug( 'tempdata.txt', $aryData, __FILE__, __LINE__);

	return $aryData;
}



// 2004.09.28 suzukaze update start
/**
* ���Ѹ����׻������ؿ�
*
*	strProductCode ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ���ǡ������������ؿ�
*
*	@param  String $strProductCode	���ʥ�����
*	@param  Object $objDB			DB���֥�������
*	@return Array  $aryData			���Ѹ����ǡ���
*	@access public
*/
function fncGetEstimateToProductCode( $strProductCode, $objDB )
{
	require_once( LIB_DEBUGFILE );

	global $g_aryTemp;



	$aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
	$aryQuery[] = " to_char(p.dtmDeliveryLimitDate,'YYYY/MM/DD') AS dtmDeliveryLimitDate,";
	$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
	$aryQuery[] = " u.strUserDisplayCode AS strInChargeUserDisplayCode,";
	$aryQuery[] = " u.strUserDisplayName AS strInChargeUserDisplayName,";
	$aryQuery[] = " p.curRetailPrice, ";
	$aryQuery[] = " p.lngCartonQuantity,";

	// ����ñ�̤�ctn�ʤ�С�����ͽ�����pcs���Ѵ�����
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	// ����ñ�̤�ctn�ʤ�С��ײ�C/t�Ϥ��Τޤ�����ͽ���
	$aryQuery[] = " CASE WHEN ( p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_PCS . " OR p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_SET . " ) AND p.lngCartonQuantity <> 0 ";
	$aryQuery[] = "  THEN p.lngProductionQuantity / p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngPlanCartonProduction,";

	$aryQuery[] = " p.lngProductionUnitCode,";
	$aryQuery[] = " p.curProductPrice, ";
	$aryQuery[] = " e.lngRevisionNo, ";
	$aryQuery[] = " e.lngEstimateStatusCode, ";
	$aryQuery[] = " e.curFixedCost, ";
	$aryQuery[] = " e.curMemberCost, ";
	$aryQuery[] = " e.curManufacturingCost, ";
	$aryQuery[] = " e.lngEstimateNo ";
	$aryQuery[] = " ,e.strNote ";

	$aryQuery[] = "FROM m_Estimate e";
	$aryQuery[] = "INNER JOIN m_Product p ON ( e.strProductCode = p.strProductCode AND p.bytInvalidFlag = FALSE )";
	$aryQuery[] = "LEFT OUTER JOIN m_Group g ON ( p.lngInChargeGroupCode = g.lngGroupCode )";
	$aryQuery[] = "LEFT OUTER JOIN m_User u ON ( p.lngInChargeUserCode = u.lngUserCode )";
	$aryQuery[] = "WHERE e.strProductCode = '" . $strProductCode . "'";
	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );

	if ( $lngResultNum < 1 )
	{
		return FALSE;
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$objDB->freeResult( $lngResultID );
	unset ( $lngResultID );
	unset ( $lngResultNum );


	// �ե�����ƥ�ݥ��ե饰ͭ��
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
		$aryData["strProductCode"]				= $g_aryTemp["strProductCode"];
		$aryData["strProductName"]				= $g_aryTemp["strProductName"];
		$aryData["dtmDeliveryLimitDate"]		= $g_aryTemp["dtmDeliveryLimitDate"];
		$aryData["strInChargeGroupDisplayCode"]	= $g_aryTemp["strGroupDisplayCode"];
		$aryData["strInChargeUserDisplayCode"]	= $g_aryTemp["strUserDiplayCode"];
		$aryData["strInChargeUserDisplayName"]	= $g_aryTemp["strUserDisplayName"];
		$aryData["curRetailPrice"]				= $g_aryTemp["curRetailPrice"];
		$aryData["lngCartonQuantity"]			= $g_aryTemp["lngCartonQuantity"];
		$aryData["lngPlanCartonProduction"]		= $g_aryTemp["lngPlanCartonProduction"];
		$aryData["lngProductionQuantity"]		= $g_aryTemp["lngProductionQuantity"];
		$aryData["curProductPrice"]				= $g_aryTemp["curProductPrice"];
		$aryData["curProductPrice_hidden"]		= $g_aryTemp["curProductPrice"];
		$aryData["strRemark"]					= $g_aryTemp["strRemark"];
	}
	// �̾�
	else
	{
		$aryData["strProductCode"]				= $objResult->strproductcode;
		$aryData["strProductName"]				= $objResult->strproductname;
		$aryData["dtmDeliveryLimitDate"]		= $objResult->dtmdeliverylimitdate;
		$aryData["strInChargeGroupDisplayCode"]	= $objResult->strinchargegroupdisplaycode;
		$aryData["strInChargeUserDisplayCode"]	= $objResult->strinchargeuserdisplaycode;
		$aryData["strInChargeUserDisplayName"]	= $objResult->strinchargeuserdisplayname;
		$aryData["curRetailPrice"]				= $objResult->curretailprice;
		$aryData["lngCartonQuantity"]			= $objResult->lngcartonquantity;
		$aryData["lngPlanCartonProduction"]		= $objResult->lngplancartonproduction;
		$aryData["lngProductionQuantity"]		= $objResult->lngproductionquantity;
		$aryData["curProductPrice"]				= $objResult->curproductprice;
		$aryData["curProductPrice_hidden"]		= $objResult->curproductprice;
		$aryData["strRemark"]					= $objResult->strnote;
	}

	$aryData["lngProductionUnitCode"]	= $objResult->lngproductionunitcode;
	$aryData["lngRevisionNo"]			= $objResult->lngrevisionno;
	$aryData["lngEstimateStatusCode"]	= $objResult->lngestimatestatuscode;
	$aryData["curFixedCost"]			= $objResult->curfixedcost;
	$aryData["curMemberCost"]			= $objResult->curmembercost;
	$aryData["curManufacturingCost"]	= $objResult->curmanufacturingcost;
	$aryData["lngEstimateNo"]			= $objResult->lngestimateno;


	unset ( $objResult );

	return $aryData;
}
// 2004.09.28 suzukaze update end



/**
* ���Ѹ����׻����ټ����ؿ�
*
*	lngEstimateNo ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ������٥ǡ������������ؿ�
*
*	@param  String $lngEstimateNo	���Ѹ����ʥ�С�
*	@param  Array  $aryRate			�̲ߥ졼�ȥ����ɤ򥭡��Ȥ����̲ߥ졼��
*	@param  Object $objDB			DB���֥�������
*	@return Array  $aryDetail		���Ѹ������٥ǡ���
*	@access public
*/
function fncGetEstimateDetail( $lngEstimateNo, $aryRate, $objDB )
{

require_once( LIB_DEBUGFILE );

/*
	$aryQuery[] = "SELECT *";
	$aryQuery[] = "FROM t_EstimateDetail e";
	$aryQuery[] = " LEFT JOIN m_Company c ON ( e.lngCustomerCompanyCode = c.lngCompanyCode )";
	$aryQuery[] = " INNER JOIN m_StockSubject ss ON ( e.lngStockSubjectCode = ss.lngStockSubjectCode )";
	$aryQuery[] = " INNER JOIN m_StockItem si ON ( e.lngStockItemCode = si.lngStockItemCode AND e.lngStockSubjectCode = si.lngStockSubjectCode)";
	$aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo;
//	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo AND e.lngEstimateDetailNo = e2.lngEstimateDetailNo)";
//================================================================================================
//��050407bykou revisionno��MAX�ͤ����ʤ��ν���
	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
//================================================================================================

	$aryQuery[] = " ORDER BY e.lngStockSubjectCode, e.lngEstimateDetailNo ";
*/
	$aryQuery[] = "SELECT";
	 $aryQuery[] = "	te.lngestimateno,	";
	 $aryQuery[] = "	te.lngestimatedetailno,	";
	 $aryQuery[] = "	te.lngrevisionno,	";
	 $aryQuery[] = "	te.lngstocksubjectcode,	";
	 $aryQuery[] = "	ss.strStockSubjectName,	";
	 $aryQuery[] = "	te.lngstockitemcode,	";
	 $aryQuery[] = "	si.strStockItemName,	";
	 $aryQuery[] = "	te.lngcustomercompanycode,	";
	 $aryQuery[] = "	mc.strCompanyDisplayCode,	";
	 $aryQuery[] = "	mc.strCompanyDisplayName,	";
	 $aryQuery[] = "	te.bytpayofftargetflag,	";
	 $aryQuery[] = "	te.bytpercentinputflag,	";
	 $aryQuery[] = "	te.lngmonetaryunitcode,	";
	 $aryQuery[] = "	te.lngmonetaryratecode,	";
	 $aryQuery[] = "	te.curconversionrate,	";
	 $aryQuery[] = "	te.lngproductquantity,	";
	 $aryQuery[] = "	te.curproductprice,	";
	 $aryQuery[] = "	te.curproductrate,	";
	 $aryQuery[] = "	te.cursubtotalprice,	";
	 $aryQuery[] = "	te.strnote,	";
	 $aryQuery[] = "	te.lngsortkey,	";
	 $aryQuery[] = "	te.lngsalesdivisioncode,	";
	 $aryQuery[] = "	te.lngsalesclasscode	";

	$aryQuery[] = "FROM t_EstimateDetail te";
	$aryQuery[] = "LEFT JOIN m_Company mc ON ( te.lngCustomerCompanyCode = mc.lngCompanyCode )";
	$aryQuery[] = "LEFT JOIN m_StockSubject ss ON ( te.lngStockSubjectCode = ss.lngStockSubjectCode )";
	$aryQuery[] = "LEFT JOIN m_StockItem si ON ((te.lngStockItemCode = si.lngStockItemCode AND te.lngStockSubjectCode = si.lngStockSubjectCode) )";
	$aryQuery[] = "LEFT JOIN m_salesclass msc on (te.lngsalesclasscode = msc.lngsalesclasscode)";
	$aryQuery[] = "LEFT JOIN m_salesdivision msd on (te.lngsalesdivisioncode = msd.lngsalesdivisioncode)";
	$aryQuery[] = "WHERE te.lngEstimateNo = " . $lngEstimateNo;
	$aryQuery[] = "AND te.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE te.lngEstimateNo = e2.lngEstimateNo)";
//	$aryQuery[] = "AND te.lngcustomercompanycode IS NOT NULL";
	$aryQuery[] = "ORDER BY te.lngStockSubjectCode, te.lngEstimateDetailNo ";

	$aryQuery = join ( " ", $aryQuery );
fncDebug( 'estimate_cmn_lib_e_0.txt', $aryQuery, __FILE__, __LINE__);

	list ( $lngResultID, $lngResultNum ) = fncQuery($aryQuery, $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// �������ܤ�����ο��ͥ������б������뤿�����������
	$aryStockKey = Array ( "431" => 0 , "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7 );

	// ����������Υ����󥿡����������
	$aryCount = Array ( "431" => 0 , "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0 );

	// Boolean���б������뤿�����������
	$aryBooleanString = Array ( "t" => "true" , "f" => "false", "true" => "true" , "false" => "false", "" => "false" );

	$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );


	$aryDetail = array();
	$lngSalesClassCnt = 0;
	

	// ���Ѹ����ơ��֥�ǡ�������
	// ���٤ο������롼��
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// ----------------------------------------------------------------
		// �������ܤ��󤫡����ʬ����󤫡���Ƚ�Ǥ���
		// null�Ǥ�̵����0�ʳ���������������
		// 0 �������ʬ��
		// ----------------------------------------------------------------
		if( !is_null($objResult->lngstocksubjectcode) && $objResult->lngstocksubjectcode != 0 )
		{
			// �������ܡ���ʬ���ν���
			$lngKamokuKey = $aryStockKey[$objResult->lngstocksubjectcode];
			$lngKamokuCnt = $aryCount[$objResult->lngstocksubjectcode];
		}
		else
		{

			$lngKamokuKey = 11;
			$lngKamokuCnt = $lngSalesClassCnt;

			// ���ʬ�ࡦ���ܡ��ν���
			// Key �ϡ�11�ɤ�����Ѥ� �� $aryMapping[PROC_SALES][SALESCLS_1]	����Ū�����������ˤ��ײ�¤
			$aryDetail[$lngKamokuKey][$lngSalesClassCnt]["lngSalesDivisionCode"]	= $objResult->lngsalesdivisioncode;
			$aryDetail[$lngKamokuKey][$lngSalesClassCnt]["lngSalesClassCode"] 		= $objResult->lngsalesclasscode;
			
			$lngSalesClassCnt++;	// 11��Υǡ������󥯥����
		}


		// $aryDetail[�����������ֹ�][�����襫���󥿡�][���٥����̾]
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["lngStockSubjectCode"]		= $objResult->lngstocksubjectcode;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strStockSubjectName"]		= $objResult->strstocksubjectname;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["lngStockItemCode"]		= $objResult->lngstockitemcode;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strStockItemName"]		= $objResult->strstockitemname;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["bytPayOffTargetFlag"]		= $aryBooleanString[$objResult->bytpayofftargetflag];
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["lngCustomerCompanyCode"]	= $objResult->strcompanydisplaycode;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strCompanyDisplayCode"]	= $objResult->strcompanydisplaycode;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strCompanyDisplayName"]	= $objResult->strcompanydisplayname;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["bytPercentInputFlag"]		= $aryBooleanString[$objResult->bytpercentinputflag];
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["lngProductQuantity"]		= $objResult->lngproductquantity;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curProductRate"]			= $objResult->curproductrate;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curProductPrice"]			= $objResult->curproductprice;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curSubTotalPrice"]		= $objResult->cursubtotalprice;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strNote"]					= $objResult->strnote;
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["lngMonetaryUnitCode"]		= $aryMonetaryUnit[$objResult->lngmonetaryunitcode];
/*
		if ( count ( $aryRate ) > 0 )
		{
			$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curSubTotalPriceJP"]	= $objResult->cursubtotalprice * $aryRate[$objResult->lngmonetaryunitcode];
		}
		else
		{
			$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curSubTotalPriceJP"]	= $objResult->cursubtotalprice * $objResult->curconversionrate;
		}
*/
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curConversionRate"]		= $objResult->curconversionrate;	//�졼�Ȥ���Ͽ���줿�졼�Ȥ�����
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curSubTotalPriceJP"]		= $objResult->cursubtotalprice * $objResult->curconversionrate;

		//$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strCompanyDisplayName"]	= $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname;
		//$aryDetail[$lngKamokuKey][$lngKamokuCnt]["strCompanyDisplayName"]	= $objResult->strcompanydisplayname;

		$aryCount[$objResult->lngstocksubjectcode]++;
		unset ( $objResult );
	}

	unset ( $lngResultID );
	unset ( $lngResultNum );
	unset ( $aryCount );

fncDebug( 'estimate_cmn_lib_e_2.txt', $aryDetail, __FILE__, __LINE__);

	return $aryDetail;
}



/**
* ���Ѹ����׻����ټ����ؿ��ʹ��������ѹ��ѡ�
*
*	lngEstimateNo ���鸫�Ѹ����׻��Ƽ�ɽ���˻��Ѥ������٥ǡ������������ؿ��ʹ����κݤϿ��������ʥޥ����ξ�����Ʒ׻���
*
*	@param  String	$lngEstimateNo				���Ѹ����ʥ�С�
*	@param  Array	$aryRate					�̲ߥ졼�ȥ����ɤ򥭡��Ȥ����̲ߥ졼��
*	@param	Integer	$lngProductionQuantity		���ʥޥ���������ͽ�����pcs)
*	@param	Integer	$lngOldProductionQuantity	���Ѹ����ޥ���������ͽ�����pcs)
*	@param	Float	$curProductPrice			���ʥޥ�����Ǽ��
*	@param  Object	$objDB						DB���֥�������
*	@return Array	$aryDetail					���Ѹ������٥ǡ���
*	@access public
*/
function fncGetEstimateDetailRenew( $lngEstimateNo, $aryRate, $lngProductionQuantity, $lngOldProductionQuantity, $curProductPrice,$curRetailPrice, $objDB )
{
	$aryQuery[] = "SELECT *";
	$aryQuery[] = "FROM t_EstimateDetail e";
	$aryQuery[] = " LEFT JOIN m_Company c ON ( e.lngCustomerCompanyCode = c.lngCompanyCode )";
	$aryQuery[] = " INNER JOIN m_StockSubject ss ON ( e.lngStockSubjectCode = ss.lngStockSubjectCode )";
	$aryQuery[] = " INNER JOIN m_StockItem si ON ( e.lngStockItemCode = si.lngStockItemCode AND e.lngStockSubjectCode = si.lngStockSubjectCode)";
	$aryQuery[] = "WHERE e.lngEstimateNo = " . $lngEstimateNo;
//	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo AND e.lngEstimateDetailNo = e2.lngEstimateDetailNo)";
//=============================================================================
//��050407bykou revisionno��MAX�ͤ����ʤ��ν���
	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
//=============================================================================

	$aryQuery[] = " ORDER BY e.lngStockSubjectCode, e.lngEstimateDetailNo ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// �������ܤ�����ο��ͥ������б������뤿�����������
	$aryStockKey = Array ( "431" => 0 , "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7 );

	// ����������Υ����󥿡����������
	$aryCount = Array ( "431" => 0 , "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0 );

	// Boolean���б������뤿�����������
	$aryBooleanString = Array ( "t" => "true" , "f" => "false", "true" => "true" , "false" => "false", "" => "false" );

	$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );

	// ���Ѹ����ơ��֥�ǡ�������
	// ���٤ο������롼��
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		$lngChangeFlag = 0;

		// $aryDetail[�����������ֹ�][�����襫���󥿡�][���٥����̾]
		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockSubjectCode"]
		= $objResult->lngstocksubjectcode;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strStockSubjectName"]
		= $objResult->strstocksubjectname;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockItemCode"]
		= $objResult->lngstockitemcode;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strStockItemName"]
		= $objResult->strstockitemname;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPayOffTargetFlag"]
		= $aryBooleanString[$objResult->bytpayofftargetflag];

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngCustomerCompanyCode"]
		= $objResult->strcompanydisplaycode;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayCode"]
		= $objResult->strcompanydisplaycode;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
		= $objResult->strcompanydisplayname;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPercentInputFlag"]
		= $aryBooleanString[$objResult->bytpercentinputflag];

		// �ײ�Ŀ������Ѹ����ޥ�������¸����Ƥ�������ͽ�����Ʊ���ͤξ�硢���ʥޥ���������ͽ������ѹ�����
		if ( $lngOldProductionQuantity != "" AND $lngOldProductionQuantity == $objResult->lngproductquantity )
		{
			$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
			= $lngProductionQuantity;
			$lngChangeFlag = 1;
		}
		else
		{
			$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
			= $objResult->lngproductquantity;
		}

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductRate"]
		= $objResult->curproductrate;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductPrice"]
		= $objResult->curproductprice;
//=================================================================================================================
// 050316 by kou
// �������鸫�ѥǡ������ɤ߼����˾��ʥޥ���������Ʒ׻��Τ��ᡡ�ڻ���㳰�����ꤹ��
//		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curRetailPrice"]
//		= $objResult->curretailprice;
		// �ײ�Ŀ����ѹ��ˤʤä����ϡ��ײ踶�����ѹ�����
		if ( $lngChangeFlag == 1 )
		{
			// �����ϥե饰��ON�ξ��
			if ( $aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPercentInputFlag"] == "true" )
			{
				if ($objResult->lngstocksubjectcode == "401" and $objResult->lngstockitemcode == "1" )
					{
						$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
						= $lngProductionQuantity * $curRetailPrice * $objResult->curproductrate;
						$curSubTotalPrice = $lngProductionQuantity * $curRetailPrice * $objResult->curproductrate;
					
					}
					else
//====================================================================================================================
					{
						$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
						= $lngProductionQuantity * $curProductPrice * $objResult->curproductrate;
						$curSubTotalPrice = $lngProductionQuantity * $curProductPrice * $objResult->curproductrate;
					}	
			}
			// OFF�ξ��
			else
			{
				$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				= $lngProductionQuantity * $objResult->curproductprice;
				$curSubTotalPrice = $lngProductionQuantity * $objResult->curproductprice;
			}
		}
		else
		{
			$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
			= $objResult->cursubtotalprice;
			$curSubTotalPrice = $objResult->cursubtotalprice;
		}

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strNote"]
		= $objResult->strnote;

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngMonetaryUnitCode"]
		= $aryMonetaryUnit[$objResult->lngmonetaryunitcode];

		if ( count ( $aryRate ) > 0 )
		{
			$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
			= $curSubTotalPrice * $aryRate[$objResult->lngmonetaryunitcode];
		}
		else
		{
			$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
			= $curSubTotalPrice * $objResult->curconversionrate;
		}

		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curConversionRate"]
		= $objResult->curconversionrate;

		//$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
		//= $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname;
		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
		= $objResult->strcompanydisplayname;

		$aryCount[$objResult->lngstocksubjectcode]++;
		unset ( $objResult );
	}

	unset ( $lngResultID );
	unset ( $lngResultNum );
	unset ( $aryCount );

	return $aryDetail;
}

/**
*
	���Ѹ����׻��� ���١������ʬ��HTML����
*
*/
function fncGetEstimateDetail_Sales_Html( $aryDetail, $strDetailTemplatePath, $objDB )
{
require_once( LIB_DEBUGFILE );

	// ���Ѹ������٥ƥ�ץ졼�ȼ���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( $strDetailTemplatePath );
	$strTemplate = $objTemplate->strTemplate;

	// �������ܥ����ɤ򥭡��Ȥ������̾������������
	$arySalesClass = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", "Array", "", $objDB );

	// ���ɽ�������ɤ򥭡��Ȥ�����̾Ϣ����������
	$aryCompanyName = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "strCompanyDisplayName", "Array", "", $objDB );

fncDebug( 'fncGetEstimateDetail_Sales_Html.txt', $aryDetail, __FILE__, __LINE__);



	$aryHiddenString = array();
	$arySalesDetail	= array();

	//////////////////////////////////////////////////////////////////
	// ���٥ǡ���
	//////////////////////////////////////////////////////////////////
	// $aryDetail[����ʬ][���ٹ�][����]
	for ( $i = 11; $i <= 11; $i++ )
	{

		$lngSalesDivisionCode = 0;
		$strSalesDivisionName = "";
		$curSubjectTotalCost = 0;

		for ( $j = 0; $j < count ( $aryDetail[$i] ); $j++ )
		{

			$lngStockItemCode = 0;
			// HIDDEN
			$aryKeys = array_keys ( $aryDetail[$i][$j] );


			foreach ( $aryKeys as $strKey )
			{
				$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][" . $strKey . "]' value='" . $aryDetail[$i][$j][$strKey] . "'>\n";

				if ( $strKey == "lngSalesDivisionCode" )
				{
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strSalesDivisionName\"]' value='" . "���������" . "'>\n";
					$aryDetail[$i][$j]["strSalesDivisionName"] = "���������";
					$lngSalesDivisionCode = $aryDetail[$i][$j][$strKey];
					$strSalesDivisionName = $aryDetail[$i][$j]["strSalesDivisionName"];
					$arySalesDetail[$i]["lngSalesDivisionCode"] = $lngSalesDivisionCode;
					$arySalesDetail[$i]["strSalesDivisionName"] = $strSalesDivisionName;
				}
				if ( $strKey == "lngSalesClassCode" )
				{
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strSalesClassName\"]' value='" . $arySalesClass[$aryDetail[$i][$j][$strKey]] . "'>\n";
					$aryDetail[$i][$j]["strSalesClassName"] = $arySalesClass[$aryDetail[$i][$j][$strKey]];
					$lngSalesClassCode = $aryDetail[$i][$j][$strKey];
					$strSalesClassName = $aryDetail[$i][$j]["strSalesClassName"];
				}

				if ( $strKey == "lngCustomerCompanyCode" )
				{
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strCompanyDisplayCode\"]' value='" . $aryDetail[$i][$j][$strKey] . "'>\n";
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strCompanyDisplayName\"]' value='" . $aryCompanyName[$aryDetail[$i][$j][$strKey]] . "'>\n";
					$aryDetail[$i][$j]["strCompanyDisplayCode"] = $aryDetail[$i][$j][$strKey];
					$aryDetail[$i][$j]["strCompanyDisplayName"] = $aryCompanyName[$aryDetail[$i][$j][$strKey]];
				}
				
				
			}
			
			// ����
			if( is_numeric($aryDetail[$i][$j]["lngProductQuantity"]) )
			{
				$aryDetail[$i][$j]["lngProductQuantity"] = floor($aryDetail[$i][$j]["lngProductQuantity"]);
			}
			// ñ��
			$aryDetail[$i][$j]["strPlanPrice"] = $aryDetail[$i][$j]["lngMonetaryUnitCode"] . " " . number_format($aryDetail[$i][$j]["curProductPrice"], 4, ".", ",");
//fncDebug( 'fncGetEstimateDetail_Sales_hidden_Tanka.txt', $aryDetail[$i][$j]["curSubTotalPrice"], __FILE__, __LINE__);

			//
			$curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
			$aryDetail[$i][$j]["curSubTotalPriceJP"] = number_format( $aryDetail[$i][$j]["curSubTotalPriceJP"], 2, '.', ',' );


			// �֤�����
			$objTemplate->replace( $aryDetail[$i][$j] );
			$objTemplate->complete();

						// ����������˥ƥ�ץ졼���ݻ�
			$aryDetailTemplate[$i] .= $objTemplate->strTemplate;
			$objTemplate->strTemplate = $strTemplate;

		}

		// ���ʬ�ब¸�ߤ��Ƥ����
//		if( (int)$lngSalesDivisionCode != 0 )
//		{
			// ����ʬ���פλ���
			$arySalesDetail[$i]["curSubjectTotalCost"] = number_format( $curSubjectTotalCost, 2, '.', ',' );

			// �ƥ�ץ졼���ɤ߹���
			$objSubTemplate = new clsTemplate();
			$objSubTemplate->getTemplate( "estimate/regist/plan_subjecttotal_sales.tmpl" );

			// �ƥ�ץ졼������
			$objSubTemplate->replace( $arySalesDetail[$i] );
			$objSubTemplate->complete();

			// ����������˥ƥ�ץ졼���ݻ�
			$aryDetailTemplate[$i] .= $objSubTemplate->strTemplate;

			unset ( $objSubTemplate );
//		}



		$aryEstimateDetail["strDetailSalesTemplate"]	.= $aryDetailTemplate[$i];

	}

fncDebug( 'fncGetEstimateDetail_Sales_hidden.txt', $arySalesDetail, __FILE__, __LINE__);

	return array($aryEstimateDetail, $curSubjectTotalCost, $aryHiddenString);
}


/**
* ���Ѹ����׻�����HTML����ʸ��������ؿ�
*
*	���Ѹ����׻����٥ǡ��������٥ƥ�ץ졼�ȤˤϤ�����ʸ������������ؿ�
*
*	@param  String $strProductCode	���ʥ�����
*	@param  String $aryDetail		���Ѹ����׻����٥ǡ���
*	@param  String $curStandardRate	ɸ����
*	@param  Object $objDB			DB���֥�������
*	@return Array  $aryDetail		���Ѹ������٥ǡ���
*	@access public
*/
function fncGetEstimateDetailHtml( $aryDetail, $strDetailTemplatePath, $objDB )
{
require_once ( LIB_DEBUGFILE );

	// ���Ѹ������٥ƥ�ץ졼�ȼ���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( $strDetailTemplatePath );
	$strTemplate = $objTemplate->strTemplate;

	// �������ܥ����ɤ򥭡��Ȥ������̾������������
	$aryStockSubjectCode = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB );

	// ���ɽ�������ɤ򥭡��Ȥ�����̾Ϣ����������
	$aryCompanyName = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "strCompanyDisplayName", "Array", "", $objDB );

	// ���������оݤ��б������뤿�����������
	$aryPayOffFlag = Array ( "t" => "��" , "f" => "", "true" => "��" , "false" => "", "" => "" );

	// ����������������
	$curFixedCost = 0;
	$curMemberCost = 0;

	// �����񾮷� added by k.saito
	$curFixedCostSubtotal = 0;

	// ��������
	$curCheckCost = 0;

	//////////////////////////////////////////////////////////////////
	// ���٥ǡ���
	//////////////////////////////////////////////////////////////////
	// $aryDetail[��������][���ٹ�][����]
	for ( $i = 0; $i < 8; $i++ )
	{
		$lngStockSubjectCode = 0;
		$strStockSubjectName = 0;
		$curSubjectTotalCost = 0;
		for ( $j = 0; $j < count ( $aryDetail[$i] ); $j++ )
		{
			$lngStockItemCode = 0;
			// HIDDEN
			$aryKeys = array_keys ( $aryDetail[$i][$j] );

			foreach ( $aryKeys as $strKey )
			{
				$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][" . $strKey . "]' value='" . $aryDetail[$i][$j][$strKey] . "'>\n";

				if ( $strKey == "lngStockSubjectCode" )
				{
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strStockSubjectName\"]' value='" . $aryStockSubjectCode[$aryDetail[$i][$j][$strKey]] . "'>\n";
					$aryDetail[$i][$j]["strStockSubjectName"] = $aryStockSubjectCode[$aryDetail[$i][$j][$strKey]];
					$lngStockSubjectCode = $aryDetail[$i][$j][$strKey];
					$strStockSubjectName = $aryDetail[$i][$j]["strStockSubjectName"];
					$arySubDetail[$i]["strStockSubjectName"]  = $aryStockSubjectCode[$aryDetail[$i][$j][$strKey]];
				}
				if ( $strKey == "lngStockItemCode" )
				{
					$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryDetail[$i][$j][$strKey], "lngstocksubjectcode = " . $lngStockSubjectCode, $objDB );
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strStockItemName\"]' value='" . $strStockItemName . "'>\n";
					$lngStockItemCode = $aryDetail[$i][$j][$strKey];
					$aryDetail[$i][$j]["strStockItemName"] = $strStockItemName;
				}

				if ( $strKey == "lngCustomerCompanyCode" )
				{
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strCompanyDisplayCode\"]' value='" . $aryDetail[$i][$j][$strKey] . "'>\n";
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strCompanyDisplayName\"]' value='" . $aryCompanyName[$aryDetail[$i][$j][$strKey]] . "'>\n";
					$aryDetail[$i][$j]["strCompanyDisplayCode"] = $aryDetail[$i][$j][$strKey];
					$aryDetail[$i][$j]["strCompanyDisplayName"] = $aryCompanyName[$aryDetail[$i][$j][$strKey]];
				}
			}
			unset ( $aryKeys );
			unset ( $strKey );

			$aryDetail[$i][$j]["bytPayOffTargetFlag"] = $aryPayOffFlag[$aryDetail[$i][$j]["bytPayOffTargetFlag"]];

			if ( $aryDetail[$i][$j]["bytPercentInputFlag"] == "t" or $aryDetail[$i][$j]["bytPercentInputFlag"] == "true" )
			{
				$aryDetail[$i][$j]["strPlanPrice"] = number_format($aryDetail[$i][$j]["curProductRate"] * 100, 4, ".", ",") . " %";
			}
			else
			{
				$aryDetail[$i][$j]["strPlanPrice"] = $aryDetail[$i][$j]["lngMonetaryUnitCode"] . " " . number_format($aryDetail[$i][$j]["curProductPrice"], 4, ".", ",");
			}

			// �����Ȳû�
// 2004.10.30 suzukaze update start
			if ( !is_numeric( $aryDetail[$i][$j]["curSubTotalPriceJP"] ) )
			{
				$aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace( "\\", "", $aryDetail[$i][$j]["curSubTotalPriceJP"] );
				$aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace( " ", "", $aryDetail[$i][$j]["curSubTotalPriceJP"] );
				$aryDetail[$i][$j]["curSubTotalPriceJP"] = str_replace( ",", "", $aryDetail[$i][$j]["curSubTotalPriceJP"] );
			}
			if ( $aryDetail[$i][$j]["curSubTotalPriceJP"] != "" )
			{
				$aryCost[$i] += $aryDetail[$i][$j]["curSubTotalPriceJP"];
				// �оݤ�������ʤ��
				if ( $i < 3 )
				{
// start modified by k.saito 2005.01.27
					// �����оݤξ�硢�������פ˲û�����
					if ( $aryDetail[$i][$j]["bytPayOffTargetFlag"] == "��" )
					{
						$curFixedCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
					}

					// �����оݳ���׼���
					else
					{
						$curNonFixedCost	+= $aryDetail[$i][$j]["curSubTotalPriceJP"];
					}


					// �����оݴط��ʤ��������񾮷פ˲û�����
					$curFixedCostSubtotal   += $aryDetail[$i][$j]["curSubTotalPriceJP"];
					// ������ξ��פ˲û�����
					$curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];

//					}
//					else
//					{
//						$curMemberCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
//					}
// end modified
				}
				// �оݤ�������ʤ��
				else
				{
					if ( $aryDetail[$i][$j]["bytPayOffTargetFlag"] == "��" )
					{
						$curFixedCost        += $aryDetail[$i][$j]["curSubTotalPriceJP"];
					}
					else
					{
						$curMemberCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
						$curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
					}
				}
			}

			// ���������б�
			if ( $lngStockSubjectCode == "403" and $lngStockItemCode == "6" )
			{
				$curCheckCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
			}

			// �ײ�Ŀ��û�
			if ( !is_numeric( $aryDetail[$i][$j]["lngProductQuantity"] ) )
			{
				$aryDetail[$i][$j]["lngProductQuantity"] = str_replace( "\\", "", $aryDetail[$i][$j]["lngProductQuantity"] );
				$aryDetail[$i][$j]["lngProductQuantity"] = str_replace( " ", "", $aryDetail[$i][$j]["lngProductQuantity"] );
				$aryDetail[$i][$j]["lngProductQuantity"] = str_replace( ",", "", $aryDetail[$i][$j]["lngProductQuantity"] );
			}
			if ( $aryDetail[$i][$j]["curSubTotalPriceJP"] != "" )
			{
				$aryProductQuantity[$i] += $aryDetail[$i][$j]["lngProductQuantity"];
			}
// 2004.10.30 suzukaze update end
fncDebug( 'lib_e_fncGetCommaNumber.txt', $aryDetail[$i][$j], __FILE__, __LINE__, "a");

			// ����޽���
			$aryDetail[$i][$j] = fncGetCommaNumber( $aryDetail[$i][$j] );


			// �֤�����
			$objTemplate->replace( $aryDetail[$i][$j] );
			$objTemplate->complete();

			if ( $aryDetail[$i][$j]["bytPercentInputFlag"] == "true" )
			{
				$aryDetail[$i][$j]["curProductPrice"] = $aryDetail[$i][$j]["curConversionRate"];
			}

			// ����������˥ƥ�ץ졼���ݻ�
			$aryDetailTemplate[$i] .= $objTemplate->strTemplate;

fncDebug( 'lib_e_fncGetEstimateDetailHtml.txt', $objTemplate->strTemplate, __FILE__, __LINE__, "a");

			$objTemplate->strTemplate = $strTemplate;
		}
/*
		// ���������б�
		// �������ܡ�1230�פǡ��������ѹ�פ���0�ߡפǤϤʤ����������ܡ�1230�׹�פ���0�ߡפϤʤ����
		// �����񸺻�����
		if ( $lngStockSubjectCode == "1230" and $curCheckCost != 0 and $aryCost[$i] != 0 )
		{
			$lngCount = count ( $aryDetail[$i] );
			$aryDetail[$i][$lngCount]["lngStockSubjectCode"] = $lngStockSubjectCode;
			$aryDetail[$i][$lngCount]["strStockSubjectName"] = $strStockSubjectName;
			$aryDetail[$i][$lngCount]["strStockItemName"] = "�����񸺻�";
			$aryDetail[$i][$lngCount]["curSubTotalPriceJP"] = 0 - $curCheckCost;

			$aryCost[$i] += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];
			$curMemberCost       += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];	// ��������
			$curSubjectTotalCost += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];

			// ����޽���
			$aryDetail[$i][$lngCount] = fncGetCommaNumber( $aryDetail[$i][$lngCount] );



			// �֤�����
			$objTemplate->replace( $aryDetail[$i][$lngCount] );
			$objTemplate->complete();

			// ����������˥ƥ�ץ졼���ݻ�
			$aryDetailTemplate[$i] .= $objTemplate->strTemplate;

			$objTemplate->strTemplate = $strTemplate;
		}
*/

		// ���ܷפ����ʳ��Ǥ����
//		if ( $curSubjectTotalCost != 0 )
//		{
		// �������ܤ�¸�ߤ��Ƥ����
		if( (int)$lngStockSubjectCode != 0 )
		{
			// �������ܷפλ���
			$arySubDetail[$i]["curSubjectTotalCost"] = number_format( $curSubjectTotalCost, 2, '.', ',' );

			// �ƥ�ץ졼���ɤ߹���
			$objSubTemplate = new clsTemplate();
			$objSubTemplate->getTemplate( "estimate/regist/plan_subjecttotal.tmpl" );

			// �ƥ�ץ졼������
			$objSubTemplate->replace( $arySubDetail[$i] );
			$objSubTemplate->complete();

			// ����������˥ƥ�ץ졼���ݻ�
			$aryDetailTemplate[$i] .= $objSubTemplate->strTemplate;

			unset ( $objSubTemplate );
		}
	}

	unset ( $objTemplate );
	unset ( $strTemplate );
	unset ( $aryDetail );
	unset ( $arySubDetail );

	$aryEstimate["curFixedCost"] = 0;
	$aryEstimate["curMemberCost"] = 0;


	// ����������ʬ
	for ( $i = 0; $i < 3; $i++ )
	{
		$aryEstimateDetail["strFixCostTemplate"]	.= $aryDetailTemplate[$i];
		$aryEstimate["lngFixedQuantity"]			+= $aryProductQuantity[$i];
	}

	// �����񾮷� added by k.saito 
	$aryEstimate["curFixedCostSubtotal"] = $curFixedCostSubtotal;
	// �������פϷ׻���
	$aryEstimate["curFixedCost"] = $curFixedCost;


// �����оݳ����
$aryEstimate["curNonFixedCost"]	= ( is_null($curNonFixedCost) || empty($curNonFixedCost) ) ? 0.00 : $curNonFixedCost;


	// ����������ʬ
	for ( $i = 3; $i < 8; $i++ )
	{
		$aryEstimateDetail["strMemberCostTemplate"]	.= $aryDetailTemplate[$i];
	}
	// �������פ�����ͽ����Ȥ��뤿�ᡢ�����Ǥ϶����ͤȤ���
	$aryEstimate["lngMemberQuantity"]			     = "";
	// �������פϷ׻���
	$aryEstimate["curMemberCost"] = $curMemberCost;

	unset ( $aryDetailTemplate );
	unset ( $aryCost );

	return Array ( $aryEstimateDetail, $aryEstimate, $aryHiddenString );
}



/**
* ���Ѹ����׻��׻���̼����ؿ�
*
*	����¤���ѡ���������פθ��Ѹ����׻��׻���̥ǡ������������ؿ�
*
*	@param  Array  $aryEstimateData	���Ѹ����׻��ǡ���
*	@param  Object $objDB			DB���֥�������
*	@return Array  $aryEstimateData	���Ѹ����׻��ǡ���
*	@access public
*/
function fncGetEstimateCalculate( $aryEstimateData )
{

require_once ( LIB_DEBUGFILE );


	// ������ñ�� �� �������׷ײ踶�� / ����ͽ���
	if ( $aryEstimateData["lngProductionQuantity"] <> 0 )
	{
		$aryEstimateData["curFixedProductPrice"]    = $aryEstimateData["curFixedCost"] / $aryEstimateData["lngProductionQuantity"];
	}
	else
	{
		$aryEstimateData["curFixedProductPrice"]    = 0.00;
	}

	// �������׷ײ�Ŀ�
	$aryEstimateData["lngMemberQuantity"]        = $aryEstimateData["lngProductionQuantity"];

	// ������ñ�� �� �������׷ײ踶�� / ����ͽ���
	if ( $aryEstimateData["lngMemberQuantity"] <> 0 )
	{
		$aryEstimateData["curMemberProductPrice"]    = $aryEstimateData["curMemberCost"] / $aryEstimateData["lngMemberQuantity"];
	}
	else
	{
		$aryEstimateData["curMemberProductPrice"]    = 0.00;
	}

//var_dump( $aryEstimateData["curNonFixedCost"] );
	// ����¤���� �� ������ �� ������ + �����оݳ����
	$aryEstimateData["curManufacturingCost"]	 = $aryEstimateData["curFixedCost"] + $aryEstimateData["curMemberCost"];// + $aryEstimateData["curNonFixedCost"];


	// ����¤���ѷײ�Ŀ� �� ����ͽ�����pcs��
	$aryEstimateData["lngManufacturingQuantity"] = $aryEstimateData["lngProductionQuantity"];

	// ����¤����ñ�� �� ����¤���� / ����ͽ���
	if ( $aryEstimateData["lngProductionQuantity"] <> 0 )
	{
		$aryEstimateData["curManufacturingProductPrice"] = $aryEstimateData["curManufacturingCost"] / $aryEstimateData["lngProductionQuantity"];
	}


	// ͽ������ �� ����ͽ��� �� Ǽ�� + �����оݳ����
	//$aryEstimateData["curAmountOfSales"]	= $aryEstimateData["lngProductionQuantity"] * $aryEstimateData["curProductPrice"] + $aryEstimateData["curNonFixedCost"];
	// ͽ����������
	$aryEstimateData["curAmountOfSales"]	= $aryEstimateData["lngProductionQuantity"] * $aryEstimateData["curProductPrice"];
	// ͽ�����������
	$aryEstimateData["curFixedCostSales"]	= $aryEstimateData["curFixedCostSales"];


	// ͽ����������	�� ͽ���������� - ����¤����
	$aryEstimateData["curProductPlanProfit"]	= $aryEstimateData["curAmountOfSales"] - $aryEstimateData["curManufacturingCost"];
	// ͽ�����������	�� ͽ����������� - �����оݳ����
	$aryEstimateData["curFixedPlanProfit"]	= $aryEstimateData["curFixedCostSales"] - $aryEstimateData["curNonFixedCost"];


	// ͽ�������� �� ͽ���������� + ͽ�����������
	$aryEstimateData["curTotalPlanPrice"] = $aryEstimateData["curAmountOfSales"] + $aryEstimateData["curFixedCostSales"];


	// �����ɸ����  �� ͽ���������� + ͽ�����������
	$aryEstimateData["curTargetProfit"] = $aryEstimateData["curProductPlanProfit"] + $aryEstimateData["curFixedPlanProfit"];
	

// fncDebug( 'lib_e_fncGetEstimateCalculate_01.txt', $aryEstimateData["curTargetProfit"], __FILE__, __LINE__);


	// �����ɸ���� �� ͽ������ �� ����¤����
//	$aryEstimateData["curTargetProfit"]			= $aryEstimateData["curAmountOfSales"] - $aryEstimateData["curManufacturingCost"];


	// ��ɸ����Ψ �� �����ɸ���� / ͽ��������
	if ( $aryEstimateData["curAmountOfSales"] <> 0 )
	{
		$aryEstimateData["curAchievementRatio"]		= round ( $aryEstimateData["curTargetProfit"] / $aryEstimateData["curTotalPlanPrice"] * 100, 2);
	}

	// ������¤���� �� ͽ�������� �� ɸ����
	$aryEstimateData["curStandardCost"]			= $aryEstimateData["curTotalPlanPrice"] * $aryEstimateData["curStandardRate"] / 100;

	// ��������� �� �����ɸ���� �� ������¤����
	$aryEstimateData["curProfitOnSales"]		= $aryEstimateData["curTargetProfit"] - $aryEstimateData["curStandardCost"];

	return $aryEstimateData;
}



/**
* ����޽������ͥǡ��������ؿ�
*
*	����޽�����ܤ������ͥǡ������������ؿ�
*
*	@param  Array $aryNumberData	���ͥǡ���
*	@return Array $aryNumberData	���ͥǡ���
*	@access public
*/
function fncGetCommaNumber( $aryNumberData )
{
	$aryKeys = array_keys ( $aryNumberData );
	foreach ( $aryKeys as $strKey )
	{
		if( !is_string($strKey) ) continue;
		if ( $strKey == "curProductPrice" || $strKey == "curRetailPrice" )
		{
			preg_match ( "/\.(\d+)$/", $aryNumberData[$strKey], $lngFloor );
			$aryNumberData[$strKey] = ($aryNumberData[$strKey] != "") ? number_format ( $aryNumberData[$strKey], 4, '.', ',' ) : "";
		}
		elseif ( preg_match ( "/^cur/", $strKey ) && $strKey != "curProductPrice_hidden" )
		{
			preg_match ( "/\.(\d+)$/", $aryNumberData[$strKey], $lngFloor );
			$aryNumberData[$strKey] =  ($aryNumberData[$strKey] != "") ? number_format ( $aryNumberData[$strKey], 2, '.', ',' ) : "";
		}
		elseif ( preg_match ( "/Quantity$/", $strKey ) )
		{
			$aryNumberData[$strKey] =  ($aryNumberData[$strKey] != "") ? number_format ( $aryNumberData[$strKey] ) : "";
		}
	}

	return $aryNumberData;
}




return TRUE;
?>
