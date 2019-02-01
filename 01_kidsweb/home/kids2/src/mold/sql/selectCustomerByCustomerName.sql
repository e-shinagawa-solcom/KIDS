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
    mc.strcompanydisplayname LIKE '%' || $1 || '%'
AND mar.lngattributecode in (2, 99)
AND mc.bytcompanydisplayflag = true
ORDER BY
    mc.strcompanydisplaycode
;
