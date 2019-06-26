SELECT
    payfcd payfdisplaycode
    , payfformalname payfdisplayname
FROM
    m_payfinfo
WHERE
    invalidflag = false;
;
