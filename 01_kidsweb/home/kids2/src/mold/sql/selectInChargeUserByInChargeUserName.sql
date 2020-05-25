SELECT DISTINCT
  mu.struserdisplaycode usercode
  , mu.struserdisplayname username 
FROM
  m_user mu
  , m_group mg
  , m_grouprelation mgr
  , m_groupattributerelation mgar 
WHERE
  mu.lngusercode = mgr.lngusercode 
  and mg.lnggroupcode = mgr.lnggroupcode 
  and mgar.lnggroupcode = mg.lnggroupcode 
  AND mgar.lngattributecode = 1 
  AND mg.bytgroupdisplayflag in ($3, $4)
  AND mu.bytuserdisplayflag in ($3, $4) 
  AND mg.strgroupdisplaycode LIKE '%' || $1 || '%'
  AND sf_translate_case(mu.struserdisplayname) LIKE '%' || sf_translate_case($2) || '%'
ORDER BY
  mu.struserdisplaycode
;