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
	To_char(s.dtmAppropriationDate,'yyyy/mm/dd') as dtmAppropriationDate
	,s.strStockCode
	, mpo.strOrderCode || '_' || lpad(to_char(mpo.lngrevisionno, 'FM99'), 2, '0') as strOrderCode
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
	inner join t_StockDetail sd 
		on s.lngStockNo             = sd.lngStockNo
		AND s.lngRevisionNo             = sd.lngRevisionNo

  LEFT JOIN ( 
    select
      tpod1.lngpurchaseorderno
      , tpod1.lngrevisionno
      , tpod1.lngorderno
      , tpod1.lngorderdetailno
      , tpod1.lngorderrevisionno 
    from
      t_purchaseorderdetail tpod1 
      inner join ( 
        select
          max(lngrevisionno) lngrevisionno
          , lngpurchaseorderno 
        from
          m_purchaseorder 
        group by
          lngpurchaseorderno
      ) mpo_max 
        on tpod1.lngpurchaseorderno = mpo_max.lngpurchaseorderno 
        and tpod1.lngrevisionno = mpo_max.lngrevisionno
  ) tpod 
    on tpod.lngorderno = sd.lngorderno 
    and tpod.lngorderdetailno = sd.lngorderdetailno 
    and tpod.lngorderrevisionno = sd.lngorderrevisionno 
  LEFT JOIN m_purchaseorder mpo 
    ON tpod.lngpurchaseorderno = mpo.lngpurchaseorderno 
    and tpod.lngrevisionno = mpo.lngrevisionno 
		
	INNER JOIN m_Product p 
		on sd.strProductCode        = p.strProductCode
		AND sd.strReviseCode        = p.strReviseCode

	inner join m_Company c 
		on s.lngCustomerCompanyCode = c.lngCompanyCode

	inner join m_Group g 
		on p.lnginchargegroupcode   = g.lngGroupCode

	inner join m_TaxClass tc 
		on sd.lngTaxClassCode       = tc.lngTaxClassCode

	inner join m_StockSubject ss 
		on sd.lngStockSubjectCode   = ss.lngStockSubjectCode

	inner join m_StockItem si 
		on sd.lngStockItemCode      = si.lngStockItemCode
		AND sd.lngStockSubjectCode   = si.lngStockSubjectCode

	inner join m_ProductUnit pu 
		on sd.lngProductUnitCode    = pu.lngProductUnitCode

	inner join m_PayCondition pc 
		on s.lngPayConditionCode    = pc.lngPayConditionCode
	inner join m_monetaryunit mu 
		on s.lngmonetaryunitcode = mu.lngmonetaryunitcode

	left outer join m_category mcg 
		on p.lngcategorycode = mcg.lngcategorycode
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
	  SELECT MAX ( p2.lngRevisionNo ) FROM m_Product p2 WHERE p2.lngProductNo = p.lngProductNo and p2.strrevisecode = p.strrevisecode
	)
	 AND 0 <=
	(
	  SELECT MIN ( p3.lngRevisionNo ) FROM m_Product p3 WHERE p3.lngProductNo = p.lngProductNo and p3.strrevisecode = p.strrevisecode AND p3.bytInvalidFlag = false
	)
	AND date_trunc ( 'day', s.dtmAppropriationDate ) >= '_%dtmAppropriationDateFrom%_'
	AND date_trunc ( 'day', s.dtmAppropriationDate ) <= '_%dtmAppropriationDateTo%_'
	AND p.bytInvalidFlag        = FALSE
	AND s.bytInvalidFlag        = FALSE
	/* AND s.lngGroupCode           = g.lngGroupCode */
/* 条件：1.仕入科目、顧客の順 2.仕入科目、グループ、製品の順 */
ORDER BY _%strExportConditions%_
