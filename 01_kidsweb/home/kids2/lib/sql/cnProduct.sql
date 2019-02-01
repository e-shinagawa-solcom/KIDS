/*
	概要：「製品コード」から「製品名称」を取得
	対象：発注管理、仕入管理、受注管理、売上管理
	作成：watanabe
	更新：斎藤和志
	備考：「コード」から一致する「名称」を取得
*/
SELECT mp.lngproductno,
	CASE WHEN mp.strproductname IS NULL THEN '（製品名称が空です）'
		ELSE mp.strproductname
	END
FROM m_product mp
WHERE mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
	and ( mp.lngproductstatuscode = 0 or mp.lngproductstatuscode is null) /* WF申請中 */
