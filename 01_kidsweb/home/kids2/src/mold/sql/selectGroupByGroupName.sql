SELECT
  mg.strgroupdisplaycode groupdisplaycode
  , mg.strgroupdisplayname groupdisplayname
FROM
  m_group mg
  , m_groupattributerelation mgar 
WHERE
  mg.lnggroupcode = mgar.lnggroupcode 
  AND mgar.lngattributecode = 1 
  AND mg.bytgroupdisplayflag in ($2, $3) 
  AND sf_translate_case(mg.strgroupdisplayname) LIKE '%' || sf_translate_case($1) || '%' 
ORDER BY
  mg.strgroupdisplaycode
;