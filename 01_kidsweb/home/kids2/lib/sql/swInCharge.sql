/*
	概要：顧客・担当者検索
	対象：共通サブウィンドウ
	作成：斎藤和志
	備考：顧客コード＋担当者コード＋名称から一致する「コード＋名称」一覧を生成
*/
SELECT DISTINCT mu.struserdisplaycode,(mu.struserdisplaycode || ' ' || mu.struserdisplayname) AS struserdisplaycodename, mu.struserdisplayname
FROM m_user mu, m_company mc, m_attributerelation mal
WHERE mc.lngcompanycode = mal.lngcompanycode 
	AND mal.lngattributecode = 2
	AND mu.lngcompanycode = mc.lngcompanycode
	AND mc.bytcompanydisplayflag = true
	AND mu.bytuserdisplayflag = true
	AND mc.strcompanydisplaycode LIKE '%_%strFormValue0%_%'
	AND mu.struserdisplaycode LIKE '%_%strFormValue1%_%'
	AND mu.struserdisplayname LIKE '%_%strFormValue2%_%'
ORDER BY mu.struserdisplaycode
