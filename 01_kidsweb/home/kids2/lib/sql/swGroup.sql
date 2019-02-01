/*
	概要：部門検索
	対象：共通サブウィンドウ
	作成：斎藤和志
	備考：部門コード＋名称から一致する「コード＋名称」一覧を生成
*/
SELECT DISTINCT mg.strgroupdisplaycode,(mg.strgroupdisplaycode || ' ' || mg.strgroupdisplayname) AS strgroupdisplaycodename, mg.strgroupdisplayname
FROM m_group mg, m_attributerelation mar
WHERE  mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mg.strgroupdisplaycode LIKE '%_%strFormValue0%_%'
	AND mg.strgroupdisplayname LIKE '%_%strFormValue1%_%'
ORDER BY mg.strgroupdisplaycode
