SELECT
      lnggroupcode
    , strgroupdisplaycode
FROM
    m_group
WHERE
    lnggroupcode = $1
;
