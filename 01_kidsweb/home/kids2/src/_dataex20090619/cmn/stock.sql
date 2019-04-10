/*
	���ס�����(��������ɽ)���ֵ����̡סֻ������ܡ����硦�����̡�
	�оݡ��ǡ����������ݡ���
	������chiba
	���͡�

	��������
	2004.04.07	���ɽ�����ˤ����� 0.XX �ξ��� 0 ���ä��Ƥ��ޤ��Х��ν���
	2004.04.27	ȯ��ʤ���������Ϥ��ʤ��Х��ν���
	2004.04.28	���Ǥκݤι�׶�ۤ����ս����
	2004.05.06	���ܱ߰ʳ��Ǳߴ�����������ü���������ɲ�
	2005.10.31  ���硦ô���Ԥ����ʥޥ�����껲�Ȥ���褦���ѹ�
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
	,sd.strProductCode
	,p.strProductName
	,p.strGoodsCode
	,
	/* �ٻѷ׾�ξ�硢ñ������ǳ�� */
	CASE WHEN sd.lngConversionClassCode = 2 THEN To_char( sd.curProductPrice / p.lngCartonQuantity, '9999990.9999' )
	  ELSE To_char( sd.curProductPrice, '9999990.9999' )
	END AS curProductPrice
	/*pu.strProductUnitName*/
	,(select  strproductunitname from m_productunit where lngproductunitcode = 1) as productunit
	,
	/* �ٻѷ׾�ξ�硢���ʿ��̤򥫡��ȥ�������ǳݤ��� */
	CASE WHEN sd.lngConversionClassCode = 2 THEN  sd.lngProductQuantity * p.lngCartonQuantity
	  ELSE sd.lngProductQuantity
	END AS lngProductQuantity
	,tc.strTaxClassName
	,mu.strmonetaryunitname
	,
	/* ��ȴ��ۤ��Ф��Ƥϡ����ܱ߰ʳ��Ǳߴ����������ü���������ڼΤơˤ�Ԥ� */
	CASE WHEN s.lngMonetaryUnitCode = 1 THEN To_char( sd.curSubTotalPrice, '999999990.99' )
	  ELSE To_char( TRUNC( sd.curSubTotalPrice * s.curConversionRate ), '999999990.99' )
	END AS curSubTotalPrice
	,
	/* �ǳۤ��Ф��Ƥϡ����ĤǤ�ü���������ڼΤơˤ�Ԥ� */
	To_char( TRUNC( sd.curTaxPrice * s.curConversionRate ), '999999990.99' ) AS curTaxPrice
	,
	/*
	���ܱ߰ʳ��Ǥϡ��̾�����ǤʤΤǡ���׶�ۤ���ȴ��ۤǱߴ����ˤ�ü���������ڼΤơˤ�Ԥ�
	���ܱߤξ��Ǥϡ����ǡ����Ǥξ��ˡ���׶�ۤϡ��ǳۡ���ȴ��ۤǷ׻�����
	���ܱߤξ��Ǥϡ�����Ǥξ��ˡ���׶�ۤ���ȴ���
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
	/*2006/08/28��by�⡡����졼�Ȥ�TTM�졼�Ȥ�ȹ礹�뤿��˹�פ����ĽФ��褦�ˤ��ޤ���*/
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
	LEFT JOIN m_Order o
		ON s.lngOrderNo = o.lngOrderNo
	,t_StockDetail sd
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
	AND date_trunc ( 'day', s.dtmAppropriationDate ) >= '_%dtmAppropriationDateFrom%_'
	AND date_trunc ( 'day', s.dtmAppropriationDate ) <= '_%dtmAppropriationDateTo%_'
	AND p.bytInvalidFlag        = FALSE
	AND s.bytInvalidFlag        = FALSE
	AND s.lngCustomerCompanyCode = c.lngCompanyCode
	/* AND s.lngGroupCode           = g.lngGroupCode */
	and p.lnginchargegroupcode   = g.lngGroupCode
	AND s.lngStockNo             = sd.lngStockNo
	AND s.lngPayConditionCode    = pc.lngPayConditionCode
	AND sd.lngStockItemCode      = si.lngStockItemCode
	AND sd.lngStockSubjectCode   = si.lngStockSubjectCode
	AND sd.lngStockSubjectCode   = ss.lngStockSubjectCode
	AND sd.strProductCode        = p.strProductCode
	AND sd.lngProductUnitCode    = pu.lngProductUnitCode
	AND sd.lngTaxClassCode       = tc.lngTaxClassCode
	AND s.lngmonetaryunitcode = mu.lngmonetaryunitcode
	and p.lngcategorycode = mcg.lngcategorycode
/* ��1.�������ܡ��ܵҤν� 2.�������ܡ����롼�ס����ʤν� */
ORDER BY _%lngExportConditions%_
