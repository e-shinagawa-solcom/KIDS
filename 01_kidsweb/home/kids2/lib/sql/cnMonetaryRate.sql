/*
	���ס����̲ߥ졼�ȥ����ɡס��̲�ñ�̥����ɡפ����Ŭ�ѽ�λ��+1�פ����
	�оݡ��ޥ�������
	������chiba
*/
/*
SELECT dtmApplyEndDate, to_char ( date_trunc('month', dtmApplyEndDate + interval '1 month' ), 'YYYY/MM/DD')
FROM m_MonetaryRate
WHERE lngMonetaryRateCode = _%strFormValue0%_
 AND lngMonetaryUnitCode = _%strFormValue1%_
 AND dtmApplyStartDate < now()
 AND dtmApplyEndDate > now()
UNION 
SELECT now() AS dtmApplyEndDate, to_char ( date_trunc('month', now() + interval '1 month' ), 'YYYY/MM/DD')
ORDER BY dtmApplyEndDate DESC
*/
SELECT dtmApplyEndDate, to_char ( date_trunc('month', dtmApplyendDate + interval '1 month' ), 'YYYY/MM/DD')
FROM m_MonetaryRate
WHERE lngMonetaryRateCode = _%strFormValue0%_
 AND lngMonetaryUnitCode = _%strFormValue1%_
 AND dtmApplyStartDate < now()
 AND dtmApplyEndDate > now()
UNION 
SELECT now() as dtmApplyEndDate, to_char ( date_trunc('month', now()  ), 'YYYY/MM/DD')
order by dtmApplyEndDate desc
limit 1