<?
/** 
*	見積原価管理用ライブラリ
*
*	見積原価管理用関数ライブラリ
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.09.28	fncGetEstimateToProductCode 関数の追加
*/


// 社内レート定義
define ("DEF_MONETARYCLASS_SHANAI", 	2);		// 社内


/**
* 見積原価管理(検索に使用)
*
*	見積原価データ読み込み、検索、詳細情報取得クエリ関数
*
*	@param  String $lngEstimateCode 見積原価コード
*	@param  Array  $aryData     FORMデータ
*	@param  Object $objDB       DBオブジェクト
*	@access public
*/
function getEstimateQuery( $lngUserCode, $aryData, $objDB )
{

	require_once( LIB_DEBUGFILE );
fncDebug( 'getEstimateQuery_01.txt', $lngUserCode, __FILE__, __LINE__);
fncDebug( 'getEstimateQuery_01.txt', $aryData, __FILE__, __LINE__,'a');

	// ソートするカラムの対象番号設定

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
	// 取得項目
	//////////////////////////////////////////////////////////////////////////
	$timeString = "'" . fncGetDateTimeString() . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT curStandardRate FROM m_EstimateStandardRate WHERE dtmApplyStartDate <= '" . $timeString . "' AND dtmApplyEndDate >= '" . $timeString ."'", $objDB );
	if ( $lngResultNum < 1 )
	{
// 2004.10.01 suzukaze update start
		// もし当月の標準割合が参照できない場合最新の日付の標準割合を参照
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

	// 製品単位がctnならば、生産予定数はpcsに変換する
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN to_char( p.lngProductionQuantity * p.lngCartonQuantity, '9,999,999,999' ) ";
	$aryQuery[] = "  ELSE to_char( p.lngProductionQuantity, '9,999,999,999' ) ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	// 製品単位がctnならば、計画C/tはそのまま生産予定数
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

	// 企画目標利益 ⇒ 予定売上高 − 総製造費用
	$aryQuery[] = " to_char( e.curSalesAmount - e.curManufacturingCost, '999,999,990.9999' )  AS curTargetProfit, ";

	// 目標利益率 ⇒ 企画目標利益 / 予定売上高
	$aryQuery[] = " CASE WHEN e.curSalesAmount <> 0 ";
	$aryQuery[] = "  THEN to_char( (e.curSalesAmount - e.curManufacturingCost) / e.curSalesAmount * 100, '9,999,999,990.99' ) || ' %' ";
	$aryQuery[] = "  ELSE to_char( 0, '0.99' ) || ' %' ";
	$aryQuery[] = " END AS curAchievementRatio,";

	// 間接製造経費
//	$aryQuery[] = " to_char( e.curSalesAmount * " . $objResult->curstandardrate . ", '9,999,999,990.99' ) AS curStandardRate,";
	// 標準割合
	$aryQuery[] = $objResult->curstandardrate . " AS curStandardRate,";

	// 売上総利益
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
// 050407 bykou revisionのMAX値を取れないための修正
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
	// 条件処理
	//////////////////////////////////////////////////////////////////////////
	// A:指定した見積原価コード
	if ( $aryData["lngUserCodeConditions"] && $lngUserCode != "" )
	{
		$aryQueryWhere[] = " u.lngUserCode = $lngUserCode \n";
	}

	// B:各検索条件
	// 製品コード
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

	// 製品名
	if ( $aryData["strProductNameConditions"] && $aryData["strProductName"] != "" )
	{
		$aryQueryWhere[] = " p.strProductName LIKE '%" . $aryData["strProductName"] . "%' \n";
	}

	// 担当グループ表示コード
	if ( $aryData["strInchargeGroupDisplayCodeConditions"] && $aryData["lngInChargeGroupCode"] != "" )
	{
		$aryQueryWhere[] = " g.strGroupDisplayCode = '" . $aryData["lngInChargeGroupCode"] . "' \n";
	}

	// 担当者表示コード
	if ( $aryData["strInchargeUserDisplayCodeConditions"] && $aryData["lngInChargeUserCode"] != "" )
	{
		$aryQueryWhere[] = " u1.strUserDisplayCode = '" . $aryData["lngInChargeUserCode"] . "' \n";
	}

	// 入力者表示コード
	if ( $aryData["strInputUserDisplayCodeConditions"] && $aryData["lngInputUserCode"] != "" )
	{
		$aryQueryWhere[] = " u2.strUserDisplayCode = '" . $aryData["lngInputUserCode"] . "' \n";
	}

	// 作成日
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

	// 納期
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
	// ワークフロー状態
	if ( $aryData["lngWorkFlowStatusCodeConditions"] )
	{
		// ワークフロー状態
		if ( $aryData["lngWorkFlowStatusCode"] != "" )
		{
			if ( $aryData["lngWorkFlowStatusCode"] )
			{
				// チェックボックス値より、配列をそのまま代入
				$arySearchStatus = $aryData["lngWorkFlowStatusCode"];
				
				if ( is_array( $arySearchStatus ) )
				{
					$aryQueryWF[] = " AND e.lngestimatestatuscode in ( ";

					// WF状態は複数設定されている可能性があるので、設定個数分ループ
					$strBuff = "";
					for ( $j = 0; $j < count($arySearchStatus); $j++ )
					{
						// 初回処理
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
	// ソート処理
	//////////////////////////////////////////////////////////////////////////
	// $strSort 構造 "sort_[対象番号]_[降順・昇順]"
	// $strSort から対象番号、降順・昇順を取得
	list ( $sort, $column, $DESC ) = explode ( "_", $aryData["strSort"] );
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
	// クエリ実行
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
* GETデータ引数URL生成関数
*
*	@param  Array  $aryData GETデータ
*	@return String          URL(**.php?・・・以降の文字列)
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
* 標準割合取得関数
*
*	標準割合データクエリ関数
*
*	@param  String  $strProductCode	 製品コード
*	@param  Object  $objDB			 DBオブジェクト
*	@return Integer $curStandardRate 標準割合
*	@access public
*/
function fncGetEstimateDefault( $objDB )
{
    $timeString = "'" . fncGetDateTimeString() . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT To_char( curstandardrate, '990.9999' ) as curstandardrate FROM m_EstimateStandardRate WHERE dtmApplyStartDate < '" . $timeString . "' AND dtmApplyEndDate > '" . $timeString . "'", $objDB );

	if ( $lngResultNum < 1 )
	{
// 2004.10.01 suzukaze update start
		// もし当月の標準割合が参照できない場合最新の日付の標準割合を参照
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

// 標準割合の値については ％表記にて扱う
	$curStandardRate = $curStandardRate * 100;

	unset ( $objResult );

	return $curStandardRate;
}



/**
* 見積原価作成時の社内通貨取得関数
*
*	社内通貨データクエリ関数
*
*	@param  String  $dtmInsertDate	 見積原価登録日
*	@param  Object  $objDB			 DBオブジェクト
*	@return Integer $curStandardRate 標準割合
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
* デフォルト見積原価取得関数
*
*	デフォルト見積原価取得データクエリ関数
*
*	@param  Integer $lngProductionQuantity	生産予定数
*	@param  Array  	$curProductPrice		納価
*	@param  Array  	$aryRate		 通貨レートコードをキーとする通貨レート
*	@param  Object  $objDB			 DBオブジェクト
*	@return Array 	$aryDefaultValue デフォルト値
*	@access public
*/
function fncGetEstimateDefaultValue( $lngProductionQuantity, $curProductPrice, $aryRate, $objDB, $sessionid )
{
	require_once( LIB_DEBUGFILE );

	global $g_aryTemp;


    $timeString = "'" . fncGetDateTimeString() . "'";
	$aryQuery[] = "SELECT *";
	$aryQuery[] = "FROM m_EstimateDefault e";
	$aryQuery[] = " LEFT JOIN m_Company c ON ( e.lngCustomerCompanyCode = c.lngCompanyCode )";
	$aryQuery[] = "WHERE e.dtmApplyStartDate < '" . $timeString . "' AND e.dtmApplyEndDate > '" . $timeString . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum < 1 )
	{
		fncOutputError( 1502, DEF_WARNING, "", TRUE, "estimate/regist/edit.php?strSessionID=" . $sessionid . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}

	// 仕入科目を配列の数値キーに対応させるための配列生成
	$aryStockKey = Array ( "431" => 0 , "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7 );

	// 仕入科目毎のカウンター配列を生成
	$aryCount = Array ( "431" => 0 , "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0 );

	// Booleanに対応させるための配列生成
	$aryBooleanString = Array ( "t" => "true" , "f" => "false", "true" => "true" , "false" => "false", "" => "false" );

	$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );




	// Tempフラグが有効
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
		$aryDefaultValue	= $g_aryTemp["aryDitail"];
	}
	else
	{
		// 見積原価テーブルデータ取得
		// 明細の数だけループ
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngResultID, $i );

			// $aryDetail[科目毎配列番号][科目毎カウンター][明細カラム名]
			// 仕入科目コード
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockSubjectCode"]
			= $objResult->lngstocksubjectcode;

			// 仕入部品コード
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngStockItemCode"]
			= $objResult->lngstockitemcode;

			// 償却対象フラグ
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPayOffTargetFlag"]
			= $aryBooleanString[$objResult->bytpayofftargetflag];

			// 仕入先コード
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngCustomerCompanyCode"]
			= $objResult->strcompanydisplaycode;

			// %入力フラグ
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["bytPercentInputFlag"]
			= $aryBooleanString[$objResult->bytpercentinputflag];

			// もし、パーセント入力フラグが設定されていれば以下の値を引数より設定する
			if ( $aryBooleanString[$objResult->bytpercentinputflag] == "true" )
			{
				// 計画個数
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
				= $lngProductionQuantity;

				// 計画率
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductRate"]
				= $objResult->curproductrate;

				// 計画原価
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				= $lngProductionQuantity * $curProductPrice * $objResult->curproductrate;
			}
			else
			{
				// 計画個数
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngProductQuantity"]
				= $objResult->lngproductquantity;

				// 計画率
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductRate"]
				= $objResult->curproductrate;

				// 計画原価
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				= $objResult->cursubtotalprice;
			}

			// 単価
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curProductPrice"]
			= $objResult->curproductprice;

			// 備考
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strNote"]
			= $objResult->strnote;

			// 通貨コード
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["lngMonetaryUnitCode"]
			= $aryMonetaryUnit[$objResult->lngmonetaryunitcode];


			// 日本円以外の計画原価小計
			if ( count ( $aryRate ) > 0 )
			{
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
				= $aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				 * $aryRate[$objResult->lngmonetaryunitcode];
			}
			// 日本円の計画原価小計
			else
			{
				$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPriceJP"]
				= $aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curSubTotalPrice"]
				 * $objResult->curconversionrate;
			}

			// 換算レート
			$aryDefaultValue[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curConversionRate"]
			= $objResult->curconversionrate;


			//$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["strCompanyDisplayName"]
			//= $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname;

			// 仕入先名称
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
* 通貨レート取得関数
*
*	通貨レートクエリ関数
*
*	@param  Object $objDB	DBオブジェクト
*	@return Array  $aryRate	通貨レート
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
	$aryQuery[] = "WHERE mmr.dtmapplystartdate <= '" . fncGetDateTimeString() . "' ";
	$aryQuery[] = "	AND mmr.dtmapplyenddate >= '" . fncGetDateTimeString() . "' ";
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
* 製品情報取得関数
*
*	strProductCode から見積原価計算各種表示に使用するデータを取得する関数
*
*	@param  String $strProductCode	製品コード
*	@param  Object $objDB			DBオブジェクト
*	@return Array  $aryData			製品データ
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

	// 製品単位がctnならば、生産予定数はpcsに変換する
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	$aryQuery[] = " p.lngProductionUnitCode,";

	// 製品単位がctnならば、計画C/tはそのまま生産予定数
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

	// テンポラリフラグが有効
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

//		$aryData["curConversionRate"]			= $aryEstimateData["curConversionRate"];	// 換算レート
//		$aryData["curStandardRate"]				= $aryEstimateData["curStandardRate"];		// 標準割合
		$aryData["blnTempFlag"]					= $aryEstimateData["blnTempFlag"];			// テンポラリフラグ
	}
	// ファイルテンポラリフラグが有効
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

		$aryData["strRemark"]					= $g_aryTemp["strRemark"];						// コメント

fncDebug( 'tempdata.txt', $g_aryTemp, __FILE__, __LINE__);
	}
	// 通常
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
	$aryData["lngproductstatuscode"]	= $objResult->lngproductstatuscode;	// 製品状態

	unset ( $objResult );

//fncDebug( 'tempdata.txt', $aryData, __FILE__, __LINE__);
	return $aryData;
}



/**
* 見積原価計算取得関数
*
*	lngEstimateNo から見積原価計算各種表示に使用するデータを取得する関数
*
*	@param  String $lngEstimateNo	見積原価ナンバー
*	@param  Object $objDB			DBオブジェクト
*	@return Array  $aryData			見積原価データ
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

	// 製品単位がctnならば、生産予定数はpcsに変換する
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	// 製品単位がctnならば、計画C/tはそのまま生産予定数
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





	$blnTempFlag	= false;	// テンポラリフラグ
	$lngTempNo		= 0;		// テンポラリ番号
	$aryTemp		= array();	// テンポラリデータ配列

	// 見積状態が「承認」以外の場合
	if( $objResult->lngestimatestatuscode != DEF_ESTIMATE_APPROVE )
	{
		// テンポラリ番号取得
		$lngTempNo = fncGetMasterValue( "m_estimate", "lngestimateno", "lngtempno",  $lngEstimateNo, '', $objDB );

		// テンポラリ番号が存在する場合
		if( $lngTempNo )
		{
			// テンポラリデータ取得
			$aryTemp = fncGetTempData($objDB, $lngTempNo);

			// テンポラリデータ取得失敗の場合
			if( !$aryTemp ) fncOutputError( 9061, DEF_WARNING, "", TRUE, "", $objDB );

			// 成功
			else $blnTempFlag	= true;
		}
	}

//fncDebug( 'tempno.txt', $aryTemp, __FILE__, __LINE__);


	// テンポラリフラグ有効
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

		$aryData["lngPlanCartonProduction"]		= $aryTemp["lngplancartonproduction"];	// 計画C/t

//		$aryData["curConversionRate"]			= $aryTemp["curconversionrate"];		// 換算レート
//		$aryData["curStandardRate"]				= $aryTemp["curstandardrate"];			// 標準割合
	}
	// 通常
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

	$aryData["blnTempFlag"]					= $blnTempFlag;	// テンポラリフラグ


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

	$aryData["strRemark"]					= $objResult->strnote;	// コメント

	unset ( $objResult );

//fncDebug( 'tempdata.txt', $aryData, __FILE__, __LINE__);

	return $aryData;
}



// 2004.09.28 suzukaze update start
/**
* 見積原価計算取得関数
*
*	strProductCode から見積原価計算各種表示に使用するデータを取得する関数
*
*	@param  String $strProductCode	製品コード
*	@param  Object $objDB			DBオブジェクト
*	@return Array  $aryData			見積原価データ
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

	// 製品単位がctnならば、生産予定数はpcsに変換する
	$aryQuery[] = " CASE WHEN p.lngProductionUnitCode = " . DEF_PRODUCTUNIT_CTN;
	$aryQuery[] = "  THEN p.lngProductionQuantity * p.lngCartonQuantity ";
	$aryQuery[] = "  ELSE p.lngProductionQuantity ";
	$aryQuery[] = " END AS lngProductionQuantity, ";

	// 製品単位がctnならば、計画C/tはそのまま生産予定数
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


	// ファイルテンポラリフラグ有効
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
	// 通常
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
* 見積原価計算明細取得関数
*
*	lngEstimateNo から見積原価計算各種表示に使用する明細データを取得する関数
*
*	@param  String $lngEstimateNo	見積原価ナンバー
*	@param  Array  $aryRate			通貨レートコードをキーとする通貨レート
*	@param  Object $objDB			DBオブジェクト
*	@return Array  $aryDetail		見積原価明細データ
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
//　050407bykou revisionnoのMAX値が取れないの修正
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

	// 仕入科目を配列の数値キーに対応させるための配列生成
	$aryStockKey = Array ( "431" => 0 , "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7 );

	// 仕入科目毎のカウンター配列を生成
	$aryCount = Array ( "431" => 0 , "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0 );

	// Booleanに対応させるための配列生成
	$aryBooleanString = Array ( "t" => "true" , "f" => "false", "true" => "true" , "false" => "false", "" => "false" );

	$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );


	$aryDetail = array();
	$lngSalesClassCnt = 0;
	

	// 見積原価テーブルデータ取得
	// 明細の数だけループ
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// ----------------------------------------------------------------
		// 仕入科目の列か、売上分類の列か、を判断する
		// nullでは無く、0以外　→　仕入科目
		// 0 →　売上分類
		// ----------------------------------------------------------------
		if( !is_null($objResult->lngstocksubjectcode) && $objResult->lngstocksubjectcode != 0 )
		{
			// 仕入科目・区分　の処理
			$lngKamokuKey = $aryStockKey[$objResult->lngstocksubjectcode];
			$lngKamokuCnt = $aryCount[$objResult->lngstocksubjectcode];
		}
		else
		{

			$lngKamokuKey = 11;
			$lngKamokuCnt = $lngSalesClassCnt;

			// 売上分類・科目　の処理
			// Key は“11”で定義済み → $aryMapping[PROC_SALES][SALESCLS_1]	将来的に増えた場合には要改造
			$aryDetail[$lngKamokuKey][$lngSalesClassCnt]["lngSalesDivisionCode"]	= $objResult->lngsalesdivisioncode;
			$aryDetail[$lngKamokuKey][$lngSalesClassCnt]["lngSalesClassCode"] 		= $objResult->lngsalesclasscode;
			
			$lngSalesClassCnt++;	// 11列のデータインクリメント
		}


		// $aryDetail[科目毎配列番号][科目毎カウンター][明細カラム名]
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
		$aryDetail[$lngKamokuKey][$lngKamokuCnt]["curConversionRate"]		= $objResult->curconversionrate;	//レートを登録されたレートから取る
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
* 見積原価計算明細取得関数（更新時値変更用）
*
*	lngEstimateNo から見積原価計算各種表示に使用する明細データを取得する関数（更新の際は新しい製品マスタの情報より再計算）
*
*	@param  String	$lngEstimateNo				見積原価ナンバー
*	@param  Array	$aryRate					通貨レートコードをキーとする通貨レート
*	@param	Integer	$lngProductionQuantity		製品マスタの生産予定数（pcs)
*	@param	Integer	$lngOldProductionQuantity	見積原価マスタの生産予定数（pcs)
*	@param	Float	$curProductPrice			製品マスタの納価
*	@param  Object	$objDB						DBオブジェクト
*	@return Array	$aryDetail					見積原価明細データ
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
//　050407bykou revisionnoのMAX値が取れないの修正
	$aryQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM t_EstimateDetail e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
//=============================================================================

	$aryQuery[] = " ORDER BY e.lngStockSubjectCode, e.lngEstimateDetailNo ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
	unset ( $aryQuery );

	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// 仕入科目を配列の数値キーに対応させるための配列生成
	$aryStockKey = Array ( "431" => 0 , "433" => 1, "403" => 2, "402" => 3, "401" => 4, "420" => 5, "1224" => 6, "1230" => 7 );

	// 仕入科目毎のカウンター配列を生成
	$aryCount = Array ( "431" => 0 , "433" => 0, "403" => 0, "402" => 0, "401" => 0, "420" => 0, "1224" => 0, "1230" => 0 );

	// Booleanに対応させるための配列生成
	$aryBooleanString = Array ( "t" => "true" , "f" => "false", "true" => "true" , "false" => "false", "" => "false" );

	$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );

	// 見積原価テーブルデータ取得
	// 明細の数だけループ
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		$lngChangeFlag = 0;

		// $aryDetail[科目毎配列番号][科目毎カウンター][明細カラム名]
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

		// 計画個数が見積原価マスタに保存されている生産予定数と同じ値の場合、製品マスタの生産予定数に変更する
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
// 修正から見積データを読み取る時に商品マスターから再計算のため　証紙を例外で設定する
//		$aryDetail[$aryStockKey[$objResult->lngstocksubjectcode]][$aryCount[$objResult->lngstocksubjectcode]]["curRetailPrice"]
//		= $objResult->curretailprice;
		// 計画個数が変更になった場合は、計画原価を変更する
		if ( $lngChangeFlag == 1 )
		{
			// ％入力フラグがONの場合
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
			// OFFの場合
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
	見積原価計算書 明細（売上部分）HTML出力
*
*/
function fncGetEstimateDetail_Sales_Html( $aryDetail, $strDetailTemplatePath, $objDB )
{
require_once( LIB_DEBUGFILE );

	// 見積原価明細テンプレート取得
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( $strDetailTemplatePath );
	$strTemplate = $objTemplate->strTemplate;

	// 仕入科目コードをキーとする仕入名称想配列を取得
	$arySalesClass = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", "Array", "", $objDB );

	// 会社表示コードをキーとする会社名連想配列を取得
	$aryCompanyName = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "strCompanyDisplayName", "Array", "", $objDB );

fncDebug( 'fncGetEstimateDetail_Sales_Html.txt', $aryDetail, __FILE__, __LINE__);



	$aryHiddenString = array();
	$arySalesDetail	= array();

	//////////////////////////////////////////////////////////////////
	// 明細データ
	//////////////////////////////////////////////////////////////////
	// $aryDetail[売上区分][明細行][項目]
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
					$aryHiddenString[] = "<input type='hidden' name='aryDetail[" . $i . "][" .  $j . "][\"strSalesDivisionName\"]' value='" . "固定費売上" . "'>\n";
					$aryDetail[$i][$j]["strSalesDivisionName"] = "固定費売上";
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
			
			// 数量
			if( is_numeric($aryDetail[$i][$j]["lngProductQuantity"]) )
			{
				$aryDetail[$i][$j]["lngProductQuantity"] = floor($aryDetail[$i][$j]["lngProductQuantity"]);
			}
			// 単価
			$aryDetail[$i][$j]["strPlanPrice"] = $aryDetail[$i][$j]["lngMonetaryUnitCode"] . " " . number_format($aryDetail[$i][$j]["curProductPrice"], 4, ".", ",");
//fncDebug( 'fncGetEstimateDetail_Sales_hidden_Tanka.txt', $aryDetail[$i][$j]["curSubTotalPrice"], __FILE__, __LINE__);

			//
			$curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
			$aryDetail[$i][$j]["curSubTotalPriceJP"] = number_format( $aryDetail[$i][$j]["curSubTotalPriceJP"], 2, '.', ',' );


			// 置き換え
			$objTemplate->replace( $aryDetail[$i][$j] );
			$objTemplate->complete();

						// 仕入科目毎にテンプレート保持
			$aryDetailTemplate[$i] .= $objTemplate->strTemplate;
			$objTemplate->strTemplate = $strTemplate;

		}

		// 売上分類が存在していれば
//		if( (int)$lngSalesDivisionCode != 0 )
//		{
			// 売上区分　計の算出
			$arySalesDetail[$i]["curSubjectTotalCost"] = number_format( $curSubjectTotalCost, 2, '.', ',' );

			// テンプレート読み込み
			$objSubTemplate = new clsTemplate();
			$objSubTemplate->getTemplate( "estimate/regist/plan_subjecttotal_sales.tmpl" );

			// テンプレート生成
			$objSubTemplate->replace( $arySalesDetail[$i] );
			$objSubTemplate->complete();

			// 仕入科目毎にテンプレート保持
			$aryDetailTemplate[$i] .= $objSubTemplate->strTemplate;

			unset ( $objSubTemplate );
//		}



		$aryEstimateDetail["strDetailSalesTemplate"]	.= $aryDetailTemplate[$i];

	}

fncDebug( 'fncGetEstimateDetail_Sales_hidden.txt', $arySalesDetail, __FILE__, __LINE__);

	return array($aryEstimateDetail, $curSubjectTotalCost, $aryHiddenString);
}


/**
* 見積原価計算明細HTML出力文字列取得関数
*
*	見積原価計算明細データを明細テンプレートにはめ込んだ文字列を取得する関数
*
*	@param  String $strProductCode	製品コード
*	@param  String $aryDetail		見積原価計算明細データ
*	@param  String $curStandardRate	標準割合
*	@param  Object $objDB			DBオブジェクト
*	@return Array  $aryDetail		見積原価明細データ
*	@access public
*/
function fncGetEstimateDetailHtml( $aryDetail, $strDetailTemplatePath, $objDB )
{
require_once ( LIB_DEBUGFILE );

	// 見積原価明細テンプレート取得
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( $strDetailTemplatePath );
	$strTemplate = $objTemplate->strTemplate;

	// 仕入科目コードをキーとする仕入名称想配列を取得
	$aryStockSubjectCode = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB );

	// 会社表示コードをキーとする会社名連想配列を取得
	$aryCompanyName = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "strCompanyDisplayName", "Array", "", $objDB );

	// 減価償却対象に対応させるための配列生成
	$aryPayOffFlag = Array ( "t" => "○" , "f" => "", "true" => "○" , "false" => "", "" => "" );

	// 固定費、部材費合計値
	$curFixedCost = 0;
	$curMemberCost = 0;

	// 固定費小計 added by k.saito
	$curFixedCostSubtotal = 0;

	// 検査費用
	$curCheckCost = 0;

	//////////////////////////////////////////////////////////////////
	// 明細データ
	//////////////////////////////////////////////////////////////////
	// $aryDetail[仕入科目][明細行][項目]
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

			// コスト加算
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
				// 対象が固定費ならば
				if ( $i < 3 )
				{
// start modified by k.saito 2005.01.27
					// 償却対象の場合、固定費合計に加算する
					if ( $aryDetail[$i][$j]["bytPayOffTargetFlag"] == "○" )
					{
						$curFixedCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
					}

					// 償却対象外合計取得
					else
					{
						$curNonFixedCost	+= $aryDetail[$i][$j]["curSubTotalPriceJP"];
					}


					// 償却対象関係なく、固定費小計に加算する
					$curFixedCostSubtotal   += $aryDetail[$i][$j]["curSubTotalPriceJP"];
					// 科目毎の小計に加算する
					$curSubjectTotalCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];

//					}
//					else
//					{
//						$curMemberCost       += $aryDetail[$i][$j]["curSubTotalPriceJP"];
//					}
// end modified
				}
				// 対象が部材費ならば
				else
				{
					if ( $aryDetail[$i][$j]["bytPayOffTargetFlag"] == "○" )
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

			// 検査費用対応
			if ( $lngStockSubjectCode == "403" and $lngStockItemCode == "6" )
			{
				$curCheckCost += $aryDetail[$i][$j]["curSubTotalPriceJP"];
			}

			// 計画個数加算
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

			// カンマ処理
			$aryDetail[$i][$j] = fncGetCommaNumber( $aryDetail[$i][$j] );


			// 置き換え
			$objTemplate->replace( $aryDetail[$i][$j] );
			$objTemplate->complete();

			if ( $aryDetail[$i][$j]["bytPercentInputFlag"] == "true" )
			{
				$aryDetail[$i][$j]["curProductPrice"] = $aryDetail[$i][$j]["curConversionRate"];
			}

			// 仕入科目毎にテンプレート保持
			$aryDetailTemplate[$i] .= $objTemplate->strTemplate;

fncDebug( 'lib_e_fncGetEstimateDetailHtml.txt', $objTemplate->strTemplate, __FILE__, __LINE__, "a");

			$objTemplate->strTemplate = $strTemplate;
		}
/*
		// 検査費用対応
		// 仕入科目「1230」で、検査費用合計が「0円」ではなく、仕入科目「1230」合計が「0円」はない場合
		// 固定費減算処理
		if ( $lngStockSubjectCode == "1230" and $curCheckCost != 0 and $aryCost[$i] != 0 )
		{
			$lngCount = count ( $aryDetail[$i] );
			$aryDetail[$i][$lngCount]["lngStockSubjectCode"] = $lngStockSubjectCode;
			$aryDetail[$i][$lngCount]["strStockSubjectName"] = $strStockSubjectName;
			$aryDetail[$i][$lngCount]["strStockItemName"] = "固定費減算";
			$aryDetail[$i][$lngCount]["curSubTotalPriceJP"] = 0 - $curCheckCost;

			$aryCost[$i] += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];
			$curMemberCost       += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];	// 部材費合計
			$curSubjectTotalCost += $aryDetail[$i][$lngCount]["curSubTotalPriceJP"];

			// カンマ処理
			$aryDetail[$i][$lngCount] = fncGetCommaNumber( $aryDetail[$i][$lngCount] );



			// 置き換え
			$objTemplate->replace( $aryDetail[$i][$lngCount] );
			$objTemplate->complete();

			// 仕入科目毎にテンプレート保持
			$aryDetailTemplate[$i] .= $objTemplate->strTemplate;

			$objTemplate->strTemplate = $strTemplate;
		}
*/

		// 科目計が０以外であれば
//		if ( $curSubjectTotalCost != 0 )
//		{
		// 仕入科目が存在していれば
		if( (int)$lngStockSubjectCode != 0 )
		{
			// 仕入科目計の算出
			$arySubDetail[$i]["curSubjectTotalCost"] = number_format( $curSubjectTotalCost, 2, '.', ',' );

			// テンプレート読み込み
			$objSubTemplate = new clsTemplate();
			$objSubTemplate->getTemplate( "estimate/regist/plan_subjecttotal.tmpl" );

			// テンプレート生成
			$objSubTemplate->replace( $arySubDetail[$i] );
			$objSubTemplate->complete();

			// 仕入科目毎にテンプレート保持
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


	// 固定明細部分
	for ( $i = 0; $i < 3; $i++ )
	{
		$aryEstimateDetail["strFixCostTemplate"]	.= $aryDetailTemplate[$i];
		$aryEstimate["lngFixedQuantity"]			+= $aryProductQuantity[$i];
	}

	// 固定費小計 added by k.saito 
	$aryEstimate["curFixedCostSubtotal"] = $curFixedCostSubtotal;
	// 固定費合計は計算値
	$aryEstimate["curFixedCost"] = $curFixedCost;


// 償却対象外合計
$aryEstimate["curNonFixedCost"]	= ( is_null($curNonFixedCost) || empty($curNonFixedCost) ) ? 0.00 : $curNonFixedCost;


	// 部材明細部分
	for ( $i = 3; $i < 8; $i++ )
	{
		$aryEstimateDetail["strMemberCostTemplate"]	.= $aryDetailTemplate[$i];
	}
	// 部材費合計は生産予定数とするため、ここでは空白値とする
	$aryEstimate["lngMemberQuantity"]			     = "";
	// 部材費合計は計算値
	$aryEstimate["curMemberCost"] = $curMemberCost;

	unset ( $aryDetailTemplate );
	unset ( $aryCost );

	return Array ( $aryEstimateDetail, $aryEstimate, $aryHiddenString );
}



/**
* 見積原価計算計算結果取得関数
*
*	総製造費用～売上総利益の見積原価計算計算結果データを取得する関数
*
*	@param  Array  $aryEstimateData	見積原価計算データ
*	@param  Object $objDB			DBオブジェクト
*	@return Array  $aryEstimateData	見積原価計算データ
*	@access public
*/
function fncGetEstimateCalculate( $aryEstimateData )
{

require_once ( LIB_DEBUGFILE );


	// 固定費単価 ⇒ 部材費合計計画原価 / 生産予定数
	if ( $aryEstimateData["lngProductionQuantity"] <> 0 )
	{
		$aryEstimateData["curFixedProductPrice"]    = $aryEstimateData["curFixedCost"] / $aryEstimateData["lngProductionQuantity"];
	}
	else
	{
		$aryEstimateData["curFixedProductPrice"]    = 0.00;
	}

	// 部材費合計計画個数
	$aryEstimateData["lngMemberQuantity"]        = $aryEstimateData["lngProductionQuantity"];

	// 部材費単価 ⇒ 部材費合計計画原価 / 生産予定数
	if ( $aryEstimateData["lngMemberQuantity"] <> 0 )
	{
		$aryEstimateData["curMemberProductPrice"]    = $aryEstimateData["curMemberCost"] / $aryEstimateData["lngMemberQuantity"];
	}
	else
	{
		$aryEstimateData["curMemberProductPrice"]    = 0.00;
	}

//var_dump( $aryEstimateData["curNonFixedCost"] );
	// 総製造費用 ⇒ 固定費 ＋ 部材費 + 償却対象外合計
	$aryEstimateData["curManufacturingCost"]	 = $aryEstimateData["curFixedCost"] + $aryEstimateData["curMemberCost"];// + $aryEstimateData["curNonFixedCost"];


	// 総製造費用計画個数 ⇒ 生産予定数（pcs）
	$aryEstimateData["lngManufacturingQuantity"] = $aryEstimateData["lngProductionQuantity"];

	// 総製造費用単価 ⇒ 総製造費用 / 生産予定数
	if ( $aryEstimateData["lngProductionQuantity"] <> 0 )
	{
		$aryEstimateData["curManufacturingProductPrice"] = $aryEstimateData["curManufacturingCost"] / $aryEstimateData["lngProductionQuantity"];
	}


	// 予定売上高 ⇒ 生産予定数 × 納価 + 償却対象外合計
	//$aryEstimateData["curAmountOfSales"]	= $aryEstimateData["lngProductionQuantity"] * $aryEstimateData["curProductPrice"] + $aryEstimateData["curNonFixedCost"];
	// 予定製品売上高
	$aryEstimateData["curAmountOfSales"]	= $aryEstimateData["lngProductionQuantity"] * $aryEstimateData["curProductPrice"];
	// 予定固定費売上高
	$aryEstimateData["curFixedCostSales"]	= $aryEstimateData["curFixedCostSales"];


	// 予定製品利益	⇒ 予定製品売上高 - 総製造費用
	$aryEstimateData["curProductPlanProfit"]	= $aryEstimateData["curAmountOfSales"] - $aryEstimateData["curManufacturingCost"];
	// 予定固定費利益	⇒ 予定固定費売上高 - 償却対象外合計
	$aryEstimateData["curFixedPlanProfit"]	= $aryEstimateData["curFixedCostSales"] - $aryEstimateData["curNonFixedCost"];


	// 予定総売上高 ⇒ 予定製品売上高 + 予定固定費売上高
	$aryEstimateData["curTotalPlanPrice"] = $aryEstimateData["curAmountOfSales"] + $aryEstimateData["curFixedCostSales"];


	// 企画目標利益  ⇒ 予定製品利益 + 予定固定費利益
	$aryEstimateData["curTargetProfit"] = $aryEstimateData["curProductPlanProfit"] + $aryEstimateData["curFixedPlanProfit"];
	

// fncDebug( 'lib_e_fncGetEstimateCalculate_01.txt', $aryEstimateData["curTargetProfit"], __FILE__, __LINE__);


	// 企画目標利益 ⇒ 予定売上高 − 総製造費用
//	$aryEstimateData["curTargetProfit"]			= $aryEstimateData["curAmountOfSales"] - $aryEstimateData["curManufacturingCost"];


	// 目標利益率 ⇒ 企画目標利益 / 予定総売上高
	if ( $aryEstimateData["curAmountOfSales"] <> 0 )
	{
		$aryEstimateData["curAchievementRatio"]		= round ( $aryEstimateData["curTargetProfit"] / $aryEstimateData["curTotalPlanPrice"] * 100, 2);
	}

	// 間接製造経費 ⇒ 予定総売上高 × 標準割合
	$aryEstimateData["curStandardCost"]			= $aryEstimateData["curTotalPlanPrice"] * $aryEstimateData["curStandardRate"] / 100;

	// 売上総利益 ⇒ 企画目標利益 − 間接製造経費
	$aryEstimateData["curProfitOnSales"]		= $aryEstimateData["curTargetProfit"] - $aryEstimateData["curStandardCost"];

	return $aryEstimateData;
}



/**
* カンマ処理数値データ取得関数
*
*	カンマ処理を施した数値データを取得する関数
*
*	@param  Array $aryNumberData	数値データ
*	@return Array $aryNumberData	数値データ
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
