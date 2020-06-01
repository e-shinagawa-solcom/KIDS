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
  AND mg.bytgroupdisplayflag in ($4, $5) 
  AND mu.bytuserdisplayflag in ($4, $5) 
  AND mg.strgroupdisplaycode LIKE '%' || $1 || '%'
  AND mu.struserdisplaycode = $2
  AND sf_translate_case(mu.struserdisplayname) LIKE '%' || sf_translate_case($3) || '%'
  AND mu.bytinvalidflag = false
ORDER BY
  mu.struserdisplaycode
;