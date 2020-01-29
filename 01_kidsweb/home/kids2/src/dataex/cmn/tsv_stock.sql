/*
	概要：経理(仕入一覧表)　「客先別」「仕入科目・部門・製品別」
	対象：データエクスポート
	作成：chiba
	備考：

	更新履歴：
	2004.04.07	金額表示部において 0.XX の場合に 0 が消えてしまうバグの修正
	2004.04.27	発注なし仕入を出力しないバグの修正
	2004.04.28	内税の際の合計金額を求める箇所を修正
	2004.05.06	日本円以外で円換算した場合の端数処理を追加
	2005.10.31  部門・担当者を製品マスタより参照するように変更
*/
SELECT
	s.dtmAppropriationDate
	,s.strStockCode, o.strOrderCode
	,c.strCompanyDisplayCode
	,c.strCompanyDisplayName
	,g.strGroupDisplayCode
	,g.strGroupDisplayName
	,mcg.strcategoryname
	,s.strSlipCode
	,sd.lngStockSubjectCode
	,ss.strStockSubjectName
	,sd.lngStockItemCode
	,si.strStockItemName
	,sd.strProductCode || '_' || sd.strReviseCode as strProductCode
	,p.strProductName
	,p.strGoodsCode
	,
	/* 荷姿計上の場合、単価を数で割る */
	CASE WHEN sd.lngConversionClassCode = 2 THEN To_char( sd.curProductPrice / p.lngCartonQuantity, '9999990.9999' )
	  ELSE To_char( sd.curProductPrice, '9999990.9999' )
	END AS curProductPrice
	/*pu.strProductUnitName*/
	,(select  strproductunitname from m_productunit where lngproductunitcode = 1) as productunit
	,
	/* 荷姿計上の場合、製品数量をカートン入り数で掛ける */
	CASE WHEN sd.lngConversionClassCode = 2 THEN  sd.lngProductQuantity * p.lngCartonQuantity
	  ELSE sd.lngProductQuantity
	END AS lngProductQuantity
	,tc.strTaxClassName
	,mu.strmonetaryunitname
	,
	/* 税抜金額に対しては　日本円以外で円換算する場合は端数処理（切捨て）を行う */
	CASE WHEN s.lngMonetaryUnitCode = 1 THEN To_char( sd.curSubTotalPrice, '999999990.99' )
	  ELSE To_char( TRUNC( sd.curSubTotalPrice * s.curConversionRate ), '999999990.99' )
	END AS curSubTotalPrice
	,
	/* 税額に対しては　いつでも端数処理（切捨て）を行う */
	To_char( TRUNC( sd.curTaxPrice * s.curConversionRate ), '999999990.99' ) AS curTaxPrice
	,
	/*
	日本円以外では、通常非課税なので、合計金額は税抜金額で円換算にて端数処理（切捨て）を行う
	日本円の場合では、内税、外税の場合に、合計金額は　税額＋税抜金額で計算する
	日本円の場合では、非課税の場合に、合計金額は税抜金額
	*/
	CASE WHEN ( s.lngMonetaryUnitCode <> 1 AND sd.lngTaxClassCode = 1 ) 
	       THEN To_char( TRUNC( sd.curSubTotalPrice * s.curConversionRate ), '999999990.99' )
	  WHEN ( s.lngMonetaryUnitCode = 1 AND ( sd.lngTaxClassCode = 2 OR sd.lngTaxClassCode = 3 ) )
	       THEN To_char( sd.curSubTotalPrice  + TRUNC( sd.curTaxPrice ), '999999990.99' )
	  WHEN ( s.lngMonetaryUnitCode = 1 AND sd.lngTaxClassCode = 1 ) 
	       THEN To_char( sd.curSubTotalPrice, '999999990.99' )
	  ELSE To_char( TRUNC( sd.curSubTotalPrice * s.curConversionRate ) + TRUNC( sd.curTaxPrice * s.curConversionRate ), '999999990.99' )
	END AS curTotalPrice
	,
	/*2006/08/28　by高　社内レートとTTMレートを照合するために合計が２つ出すようにしました*/
	CASE WHEN ( s.lngMonetaryUnitCode <> 1 AND sd.lngTaxClassCode = 1 ) 
	       THEN To_char( TRUNC( sd.curSubTotalPrice * (select mm.curconversionrate from m_monetaryrate  mm where
				mm.lngmonetaryratecode = '1'
				and s.lngmonetaryunitcode = mm.lngmonetaryunitcode
				and s.dtmAppropriationDate >= mm.dtmapplystartdate
				and s.dtmappropriationdate <= mm.dtmapplyenddate
				) ), '999999990.99' )
	  WHEN ( s.lngMonetaryUnitCode = 1 AND ( sd.lngTaxClassCode = 2 OR sd.lngTaxClassCode = 3 ) )
	       THEN To_char( sd.curSubTotalPrice  + TRUNC( sd.curTaxPrice ), '999999990.99' )
	  WHEN ( s.lngMonetaryUnitCode = 1 AND sd.lngTaxClassCode = 1 ) 
	       THEN To_char( sd.curSubTotalPrice, '999999990.99' )
	  ELSE To_char( TRUNC( sd.curSubTotalPrice * s.curConversionRate ) + TRUNC( sd.curTaxPrice * s.curConversionRate ), '999999990.99' )
	END AS curTotalPriceTTM
FROM
	m_Stock s 
	,t_StockDetail sd
	LEFT JOIN m_Order o
		ON sd.lngOrderNo = o.lngOrderNo
		and sd.lngOrderRevisionNo = o.lngRevisionNo
	,m_Product p
	,m_Company c
	,m_Group g
	,m_TaxClass tc
	,m_StockSubject ss
	,m_StockItem si
	,m_ProductUnit pu
	,m_PayCondition pc
	,m_monetaryunit mu
	,m_category mcg
WHERE
	s.lngRevisionNo =
	(
	  SELECT MAX ( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s.strStockCode = s2.strStockCode
	)
	AND 0 <=
	(
	  SELECT MIN ( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s.strStockCode = s3.strStockCode AND s3.bytInvalidFlag = false
	)
	AND p.lngrevisionno =
	(
	  SELECT MAX ( p2.lngRevisionNo ) FROM m_Product p2 WHERE p2.lngProductNo = p.lngProductNo
	)
	 AND 0 <=
	(
	  SELECT MIN ( p3.lngRevisionNo ) FROM m_Product p3 WHERE p3.lngProductNo = p.lngProductNo AND p3.bytInvalidFlag = false
	)
	AND date_trunc ( 'day', s.dtmAppropriationDate ) >= '_%dtmAppropriationDateFrom%_'
	AND date_trunc ( 'day', s.dtmAppropriationDate ) <= '_%dtmAppropriationDateTo%_'
	AND p.bytInvalidFlag        = FALSE
	AND s.bytInvalidFlag        = FALSE
	AND s.lngCustomerCompanyCode = c.lngCompanyCode
	/* AND s.lngGroupCode           = g.lngGroupCode */
	and p.lnginchargegroupcode   = g.lngGroupCode
	AND s.lngStockNo             = sd.lngStockNo
	AND s.lngRevisionNo             = sd.lngRevisionNo
	AND s.lngPayConditionCode    = pc.lngPayConditionCode
	AND sd.lngStockItemCode      = si.lngStockItemCode
	AND sd.lngStockSubjectCode   = si.lngStockSubjectCode
	AND sd.lngStockSubjectCode   = ss.lngStockSubjectCode
	AND sd.strProductCode        = p.strProductCode
	AND sd.strReviseCode        = p.strReviseCode
	AND sd.lngProductUnitCode    = pu.lngProductUnitCode
	AND sd.lngTaxClassCode       = tc.lngTaxClassCode
	AND s.lngmonetaryunitcode = mu.lngmonetaryunitcode
	and p.lngcategorycode = mcg.lngcategorycode
/* 条件：1.仕入科目、顧客の順 2.仕入科目、グループ、製品の順 */
ORDER BY _%lngExportConditions%_
