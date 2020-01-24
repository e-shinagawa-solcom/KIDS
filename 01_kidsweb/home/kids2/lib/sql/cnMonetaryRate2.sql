/*
	概要：「通貨レートコード」「通貨単位コード」から「適用開始月+1」を取得
	対象：マスタ管理
	作成：chiba
*/
/*
SELECT dtmApplyEndDate, 
  CASE
    WHEN lngMonetaryRateCode = 1
      THEN to_char ( date_trunc('month', dtmApplyEndDate + interval '2 month' ) - interval '1 day', 'YYYY/MM/DD')
      ELSE to_char ( date_trunc('month', dtmApplyEndDate + interval '1 year' + interval '1 month' ) - interval '1 day', 'YYYY/MM/DD')
  END
FROM m_MonetaryRate
WHERE lngMonetaryRateCode = _%strFormValue0%_
 AND lngMonetaryUnitCode = _%strFormValue1%_
 AND dtmApplyStartDate < now()
 AND dtmApplyEndDate > now()
UNION
 SELECT now() AS dtmApplyEndDate, to_char ( date_trunc('day', now() + interval '1 month' ), 'YYYY/MM/DD')
ORDER BY dtmApplyEndDate DESC

*/
SELECT dtmApplyEndDate, 
  CASE
    WHEN lngMonetaryRateCode = 1 or lngMonetaryUnitCode = 3
      THEN to_char ( date_trunc('month', dtmApplyEndDate + interval '2 month' ) - interval '1 day', 'YYYY/MM/DD')
      ELSE to_char ( date_trunc('month', dtmApplyEndDate + interval '1 year' + interval '1 month' ) - interval '1 day', 'YYYY/MM/DD')
  END
FROM m_MonetaryRate
WHERE lngMonetaryRateCode = _%strFormValue0%_
 AND lngMonetaryUnitCode = _%strFormValue1%_
 AND dtmApplyStartDate < now()
 AND dtmApplyEndDate > now()
UNION

SELECT now() AS dtmApplyEndDate,
 CASE
    WHEN 1 = _%strFormValue0%_ or 3= _%strFormValue1%_
      THEN to_char ( date_trunc('month', now() + interval '1 month' ) - interval '1 day', 'YYYY/MM/DD')
      ELSE to_char ( date_trunc('month', now() + interval '1 year' + interval '1 month' ) - interval '1 day', 'YYYY/MM/DD')
  END
ORDER BY dtmApplyEndDate DESC
