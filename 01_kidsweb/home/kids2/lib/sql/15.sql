/*
	概要：グループ検索
	対象：ユーザー管理
	作成：chiba
	修正：斎藤和志
	備考：会社から一致する「グループ」一覧を生成
*/
SELECT DISTINCT ON ( mg.lngGroupCode ) mg.lngGroupCode, (mg.strGroupDisplayCode || ' ' || mg.strGroupDisplayName) AS strgroupdisplaycodename
FROM m_group mg, m_attributerelation mar
WHERE mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode <> 0
	AND mg.bytGroupDisplayFlag = TRUE
	AND mg.lngCompanyCode = _%strFormValue0%_ 
ORDER BY mg.lnggroupcode
