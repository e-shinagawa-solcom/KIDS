SELECT
      strproductcode
    , strproductname
FROM
    m_product
WHERE
    strproductcode = $1
    and strrevisecode= $2
;
