SELECT
      moldno
    , historyno
    , version
FROM
    t_moldhistory
WHERE
    moldno in(
        _%moldno%_
    )
ORDER BY
      moldno
    , historyno
    , version
;
