SELECT DISTINCT
  mu.struserdisplaycode usercode
  , mu.struserdisplayname username 
FROM
  m_user mu
  , m_company mc
  , m_attributerelation mal 
WHERE
  mc.lngcompanycode = mal.lngcompanycode 
  AND mal.lngattributecode = 2 
  AND mu.lngcompanycode = mc.lngcompanycode 
  AND mc.bytcompanydisplayflag = true 
  AND mu.bytuserdisplayflag = true 
  AND mc.strcompanydisplaycode LIKE '%' || $1 || '%' 
  AND sf_translate_case(mu.struserdisplayname) LIKE '%' || sf_translate_case($2) || '%' 
ORDER BY
  mu.struserdisplaycode
; 