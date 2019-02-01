/*
	���ס��졼�ȥ�����	��	�����졼��
	�оݡ�ȯ���������������
	���������͵�ʸ
	���͡�1.��Υ��쥯��ʸ�ǡ����֤��θ���ʤ����ֿ������졼�Ȥ�����
		  2.���Υ��쥯��ʸ�ǡ����֤��θ�����졼�Ȥ�����
		  3.��礷����̤ǡ����ֽ���¤��ؤ�
		����ǡ�2�������2�η�̤�2���ʤ����1�η�̤����뤳�Ȥ��Ǥ���
*/
SELECT mmr.lngMonetaryRateCode, mmr.curConversionRate, MAX(mmr.dtmapplystartdate)
FROM m_MonetaryRate mmr
JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode
WHERE mmr.lngmonetaryratecode = '_%strFormValue0%_'
	AND mmu.strmonetaryunitsign = '_%strFormValue1%_'
	AND mmr.dtmapplystartdate = (SELECT MAX(mmr2.dtmapplystartdate) FROM m_MonetaryRate mmr2 WHERE mmr2.lngMonetaryRateCode = mmr.lngMonetaryRateCode AND mmr2.lngMonetaryUnitCode = mmr.lngMonetaryUnitCode)
GROUP BY mmr.lngMonetaryRateCode, mmr.curConversionRate
UNION
SELECT mmr.lngMonetaryRateCode, mmr.curConversionRate, MAX(mmr.dtmapplystartdate)
FROM m_MonetaryRate mmr
JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode
WHERE mmr.dtmapplystartdate <= '_%strFormValue2%_'
	AND mmr.dtmapplyenddate >= '_%strFormValue2%_'
	AND mmr.lngmonetaryratecode = '_%strFormValue0%_'
	AND mmu.strmonetaryunitsign = '_%strFormValue1%_'
GROUP BY mmr.lngMonetaryRateCode, mmr.curConversionRate
ORDER BY 3
