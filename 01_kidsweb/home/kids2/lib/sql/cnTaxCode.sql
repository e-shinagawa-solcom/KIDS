/*
	���ס��׾���	��	�ǥ�����
	�оݡ���������
	���������͵�ʸ
	���͡�
*/
SELECT lngtaxcode, lngtaxcode, MAX(dtmapplystartdate)
FROM m_tax
WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax)
GROUP BY lngtaxcode
UNION
SELECT lngtaxcode, lngtaxcode, MAX(dtmapplystartdate)
FROM m_tax
WHERE dtmapplystartdate <= '_%strFormValue0%_'
	AND dtmapplyenddate >= '_%strFormValue0%_'
GROUP BY lngtaxcode
ORDER BY 3
