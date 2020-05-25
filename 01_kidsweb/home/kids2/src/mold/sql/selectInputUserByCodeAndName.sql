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
  AND mg.bytgroupdisplayflag in ($3, $4) 
  AND mu.bytuserdisplayflag in ($3, $4) 
  AND mu.struserdisplaycode = $1
  AND sf_translate_case(mu.struserdisplayname) LIKE '%' || sf_translate_case($2) || '%'
ORDER BY
  mu.struserdisplaycode
;
