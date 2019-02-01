SELECT
      moldno
    , historyno
    , destinationfactory
FROM
    t_moldhistory
WHERE
    (moldno, historyno) in
    (
        SELECT
              tmh.moldno
            , max(tmh.historyno) as historyno
        FROM
            t_moldhistory tmh
        WHERE
            tmh.moldno =
            (
                SELECT
                    mm.moldno
                FROM
                    m_mold mm
                WHERE
                    mm.moldno = $1

                AND not exists
                    (
                        SELECT distinct
                            moldno
                        FROM
                            t_moldhistory
                        WHERE
                            moldno = $1
                        AND status = '30'
                        AND deleteflag = false
                    )

                AND not exists
                    (
                        SELECT
                            moldno
                        FROM
                            t_moldhistory
                        WHERE
                            moldno = $1
                        AND now()::date < actiondate
                        AND deleteflag = false
                    )
            )
         AND tmh.status in ('10', '20')
         AND tmh.deleteflag = false
         GROUP BY
               tmh.moldno
    )
;
