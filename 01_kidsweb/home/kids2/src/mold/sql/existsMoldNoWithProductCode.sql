SELECT
      mm.moldno
    , mm.productcode
FROM
    m_mold mm
WHERE
    mm.moldno = $1
AND mm.productcode = $2
AND mm.deleteflag = false
;
