SELECT
      mc.lngcompanycode as companycode
    , mc.strcompanydisplaycode as customerdisplaycode
    , mc.strcompanydisplayname as customerdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mc.strcompanydisplaycode = $1
AND mar.lngattributecode in (2, 99)
AND mc.bytcompanydisplayflag = true
;
