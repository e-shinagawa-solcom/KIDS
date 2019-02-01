SELECT
      mb.businesscodename
    , mb.businesscode
    , mb.description
FROM
    m_businesscode mb
WHERE
    mb.businesscodename = $1
AND mb.businesscode = $2
;
