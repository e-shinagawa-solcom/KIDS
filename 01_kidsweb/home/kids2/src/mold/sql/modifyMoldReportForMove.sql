INSERT INTO m_moldreport
(
      MoldReportId
    , ReportCategory
    , RequestDate
    , SendTo
    , ProductCode
    , GoodsCode
    , RequestCategory
    , ActionRequestDate
    , TransferMethod
    , SourceFactory
    , DestinationFactory
    , InstructionCategory
    , CustomerCode
    , KuwagataGroupCode
    , KuwagataUserCode
    , FinalKeep
    , ReturnSchedule
    , Note
    , MarginalNote
    , CreateBy
    , UpdateBy
    , Created
    , Updated
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
    , $17
    , $18
    , $19
    , $20
    , $21
    , $22
    , $23
    , $24
)
RETURNING
    *
;
