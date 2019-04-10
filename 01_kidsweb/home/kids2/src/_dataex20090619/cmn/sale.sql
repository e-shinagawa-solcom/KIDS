/*
	概要：売上レシピ　部門・客先別
	対象：データエクスポート
	作成：chiba
	備考：

	更新履歴：
	2004.04.07	金額表示部において 0.XX の場合に 0 が消えてしまうバグの修正
	2004.04.27	受注なし売上を出力するように修正
	2004.05.06	税額に対して端数処理（切捨て）するように修正
	2005.10.31  部門・担当者を製品マスタより参照するように変更
*/
SELECT
	sa.dtmAppropriationDate
	,sa.strSalesCode
/*
	,r.strReceiveCode 
*/
	,r.strcustomerreceivecode
	,c.strCompanyDisplayCode
	,c.strCompanyDisplayName
	,g.strGroupDisplayCode
	,g.strGroupDisplayName
	,mcg.strcategoryname
	,sa.strSlipCode
	,sc.strSalesClassName
	,sd.strProductCode
	,p.strProductName
	,p.strGoodsCode
	,mu.strmonetaryunitname
	,
	/* 荷姿単位計上の場合、単価をカートン入り数で割る */
	CASE WHEN sd.lngConversionClassCode = 2 THEN To_char( sd.curProductPrice / p.lngCartonQuantity, '999999990.9999' )
	  ELSE To_char( sd.curProductPrice, '999999990.9999' )
	END AS curProductPrice,
/*
	'pcs', 
*/
	(select  strproductunitname from m_productunit where lngproductunitcode = 1) as productunit,
	/*
	pu.strProductUnitName,
	*/
	/* 荷姿単位計上の場合、製品数量をカートン入り数で掛ける */
	CASE WHEN sd.lngConversionClassCode = 2 THEN  sd.lngProductQuantity * p.lngCartonQuantity
	  ELSE sd.lngProductQuantity
	END AS lngProductQuantity
/*	
	,To_char( sd.curSubTotalPrice * sa.curConversionRate, '999999990.99' ) AS curSubTotalPrice
*/
	/* 税抜金額に対しては　日本円以外で円換算する場合は端数処理（切捨て）を行う */
	,CASE WHEN sa.lngMonetaryUnitCode = 1 THEN To_char( sd.curSubTotalPrice, '999999990.99' )
	  ELSE To_char( TRUNC( sd.curSubTotalPrice * sa.curConversionRate ), '999999990.99' )
	END AS curSubTotalPrice
	
	
	,To_char( TRUNC( sd.curTaxPrice * sa.curConversionRate ), '999999990.99' ) AS curTaxPrice
	,
/*
	CASE WHEN sd.lngTaxClassCode = 2 THEN  To_char( ( sd.curSubTotalPrice * sa.curConversionRate ) + ( TRUNC( sd.curTaxPrice * sa.curConversionRate ) ), '999999990.99' )
	  ELSE To_char( sd.curSubTotalPrice * sa.curConversionRate, '999999990.99' )
	END AS curTotalPrice
*/
	/*
	日本円以外では、通常非課税なので、合計金額は税抜金額で円換算にて端数処理（切捨て）を行う
	日本円の場合では、内税、外税の場合に、合計金額は　税額＋税抜金額で計算する
	日本円の場合では、非課税の場合に、合計金額は税抜金額
	*/
	CASE WHEN ( sa.lngMonetaryUnitCode <> 1 AND sd.lngTaxClassCode = 1 ) 
	       THEN To_char( TRUNC( sd.curSubTotalPrice * sa.curConversionRate ), '999999990.99' )
	  WHEN ( sa.lngMonetaryUnitCode = 1 AND ( sd.lngTaxClassCode = 2 OR sd.lngTaxClassCode = 3 ) )
	       THEN To_char( sd.curSubTotalPrice  + TRUNC( sd.curTaxPrice ), '999999990.99' )
	  WHEN ( sa.lngMonetaryUnitCode = 1 AND sd.lngTaxClassCode = 1 ) 
	       THEN To_char( sd.curSubTotalPrice, '999999990.99' )
	  ELSE To_char( TRUNC( sd.curSubTotalPrice * sa.curConversionRate ) + TRUNC( sd.curTaxPrice * sa.curConversionRate ), '999999990.99' )
	END AS curTotalPrice


	,sd.strNote
FROM
	m_Sales sa
		left join t_SalesDetail sd
			on sd.lngsalesno = sa.lngsalesno
			left join m_Receive r
				on r.lngreceiveno = sd.lngReceiveNo
	,m_Company c
	,m_Group g
	,m_SalesClass sc
	,m_Product p
	,m_ProductUnit pu
	,m_monetaryunit mu
	,m_category mcg
WHERE
	sa.lngRevisionNo = 
	(
	  SELECT MAX ( sa2.lngRevisionNo ) FROM m_Sales sa2 WHERE sa2.strSalesCode = sa.strSalesCode
	)
	 AND 0 <=
	(
	  SELECT MIN ( sa3.lngRevisionNo ) FROM m_Sales sa3 WHERE sa.strSalesCode = sa3.strSalesCode AND sa3.bytInvalidFlag = false
	)
	AND date_trunc ( 'day', sa.dtmAppropriationDate ) >= '_%dtmAppropriationDateFrom%_'
	AND date_trunc ( 'day', sa.dtmAppropriationDate ) <= '_%dtmAppropriationDateTo%_'
	AND p.bytInvalidFlag         = FALSE
	AND sa.bytInvalidFlag        = FALSE

	AND sa.lngCustomerCompanyCode = c.lngCompanyCode
	/* AND sa.lngGroupCode          = g.lngGroupCode*/
	and p.lnginchargegroupcode   = g.lngGroupCode
	AND sd.lngSalesClassCode     = sc.lngSalesClassCode
	AND sa.lngSalesNo            = sd.lngSalesNo
	AND sd.strProductCode        = p.strProductCode
	AND sd.lngProductUnitCode    = pu.lngProductUnitCode
	AND sa.lngmonetaryunitcode = mu.lngmonetaryunitcode
	and p.lngcategorycode = mcg.lngcategorycode

	/* 条件：1.グループ、顧客の順 2.グループ、製品の順 */
	ORDER BY _%lngExportConditions%_
