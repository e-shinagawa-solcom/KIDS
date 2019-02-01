SELECT
    mu.struserdisplayname userdisplayname
FROM
    m_user mu
WHERE
      mu.struserdisplaycode = $1
;
