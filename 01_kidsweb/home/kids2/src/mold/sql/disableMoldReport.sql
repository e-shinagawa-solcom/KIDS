UPDATE m_moldreport
SET deleteflag = true
WHERE
    moldreportid = $1
AND revision = $2
AND version = $3
;
