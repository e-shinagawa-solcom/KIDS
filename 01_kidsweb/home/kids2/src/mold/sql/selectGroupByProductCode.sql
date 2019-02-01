SELECT
    strgroupdisplaycode groupdisplaycode
FROM
    m_product mp
INNER JOIN
    m_group mg
  ON
    mp.lnginchargegroupcode = mg.lnggroupcode
WHERE
    mp.strproductcode = $1
;
