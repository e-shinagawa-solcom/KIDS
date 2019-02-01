<?
/** 
*	発注　詳細、削除、無効化関数群
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
*	2004.03.29	詳細表示時の明細行番号の表示部分を表示用ソートキーを表示するように変更
*	2004.05.31	金型番号の指定箇所の追加
*
*/

/**
* 指定された発注番号から発注ヘッダ情報を取得するＳＱＬ文を作成
*
*	指定発注番号のヘッダ情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngOrderNo 			取得する発注番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetPurchaseHeadNoToInfoSQL ( $lngOrderNo )
{
	// SQL文の作成
	$aryQuery[] = "SELECT distinct on (o.lngOrderNo) o.lngOrderNo as lngOrderNo, o.lngRevisionNo as lngRevisionNo";

	// 登録日
	$aryQuery[] = ", to_char( o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
	// 計上日
	$aryQuery[] = ", to_char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
	// 発注No
	$aryQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
	// 発注コード
	$aryQuery[] = ", o.strOrderCode as strRealOrderCode";
	// 入力者
	$aryQuery[] = ", o.lngInputUserCode as lngInputUserCode";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
	// 仕入先
	$aryQuery[] = ", o.lngCustomerCompanyCode as lngCustomerCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
	$aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
	// 部門
	$aryQuery[] = ", o.lngGroupCode as lngInChargeGroupCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
	$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
	// 担当者
	$aryQuery[] = ", o.lngUserCode as lngInChargeUserCode";
	$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
	$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
	// 納品場所
	$aryQuery[] = ", o.lngDeliveryPlaceCode as lngDeliveryPlaceCode";
	$aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryDisplayCode";
	$aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryDisplayName";
	// 通貨
	$aryQuery[] = ", o.lngMonetaryUnitCode as lngMonetaryUnitCode";
	$aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
	$aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	// レートタイプ
	$aryQuery[] = ", o.lngMonetaryRateCode as lngMonetaryRateCode";
	$aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
	// 換算レート
	$aryQuery[] = ", o.curConversionRate as curConversionRate";
	// 状態
	$aryQuery[] = ", o.lngOrderStatusCode as lngOrderStatusCode";
	$aryQuery[] = ", os.strOrderStatusName as strOrderStatusName";
	// 支払条件
	$aryQuery[] = ", o.lngPayConditionCode as lngPayConditionCode";
	$aryQuery[] = ", pc.strPayConditionName as strPayConditionName";
	// 発注有効期限日
	$aryQuery[] = ", to_char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
	// 備考
	$aryQuery[] = ", o.strNote as strNote";
	// 合計金額
	$aryQuery[] = ", To_char( o.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";

	$aryQuery[] = " FROM m_Order o LEFT JOIN t_OrderDetail ot USING (lngOrderNo)";
	$aryQuery[] = " LEFT JOIN m_Product p ON ot.strproductCode = p.strproductCode";
	$aryQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Company delv_c ON o.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
	$aryQuery[] = " LEFT JOIN m_PayCondition pc ON o.lngPayConditionCode = pc.lngPayConditionCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON o.lngMonetaryRateCode = mr.lngMonetaryRateCode";

	$aryQuery[] = " WHERE o.lngOrderNo = " . $lngOrderNo . "";

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* 指定された発注番号から発注明細情報を取得するＳＱＬ文を作成
*
*	指定発注番号の明細情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngOrderNo 			取得する発注番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetPurchaseDetailNoToInfoSQL ( $lngOrderNo )
{
// 2004.03.29 suzukaze update start
	// SQL文の作成
//	$aryQuery[] = "SELECT distinct on (od.lngOrderDetailNo) od.lngOrderDetailNo as lngRecordNo, ";
	$aryQuery[] = "SELECT distinct on (od.lngSortKey) od.lngSortKey as lngRecordNo, ";
// 2004.03.29 suzukaze update end
	$aryQuery[] = "od.lngOrderNo as lngOrderNo, od.lngRevisionNo as lngRevisionNo";

	// 製品コード・名称
	$aryQuery[] = ", od.strProductCode as strProductCode";
	$aryQuery[] = ", p.strProductName as strProductName";
	// 仕入科目
	$aryQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
	$aryQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
	// 仕入部品
	$aryQuery[] = ", od.lngStockItemCode as lngStockItemCode";
	$aryQuery[] = ", si.strStockItemName as strStockItemName";
	// 金型番号
	$aryQuery[] = ", od.strMoldNo as strMoldNo";
	// 顧客品番
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode";
	// 運搬方法
	$aryQuery[] = ", od.lngDeliveryMethodCode as lngDeliveryMethodCode";
	$aryQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
	// 納期
	$aryQuery[] = ", od.dtmDeliveryDate as dtmDeliveryDate";
// 2004.03.17 suzukaze update start
	// 単価
	$aryQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
//	$aryQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.99' )  as curProductPrice";
// 2004.03.17 suzukaze update start
	// 単位
	$aryQuery[] = ", od.lngProductUnitCode as lngProductUnitCode";
	$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
	// 数量
	$aryQuery[] = ", To_char( od.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// 税抜金額
	$aryQuery[] = ", To_char( od.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// 明細備考
	$aryQuery[] = ", od.strNote as strDetailNote";

	// 明細行を表示する場合
	$aryQuery[] = " FROM t_OrderDetail od LEFT JOIN m_Product p USING (strProductCode)";
	$aryQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
//	$aryQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)";
	$aryQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
	$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
	$aryQuery[] = ", m_StockItem si ";
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " ";
	$aryQuery[] = "AND si.lngStockSubjectCode = ss.lngStockSubjectCode ";
	$aryQuery[] = "AND od.lngStockItemCode = si.lngStockItemCode ";

// 2004.03.29 suzukaze update start
	$aryQuery[] = " ORDER BY od.lngSortKey ASC ";
// 2004.03.29 suzukaze update end

	$strQuery = implode( "\n", $aryQuery );

	return $strQuery;
}






/**
* 詳細表示関数（ヘッダ用）
*
*	テーブル構成で発注データ詳細を出力する関数
*	ヘッダ行を表示する
*
*	@param  Array 	$aryResult 				ヘッダ行の検索結果が格納された配列
*	@access public
*/
function fncSetPurchaseHeadTabelData ( $aryResult )
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
		else if ( $strColumnName == "dtmorderappdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmorderappdate"] );
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

		// 仕入先
		else if ( $strColumnName == "lngcustomercode" )
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

		// 納品場所
		else if ( $strColumnName == "lngdeliveryplacecode" )
		{
			if ( $aryResult["strdeliverydisplaycode"] )
			{
				$aryNewResult[$strColumnName] = "[" . $aryResult["strdeliverydisplaycode"] ."]";
			}
			else
			{
				$aryNewResult[$strColumnName] = "      ";
			}
			$aryNewResult[$strColumnName] .= " " . $aryResult["strdeliverydisplayname"];
		}

		// 合計金額
		else if ( $strColumnName == "curtotalprice" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strmonetaryunitsign"] . " ";
			if ( !$aryResult["curtotalprice"] )
			{
				$aryNewResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewResult[$strColumnName] .= $aryResult["curtotalprice"];
			}
		}

		// 状態
		else if ( $strColumnName == "lngorderstatuscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strorderstatusname"];
		}

		// 支払条件
		else if ( $strColumnName == "lngpayconditioncode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strpayconditionname"];
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

		// 発注有効期限日
		else if ( $strColumnName == "dtmexpirationdate" )
		{
			$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult["dtmexpirationdate"] );
		}

		// 備考
		else if ( $strColumnName == "strnote" )
		{
			// 備考の特殊文字変換
			$aryResult["strNote"] = fncHTMLSpecialChars( $aryResult["strNote"] );

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
*	テーブル構成で発注データ詳細を出力する関数
*	明細行を表示する
*
*	@param  Array 	$aryDetailResult 	明細行の検索結果が格納された配列（１データ分）
*	@param  Array 	$aryHeadResult 		ヘッダ行の検索結果が格納された配列（参照用）
*	@access public
*/
function fncSetPurchaseDetailTabelData ( $aryDetailResult, $aryHeadResult )
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

		// 仕入科目
		else if ( $strColumnName == "lngstocksubjectcode" )
		{
			if ( $aryDetailResult["lngstocksubjectcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngstocksubjectcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strstocksubjectname"];
		}

		// 仕入部品
		else if ( $strColumnName == "lngstockitemcode" )
		{
			if ( $aryDetailResult["lngstockitemcode"] )
			{
				$aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngstockitemcode"] ."]";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] = "      ";
			}
			$aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strstockitemname"];
		}

		// 金型番号
		else if ( $strColumnName == "strmoldno" )
		{
// 2004.05.31 suzukaze update start
			// 仕入科目が４３３　金型海外償却　仕入部品が１ Injection Moldの場合
			// 仕入科目が４３１　金型償却高　　仕入部品が８ 金型の場合
			if ( $aryDetailResult["strmoldno"] 
				and ( $aryDetailResult["lngstocksubjectcode"] = 433 and $aryDetailResult["lngstockitemcode"] = 1 ) 
				or  ( $aryDetailResult["lngstocksubjectcode"] = 431 and $aryDetailResult["lngstockitemcode"] = 8 ) )
			{
				$aryNewDetailResult[$strColumnName] = $aryDetailResult["strmoldno"];
			}
// 2004.05.31 suzukaze update end
		}

		// 顧客品番
		else if ( $strColumnName == "strgoodscode" )
		{
			$aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
		}

		// 運搬方法
		else if ( $strColumnName == "lngdeliverymethodcode" )
		{
			if ( $aryDetailResult["strdeliverymethodname"] == "" )
			{
				$aryDetailResult["strdeliverymethodname"] = "未定";
			}
			$aryNewDetailResult[$strColumnName] .= $aryDetailResult["strdeliverymethodname"];
		}

		// 納期
		else if ( $strColumnName == "dtmdeliverydate" )
		{
			$aryNewDetailResult[$strColumnName] = str_replace( "-", "/", $aryDetailResult["dtmdeliverydate"] );
		}

		// 単価
		else if ( $strColumnName == "curproductprice" )
		{
			$aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryDetailResult["curproductprice"] )
			{
				$aryNewDetailResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] .= $aryDetailResult["curproductprice"];
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
			$aryNewDetailResult[$strColumnName] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryDetailResult["cursubtotalprice"] )
			{
				$aryNewDetailResult[$strColumnName] .= "0.00";
			}
			else
			{
				$aryNewDetailResult[$strColumnName] .= $aryDetailResult["cursubtotalprice"];
			}
		}

		// 明細備考
		else if ( $strColumnName == "strdetailnote" )
		{
			// 備考の特殊文字変換
			$aryDetailResult[$strColumnName] = fncHTMLSpecialChars( $aryDetailResult[$strColumnName] );

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
function fncSetPurchaseTabelName ( $aryResult, $aryTytle )
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
* 指定のコードのデータを他のマスタで使用しているコード取得
*
*	指定コードに対して、指定されたマスタの検索関数
*
*	@param  String 		$strCode 		検索対象コード
*	@param	Integer		$lngMode		検索モード	1:発注コードから仕入マスタ	（順次追加）
*	@param  Object		$objDB			DBオブジェクト
*	@return Array 		$aryCode		検索対象コードが使用されているマスタ内のコードの配列
*	@access public
*/
function fncGetDeleteCodeToMaster ( $strCode, $lngMode, $objDB )
{
	// SQL文の作成
	$strQuery = "SELECT distinct on (";
	switch ( $lngMode )
	{
		case 1:		// 発注コードから仕入マスタの検索時
			$strQuery .= "s.strStockCode) s.strStockCode as lngSearchNo FROM m_Stock s, m_Order o ";
			$strQuery .= "WHERE s.lngOrderNo = o.lngOrderNo AND s.bytInvalidFlag = FALSE AND o.strOrderCode = '";
			break;
	}
	$strQuery .= $strCode . "'";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryCode[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryCode = FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return $aryCode;
}






/**
* 指定のNOのデータを他のマスタで使用しているコード取得
*
*	指定NOに対して、指定されたマスタの検索関数
*
*	@param  Integer 	$lngNo 			検索対象No
*	@param	Integer		$lngMode		検索モード	1:発注コードから仕入マスタ	（順次追加）
*	@param  Object		$objDB			DBオブジェクト
*	@return Array 		$aryCode		検索対象コードが使用されているマスタ内のコードの配列
*	@access public
*/
function fncGetDeleteNoToMaster ( $lngNo, $lngMode, $objDB )
{
	// SQL文の作成
	$strQuery = "SELECT distinct on (";
	switch ( $lngMode )
	{
		case 1:		// 発注Noから仕入マスタの検索時
			$strQuery .= "s.lngOrderNo) s.lngOrderNo as lngSearchNo FROM m_Stock s ";
			$strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngOrderNo = ";
			break;
		case 2:		// 受注Noから売上マスタの検索時
			$strQuery .= "s.lngReceiveNo) s.lngReceiveNo as lngSearchNo FROM m_Sales s ";
			$strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngReceiveNo = ";
			break;
	}
	$strQuery .= $lngNo;

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryCode[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryCode = FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return $aryCode;
}






/**
* 指定の発注データについて無効化することでどうなるかケースわけする
*
*	指定の発注データの状態を調査し、ケースわけする関数
*
*	@param  Array 		$aryOrderData 	発注データ
*	@param  Object		$objDB			DBオブジェクト
*	@return Integer 	$lngCase		状態のケース
*										1: 対象発注データを無効化しても、最新の発注データが影響受けない
*										2: 対象発注データを無効化することで、最新の発注データが入れ替わる
*										3: 対象発注データが削除データで、発注が復活する
*										4: 対象発注データを無効化することで、最新の発注データになりうる発注データがない
*	@access public
*/
function fncGetInvalidCodeToMaster ( $aryOrderData, $objDB )
{
	// 発注コードの取得
	$strOrderCode = substr( $aryOrderData["strordercode"], 0, 8);

	// 削除対象発注と同じ発注コードの最新の発注Noを調べる
	$strQuery = "SELECT lngOrderNo FROM m_Order o WHERE o.strOrderCode = '" . $strOrderCode . "' AND o.bytInvalidFlag = FALSE ";
	$strQuery .= " AND o.lngRevisionNo >= 0";
	$strQuery .= " AND o.lngRevisionNo = ( "
		. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode )";
	$strQuery .= " AND o.strReviseCode = ( "
		. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode )";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
	}
	else
	{
		$lngCase = 4;
	}
	$objDB->freeResult( $lngResultID );

	// 削除対象が最新かどうかのチェック
	if ( $lngCase != 4 )
	{
		if ( $lngNewOrderNo == $aryOrderData["lngorderno"] )
		{
			// 最新の場合
			// 削除対象発注以外でと同じ発注コードの最新の発注Noを調べる
			$strQuery = "SELECT lngOrderNo FROM m_Order o WHERE o.strOrderCode = '" . $strOrderCode . "' AND o.bytInvalidFlag = FALSE ";
			$strQuery .= " AND o.lngOrderNo <> " . $aryOrderData["lngorderno"] . " AND o.lngRevisionNo >= 0";

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
		// 対象発注が削除データかどうかの確認
		else if ( $aryOrderData["lngrevisionno"] < 0 )
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






?>