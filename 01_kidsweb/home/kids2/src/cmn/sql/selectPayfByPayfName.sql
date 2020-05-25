SELECT
    payfcd payfdisplaycode
    , payfformalname payfdisplayname
FROM
    m_payfinfo
WHERE
    sf_translate_case(payfformalname) LIKE '%' || sf_translate_case($1) || '%'
    and invalidflag = false
ORDER BY payfcd
;
