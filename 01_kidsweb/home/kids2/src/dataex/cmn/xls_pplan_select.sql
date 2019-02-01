
	tgp.lngProductNo AS strReportKeyCode,
	tgp.lngGoodsPlanProgressCode,
	me.lngEstimateNo,
	me.lngEstimateStatusCode,

	mp.strProductName, 
	mp.strProductCode, 
	mp.strGoodsCode,
	mp.dtmDeliveryLimitDate, 
	'', 
	mp.curRetailPrice, 
	mp.lngFactoryCode,
	mc.strCompanyDisplayName	AS strCompanyDisplayName,
	mp.lngCustomerCompanyCode,
	mc2.strCompanyDisplayName	AS strCustomerCompanyName,
	mp.lngProductUnitCode,
	CASE WHEN mp.lngProductionUnitCode = 2 THEN 
		mp.lngProductionQuantity
	ELSE
		mp.lngProductionQuantity / mp.lngCartonQuantity
	END AS lngDeliveryQuantity,
	mp.lngCartonQuantity,
	mp.lngBoxQuantity,
	mp.lngInchargeUserCode,
	mu.strUserDisplayName,
	mp.lngBoxQuantity,
	mp.lngCertificateClassCode,
	mcc.strCertificateClassName,
	mg.lngGroupCode,
	mg.strGroupDisplayCode,
	mg.strGroupDisplayColor
