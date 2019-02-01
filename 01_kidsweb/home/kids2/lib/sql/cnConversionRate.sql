/*
	概要：レートタイプ	→	換算レート
	対象：発注管理、仕入管理
	作成：手塚貴文
	備考：1.上のセレクト文で、期間を考慮しない一番新しいレートを得る
		  2.下のセレクト文で、期間を考慮したレートを得る
		  3.結合した結果で、期間順に並び替え
		これで、2があれば2の結果を、2がなければ1の結果を得ることができる
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
