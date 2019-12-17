SELECT
      moldno
    , vendercode
    , productcode
    , strrevisecode
    , created
    , createby
    , updated
    , updateby
    , version
    , deleteflag
FROM
    m_mold
WHERE
    productcode = $1
AND deleteflag = false
;
