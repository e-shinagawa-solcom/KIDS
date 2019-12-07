INSERT INTO m_moldreport
(
      ReportCategory
    , RequestDate
    , SendTo
    , ProductCode
    , GoodsCode
    , RequestCategory
    , ActionRequestDate
    , InstructionCategory
    , CustomerCode
    , KuwagataGroupCode
    , KuwagataUserCode
    , Note
    , MarginalNote
    , CreateBy
    , UpdateBy
    , strReviseCode
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
    , $10
    , $11
    , $12
    , $13
    , $14
    , $15
    , $16
)
RETURNING
    *
;
