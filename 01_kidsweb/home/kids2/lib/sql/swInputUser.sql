/*
	概要：入力者検索
	対象：共通サブウィンドウ
	作成：斎藤和志
	備考：入力者コード＋＋名称から一致する「コード＋名称」一覧を生成
*/
SELECT DISTINCT mu.struserdisplaycode,(mu.struserdisplaycode || ' ' || mu.struserdisplayname) AS struserdisplaycodename, mu.struserdisplayname
FROM m_group mg,  m_grouprelation mgr,  m_user mu,  m_attributerelation mar
WHERE mu.lngcompanycode = mar.lngcompanycode
	AND mu.lngusercode = mgr.lngusercode
	AND mg.lnggroupcode = mgr.lnggroupcode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mu.bytuserdisplayflag = true
	AND mu.struserdisplaycode LIKE '%_%strFormValue0%_%'
	AND mu.struserdisplayname LIKE '%_%strFormValue1%_%'
ORDER BY mu.struserdisplaycode
