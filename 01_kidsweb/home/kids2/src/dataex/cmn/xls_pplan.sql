SELECT DISTINCT
_%PPLAN_SELECT_CONDITION%_
FROM m_product mp
LEFT OUTER JOIN t_goodsplan tgp ON tgp.lngProductNo = mp.lngProductNo AND tgp.lngRevisionNo = ( 
	SELECT MAX ( tgp2.lngRevisionNo )
	FROM t_GoodsPlan tgp2
	WHERE tgp.lngProductNo = tgp2.lngProductNo
	)
	AND (tgp.lngGoodsPlanProgressCode = 1 OR tgp.lngGoodsPlanProgressCode = 4)
LEFT OUTER JOIN m_estimate me ON me.strProductCode = mp.strProductCode AND me.lngRevisionNo = (
	SELECT MAX ( me2.lngRevisionNo )
	FROM m_estimate me2
	WHERE me.strProductCode = me2.strProductCode AND me2.bytInvalidFlag = false
	)
LEFT OUTER JOIN m_Company mc ON mc.lngCompanyCode = mp.lngFactoryCode
LEFT OUTER JOIN m_Company mc2 ON mc2.lngCompanyCode = mp.lngCustomerCompanyCode
LEFT OUTER JOIN m_Group mg on mg.lngGroupCode = mp.lngInchargeGroupCode
LEFT OUTER JOIN m_User mu ON mu.lngUserCode = mp.lngInchargeUserCode
LEFT OUTER JOIN m_CertificateClass mcc ON mcc.lngCertificateClassCode = mp.lngCertificateClassCode

WHERE
	to_char(mp.dtmDeliveryLimitDate,'yyyy-mm') like '_%YEAR%_-_%MONTH%_%'
	AND (tgp.lngGoodsPlanProgressCode = 1 OR tgp.lngGoodsPlanProgressCode = 4)
	AND mp.bytInvalidFlag = false
	AND
		CASE WHEN '0' = '_%lngInchargeGroupCode%_'
		THEN TRUE
		ELSE mp.lngInchargeGroupCode in (_%lngInchargeGroupCode%_)
		END

_%WHERE%_
