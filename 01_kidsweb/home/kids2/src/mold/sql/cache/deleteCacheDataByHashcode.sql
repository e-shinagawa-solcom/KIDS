DELETE
FROM
    t_cache tc
WHERE
    tc.hashcode = $1
;
