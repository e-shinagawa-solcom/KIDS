SELECT
    payfcd payfdisplaycode
    , payfformalname payfdisplayname
FROM
    m_payfinfo
WHERE
    payfcd = $1
    and invalidflag = false
ORDER BY payfcd
;
