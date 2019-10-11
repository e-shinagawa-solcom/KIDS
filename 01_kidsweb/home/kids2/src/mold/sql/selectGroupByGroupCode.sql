SELECT
  mg.strgroupdisplaycode groupdisplaycode
  , mg.strgroupdisplayname groupdisplayname
FROM
  m_group mg
  , m_groupattributerelation mgar 
WHERE
  mg.lnggroupcode = mgar.lnggroupcode 
  AND mgar.lngattributecode = 1 
  AND mg.bytgroupdisplayflag = true
  AND mg.strgroupdisplaycode = $1 
ORDER BY
  mg.strgroupdisplaycode
;