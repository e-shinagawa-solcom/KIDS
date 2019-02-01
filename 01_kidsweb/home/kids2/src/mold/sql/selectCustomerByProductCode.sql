SELECT
    mc.strcompanydisplaycode companydisplaycode
FROM
    m_product mp
INNER JOIN
    m_company mc
  ON
    mp.lngcustomercompanycode = mc.lngcompanycode
WHERE
    mp.strproductcode = $1
;
