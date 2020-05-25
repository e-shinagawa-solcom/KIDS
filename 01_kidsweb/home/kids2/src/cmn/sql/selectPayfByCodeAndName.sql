SELECT
    payfcd payfdisplaycode
    , payfformalname payfdisplayname
FROM
    m_payfinfo
WHERE
    payfcd = $1
    and sf_translate_case(payfformalname) LIKE '%' || sf_translate_case($2) || '%'
    and invalidflag = false
ORDER BY payfcd
;
