/*
	概要：計上日の締め日該当チェック
	対象：仕入
	作成：斎藤和志
	備考：計上日に指定された月内に一件以上の締め日データが存在した場合、警告
*/

SELECT COUNT(*),
		CASE WHEN COUNT(*) >= 1 THEN '入力された計上月「'|| to_char( date_trunc( 'month', to_date('_%strFormValue0%_', 'YYYY/MM') ), 'YYYY/MM') ||'」は既に締め処理済みの為入力出来ません。経理部門に相談して下さい。'
			ELSE ''
		END  as alert
FROM
	m_Stock
WHERE
	lngStockStatusCode =99
	AND bytInvalidFlag = FALSE
	AND  to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM') = to_char( date_trunc( 'month', to_date('_%strFormValue0%_', 'YYYY/MM') ), 'YYYY/MM')
