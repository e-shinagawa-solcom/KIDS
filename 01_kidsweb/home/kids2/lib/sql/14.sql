/*
	概要：製品＆部門、同一値カウント
	対象：共通サブウィンドウ
	作成：斎藤和志
	備考：顧客コード＋名称から一致する「コード＋名称」一覧を生成
*/
SELECT COUNT(*), COUNT(*) AS datacount
FROM m_product mp, m_group mg
WHERE mp.lnginchargegroupcode = mg.lnggroupcode
	AND mp.bytinvalidflag = false
	AND mg.bytgroupdisplayflag = true
	AND UPPER(mp.strproductenglishname) = UPPER('_%strFormValue0%_')
	AND mg.strgroupdisplaycode = '_%strFormValue1%_'
