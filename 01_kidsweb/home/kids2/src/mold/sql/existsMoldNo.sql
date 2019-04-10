SELECT
      mm.moldno as moldno
FROM
    m_mold mm
WHERE
    mm.moldno = $1
AND mm.deleteflag = false
;
