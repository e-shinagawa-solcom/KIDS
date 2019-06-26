SELECT
    payfcd payfdisplaycode
    , payfformalname payfdisplayname
FROM
    m_payfinfo
WHERE
    payfcd = $1
    and payfformalname LIKE '%' || $2 || '%'
    and invalidflag = false;
;
