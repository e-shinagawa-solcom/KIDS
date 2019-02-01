SELECT
    mp.strgoodscode as goodscode
FROM
    m_product mp
WHERE
    mp.strproductcode = $1
;
