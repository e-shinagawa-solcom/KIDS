SELECT
      lngusercode
    , struserdisplaycode
FROM
    m_user
WHERE
    lngusercode = $1
;
