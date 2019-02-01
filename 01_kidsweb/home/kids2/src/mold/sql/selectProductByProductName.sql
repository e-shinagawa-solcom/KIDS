SELECT
      mp.strproductcode as productcode
    , mp.strproductname as productname
FROM
    m_product mp
WHERE
    mp.strproductname LIKE '%' || $1 || '%'
ORDER BY
    mp.strproductcode
;
