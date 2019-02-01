SELECT
      lngcompanycode
    , strcompanydisplayname
FROM
    m_company
WHERE
    lngcompanycode = $1
;
