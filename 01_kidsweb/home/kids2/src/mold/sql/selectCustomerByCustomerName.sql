SELECT
      mc.strcompanydisplaycode customerdisplaycode
    , mc.strcompanydisplayname customerdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    sf_translate_case(mc.strcompanydisplayname) LIKE '%' || sf_translate_case($1) || '%'
AND mar.lngattributecode in (2, 99)
AND mc.bytcompanydisplayflag = true
ORDER BY
    mc.strcompanydisplaycode
;
