SELECT
      lngcompanycode
    , strcompanydisplaycode
FROM
    m_company
WHERE
    lngcompanycode = $1
;
