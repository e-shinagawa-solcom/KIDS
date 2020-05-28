SELECT
      moldno
    , SPLIT_PART(companydisplaycode,'_',1) as companydisplaycode
	, SPLIT_PART(companydisplaycode,'_',2) as referrer
    , (
              SELECT
                  strcompanydisplayname
              FROM
                  m_company
              WHERE
                  strcompanydisplaycode = SPLIT_PART(companydisplaycode,'_',1)
      ) companydisplayname
FROM
(
    SELECT
          mm.moldno
        , COALESCE(

              (
                  SELECT
                      c.strcompanydisplaycode || '_' || 'MOVEFAC' as strcompanydisplaycode
                  FROM
                      t_moldhistory itmh
                  INNER JOIN
                      m_company c
                    ON
                      itmh.destinationfactory = c.lngcompanycode
                  WHERE
                      (moldno, historyno, actiondate) in
                      (
                          SELECT
                                imh.moldno
                              , max(imh.historyno)
                              , max(imh.actiondate)
                          FROM
                              t_moldhistory imh
                          WHERE
                              imh.moldno = mm.moldno
                          AND imh.status in ('10', '20')
                          AND imh.deleteflag = false
                          GROUP BY
                              imh.moldno
                      )
              ),

              (
                  SELECT
                      c.strcompanydisplaycode || '_' || 'VENDER' as strcompanydisplaycode
                  FROM
                      m_mold m
                  INNER JOIN
                      m_company c
                    ON
                      m.vendercode = c.lngcompanycode
                  WHERE
                      m.moldno = mm.moldno
                  AND m.deleteflag = false
              ),

              '索引エラー'
          ) companydisplaycode
    FROM
        m_mold mm
    WHERE
        mm.productcode = $1
        and mm.strrevisecode = $2

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
                            productcode = $1
                        AND strrevisecode = $2
                        AND deleteflag = false
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
                    , max(ex_tmh.historyno) as historyno
                    , max(ex_tmh.actiondate) as actiondate
                FROM
                    t_moldhistory ex_tmh
                INNER JOIN
                    m_mold ex_mm
                  ON
                    ex_tmh.moldno = ex_mm.moldno
                WHERE
                    ex_mm.productcode = $1
                AND ex_mm.strrevisecode = $2
                AND ex_tmh.status in ('10', '20')
                AND ex_tmh.deleteflag = false
                GROUP BY
                    ex_tmh.moldno
            ) exclude
            WHERE

                now()::date < exclude.actiondate
        )


    AND mm.moldno not in
        (
            SELECT DISTINCT
                moldno
            FROM
                m_moldreport mmr
            INNER JOIN
                t_moldreportdetail tmrd
              ON
                    mmr.moldreportid = tmrd.moldreportid
                AND mmr.revision = tmrd.revision
            WHERE
                    tmrd.deleteflag = false
                AND status = '00'
                AND not mmr.moldreportid = $3
        )
) as ml
ORDER BY
    moldno
;
