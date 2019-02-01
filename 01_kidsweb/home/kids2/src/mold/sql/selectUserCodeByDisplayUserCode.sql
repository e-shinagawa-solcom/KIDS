SELECT
      lngusercode
    , struserdisplaycode
FROM
    m_user
WHERE
    struserdisplaycode = $1
;
