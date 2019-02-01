SELECT
    mp.strproductcode as productcode
FROM
    m_product mp
WHERE
    mp.strproductcode = $1
;
