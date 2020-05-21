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
	To_char(s.dtmAppropriationDate,'yyyy/mm/dd') as dtmAppropriationDate
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
	,sd.strProductCode || '_' || sd.strReviseCode as strProductCode
	,p.strProductEnglishName
	,sd.curProductPrice
	,pu.strProductUnitName
	,sd.lngProductQuantity
	,sd.curSubTotalPrice
	,sd.strNote
FROM
	m_Stock s
	inner join t_StockDetail sd 
		on s.lngStockNo             = sd.lngStockNo
		AND s.lngRevisionNo             = sd.lngRevisionNo
	LEFT JOIN m_Order o
		ON sd.lngOrderNo = o.lngOrderNo
		AND sd.lngOrderRevisionNo = o.lngRevisionNo
	inner join m_Product p 
		on sd.strProductCode        = p.strProductCode
		AND sd.strReviseCode        = p.strReviseCode
	inner join m_Company c 
		on s.lngCustomerCompanyCode = c.lngCompanyCode
	inner join m_Group g 
		on p.lnginchargegroupcode   = g.lngGroupCode
	inner join m_User u 
		on p.lnginchargeusercode    = u.lngUserCode
	inner join m_MonetaryUnit mu 
		on s.lngMonetaryUnitCode    = mu.lngMonetaryUnitCode
	inner join m_MonetaryRateClass mrc 
		on s.lngMonetaryRateCode    = mrc.lngMonetaryRateCode
	inner join m_StockSubject ss 
		on sd.lngStockSubjectCode   = ss.lngStockSubjectCode
	inner join m_StockItem si 
		on sd.lngStockItemCode      = si.lngStockItemCode
		AND sd.lngStockSubjectCode   = si.lngStockSubjectCode
	inner join m_ProductUnit pu 
		on sd.lngProductUnitCode    = pu.lngProductUnitCode
	inner join m_PayCondition pc 
		on s.lngPayConditionCode    = pc.lngPayConditionCode
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
	/* AND s.lngUserCode            = u.lngUserCode */

	/* 条件：1.L/Cのデータ 2.T/Tのデータ 3.計上日～製品到着日が月を跨いだデータ */
	_%strExportConditions%_
