/*
	���ס��׾���	��	��Ψ
	�оݡ���������
	���������͵�ʸ
	���͡�
*/
SELECT lngtaxcode, curtax, MAX(dtmapplystartdate)
FROM m_tax
WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax)
GROUP BY lngtaxcode, curtax
UNION
SELECT lngtaxcode, curtax, MAX(dtmapplystartdate)
FROM m_tax
WHERE dtmapplystartdate <= '_%strFormValue0%_'
	AND dtmapplyenddate >= '_%strFormValue0%_'
GROUP BY lngtaxcode, curtax
ORDER BY 3
