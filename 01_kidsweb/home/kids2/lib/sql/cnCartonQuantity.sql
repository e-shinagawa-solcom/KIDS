/*
	概要：「製品コード」から「カートン入り数」を取得
	対象：発注管理、仕入管理、受注管理、売上管理
	作成：watanabe
	更新：斎藤和志
	備考：「コード」から一致する「値」を取得
*/
SELECT mp.lngproductno, mp.lngCartonQuantity FROM m_product mp
WHERE mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
