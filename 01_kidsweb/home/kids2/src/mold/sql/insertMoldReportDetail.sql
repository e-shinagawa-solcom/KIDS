INSERT INTO t_moldreportdetail
(
      moldreportid
    , revision
    , listorder
    , moldno
    , molddescription
    , CreateBy
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
)
;
