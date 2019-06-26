SELECT
    payfcd payfdisplaycode
    , payfformalname payfdisplayname
FROM
    m_payfinfo
WHERE
    payfformalname LIKE '%' || $1 || '%'
    and invalidflag = false;
;
