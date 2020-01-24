/*
	概要：「仕入科目コード」から「名称」を取得
	対象：発注管理、仕入管理
	作成：watanabe
	更新：斎藤和志
	備考：「コード」から一致する「名称」を取得
*/
SELECT ms.lngstockitemcode, ms.lngstockitemcode || ' ' || ms.strstockitemname AS lngstockitemcodename 
FROM m_stockitem ms 
WHERE ms.lngstocksubjectcode = _%strFormValue0%_ AND ms.bytdisplayflag = TRUE AND ms.bytinvalidflag = FALSE 
ORDER BY ms.lngstockitemcode
