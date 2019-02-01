SELECT
      mp.strproductcode as productcode
    , mp.strproductname as productname
    , mp.*
FROM
    m_product mp
WHERE
    mp.strproductcode = $1
ORDER BY
    mp.strproductcode
;
