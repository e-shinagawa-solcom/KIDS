SELECT
      lngcompanycode
    , strcompanyname
FROM
    m_company
WHERE
    lngcompanycode = $1
;
