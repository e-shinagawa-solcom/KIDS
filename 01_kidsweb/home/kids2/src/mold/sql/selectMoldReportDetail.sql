SELECT
      moldreportid
    , revision
    , listorder
    , moldno
    , molddescription
    , created
    , createby
    , updated
    , updateby
    , version
    , deleteflag
FROM
    t_moldreportdetail
WHERE
    moldreportid = $1
AND revision = $2
AND deleteflag = false
;
