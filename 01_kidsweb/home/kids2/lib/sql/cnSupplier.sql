/*
	概要：「仕入先コード」から「名称」を取得
	対象：（共通）
	笹椪：watanabe
	更新：松木洋
	備考：
*/
SELECT mc.lngcompanycode, mc.strcompanydisplayname , lngcountrycode
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 3
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode = '_%strFormValue0%_'
ORDER BY mc.strcompanydisplaycode
