SELECT
    struserdisplayname userdisplayname 
FROM
    m_user
WHERE
    struserdisplaycode = $1
;