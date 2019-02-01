SELECT
      lnggroupcode
    , strgroupdisplaycode
FROM
    m_group
WHERE
    strgroupdisplaycode = $1
;
