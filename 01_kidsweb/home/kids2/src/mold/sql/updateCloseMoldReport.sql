UPDATE m_moldreport
SET status = '50'
WHERE
    status = '00'
AND moldreportid = $1
AND revision = $2
AND deleteflag = false
;
