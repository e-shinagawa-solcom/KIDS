INSERT INTO t_moldreportrelation
(
    moldno
  , historyno
  , moldreportid
  , revision
  , createby
  , updateby
  , created
  , updated
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
)
RETURNING
    *
;
