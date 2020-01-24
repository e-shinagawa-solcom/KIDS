/*
	概要：「lngGroupCode」から「strGroupDisplayCode」を取得
	対象：（共通）
	作成：斎藤和志
	備考：「コード」から一致する「名称」を取得
*/
SELECT mg.strgroupdisplaycode, mg.strgroupdisplaycode
FROM m_group mg, m_attributerelation mar
WHERE  mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mg.lnggroupcode = '_%strFormValue0%_'
