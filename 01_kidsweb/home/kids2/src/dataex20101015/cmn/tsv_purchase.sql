/*
	概要：Purchase Recipe　「L/C」「T/T」「on board」
	対象：データエクスポート
	作成：chiba
	備考：

	更新履歴：
	2004.04.27	発注なし仕入を出力するように修正
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
	,u.strUserDisplayCode
	,u.strUserDisplayName
	,s.strSlipCode
	,mu.strMonetaryUnitName
	,mrc.strMonetaryRateName
	,s.curConversionRate
	,pc.strPayConditionName
	,s.dtmExpirationDate
	,sd.lngStockSubjectCode
	,ss.strStockSubjectName
	,sd.lngStockItemCode
	,si.strStockItemName
	,sd.strProductCode
	,p.strProductEnglishName
	,sd.curProductPrice
	,pu.strProductUnitName
	,sd.lngProductQuantity
	,sd.curSubTotalPrice
	,sd.strNote
FROM
	m_Stock s
	LEFT JOIN m_Order o
		ON s.lngOrderNo = o.lngOrderNo
	,t_StockDetail sd
	,m_Product p
	,m_Company c
	,m_Group g
	,m_User u
	,m_MonetaryUnit mu
	,m_MonetaryRateClass mrc
	,m_StockSubject ss
	,m_StockItem si
	,m_ProductUnit pu
	,m_PayCondition pc
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
	AND date_trunc ( 'day', s.dtmAppropriationDate ) >= '_%dtmAppropriationDateFrom%_'
	AND date_trunc ( 'day', s.dtmAppropriationDate ) <= '_%dtmAppropriationDateTo%_'
	AND p.bytInvalidFlag        = FALSE
	AND s.bytInvalidFlag        = FALSE
	AND s.lngStockNo             = sd.lngStockNo
	AND s.lngCustomerCompanyCode = c.lngCompanyCode
	/* AND s.lngGroupCode           = g.lngGroupCode */
	and p.lnginchargegroupcode   = g.lngGroupCode
	/* AND s.lngUserCode            = u.lngUserCode */
	and p.lnginchargeusercode    = u.lngUserCode
	AND s.lngMonetaryUnitCode    = mu.lngMonetaryUnitCode
	AND s.lngMonetaryRateCode    = mrc.lngMonetaryRateCode
	AND s.lngPayConditionCode    = pc.lngPayConditionCode
	AND sd.lngStockItemCode      = si.lngStockItemCode
	AND sd.lngStockSubjectCode   = si.lngStockSubjectCode
	AND sd.lngStockSubjectCode   = ss.lngStockSubjectCode
	AND sd.strProductCode        = p.strProductCode
	AND sd.lngProductUnitCode    = pu.lngProductUnitCode
	and p.lngcategorycode = mcg.lngcategorycode

	/* 条件：1.L/Cのデータ 2.T/Tのデータ 3.計上日～製品到着日が月を跨いだデータ */
	_%lngExportConditions%_
