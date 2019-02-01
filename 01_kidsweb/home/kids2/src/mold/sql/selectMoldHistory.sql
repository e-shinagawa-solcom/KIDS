SELECT
      moldno
    , historyno
    , status
    , actiondate
    , sourcefactory
    , destinationfactory
    , remark1
    , remark2
    , remark3
    , remark4
    , created
    , createby
    , updated
    , updateby
    , version
    , deleteflag
FROM
    t_moldhistory
WHERE
    moldno = $1
AND historyno = $2
AND version = $3
AND deleteflag = false
;
