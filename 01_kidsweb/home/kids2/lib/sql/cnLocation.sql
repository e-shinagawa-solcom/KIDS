/*
	概要：「納品場所コード」から「名称」を取得
	対象：（共通）
	作成：斎藤和志
	備考：一致する「コード＋名称」一覧を生成
*/
SELECT mc.strcompanydisplaycode, mc.strcompanydisplayname
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 5
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode = '_%strFormValue0%_'
