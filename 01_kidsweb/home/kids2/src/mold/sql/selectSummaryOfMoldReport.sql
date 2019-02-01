SELECT
      mr.moldreportid
    , mr.revision
    , mr.version
    , mrd.moldno
FROM
    m_moldreport mr
INNER JOIN
    t_moldreportdetail mrd
  ON
        mr.moldreportid = mrd.moldreportid
    AND mr.revision = mrd.revision
WHERE
    mrd.moldno in(
        _%moldno%_
    )
ORDER BY
      mr.moldreportid
    , mr.revision
    , mr.version
    , mrd.moldno
;
