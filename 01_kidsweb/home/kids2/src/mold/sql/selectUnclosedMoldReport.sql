SELECT
      moldreportid
    , revision
    , reportcategory
    , status
    , requestdate
    , sendto
    , attention
    , carboncopy
    , productcode
    , goodscode
    , requestcategory
    , actionrequestdate
    , actiondate
    , transfermethod
    , sourcefactory
    , destinationfactory
    , instructioncategory
    , customercode
    , kuwagatagroupcode
    , kuwagatausercode
    , note
    , finalkeep
    , returnschedule
    , marginalnote
    , printed
    , created
    , createby
    , updated
    , updateby
    , version
    , deleteflag
FROM
    m_moldreport
WHERE
    status = '00'
AND actionrequestdate < now()::date
AND deleteflag = false
;
