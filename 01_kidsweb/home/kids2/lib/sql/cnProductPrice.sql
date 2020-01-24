/*
	概要：製品コード、仕入科目コード、仕入部品コード、通貨単位コードから単価リストの取得
	対象：発注管理、仕入管理
	作成：手塚貴文
	備考：
*/
SELECT mpp.lngProductPriceCode, mpp.curProductPrice
FROM m_productprice mpp
JOIN m_monetaryunit mmu ON mpp.lngmonetaryunitcode = mmu.lngmonetaryunitcode
JOIN m_product mp ON mp.lngProductNo = mpp.lngProductNo
WHERE mp.strProductCode = '_%strFormValue0%_'
	AND mpp.lngStockSubjectCode = '_%strFormValue1%_'
	AND mpp.lngStockItemCode = '_%strFormValue2%_'
	AND mmu.strmonetaryunitsign = '_%strFormValue3%_'
ORDER BY mpp.lngProductPriceCode DESC
