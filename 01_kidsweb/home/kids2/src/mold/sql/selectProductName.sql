SELECT
      mp.strproductcode as productcode
    , mp.strproductname as productname
FROM
    m_product mp
WHERE
    mp.strproductcode = $1
;
