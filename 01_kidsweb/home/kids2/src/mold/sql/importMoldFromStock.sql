insert into m_mold
(
      moldno
    , vendercode
    , productcode
    , strrevisecode
)
SELECT
      src.strmoldno
    , src.lngcustomercompanycode
    , src.strproductcode
    , src.strrevisecode
FROM
(
    SELECT
          tsd.strmoldno
        , ms.lngcustomercompanycode
        , tsd.strproductcode
        , tsd.strrevisecode
    FROM
        t_stockdetail tsd
    INNER JOIN
        m_stock ms
      ON
            tsd.lngstockno = ms.lngstockno
        AND tsd.lngrevisionno = ms.lngrevisionno
    WHERE
        (tsd.strmoldno, tsd.lngstockno, tsd.lngrevisionno, tsd.lngstockdetailno) in
        (
            SELECT
                  strmoldno
                , max(lngstockno)
                , max(lngrevisionno)
                , max(lngstockdetailno)
            FROM
                t_stockdetail
            WHERE
                strmoldno is not null
            AND
            (
                    (lngStockItemCode = 1 AND lngStockSubjectCode = 433)
                OR  (lngStockItemCode = 8 AND lngStockSubjectCode = 431)
            )
            GROUP BY
                strmoldno
        )
) src
LEFT OUTER JOIN
    m_mold mm
  ON
    src.strmoldno = mm.moldno
WHERE
   mm.moldno is null
ORDER BY
    src.strmoldno
;
