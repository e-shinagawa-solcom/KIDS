<?
/** 
*	売上　詳細、削除、無効化関数群
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	処理概要
*	検索結果関連の関数
*
*	修正履歴
*
*	2004.03.17	詳細表示時の単価部分の表示方式を小数点以下４桁に変更
*	2004.03.30	詳細表示時の表示順を明細行番号順から表示用ソートキー順に変更
*
*/



/**
* 指定された売上番号から売上ヘッダ情報を取得するＳＱＬ文を作成
*
*	指定売上番号のヘッダ情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngSalesNo 			取得する売上番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetSalesHeadNoToInfoSQL ( $lngSalesNo, $lngRevisionNo )
{
	// SQL文の作成
	$aryQuery[] = "SELECT distinct on (s.lngSalesNo) s.lngSalesNo as lngSalesNo, s.lngRevisionNo as lngRevisionNo";

	// 登録日
	$aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS' ) as dtmInsertDate";
	// 計上日
	$aryQuery[] = ", to_char( s.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmAppropriationDate";
	// 売上No
	$aryQuery[] = ", s.strSalesCode as strSalesCode";
	// 受注No
//	$aryQuery[] = ", r.strReceiveCode || '-' || r.strReviseCode as strReceiveCode";
//	$aryQuery[] = ", s.lngReceiveNo as lngReceiveNo";
	// 顧客受注番号
	$aryQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
	$aryQuery[] = ", tsd.lngReceiveNo as lngReceiveNo";
	// 売上No
	$aryQuery[] = ", s.strSlipCode as strSlipCode";
	// 入力者
	$aryQuery[] = ", s.lngInputUserCode as lngInputUserCode";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
	// 顧客
	$aryQuery[] = ", s.lngCustomerCompanyCode as lngCustomerCompanyCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
	// 部門
	$aryQuery[] = ", s.lngGroupCode as lngInChargeGroupCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
	// 担当者
	$aryQuery[] = ", s.lngUserCode as lngInChargeUserCode";
	$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
	$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
	// 通貨
	$aryQuery[] = ", s.lngMonetaryUnitCode as lngMonetaryUnitCode";
	$aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
	$aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	// レートタイプ
	$aryQuery[] = ", s.lngMonetaryRateCode as lngMonetaryRateCode";
	$aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
	// 換算レート
	$aryQuery[] = ", s.curConversionRate as curConversionRate";
	// 状態
	$aryQuery[] = ", s.lngSalesStatusCode as lngSalesStatusCode";
	$aryQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
	// 備考
	$aryQuery[] = ", s.strNote as strNote";
	// 合計金額
	$aryQuery[] = ", s.curTotalPrice";

	$aryQuery[] = " FROM m_Sales s ";
	$aryQuery[] = " left join t_salesdetail tsd on tsd.lngsalesno = s.lngsalesno ";
	$aryQuery[] = " LEFT JOIN m_Receive r ON tsd.lngReceiveNo = r.lngReceiveNo and tsd.lngreceiverevisionno = r.lngrevisionno";
	$aryQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON s.lngGroupCode = inchg_g.lngGroupCode";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON s.lngUserCode = inchg_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_SalesStatus ss USING (lngSalesStatusCode)";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON s.lngMonetaryRateCode = mr.lngMonetaryRateCode";

	$aryQuery[] = " WHERE s.lngSalesNo = " . $lngSalesNo . "";
	$aryQuery[] = " AND s.lngRevisionNo = " . $lngRevisionNo . "";

	$strQuery = implode( "\n", $aryQuery );

//fncDebug("lib_scs1.txt", $strQuery, __FILE__, __LINE__);
//fncDebug("lib_scs1-1.txt", $arySalesResult, __FILE__, __LINE__);

	return $strQuery;
}






/**
* 指定された売上番号から売上明細情報を取得するＳＱＬ文を作成
*
*	指定売上番号の明細情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngSalesNo 			取得する売上番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetSalesDetailNoToInfoSQL ( $lngSalesNo, $lngRevisionNo )
{
// 2004.03.30 suzukaze update start
	// SQL文の作成
	$aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngRecordNo, ";
	$aryQuery[] = "sd.lngSalesNo as lngSalesNo, sd.lngRevisionNo as lngRevisionNo";

	// 製品コード・名称
	$aryQuery[] = ", sd.strProductCode || '_' || sd.strReviseCode as strProductCode";
	$aryQuery[] = ", p.strProductName as strProductName";
	// 売上区分
	$aryQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
	$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
	// 営業部署
	$aryQuery[] = ", mg.strGroupDisplayCode as lnginchargegroupcode";
	$aryQuery[] = ", mg.strGroupDisplayName as strinchargegroupName";
	// 開発担当者
	$aryQuery[] = ", mu.struserdisplaycode as lnginchargeusercode";
	$aryQuery[] = ", mu.struserdisplayname as strinchargeuserName";
	// 顧客品番
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode";
	// 単価
	$aryQuery[] = ", sd.curProductPrice";
	// 単位
	$aryQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
	$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
	// 数量
	$aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// 税抜金額
	$aryQuery[] = ", sd.curSubTotalPrice";
	// 税区分
	$aryQuery[] = ", sd.lngTaxClassCode as lngTaxClassCode";
	$aryQuery[] = ", tc.strTaxClassName as strTaxClassName";
	// 税率
	$aryQuery[] = ", sd.lngTaxCode as lngTaxCode";
	$aryQuery[] = ", To_char(t.curTax * 100, '9,999,999,990' ) || '%' as curTax";
	// 税額
	$aryQuery[] = ", sd.curTaxPrice";
	// 明細備考
	$aryQuery[] = ", sd.strNote as strDetailNote";

	// 明細行を表示する場合
	$aryQuery[] = " FROM t_SalesDetail sd LEFT JOIN (";
    $aryQuery[] = "     SELECT m_product.* FROM m_product ";
    $aryQuery[] = "      INNER JOIN (";
    $aryQuery[] = "          SELECT ";
    $aryQuery[] = "              lngproductno,strrevisecode,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "          FROM m_product GROUP BY lngproductno,strrevisecode";
    $aryQuery[] = "      ) mp1 ON mp1.lngproductno = m_product.lngproductno";
    $aryQuery[] = "      AND mp1.strrevisecode = m_product.strrevisecode";
    $aryQuery[] = "      AND mp1.lngrevisionno = m_product.lngrevisionno";
	$aryQuery[] = " ) p on p.strProductCode = sd.strProductCode AND p.strrevisecode = sd.strrevisecode";
	$aryQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
	$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	$aryQuery[] = " LEFT JOIN m_group mg ON p.lnginchargegroupcode = mg.lnggroupcode";
	$aryQuery[] = " LEFT JOIN m_user mu ON p.lnginchargeusercode = mu.lngusercode";
	$aryQuery[] = " LEFT JOIN m_TaxClass tc USING (lngTaxClassCode)";
	$aryQuery[] = " LEFT JOIN m_Tax t USING (lngTaxCode)";

	$aryQuery[] = " WHERE sd.lngSalesNo = " . $lngSalesNo . "";
	$aryQuery[] = " AND sd.lngRevisionNo = " . (int)$lngRevisionNo . "";

	$aryQuery[] = " ORDER BY sd.lngSortKey ASC ";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* 詳細表示関数（ヘッダ用）
*
*	テーブル構成で売上データ詳細を出力する関数
*	ヘッダ行を表示する
*
*	@param  Array 	$aryResult 				ヘッダ行の検索結果が格納された配列
*	@access public
*/
function fncSetSalesHeadTabelData ( $aryResult )
{
	$aryColumnNames = array_keys($aryResult);

	// 表示対象カラムの配列より結果の出力
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		// 登録日
		if ( $strColumnName == "dtminsertdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", substr( $aryResult["dtminsertdate"], 0, 19 ) );
		}

		// 計上日
		else if ( $strColumnName == "dtmsalesappdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmsalesappdate"] );
		}

		// 入力者
		else if ( $strColumnName == "lnginputusercode" )
		{
			if ( $aryResult["strinputuserdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "     ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strinputuserdisplayname"];
		}

		// 顧客
		else if ( $strColumnName == "lngcustomercompanycode" )
		{
			if ( $aryResult["strcustomerdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strcustomerdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "      ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strcustomerdisplayname"];
		}

		// 部門
		else if ( $strColumnName == "lnginchargegroupcode" )
		{
			if ( $aryResult["strinchargegroupdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "    ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strinchargegroupdisplayname"];
		}

		// 担当者
		else if ( $strColumnName == "lnginchargeusercode" )
		{
			if ( $aryResult["strinchargeuserdisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "     ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strinchargeuserdisplayname"];
		}

		// 合計金額
		else if ( $strColumnName == "curtotalprice" )
		{			
            if (!$aryResult["curtotalprice"]) {
                $aryNewResult[$strColumnName] = convertPrice($aryResult["lngmonetaryunitcode"], $aryResult["strmonetaryunitsign"], 0, "price");
            } else {
                $aryNewResult[$strColumnName] = convertPrice($aryResult["lngmonetaryunitcode"], $aryResult["strmonetaryunitsign"], $aryResult["curtotalprice"], "price");
            }
		}

		// 状態
		else if ( $strColumnName == "lngsalesstatuscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strsalesstatusname"];
		}

		// 通貨
		else if ( $strColumnName == "lngmonetaryunitcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strmonetaryunitname"];
		}

		// レートタイプ
		else if ( $strColumnName == "lngmonetaryratecode" )
		{
			if ( $aryResult["lngmonetaryratecode"] and $aryResult["lngmonetaryunitcode"] != DEF_MONETARY_YEN )
			{
				$aryNewResult[$strColumnName] = $aryResult["strmonetaryratename"];
			}
			else
			{
				$aryNewResult[$strColumnName] = "";
			}
		}

		// 備考
		else if ( $strColumnName == "strnote" )
		{
			$aryNewResult[$strColumnName] = nl2br($aryResult["strnote"]);
		}

		// その他の項目はそのまま出力
		else
		{
			$aryNewResult[$strColumnName] = $aryResult[$strColumnName];
		}
	}

	return $aryNewResult;
}






/**
* 詳細表示関数（明細用）
*
*	テーブル構成で売上データ詳細を出力する関数
*	明細行を表示する
*
*	@param  Array 	$aryDetailResult 	明細行の検索結果が格納された配列（１データ分）
*	@param  Array 	$aryHeadResult 		ヘッダ行の検索結果が格納された配列（参照用）
*	@access public
*/
function fncSetSalesDetailTabelData ( $aryDetailResult, $aryHeadResult )
{
	$aryColumnNames = array_keys($aryDetailResult);

	// 表示対象カラムの配列より結果の出力
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		// 製品コード名称
		if ( $strColumnName == "strproductcode" )
		{
			if ( $aryDetailResult["strproductcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["strproductcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strproductname"];
		}

		// 売上区分
		else if ( $strColumnName == "lngsalesclasscode" )
		{
			if ( $aryDetailResult["lngsalesclasscode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngsalesclasscode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strsalesclassname"];
		}

		// 営業部署
		else if ( $strColumnName == "lnginchargegroupcode" )
		{
			if ( $aryDetailResult["lnginchargegroupcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lnginchargegroupcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strinchargegroupname"];
		}

		// 開発担当者
		else if ( $strColumnName == "lnginchargeusercode" )
		{
			if ( $aryDetailResult["lnginchargeusercode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lnginchargeusercode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strinchargeusername"];
		}

		// 顧客品番
		else if ( $strColumnName == "strgoodscode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
		}

		// 納期
		else if ( $strColumnName == "dtmdeliverydate" )
		{
			$aryNewDetailResult[$strColumnName] = str_replace( "-", "/", $aryDetailResult[$strColumnName] );
		}

		// 単価
		else if ( $strColumnName == "curproductprice" )
		{
			if (!$aryDetailResult["curproductprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], 0, "unitprice");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], $aryDetailResult["curproductprice"], "unitprice");
            }
		}

		// 単位
		else if ( $strColumnName == "lngproductunitcode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult["strproductunitname"];
		}

		// 税抜金額
		else if ( $strColumnName == "cursubtotalprice" )
		{
			if (!$aryDetailResult["cursubtotalprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], 0, "price");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], $aryDetailResult["cursubtotalprice"], "price");
            }
		}

		// 税区分
		else if ( $strColumnName == "lngtaxclasscode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult["strtaxclassname"];
		}

		// 税率
		else if ( $strColumnName == "curtax" )
		{
			if ( !$aryDetailResult["curtax"] )
			{
				$aryNewDetailResult[$strColumnName] = "";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = $aryDetailResult["curtax"];
			}
		}

		// 税額
		else if ( $strColumnName == "curtaxprice" )
		{
			if (!$aryDetailResult["curtaxprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], 0, "taxprice");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], $aryDetailResult["curtaxprice"], "taxprice");
            }
		}

		// 明細備考
		else if ( $strColumnName == "strdetailnote" )
		{
			$aryNewDetailResult[$strColumnName] = nl2br($aryDetailResult[$strColumnName]);
		}

		// その他の項目はそのまま出力
		else
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
		}
	}

	return $aryNewDetailResult;
}






/**
* 詳細表示用カラム名セット関数
*
*	詳細表示時のカラム名（日本語、英語）での設定関数
*
*	@param  Array 	$aryResult 		検索結果が格納された配列
*	@param  Array 	$aryTytle 		カラム名が格納された配列
*	@access public
*/
function fncSetSalesTabelName ( $aryResult, $aryTytle )
{
	$aryColumnNames = array_values($aryResult);

	// 表示対象カラムの配列より結果の出力
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		if ( $aryTytle[$strColumnName] )
		{
			$strNewColumnName = "CN" . $strColumnName;
			$aryNames[$strNewColumnName] = $aryTytle[$strColumnName];
		}
	}

	return $aryNames;
}






/**
* 指定の売上データについて無効化することでどうなるかケースわけする
*
*	指定の売上データの状態を調査し、ケースわけする関数
*
*	@param  Array 		$arySalesData 	売上データ
*	@param  Object		$objDB			DBオブジェクト
*	@return Integer 	$lngCase		状態のケース
*										1: 対象売上データを無効化しても、最新の売上データが影響受けない
*										2: 対象売上データを無効化することで、最新の売上データが入れ替わる
*										3: 対象売上データが削除データで、売上が復活する
*										4: 対象売上データを無効化することで、最新の売上データになりうる売上データがない
*	@access public
*/
function fncGetInvalidCodeToMaster ( $arySalesData, $objDB )
{
	// 削除対象売上と同じ売上コードの最新の売上Noを調べる
	$strQuery = "SELECT s.lngSalesNo FROM m_Sales s WHERE s.strSalesCode = '" . $arySalesData["strsalescode"] . "' AND s.bytInvalidFlag = FALSE ";
	$strQuery .= " AND s.lngRevisionNo >= 0";
	$strQuery .= " AND s.lngRevisionNo = ( "
		. "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode )";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewSalesNo = $objResult->lngsalesno;
//fncDebug("lib_scs1.txt",$objResult, __FILE__, __LINE__);

	}
	else
	{
		$lngCase = 4;
	}
	$objDB->freeResult( $lngResultID );

	// 削除対象が最新かどうかのチェック
	if ( $lngCase != 4 )
	{
		if ( $lngNewSalesNo == $arySalesData["lngsalesno"] )
		{
			// 最新の場合
			// 削除対象売上以外でと同じ売上コードの最新の売上Noを調べる
			$strQuery = "SELECT s.lngSalesNo FROM m_Sales s WHERE s.strSalesCode = '" . $strSalesCode . "' AND s.bytInvalidFlag = FALSE ";
			$strQuery .= " AND s.lngSalesNo <> " . $arySalesData["lngsalesno"] . " AND s.lngRevisionNo >= 0";

			// 検索クエリーの実行
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			if ( $lngResultNum >= 1 )
			{
				$lngCase = 2;
			}
			else
			{
				$lngCase = 4;
			}
			$objDB->freeResult( $lngResultID );
		}
		// 対象売上が削除データかどうかの確認
		else if ( $arySalesData["lngrevisionno"] < 0 )
		{
			$lngCase = 3;
		}
		else
		{
			$lngCase = 1;
		}
	}

	return $lngCase;
}

// 2004.03.09 suzukaze update start
/**
* 指定の売上データの削除に関して、その売上データを削除することでの状態変更関数
*
*	売上の状態が「納品済」の場合、受注Noを指定していた場合、分納であった場合など
*	各状態ごとにその売上に関するデータの状態を変更する
*
*	@param  Array 		$arySalesData 	売上データ
*	@param  Object		$objDB			DBオブジェクト
*	@return Boolean 	0				実行成功
*						1				実行失敗 情報取得失敗
*	@access public
*/
function fncSalesDeleteSetStatus ( $arySalesData, $objDB )
{
/*	// 削除対象売上は、受注Noを指定しない売上である
	if ( $arySalesData["lngreceiveno"] == "" or $arySalesData["lngreceiveno"] == 0 )
	{
		return 0;
	}
*/
	// 受注Noを指定している売上の場合は、指定している最新の受注のデータを取得する
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo";	//	as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode";	//	as strReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode";	//	as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode";	//	as lngMonetaryUnitCode";
	$arySql[] = "	,r.strcustomerreceivecode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
//	$arySql[] = "	r.strReceiveCode = (";
//	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $arySalesData["lngreceiveno"];
	$arySql[] = "	r.strReceiveCode in (";
	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo IN (SELECT ts.lngreceiveno FROM t_Salesdetail ts WHERE ts.lngsalesno = " . $arySalesData["lngsalesno"];
	$arySql[] = "	)";
//	$arySql[] = "SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo in (select ts.lngreceiveno from t_salesdetail ts where ts.lngsalesno = "  . $lngSalesNo .")";
	$arySql[] = "	)";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0";
	$arySql[] = "	AND r.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = (";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
	$arySql[] = "	)";
	$arySql[] = "	AND 0 <= (";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
	$arySql[] = "	)";
	$strQuery = implode("\n", $arySql);

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum )
	{
		for ( $a = 0; $a < $lngResultNum; $a++ )
		{
			$objResult1[$a]= $objDB->fetchArray( $lngResultID, $a );
//fncDebug("lib_scs1.txt", $objResult1, __FILE__, __LINE__);
		}
	$objDB->freeResult( $lngResultID );
	}else{
		// 受注Noは指定しているが現在有効な最新受注が存在しない場合はそのまま削除可能とする
			return 0;
		}
	for($k=0;$k<count($objResult1);$k++)
		{
//               削除対象売上と同じ受注Noを指定している最新売上を検索
			$arySql = array();
			$arySql[] = "SELECT distinct";
			$arySql[] = "	s.lngSalesNo as lngSalesNo";
			$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
			$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";
//			$arySql[] = "	,r.lngreceiveno as lngreceiveno";
			$arySql[] = "FROM";
			$arySql[] = "	m_Sales s";
			$arySql[] = "	left join t_salesdetail tsd";
			$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
			$arySql[] = "	,m_Receive r";
			$arySql[] = "WHERE";
			$arySql[] = "	r.lngreceiveno = " . $objResult1[$k]["lngreceiveno"];
//			$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
			$arySql[] = "	AND tsd.lngReceiveNo in (select re1.lngReceiveNo from m_Receive re1 where re1.strreceivecode = (";
			$arySql[] = "	select re2.strreceivecode from m_Receive re2 where re2.lngreceiveno = " . $objResult1[$k]["lngreceiveno"];
			$arySql[] = "	))";
			$arySql[] = "	AND s.bytInvalidFlag = FALSE";
			$arySql[] = "	AND s.lngRevisionNo >= 0";
			$arySql[] = "	AND s.lngRevisionNo = (";
			$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
			$arySql[] = "		AND 0 <= (";
			$arySql[] = "		SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
			$arySql[] = "		)";
			$arySql[] = "	AND s.lngsalesno <> '"  . $arySalesData["lngsalesno"] . "'";
			$strQuery = implode("\n", $arySql);			
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			if ( $lngResultNum )
			{
				// 削除対象以外の売上データが存在する場合
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$arySalesResult1[$i] = $objDB->fetchArray( $lngResultID, $i );
//fncDebug("lib_scs1-1.txt", $arySalesResult1, __FILE__, __LINE__);
			// 売上参照受注の状態の状態を「納品中」とする
					// 同じ受注NOを指定している売上の状態に対しても「納品中」とする
						// 更新対象売上データをロックする
					if($arySalesResult1[$i]["lngsalesstatuscode"] != 99){
							$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
									. "WHERE lngSalesNo = " . $arySalesResult1[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
							list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
							$objDB->freeResult( $lngLockResultID );
							// 「納品中」状態への更新処理
							$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_DELIVER 
									. " WHERE lngSalesNo = " . $arySalesResult1[$i]["lngsalesno"];
							list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
							$objDB->freeResult( $lngUpdateResultID );
					}	
				}
				// 更新対象受注データをロックする
				$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $objResult1[$k]["lngreceiveno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );
				// 「納品中」状態への更新処理
				$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_DELIVER . " WHERE lngReceiveNo = " . $objResult1[$k]["lngreceiveno"];
				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
				}
				else
				{
				// 削除対象以外の売上データが存在しない場合
				// 売上の参照元最新受注の状態を「受注」に戻す
				// 更新対象受注データをロックする
				$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = "  . $objResult1[$k]["lngreceiveno"] .  " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				if ( !$lngLockResultNum )
				{
					fncOutputError ( 9051, DEF_ERROR, "DB処理エラー", TRUE, "", $objDB );
				}
				$objDB->freeResult( $lngLockResultID );
				// 「受注」状態への更新処理
				$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_ORDER . " WHERE lngReceiveNo = "  . $objResult1[$k]["lngreceiveno"];
				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
		}
	return 0;
}
?>