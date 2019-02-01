SELECT
    mu.struserdisplaycode userdisplaycode
FROM
    m_product mp
INNER JOIN
    m_user mu
  ON
    mp.lnginchargeusercode = mu.lngusercode
WHERE
    mp.strproductcode = $1
;
