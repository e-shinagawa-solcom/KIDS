SELECT DISTINCT
      mu.struserdisplaycode usercode
    , mu.struserdisplayname username
FROM
  m_group mg
  , m_grouprelation mgr
  , m_user mu
  , m_attributerelation mar 
WHERE
  mu.lngcompanycode = mar.lngcompanycode 
  AND mu.lngusercode = mgr.lngusercode 
  AND mg.lnggroupcode = mgr.lnggroupcode 
  AND mar.lngattributecode = 1 
  AND mg.bytgroupdisplayflag in ($2, $3) 
  AND mu.bytuserdisplayflag in ($2, $3) 
  AND mu.struserdisplayname LIKE '%' || $1 || '%'
ORDER BY
  mu.struserdisplaycode
;