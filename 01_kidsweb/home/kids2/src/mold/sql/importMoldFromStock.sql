insert into
    m_mold (
        moldno,
        vendercode,
        productcode
    )
select
    src.strmoldno,
    src.lngcustomercompanycode,
    src.strproductcode
from
    (
        select
            tsd.strmoldno,
            s1.lngcustomercompanycode,
            tsd.strproductcode
        from
            t_stockdetail tsd
            inner join (
                SELECT
                    s.lngStockno,
                    s.lngRevisionNo,
                    s.lngcustomercompanycode
                from
                    m_Stock s
                WHERE
                    s.lngRevisionNo = (
                        SELECT
                            MAX (s2.lngRevisionNo)
                        FROM
                            m_Stock s2
                        WHERE
                            s.strStockCode = s2.strStockCode
                            AND s2.bytInvalidFlag = false
                    )
                    AND 0 <= (
                        SELECT
                            MIN (s3.lngRevisionNo)
                        FROM
                            m_Stock s3
                        WHERE
                            s.strStockCode = s3.strStockCode
                            AND s3.bytInvalidFlag = false
                    )
                order by
                    s.strStockCode,
                    s.lngRevisionNo
            ) s1 on tsd.lngStockno = s1.lngStockno
            and tsd.lngRevisionNo = s1.lngRevisionNo
        where
            tsd.strmoldno is not null
            AND (
                (
                    tsd.lngStockItemCode = 1
                    AND tsd.lngStockSubjectCode = 433
                )
                OR (
                    tsd.lngStockItemCode = 8
                    AND tsd.lngStockSubjectCode = 431
                )
            )
        group by
            tsd.strmoldno,
            s1.lngcustomercompanycode,
            tsd.strproductcode
        order by
            tsd.strproductcode,
            tsd.strmoldno
    ) as src
    LEFT OUTER JOIN m_mold mm ON src.strmoldno = mm.moldno
WHERE
    mm.moldno is null
ORDER BY
    src.strmoldno;