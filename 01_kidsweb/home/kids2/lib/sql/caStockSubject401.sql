/*
	概要：仕入科目のチェック
	対象：発注、仕入
	作成：斎藤和志
	備考：選択された仕入科目が「401」で備考が空の場合の警告メッセージ
*/
SELECT COUNT(*),
		CASE WHEN COUNT(*) = 1 THEN '仕入部品の内容を「備考」に記入して下さい。'
			ELSE ''
		END  as alert
WHERE
	 401 =  '_%strFormValue0%_'
	 AND '' = '_%strFormValue1%_'
