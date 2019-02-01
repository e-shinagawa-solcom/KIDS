UPDATE t_moldhistory
SET deleteflag = true
WHERE
    moldno = $1
AND historyno = $2
AND version = $3
;
