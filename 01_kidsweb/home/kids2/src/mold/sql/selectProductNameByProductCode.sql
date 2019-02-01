SELECT
      strproductcode
    , strproductname
FROM
    m_product
WHERE
    strproductcode = $1
;
