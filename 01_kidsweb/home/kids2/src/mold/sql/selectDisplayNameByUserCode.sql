SELECT
      lngusercode
    , struserdisplayname
FROM
    m_user
WHERE
    lngusercode = $1
;
