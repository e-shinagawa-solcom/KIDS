SELECT
  lngMonetaryUnitCode
  , curConversionRate
  , dtmApplyStartDate
  , dtmApplyEndDate 
FROM
  m_MonetaryRate 
WHERE
  lngMonetaryRateCode = '2' 
  AND lngMonetaryUnitCode = $1
ORDER BY
  dtmApplyStartDate DESC
  , dtmApplyEndDate DESC
    OFFSET 0 LIMIT 6;