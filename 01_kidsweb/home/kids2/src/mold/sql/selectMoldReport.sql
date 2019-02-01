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
    moldreportid = $1
AND revision = $2
AND version = $3
AND deleteflag = false
;
