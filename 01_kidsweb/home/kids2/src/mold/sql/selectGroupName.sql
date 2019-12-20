SELECT
  mg.strgroupdisplayname groupdisplayname
FROM
  m_group mg
  , m_groupattributerelation mgar 
WHERE
  mg.lnggroupcode = mgar.lnggroupcode 
  AND mgar.lngattributecode = 1 
  AND mg.bytgroupdisplayflag in ($2, $3)
  AND mg.strgroupdisplaycode = $1 
ORDER BY
  mg.strgroupdisplaycode
;