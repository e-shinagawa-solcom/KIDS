SELECT
    mm.moldno
FROM
    m_mold mm
WHERE

    mm.moldno = $1
AND mm.moldno not in
(
    SELECT
        obsolete_mold.moldno
    FROM
    (
        SELECT
              moldno
            , max(historyno)
            , status
        FROM
            t_moldhistory
        WHERE
            moldno in
            (
                SELECT
                    moldno
                FROM
                    m_mold
                WHERE
                    deleteflag = false
            )
        AND deleteflag = false
        GROUP BY
              moldno
            , status
        HAVING
            status in ('30')
    ) obsolete_mold
)

AND mm.moldno not in
(
    SELECT
        exclude.moldno
    FROM
    (
        SELECT
              ex_tmh.moldno
            , ex_tmh.historyno
            , max(ex_tmh.actiondate) as actiondate
        FROM
            t_moldhistory ex_tmh
        INNER JOIN
            m_mold ex_mm
          ON
            ex_tmh.moldno = ex_mm.moldno
        WHERE
            ex_tmh.status in ('10', '20')
        AND ex_tmh.deleteflag = false
        GROUP BY
            ex_tmh.moldno
          , ex_tmh.historyno
    ) exclude
    WHERE

        now()::date < exclude.actiondate
)
;
