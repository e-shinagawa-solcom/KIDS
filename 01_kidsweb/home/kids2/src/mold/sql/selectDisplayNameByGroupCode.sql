SELECT
      lnggroupcode
    , strgroupdisplayname
FROM
    m_group
WHERE
    lnggroupcode = $1
;
