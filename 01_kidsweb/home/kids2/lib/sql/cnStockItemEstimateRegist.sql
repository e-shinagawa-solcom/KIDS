/*
	概要：「仕入科目コード」から「名称」を取得
	対象：発注管理、仕入管理
	作成：watanabe
	更新：斎藤和志
	更新：涼風敬二
	備考：「コード」から一致する「名称」を取得　見積原価登録関連画面用（表示フラグ、削除フラグが設定されていない一覧の取得）
*/
SELECT ms.lngstockitemcode, ms.lngstockitemcode || ' ' || ms.strstockitemname AS lngstockitemcodename 
FROM m_stockitem ms 
WHERE ms.lngstocksubjectcode = _%strFormValue0%_ AND ms.bytdisplayestimateflag = TRUE AND ms.bytinvalidflag = FALSE 
ORDER BY ms.lngstockitemcode
