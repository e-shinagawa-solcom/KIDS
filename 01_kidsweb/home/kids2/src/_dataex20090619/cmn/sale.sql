/*
	���ס����쥷�ԡ����硦������
	�оݡ��ǡ����������ݡ���
	������chiba
	���͡�

	��������
	2004.04.07	���ɽ�����ˤ����� 0.XX �ξ��� 0 ���ä��Ƥ��ޤ��Х��ν���
	2004.04.27	����ʤ�������Ϥ���褦�˽���
	2004.05.06	�ǳۤ��Ф���ü���������ڼΤơˤ���褦�˽���
	2005.10.31  ���硦ô���Ԥ����ʥޥ�����껲�Ȥ���褦���ѹ�
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
	/* �ٻ�ñ�̷׾�ξ�硢ñ���򥫡��ȥ�������ǳ�� */
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
	/* �ٻ�ñ�̷׾�ξ�硢���ʿ��̤򥫡��ȥ�������ǳݤ��� */
	CASE WHEN sd.lngConversionClassCode = 2 THEN  sd.lngProductQuantity * p.lngCartonQuantity
	  ELSE sd.lngProductQuantity
	END AS lngProductQuantity
/*	
	,To_char( sd.curSubTotalPrice * sa.curConversionRate, '999999990.99' ) AS curSubTotalPrice
*/
	/* ��ȴ��ۤ��Ф��Ƥϡ����ܱ߰ʳ��Ǳߴ����������ü���������ڼΤơˤ�Ԥ� */
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
	���ܱ߰ʳ��Ǥϡ��̾�����ǤʤΤǡ���׶�ۤ���ȴ��ۤǱߴ����ˤ�ü���������ڼΤơˤ�Ԥ�
	���ܱߤξ��Ǥϡ����ǡ����Ǥξ��ˡ���׶�ۤϡ��ǳۡ���ȴ��ۤǷ׻�����
	���ܱߤξ��Ǥϡ�����Ǥξ��ˡ���׶�ۤ���ȴ���
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

	/* ��1.���롼�ס��ܵҤν� 2.���롼�ס����ʤν� */
	ORDER BY _%lngExportConditions%_
