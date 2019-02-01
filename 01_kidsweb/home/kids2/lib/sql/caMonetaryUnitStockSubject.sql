/*
	概要：仕入科目のチェック
	対象：発注、仕入
	作成：斎藤和志
	備考：選択された仕入科目から「通貨：2(US$)」「仕入科目：433」以外の場合の警告メッセージ
*/
SELECT COUNT(*),
		CASE WHEN COUNT(*) = 1 THEN '指定された仕入科目[433]では通貨を[US$]にする必要があります'
			ELSE ''
		END  as alert
WHERE
	2 <> (SELECT lngmonetaryunitcode FROM m_monetaryunit mm WHERE mm.strmonetaryunitsign = '_%strFormValue0%_')
	AND 433 =  '_%strFormValue1%_'
