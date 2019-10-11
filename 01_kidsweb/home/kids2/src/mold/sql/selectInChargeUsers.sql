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
  AND mg.bytgroupdisplayflag = true 
  AND mu.bytuserdisplayflag = true 
  AND mg.strgroupdisplaycode LIKE '%' || $1 || '%'
ORDER BY
  mu.struserdisplaycode
;