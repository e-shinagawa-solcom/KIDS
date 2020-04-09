UPDATE t_moldhistory
SET
      actiondate = $4
    , sourcefactory = $5
    , destinationfactory = $6
    , remark1 = $7
    , remark2 = $8
    , remark3 = $9
    , remark4 = $10
    , updateby = $11
    , updated = $12
    , status = $13
WHERE
    moldno = $1
AND historyNo = $2
AND version = $3
RETURNING
    *
;
