SELECT
      mmr.moldreportid as moldreportid
FROM
    m_moldreport mmr
WHERE
    mmr.moldreportid = $1
AND mmr.deleteflag = false
;
