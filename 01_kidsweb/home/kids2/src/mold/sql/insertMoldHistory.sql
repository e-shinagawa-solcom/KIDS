INSERT INTO t_moldhistory
(
    moldno
  , status
  , actiondate
  , sourcefactory
  , destinationfactory
  , remark1
  , remark2
  , remark3
  , remark4
  , craeted
  , CreateBy
  , updated
  , UpdateBy
)
VALUES
(
    $1
  , $2
  , $3
  , $4
  , $5
  , $6
  , $7
  , $8
  , $9
  , NOW()
  , $10
  , NOW()
  , $11
)
RETURNING
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
    , Created
    , CreateBy
    , Updated
    , UpdateBy
;
