/*
	概要：製品コード、売上区分コード、通貨単位コードから単価リストの取得
	対象：受注管理、売上管理
	作成：手塚貴文
	備考：
*/
SELECT mpp.lngProductPriceCode, mpp.curProductPrice
FROM m_productprice mpp
JOIN m_monetaryunit mmu ON mpp.lngmonetaryunitcode = mmu.lngmonetaryunitcode
JOIN m_product mp ON mp.lngProductNo = mpp.lngProductNo
WHERE mp.strProductCode = '_%strFormValue0%_'
	AND mpp.lngsalesclasscode = '_%strFormValue1%_'
	AND mmu.strmonetaryunitsign = '_%strFormValue2%_'
ORDER BY mpp.lngProductPriceCode DESC
