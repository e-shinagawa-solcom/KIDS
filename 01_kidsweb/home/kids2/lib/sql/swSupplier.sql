/*
	概要：仕入先検索
	対象：共通サブウィンドウ
	作成：斎藤和志
	備考：一致する「コード＋名称」一覧を生成
*/
SELECT mc.strcompanydisplaycode,(mc.strcompanydisplaycode || ' ' || mc.strcompanydisplayname) AS strcompanydisplaycodename, mc.strcompanydisplayname
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 3
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode LIKE '%_%strFormValue0%_%'
	AND mc.strcompanydisplayname LIKE '%_%strFormValue1%_%'
ORDER BY mc.strcompanydisplaycode
