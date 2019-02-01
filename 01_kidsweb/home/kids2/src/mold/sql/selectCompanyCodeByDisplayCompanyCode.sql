SELECT
      lngcompanycode
    , strcompanydisplaycode
FROM
    m_company
WHERE
    strcompanydisplaycode = $1
;
