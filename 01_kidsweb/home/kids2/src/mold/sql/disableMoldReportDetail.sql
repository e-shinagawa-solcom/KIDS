UPDATE t_moldreportdetail
SET deleteflag = true
WHERE
    moldreportid = $1
AND revision = $2
;
