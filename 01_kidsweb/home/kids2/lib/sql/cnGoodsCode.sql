/*
	概要：「製品コード」から「顧客品番」を取得
	対象：発注管理、仕入管理、受注管理、売上管理
	作成：watanabe
	更新：斎藤和志
	備考：「コード」から一致する「値」を取得
*/
SELECT mp.lngproductno, mp.strGoodsCode FROM m_product mp
WHERE mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
