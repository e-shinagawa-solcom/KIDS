SELECT DISTINCT
  mu.struserdisplaycode usercode
  , mu.struserdisplayname username 
FROM
 m_user mu
  , m_attributerelation mar 
WHERE
  mu.lngcompanycode = mar.lngcompanycode
  AND mar.lngattributecode = 1 
  AND mu.bytuserdisplayflag in ($1, $2)  
  AND mu.bytinvalidflag = false
ORDER BY
  mu.struserdisplaycode
;