/*
	概要：「仕入区分コード」から「仕入科目」を取得
	対象：（共通）
	作成：千葉健司
	備考：「コード」から一致する「コード」を取得
*/
SELECT lngStockSubjectCode, lngStockSubjectCode || ':' || strStockSubjectName
FROM m_Stocksubject
WHERE lngStockClassCode = '_%strFormValue0%_'
