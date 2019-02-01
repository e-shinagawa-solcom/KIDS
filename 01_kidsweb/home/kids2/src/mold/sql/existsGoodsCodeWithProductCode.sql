SELECT
      strgoodscode
    , strproductcode
FROM
    m_product
WHERE
    strgoodscode = $1
AND strproductcode = $2
;
