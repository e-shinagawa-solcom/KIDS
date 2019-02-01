UPDATE m_moldreport
SET
    printed = true
WHERE
    moldreportid = $1
AND revision = $2
AND printed = false
AND deleteflag = false
;
