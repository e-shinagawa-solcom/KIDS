SELECT
      tmrr.moldreportrelationid
    , tmrr.moldno
    , tmrr.historyno
    , tmrr.moldreportid
    , tmrr.revision
    , tmrr.created
    , tmrr.createby
    , tmrr.updated
    , tmrr.updateby
    , tmrr.version
    , tmrr.deleteflag
    , mmr.printed
    , mmr.version as report_version
FROM
    t_moldreportrelation tmrr
INNER JOIN
    m_moldreport mmr
  ON
        tmrr.moldreportid = mmr.moldreportid
    AND tmrr.revision = mmr.revision
WHERE
    tmrr.moldno = $1
AND tmrr.historyno = $2
AND tmrr.deleteflag = false
AND mmr.deleteflag = false
;
