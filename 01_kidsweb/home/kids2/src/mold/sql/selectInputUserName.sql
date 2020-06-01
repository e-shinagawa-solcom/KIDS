SELECT distinct mu.struserdisplayname userdisplayname 
FROM
  m_user mu
  , m_attributerelation mar 
WHERE
  mu.lngcompanycode = mar.lngcompanycode 
  AND mar.lngattributecode = 1 
  AND mu.bytuserdisplayflag in ($2, $3) 
  AND mu.struserdisplaycode = $1
  ;