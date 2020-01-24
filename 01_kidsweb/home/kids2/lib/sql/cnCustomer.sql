/*
	概要：「顧客コード」から「名称」を取得
	対象：（共通）
	作成：斎藤和志
	備考：一致する「コード＋名称」を取得
*/
SELECT mc.lngcompanycode, mc.strcompanydisplayname
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 2
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode = '_%strFormValue0%_'
ORDER BY mc.strcompanydisplaycode
