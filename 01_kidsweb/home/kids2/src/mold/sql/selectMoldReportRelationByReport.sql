SELECT
      moldreportrelationid
    , moldno
    , historyno
    , moldreportid
    , revision
    , created
    , createby
    , updated
    , updateby
    , version
    , deleteflag
FROM
    t_moldreportrelation
WHERE
    moldreportid = $1
AND revision = $2
AND deleteflag = false
;
